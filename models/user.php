<?php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class User extends Database
{
    public function login($username, $password)
    {
        $username = trim((string) $username);
        $password = trim((string) $password);
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {

                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_super_admin'] = $user['is_super_admin'];
                header('Location: ../dashboard');
                exit;
            }
        }
        $_SESSION['login_error'] = 'Username atau password salah.';
        header('Location: ../login');
        exit;

    }
    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        header('Location: /simedic/');
        exit;
    }
    public function canAccess(string $halaman): bool
    {
        $allowed = ['dashboard', 'stok-obat', 'pos-obat', 'histori-transaksi', 'list-product'];
        return in_array($halaman, $allowed);
    }
}

class SuperAdmin extends User
{
    public function __construct()
    {
        parent::__construct();
        if (isset($_SESSION['is_super_admin']) && !$_SESSION['is_super_admin']) {
            throw new Exception("Akses Ditolak: Class ini hanya untuk Super Admin.");
        }
    }
    public function addUser($username, $password)
    {
        $username = trim((string) $username);
        $password = trim((string) $password);
        $pass = password_hash($password, PASSWORD_DEFAULT);
        $this->runQuery("
            INSERT INTO users(username, password, is_super_admin)
            VALUES ('$username', '$pass', 0);
        ", "Username sudah digunakan");
    }
    public function deleteUser($id)
    {
        $id = (int) $id;
        $this->runQuery("
            DELETE FROM users WHERE id = $id;
        ", "Gagal menghapus user");
    }
    public function changeUserToSuperAdmin($id)
    {
        $id = (int) $id;
        $this->runQuery("
            UPDATE users SET is_super_admin = 1 WHERE id = $id;
        ", "Gagal mengubah role user");
    }
    public function changeUserToUser($id)
    {
        $id = (int) $id;
        $this->runQuery("
            UPDATE users SET is_super_admin = 0 WHERE id = $id;
        ", "Gagal mengubah role user");
    }
    public function getAllUsers(): array
    {
        $result = $this->runQuery("SELECT id, username, is_super_admin FROM users ORDER BY id ASC", "Gagal mengambil data user");
        if (!$result) {
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function canAccess(string $halaman): bool
    {
        $superOnly = ['dashboard', 'stok-obat', 'pos-obat', 'histori-transaksi', 'list-product', 'manajemen-user'];
        if (in_array($halaman, $superOnly))
            return true;
        return parent::canAccess($halaman);
    }
}

$user = new User();

try {
    $user = new SuperAdmin();
} catch (Exception $e) {
    $user = new User();
}

?>