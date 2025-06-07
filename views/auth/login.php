<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: ../admin/dashboard.php");
        exit();
    } else if ($_SESSION['user_type'] === 'user') {
        header("Location: ../user/dashboard.php");
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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_admin ? 'Login Admin' : 'Login Pemilih'; ?> - Sistem Voting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-96 backdrop-blur-sm bg-opacity-95">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">
                <?php echo $is_admin ? 'Login Admin' : 'Login Pemilih'; ?>
            </h1>
            <p class="text-gray-600 mt-2">Silakan masuk untuk melanjutkan</p>
        </div>

        <?php if ($pesan_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $pesan_error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1">
                    <input id="username" name="username" type="text" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" required
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div> <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </button>
            </div>
        </form>

        <?php if (!$is_admin): ?>
            <div class="mt-4 text-center text-sm">
                <p class="text-gray-600">
                    Belum punya akun?
                    <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Daftar disini
                    </a>
                </p>
            </div>
        <?php endif; ?>

        <div class="mt-4 text-center text-sm">
            <a href="login.php<?php echo $is_admin ? '' : '?type=admin'; ?>"
                class="font-medium text-gray-600 hover:text-gray-500">
                <?php echo $is_admin ? 'Login sebagai Pemilih' : 'Login sebagai Admin'; ?>
            </a>
        </div>
    </div>
</body>

</html>