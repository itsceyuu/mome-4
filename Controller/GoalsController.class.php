<?php
require_once "Controller.class.php";

class GoalsController extends Controller
{

    private $goalModel;

    public function __construct()
    {
        $this->startSession();
        $this->goalModel = $this->model("GoalsModel");
    }

    // INDEX - tampilkan semua goals
    public function index()
    {
        // Validasi login
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $user_id = $_SESSION['idKlien'];
        $goals = $this->goalModel->getGoalsByUser($user_id);

        $message = $_SESSION['goal_message'] ?? null;
        $errors = $_SESSION['goal_errors'] ?? [];

        unset($_SESSION['goal_message'], $_SESSION['goal_errors']);

        $this->view("Goals.php", [
            'goals' => $goals,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    // STORE - tambah goal baru
    public function store()
    {
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $user_id = $_SESSION['idKlien'];
        $title = trim($_POST['title'] ?? '');
        $target_amount = $this->sanitizeNumber($_POST['target_amount'] ?? 0);
        $current_amount = $this->sanitizeNumber($_POST['current_amount'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $deadline = trim($_POST['deadline'] ?? '');

        // Validasi
        $errors = [];
        if (empty($title)) {
            $errors[] = 'Judul goal harus diisi.';
        }
        if ($target_amount <= 0) {
            $errors[] = 'Target amount harus lebih dari 0.';
        }
        if ($current_amount < 0) {
            $errors[] = 'Current amount tidak boleh negatif.';
        }
        if ($current_amount > $target_amount) {
            $errors[] = 'Current amount tidak boleh lebih besar dari target.';
        }
        if (!empty($deadline) && strtotime($deadline) < strtotime('today')) {
            $errors[] = 'Deadline harus tanggal yang akan datang.';
        }

        if (!empty($errors)) {
            $_SESSION['goal_errors'] = $errors;
            header("Location: index.php?c=GoalsController&m=index");
            exit;
        }

        // Simpan ke database
        $this->goalModel->addGoal($user_id, $title, $target_amount, $current_amount, $description, $deadline);
        $_SESSION['goal_message'] = 'Goal berhasil ditambahkan!';

        header("Location: index.php?c=GoalsController&m=index");
        exit;
    }

    // UPDATE - edit goal lengkap
    public function update()
    {
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $user_id = $_SESSION['idKlien'];
        $id = intval($_POST['goal_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $target_amount = $this->sanitizeNumber($_POST['target_amount'] ?? 0);
        $current_amount = $this->sanitizeNumber($_POST['current_amount'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $deadline = trim($_POST['deadline'] ?? '');

        if ($id && $title && $target_amount > 0) {
            $this->goalModel->updateGoal($id, $user_id, $title, $target_amount, $current_amount, $description, $deadline);
            $_SESSION['goal_message'] = 'Goal berhasil diperbarui!';
        } else {
            $_SESSION['goal_errors'] = ['Data tidak valid.'];
        }

        header("Location: index.php?c=GoalsController&m=detail&id=" . $id);
        exit;
    }

    // UPDATE PROGRESS - tambah tabungan
    public function updateProgress()
    {
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $user_id = $_SESSION['idKlien'];
        $id = intval($_POST['goal_id'] ?? 0);
        $amount_to_add = $this->sanitizeNumber($_POST['amount_to_add'] ?? 0);

        if ($id && $amount_to_add > 0) {
            // Ambil data goal saat ini
            $goal = $this->goalModel->getGoalById($id, $user_id);

            if ($goal) {
                $new_amount = $goal['current_amount'] + $amount_to_add;

                // Cek jangan sampai melebihi target
                if ($new_amount > $goal['target_amount']) {
                    $new_amount = $goal['target_amount'];
                }

                $this->goalModel->updateProgress($id, $user_id, $new_amount);
                $_SESSION['goal_message'] = 'Progress berhasil diupdate! Rp ' . number_format($amount_to_add, 0, ',', '.') . ' ditambahkan.';
            }
        } else {
            $_SESSION['goal_errors'] = ['Jumlah harus lebih dari 0.'];
        }

        header("Location: index.php?c=GoalsController&m=detail&id=" . $id);
        exit;
    }

    // DESTROY - hapus goal
    public function destroy()
    {
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $user_id = $_SESSION['idKlien'];
        $id = intval($_POST['goal_id'] ?? 0);

        if ($id) {
            $this->goalModel->deleteGoal($id, $user_id);
            $_SESSION['goal_message'] = 'Goal berhasil dihapus.';
        }

        header("Location: index.php?c=GoalsController&m=index");
        exit;
    }

    // DETAIL - lihat detail satu goal
    public function detail()
    {
        if (!isset($_SESSION['idKlien'])) {
            header("Location: index.php?c=Login&m=index");
            exit;
        }

        $user_id = $_SESSION['idKlien'];
        $goalId = intval($_GET['id'] ?? 0);

        if (!$goalId) {
            $_SESSION['goal_errors'] = ['Goal tidak ditemukan.'];
            header("Location: index.php?c=GoalsController&m=index");
            exit;
        }

        $goal = $this->goalModel->getGoalById($goalId, $user_id);

        if (!$goal) {
            $_SESSION['goal_errors'] = ['Goal tidak ditemukan atau bukan milik Anda.'];
            header("Location: index.php?c=GoalsController&m=index");
            exit;
        }

        // Hitung progress
        $progress = 0;
        if ($goal['target_amount'] > 0) {
            $progress = round(($goal['current_amount'] / $goal['target_amount']) * 100);
            if ($progress > 100) $progress = 100;
        }

        // Hitung sisa
        $remaining = $goal['target_amount'] - $goal['current_amount'];
        if ($remaining < 0) $remaining = 0;

        $message = $_SESSION['goal_message'] ?? null;
        $errors = $_SESSION['goal_errors'] ?? [];

        unset($_SESSION['goal_message'], $_SESSION['goal_errors']);

        $this->view("GoalsDetail.php", [
            'goal' => $goal,
            'progress' => $progress,
            'remaining' => $remaining,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    // HELPER - sanitize number input
    private function sanitizeNumber($input)
    {
        // Hapus semua karakter non-numeric kecuali titik dan koma
        $clean = preg_replace('/[^0-9.,]/', '', $input);
        // Ganti koma dengan titik untuk decimal
        $clean = str_replace(',', '.', $clean);
        return floatval($clean);
    }
}
