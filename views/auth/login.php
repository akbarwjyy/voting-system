<?php
session_start();

// Include constants file first
require_once(__DIR__ . '/../../config/constants.php');

// Redirect jika sudah login
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: " . ROOT_PATH . "/views/admin/dashboard.php");
        exit();
    } else if ($_SESSION['user_type'] === 'user') {
        header("Location: " . ROOT_PATH . "/views/user/dashboard.php");
        exit();
    }
}

// Tentukan tipe login
$tipe_login = isset($_GET['type']) ? $_GET['type'] : 'user';
$is_admin = ($tipe_login === 'admin');

// Inisialisasi variabel pesan error
$pesan_error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    require_once(__DIR__ . '/../../config/database.php');
    require_once(__DIR__ . '/../../controllers/AuthController.php');

    $auth = new AuthController();

    if ($is_admin) {
        $hasil = $auth->prosesLoginAdmin($username, $password);
    } else {
        $hasil = $auth->prosesLoginUser($username, $password);
    }
    if ($hasil['sukses']) {
        // Redirect ke halaman yang sesuai
        header("Location: " . $hasil['redirect']);
        exit();
    } else {
        $pesan_error = $hasil['pesan'];
    }
}

// Set judul halaman
$pageTitle = $is_admin ? 'Login Admin' : 'Login Pemilih';

// Custom CSS untuk halaman login
$customCSS = '
<style>
    body {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
    }
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
</style>';

// Include header
require_once('../includes/header.php');
?>

<div class="flex-grow flex items-center justify-center p-4">
    <div class="login-card w-full max-w-md p-8">
        <h2 class="text-3xl font-bold text-center mb-2"><?php echo $is_admin ? 'Login Admin' : 'Login Pemilih'; ?></h2>
        <p class="text-gray-600 text-center mb-8">Silakan masuk untuk melanjutkan</p>

        <?php if ($pesan_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $pesan_error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                    <?php echo $is_admin ? 'Username' : 'Email'; ?>
                </label>
                <input type="<?php echo $is_admin ? 'text' : 'email'; ?>" id="username" name="username" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Masuk
                </button>
            </div>

            <div class="text-center">
                <?php if (!$is_admin): ?>
                    <p class="text-sm text-gray-600">
                        Belum punya akun?
                        <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">
                            Daftar disini
                        </a>
                    </p>
                <?php endif; ?>

                <p class="mt-2 text-sm text-gray-600">
                    <?php if ($is_admin): ?>
                        <a href="?type=user" class="font-medium text-blue-600 hover:text-blue-500">
                            Login sebagai Pemilih
                        </a>
                    <?php else: ?>
                        <a href="?type=admin" class="font-medium text-blue-600 hover:text-blue-500">
                            Login sebagai Admin
                        </a>
                    <?php endif; ?>
                </p>
            </div>
        </form>
    </div>
</div>

<?php require_once('../includes/footer.php'); ?>