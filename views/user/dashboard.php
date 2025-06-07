<?php
require_once(__DIR__ . '/../../controllers/AuthController.php');
require_once(__DIR__ . '/../../controllers/VoteController.php');

// Cek apakah user sudah login
AuthController::cekLoginUser();

// Inisialisasi VoteController
$voteController = new VoteController();
$statusVoting = $voteController->cekStatusVoting();

// Set page title
$pageTitle = "Dashboard Pemilih";
include('../includes/header.php');
?>

<!-- Main Content -->
<div class="max-w-7xl mx-auto py-6 px-4">
    <!-- Welcome Message -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Selamat Datang, <?php echo $_SESSION['user_nama']; ?>!
        </h1>
        <p class="text-gray-600">
            Gunakan hak suara Anda dengan bijak untuk memilih ketua OSIS periode 2025.
        </p>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Status Voting</h2>
                <?php if ($statusVoting['voting_aktif']): ?>
                    <p class="text-green-600">
                        <i class="fas fa-circle text-green-500 mr-2"></i>
                        Voting Sedang Berlangsung
                    </p>
                <?php else: ?>
                    <p class="text-red-600">
                        <i class="fas fa-circle text-red-500 mr-2"></i>
                        Voting Belum Dimulai / Sudah Berakhir
                    </p>
                <?php endif; ?>
            </div>
            <div>
                <?php if (!$_SESSION['sudah_memilih'] && $statusVoting['voting_aktif']): ?>
                    <a href="../voting/pilih.php"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                        <i class="fas fa-vote-yea mr-2"></i>
                        Mulai Voting
                    </a>
                <?php elseif ($_SESSION['sudah_memilih']): ?>
                    <div class="text-center">
                        <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i>
                            Anda Sudah Memilih
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Lihat Kandidat -->
        <a href="kandidat.php"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-user-tie text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Lihat Kandidat</h3>
                    <p class="text-gray-600">Pelajari visi & misi kandidat</p>
                </div>
            </div>
        </a>

        <!-- Lihat Hasil -->
        <a href="../voting/hasil.php"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <i class="fas fa-chart-bar text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Hasil Voting</h3>
                    <p class="text-gray-600">Lihat hasil perolehan suara</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Informasi Penting -->
    <div class="mt-6 bg-blue-50 rounded-lg p-6 border border-blue-100">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">
            <i class="fas fa-info-circle mr-2"></i>
            Informasi Penting
        </h3>
        <ul class="text-blue-700 space-y-2">
            <li class="flex items-start">
                <i class="fas fa-check-circle mt-1 mr-2"></i>
                <span>Voting hanya dapat dilakukan satu kali</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mt-1 mr-2"></i>
                <span>Pastikan mempelajari profil kandidat sebelum memilih</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle mt-1 mr-2"></i>
                <span>Hasil voting bersifat rahasia dan aman</span>
            </li>
        </ul>
    </div>
</div>

<?php include('../includes/footer.php'); ?>