<?php
session_start();
require_once(__DIR__ . '/../../controllers/AuthController.php');

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

// Inisialisasi variabel pesan
$pesan_error = '';
$pesan_sukses = '';

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController();

    // Ambil data dari form
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Proses registrasi menggunakan AuthController
    $hasil = $auth->prosesRegistrasi($nama, $email, $password, $konfirmasi_password);

    if ($hasil['sukses']) {
        $pesan_sukses = $hasil['pesan'];
        // Redirect ke halaman login setelah 2 detik
        header("refresh:2;url=login.php");
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
    <title>Registrasi Pemilih - Sistem Voting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md backdrop-blur-sm bg-opacity-95">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Registrasi Pemilih</h1>
            <p class="text-gray-600 mt-2">Daftar untuk berpartisipasi dalam voting</p>
        </div>

        <?php if ($pesan_error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $pesan_error; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($pesan_sukses): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $pesan_sukses; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" id="nama" name="nama" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label for="konfirmasi_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="mt-6">
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 transform hover:scale-105 transition duration-300">
                    <i class="fas fa-user-plus mr-2"></i> Daftar
                </button>
            </div>
        </form>

        <div class="mt-4 text-center text-sm">
            <p class="text-gray-600">
                Sudah punya akun?
                <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Login disini
                </a>
            </p>
        </div>
    </div>
</body>

</html>