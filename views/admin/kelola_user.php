<?php
require_once(__DIR__ . '/../../controllers/AuthController.php');
require_once(__DIR__ . '/../../controllers/AdminController.php');

// Cek apakah user sudah login sebagai admin
AuthController::cekLoginAdmin();

// Inisialisasi controller
$adminController = new AdminController();

// Proses aktivasi/deaktivasi jika ada
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];
    $result = false;
    $message = '';

    if ($action === 'activate') {
        $result = $adminController->aktivasiUser($user_id);
        $message = $result ? 'User berhasil diaktifkan' : 'Gagal mengaktifkan user';
    } else if ($action === 'deactivate') {
        $result = $adminController->deaktivasiUser($user_id);
        $message = $result ? 'User berhasil dinonaktifkan' : 'Gagal menonaktifkan user';
    }

    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $result ? 'success' : 'error';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil daftar user
$users = $adminController->getDaftarUser();
?>

<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-full flex flex-col">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center"> <a href="dashboard.php" class="text-2xl font-semibold text-gray-800">
                        <i class="fas fa-vote-yea text-blue-600 mr-2"></i>
                        E-Voting
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-blue-600 hover:text-blue-700">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali
                    </a>
                    <span class="text-gray-600">
                        <i class="fas fa-user mr-2"></i>
                        <?php echo $_SESSION['admin_username']; ?>
                    </span>
                    <a href="../../logout.php" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav> <!-- Main Content -->
    <div class="flex-grow">
        <div class="max-w-7xl mx-auto py-6 px-4">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Kelola User</h1>
                <p class="text-gray-600">Aktivasi dan manajemen akun pemilih</p>
            </div>

            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $_SESSION['flash_type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                    <?php
                    echo $_SESSION['flash_message'];
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- User List -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sudah Memilih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['nama']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($user['is_active']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Tidak Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($user['sudah_memilih']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Sudah
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Belum
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <?php if ($user['is_active']): ?>
                                            <button type="submit" name="action" value="deactivate"
                                                class="text-red-600 hover:text-red-900 mr-3">
                                                <i class="fas fa-user-times"></i> Nonaktifkan
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="activate"
                                                class="text-green-600 hover:text-green-900 mr-3">
                                                <i class="fas fa-user-check"></i> Aktifkan
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- Tutup div flex-grow -->

    <?php
    // Set pageTitle untuk footer
    $pageTitle = "Kelola User";
    include(BASE_PATH . '/views/includes/footer.php');
    ?>