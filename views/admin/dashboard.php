<?php
require_once(__DIR__ . '/../../controllers/AuthController.php');
require_once(__DIR__ . '/../../controllers/AdminController.php');

// Cek apakah user sudah login sebagai admin
AuthController::cekLoginAdmin();

// Inisialisasi controller
$adminController = new AdminController();

// Ambil statistik untuk dashboard
$statistik = $adminController->getStatistikDashboard();
$data = $statistik['data'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem E-Voting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-semibold text-gray-800">
                        <i class="fas fa-vote-yea text-blue-600 mr-2"></i>
                        Admin Panel
                    </span>
                </div>
                <div class="flex items-center space-x-4">
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
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4">
        <!-- Welcome Message -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">
                Selamat Datang, <?php echo $_SESSION['admin_username']; ?>!
            </h1>
            <p class="text-gray-600">
                Kelola sistem voting dengan mudah melalui dashboard admin.
            </p>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 bg-opacity-75">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-gray-600">Total Pemilih</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo $data['total_user']; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-600 text-sm font-medium">
                        <?php echo $data['total_aktif']; ?> Aktif
                    </span>
                </div>
            </div>

            <!-- Total Voted Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 bg-opacity-75">
                        <i class="fas fa-vote-yea text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-gray-600">Sudah Memilih</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo $data['total_voted']; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-blue-600 text-sm font-medium">
                        <?php echo $data['persentase_voted']; ?>% Partisipasi
                    </span>
                </div>
            </div>

            <!-- Total Candidates Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 bg-opacity-75">
                        <i class="fas fa-user-tie text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-gray-600">Total Kandidat</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo $data['total_kandidat']; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="kelola_kandidat.php" class="text-purple-600 text-sm font-medium hover:text-purple-700">
                        Kelola Kandidat <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- User Status Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 bg-opacity-75">
                        <i class="fas fa-user-clock text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-gray-600">Belum Memilih</h2>
                        <p class="text-2xl font-semibold text-gray-800">
                            <?php echo $data['total_aktif'] - $data['total_voted']; ?>
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="kelola_user.php" class="text-yellow-600 text-sm font-medium hover:text-yellow-700">
                        Kelola User <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Manage Users -->
            <a href="kelola_user.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <i class="fas fa-users-cog text-2xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Kelola Pemilih</h3>
                        <p class="text-gray-600">Aktivasi dan manajemen akun pemilih</p>
                    </div>
                </div>
            </a>

            <!-- Manage Candidates -->
            <a href="kelola_kandidat.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <i class="fas fa-user-tie text-2xl text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Kelola Kandidat</h3>
                        <p class="text-gray-600">Tambah dan edit data kandidat</p>
                    </div>
                </div>
            </a>

            <!-- View Results -->
            <a href="../voting/hasil.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <i class="fas fa-chart-bar text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Lihat Hasil</h3>
                        <p class="text-gray-600">Pantau hasil voting secara real-time</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white shadow-inner mt-8">
        <div class="max-w-7xl mx-auto py-4 px-4">
            <p class="text-center text-gray-600">
                &copy; <?php echo date('Y'); ?> Sistem E-Voting. All rights reserved.
            </p>
        </div>
    </footer>
</body>

</html>