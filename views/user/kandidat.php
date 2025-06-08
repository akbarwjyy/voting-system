<?php
// Include constants file first
require_once(__DIR__ . '/../../config/constants.php');

require_once(BASE_PATH . '/controllers/AuthController.php');
require_once(BASE_PATH . '/controllers/CandidateController.php');

// Check if user is logged in
AuthController::cekLoginUser();

// Initialize controller
$candidateController = new CandidateController();
$candidates = $candidateController->getDaftarKandidat();

// Set page title
$pageTitle = "Daftar Kandidat";
include(BASE_PATH . '/views/includes/header.php');
?>

<div class="max-w-7xl mx-auto py-6 px-4">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Daftar Kandidat</h1>
        <p class="text-gray-600">Silakan pelajari profil masing-masing kandidat sebelum memberikan suara.</p>
    </div>

    <!-- Candidates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($candidates as $kandidat): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <!-- Candidate Photo -->
                <div class="relative h-64">
                    <img src="<?php echo ROOT_PATH; ?>/assets/uploads/kandidat/<?php echo htmlspecialchars($kandidat['foto']); ?>"
                        alt="Foto <?php echo htmlspecialchars($kandidat['nama']); ?>"
                        class="w-full h-full object-cover">
                    <div class="absolute top-4 right-4 bg-blue-600 text-white px-3 py-1 rounded-full">
                        No. <?php echo htmlspecialchars($kandidat['no_urut']); ?>
                    </div>
                </div>

                <!-- Candidate Info -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        <?php echo htmlspecialchars($kandidat['nama']); ?>
                    </h3>

                    <!-- Visi -->
                    <div class="mb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Visi:</h4>
                        <p class="text-gray-600 line-clamp-3"><?php echo nl2br(htmlspecialchars($kandidat['visi'])); ?></p>
                    </div>

                    <!-- View Detail Button -->
                    <a href="<?php echo ROOT_PATH; ?>/views/user/detail_kandidat.php?id=<?php echo $kandidat['id']; ?>"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors duration-300 inline-block text-center">
                        <i class="fas fa-eye mr-2"></i>Lihat Detail
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include(BASE_PATH . '/views/includes/footer.php'); ?>