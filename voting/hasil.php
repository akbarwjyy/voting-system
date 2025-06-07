<?php
require_once(__DIR__ . '/../controllers/AuthController.php');
require_once(__DIR__ . '/../controllers/VoteController.php');

// Cek apakah user sudah login
if (!isset($_SESSION['user_type'])) {
    header('Location: ../views/auth/login.php');
    exit();
}

// Inisialisasi controller
$voteController = new VoteController();
$hasil = $voteController->getHasilVoting();

// Set page title
$pageTitle = "Hasil Voting";

// Custom CSS untuk grafik
$customCSS = '<style>
    .result-bar {
        transition: width 1s ease-out;
    }
</style>';

include('../views/includes/header.php');
?>

<!-- Main Content -->
<div class="max-w-7xl mx-auto py-6 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Hasil Voting</h1>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Perolehan Suara</h2>

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
        <?php endforeach; ?>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center text-gray-700">
                <span class="font-medium">Total Suara Masuk:</span>
                <span class="text-lg"><?php echo $hasil['total_suara']; ?> suara</span>
            </div>
        </div>
    </div>
</div>

<?php include('../views/includes/footer.php'); ?>