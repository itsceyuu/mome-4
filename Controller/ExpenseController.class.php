<?php
require_once "Controller.class.php";

class ExpenseController extends Controller {

    public function index() {
        $this->startSession();
        
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $idKlien = $_SESSION['idKlien'];

        // Panggil model transaksi
        $transaksiModel = $this->model('ExpenseModel');

        // Ambil semua transaksi user ini
        $transaksiList = $transaksiModel->getAllTransactions($idKlien);

        $filterSort = $_GET['sort'] ?? 'newest';
        $filterType = $_GET['type'] ?? '';

        // Ambil transaksi berdasarkan filter type (income/expense atau semua)
        if (!empty($filterType) && in_array($filterType, ['income', 'expense'])) {
            // Ambil hanya income atau expense
            $transaksiList = $transaksiModel->getTransactionsByType($idKlien, $filterType);
        } else {
            // Ambil semua transaksi
            $transaksiList = $transaksiModel->getAllTransactions($idKlien);
        }

        // Sort berdasarkan newest/oldest
        if ($filterSort === 'oldest') {
            usort($transaksiList, function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
        } else {
            // Default: newest (DESC)
            usort($transaksiList, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }

        // Ambil ringkasan hari ini
        $summary = $transaksiModel->getTodaySummary($idKlien);
        
        // Kirim data ke tampilan dashboard transaksi
        $this->view('Trackurexpense.php', [
            'username' => $_SESSION['username'] ?? 'User',
            'transaksiList' => $transaksiList,
            'summary' => $summary,
            'filterSort' => $filterSort,
            'filterType' => $filterType
        ]);
    }

    public function tambah() {
        $this->startSession();

        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idKlien = $_SESSION['idKlien'];
            $title = trim($_POST['title'] ?? '');
            $date = $_POST['date'] ?? '';
            $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
            $type = $_POST['type'] ?? 'expense'; // income / expense
            $description = trim($_POST['description'] ?? '');

            // Validasi input
            if (empty($title) || empty($date) || $amount <= 0) {
                $_SESSION['error'] = 'Please fill all required fields correctly! Amount must be greater than 0.';
                header("Location: index.php?c=ExpenseController&m=index");
                exit;
            }

            // Validasi type
            if (!in_array($type, ['income', 'expense'])) {
                $_SESSION['error'] = 'Invalid transaction type!';
                header("Location: index.php?c=ExpenseController&m=index");
                exit;
            }

            try {
                $transaksiModel = $this->model('ExpenseModel');
                $result = $transaksiModel->addTransaction($idKlien, $title, $date, $amount, $type, $description);

                if ($result) {
                    $_SESSION['success'] = 'Transaction added successfully!';
                } else {
                    $_SESSION['error'] = 'Failed to add transaction! Please try again.';
                }
            } catch (Exception $e) {
                error_log("Error adding transaction: " . $e->getMessage());
                $_SESSION['error'] = 'An error occurred while adding the transaction.';
            }

            header("Location: index.php?c=ExpenseController&m=index");
            exit;
        } else {
            // Jika bukan POST, redirect ke index
            header("Location: index.php?c=ExpenseController&m=index");
            exit;
        }
    }

    public function hapus() {
        $this->startSession();

        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        // Ambil ID dari POST atau GET
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['transaction_id'] ?? 0);
        } else {
            // Jika akses langsung via GET dengan parameter id
            $id = intval($_GET['id'] ?? 0);
        }

        if (empty($id)) {
            $_SESSION['error'] = 'Invalid transaction ID!';
            header("Location: index.php?c=ExpenseController&m=index");
            exit;
        }

        try {
            // Verifikasi bahwa transaction ini milik user yang login (security)
            $transaksiModel = $this->model('ExpenseModel');
            $transaction = $transaksiModel->getTransactionById($id);
            
            if (!$transaction) {
                $_SESSION['error'] = 'Transaction not found!';
                header("Location: index.php?c=ExpenseController&m=index");
                exit;
            }

            // Verifikasi ownership - pastikan transaction milik user yang login
            if (isset($transaction['user_id']) && $transaction['user_id'] != $_SESSION['idKlien']) {
                $_SESSION['error'] = 'You do not have permission to delete this transaction!';
                header("Location: index.php?c=ExpenseController&m=index");
                exit;
            }

            $result = $transaksiModel->deleteTransaction($id);

            if ($result) {
                $_SESSION['success'] = 'Transaction deleted successfully!';
            } else {
                $_SESSION['error'] = 'Failed to delete transaction! Please try again.';
            }
        } catch (Exception $e) {
            error_log("Error deleting transaction: " . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while deleting the transaction.';
        }
    
        header("Location: index.php?c=ExpenseController&m=index");
        exit;
    }

    public function edit() {
        $this->startSession();
        
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['transaction_id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $date = $_POST['date'] ?? '';
            $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
            $type = $_POST['type'] ?? 'expense';
            $description = trim($_POST['description'] ?? '');

            // Validasi input
            if (empty($id) || empty($title) || empty($date) || $amount <= 0) {
                $_SESSION['error'] = 'Please fill all required fields correctly! Amount must be greater than 0.';
                header("Location: index.php?c=ExpenseController&m=index");
                exit;
            }

            // Validasi type
            if (!in_array($type, ['income', 'expense'])) {
                $_SESSION['error'] = 'Invalid transaction type!';
                header("Location: index.php?c=ExpenseController&m=index");
                exit;
            }

            try {
                // Verifikasi bahwa transaction ini milik user yang login (security)
                $transaksiModel = $this->model('ExpenseModel');
                $transaction = $transaksiModel->getTransactionById($id);
                
                if (!$transaction) {
                    $_SESSION['error'] = 'Transaction not found!';
                    header("Location: index.php?c=ExpenseController&m=index");
                    exit;
                }

                // Verifikasi ownership - pastikan transaction milik user yang login
                if (isset($transaction['user_id']) && $transaction['user_id'] != $_SESSION['idKlien']) {
                    $_SESSION['error'] = 'You do not have permission to edit this transaction!';
                    header("Location: index.php?c=ExpenseController&m=index");
                    exit;
                }

                // Update di database
                $result = $transaksiModel->updateTransaction($id, $title, $date, $amount, $type, $description);

                if ($result) {
                    $_SESSION['success'] = 'Transaction updated successfully!';
                } else {
                    $_SESSION['error'] = 'Failed to update transaction! Please try again.';
                }
            } catch (Exception $e) {
                error_log("Error updating transaction: " . $e->getMessage());
                $_SESSION['error'] = 'An error occurred while updating the transaction.';
            }

            header("Location: index.php?c=ExpenseController&m=index");
            exit;
        } else {
            // Jika bukan POST, redirect ke index
            header("Location: index.php?c=ExpenseController&m=index");
            exit;
        }
    }
}
?>
