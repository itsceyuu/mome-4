<?php
require_once "Controller.class.php";

class RecapController extends Controller {

    public function index() {
        // Pastikan session aktif
        $this->startSession();

        // Kalau belum login, arahkan ke halaman login
        // Gunakan $_SESSION['idKlien'] untuk konsistensi dengan controller lainnya
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        // Ambil ID user yang sedang login
        $userId = $_SESSION['idKlien'];

        // Ambil bulan dari parameter GET, default ke bulan sekarang
        // Tahun selalu tahun ini
        $month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
        $year = date('Y');

        // Panggil model untuk ambil data transaksi
        $expenseModel = $this->model('ExpenseModel');
        $allTransactions = $expenseModel->getAllTransactions($userId);

        // Filter transaksi berdasarkan bulan yang dipilih (tahun ini saja)
        $transactions = array_filter($allTransactions, function($t) use ($month, $year) {
            $transDate = strtotime($t['date']);
            return date('n', $transDate) == $month && date('Y', $transDate) == $year;
        });

        // Urutkan berdasarkan tanggal ascending (dari awal bulan ke akhir bulan)
        usort($transactions, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        // Inisialisasi total dan running balance
        $totalIncome = 0;
        $totalOutcome = 0;
        $runningBalance = 0;
        $formattedTransactions = [];

        // Loop semua transaksi dan pisahkan income/expense
        foreach ($transactions as $t) {
            $income = ($t['type'] === 'income') ? $t['amount'] : 0;
            $outcome = ($t['type'] === 'expense') ? $t['amount'] : 0;

            // Update running balance
            if ($t['type'] === 'income') {
                $runningBalance += $t['amount'];
            } else {
                $runningBalance -= $t['amount'];
            }

            $formattedTransactions[] = [
                'date' => $t['date'],
                'title' => $t['title'],
                'income' => $income,
                'outcome' => $outcome,
                'balance' => $runningBalance
            ];

            $totalIncome += $income;
            $totalOutcome += $outcome;
        }

        // Hitung saldo akhir
        $finalBalance = $totalIncome - $totalOutcome;

        // Nama bulan
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        // Kirim semua data ke view
        $this->view('Recap.php', [
            'transactions' => $formattedTransactions,
            'totalIncome' => $totalIncome,
            'totalOutcome' => $totalOutcome,
            'finalBalance' => $finalBalance,
            'currentMonth' => $month,
            'monthName' => $monthNames[$month]
        ]);
    }
}
?>