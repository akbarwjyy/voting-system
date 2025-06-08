<?php
// Start the session
session_start();

// Include constants file first
require_once(__DIR__ . '/../config/constants.php');

// Include required controllers
require_once(BASE_PATH . '/controllers/AuthController.php');
require_once(BASE_PATH . '/controllers/VoteController.php');

// Cek apakah user sudah login
if (!isset($_SESSION['user_type'])) {
    header('Location: ' . ROOT_PATH . '/views/auth/login.php');
    exit();
}

// Inisialisasi controller
$voteController = new VoteController();
$statusVoting = $voteController->cekStatusVoting();

// Periksa status voting dengan detail
$isVotingActive = false;
$pesanStatus = 'Voting Belum Dimulai';

if ($statusVoting['sukses']) {
    if ($statusVoting['voting_aktif']) {
        $isVotingActive = true;
        $pesanStatus = 'Voting Sedang Berlangsung';
    } elseif (isset($statusVoting['dalam_periode_waktu']) && !$statusVoting['dalam_periode_waktu']) {
        $pesanStatus = 'Di Luar Jadwal Voting';
    } elseif (isset($statusVoting['manual_aktif']) && !$statusVoting['manual_aktif']) {
        $pesanStatus = 'Voting Belum Diaktifkan';
    }
}

// Ambil hasil voting
$hasil = $voteController->getHasilVoting();

// Tambahkan informasi status voting ke hasil
$hasil['voting_aktif'] = $isVotingActive;
$hasil['pesan_status'] = $statusVoting['pesan'] ?? '';

// Set page title
$pageTitle = "Hasil Voting";

// Custom CSS untuk grafik
$customCSS = '<style>
    .result-bar {
        transition: width 1s ease-out;
    }
</style>';

include(BASE_PATH . '/views/includes/header.php');
?>

<!-- Main Content -->
<div class="max-w-7xl mx-auto py-6 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Hasil Voting</h1> <!-- Status Voting -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Status Voting</h2>
            <?php if ($isVotingActive): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-circle text-green-500 mr-2"></i>
                    <?php echo $pesanStatus; ?>
                </span>
            <?php else: ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <i class="fas fa-circle text-red-500 mr-2"></i>
                    <?php echo $pesanStatus; ?>
                </span>
            <?php endif; ?>
        </div>

        <?php if (!$isVotingActive && $_SESSION['user_type'] === 'admin'): ?> <div class="mt-4 text-sm text-gray-600">
                <p class="flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Status: <?php echo isset($statusVoting['manual_aktif']) && $statusVoting['manual_aktif'] ? 'Switch Aktif' : 'Switch Nonaktif'; ?>
                </p>
                <?php if (isset($statusVoting['waktu_mulai']) && $statusVoting['waktu_mulai']): ?>
                    <p class="flex items-center mt-1">
                        <i class="far fa-clock mr-2"></i>
                        Waktu Mulai: <?php echo date('d/m/Y H:i', strtotime($statusVoting['waktu_mulai'])); ?>
                    </p>
                <?php endif; ?>
                <?php if (isset($statusVoting['waktu_selesai'])): ?>
                    <p class="flex items-center mt-1">
                        <i class="far fa-clock mr-2"></i>
                        Waktu Selesai: <?php echo date('d/m/Y H:i', strtotime($statusVoting['waktu_selesai'])); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Perolehan Suara</h2>

        <?php if (empty($hasil['hasil'])): ?>
            <div class="text-center py-8 text-gray-600">
                <i class="fas fa-info-circle text-4xl mb-4"></i>
                <p>Belum ada data hasil voting yang dapat ditampilkan.</p>
            </div>
        <?php else: ?>
            <?php foreach ($hasil['hasil'] as $data): ?>
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-gray-700"><?php echo $data['nama']; ?></span>
                        <span class="text-blue-600"><?php echo $data['persentase']; ?>%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="result-bar bg-blue-600 rounded-full h-4"
                            style="width: <?php echo $data['persentase']; ?>%"></div>
                    </div>
                    <div class="mt-1 text-sm text-gray-600">
                        <?php echo $data['jumlah_suara']; ?> suara
                    </div>
                </div>
            <?php endforeach; ?> <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center text-gray-700">
                    <span class="font-medium">Total Suara Masuk:</span>
                    <span class="text-lg"><?php echo isset($hasil['total_suara']) ? $hasil['total_suara'] : 0; ?> suara</span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Tutup koneksi database jika ada
if (isset($voteController)) {
    unset($voteController);
}

// Include footer
include(BASE_PATH . '/views/includes/footer.php');
?>