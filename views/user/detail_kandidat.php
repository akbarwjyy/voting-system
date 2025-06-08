<?php
// Include constants file first
require_once(__DIR__ . '/../../config/constants.php');

require_once(BASE_PATH . '/controllers/AuthController.php');
require_once(BASE_PATH . '/controllers/CandidateController.php');

// Check if user is logged in
AuthController::cekLoginUser();

// Get kandidat ID from URL
$kandidat_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Initialize controller and get kandidat data
$candidateController = new CandidateController();
$kandidat = $candidateController->ambilKandidatById($kandidat_id);

if (!$kandidat) {
    header('Location: ' . ROOT_PATH . '/views/user/kandidat.php');
    exit();
}

// Set page title
$pageTitle = "Detail Kandidat: " . htmlspecialchars($kandidat['nama']);
include(BASE_PATH . '/views/includes/header.php');
?>

<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="mb-6">
        <a href="<?php echo ROOT_PATH; ?>/views/user/kandidat.php" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Kandidat
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="md:flex">
            <!-- Candidate Photo -->
            <div class="md:w-1/3">
                <img src="<?php echo ROOT_PATH; ?>/assets/uploads/kandidat/<?php echo htmlspecialchars($kandidat['foto']); ?>"
                    alt="Foto <?php echo htmlspecialchars($kandidat['nama']); ?>"
                    class="w-full h-full object-cover">
            </div>

            <!-- Candidate Info -->
            <div class="p-6 md:w-2/3">
                <div class="flex justify-between items-start mb-4">
                    <h1 class="text-3xl font-bold text-gray-900">
                        <?php echo htmlspecialchars($kandidat['nama']); ?>
                    </h1>
                    <span class="bg-blue-600 text-white px-4 py-2 rounded-full">
                        No. <?php echo htmlspecialchars($kandidat['no_urut']); ?>
                    </span>
                </div>

                <!-- Visi -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">Visi:</h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($kandidat['visi'])); ?></p>
                    </div>
                </div>

                <!-- Misi -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">Misi:</h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($kandidat['misi'])); ?></p>
                    </div>
                </div>

                <?php if (!$_SESSION['sudah_memilih']): ?>
                    <div class="mt-8">
                        <a href="<?php echo ROOT_PATH; ?>/voting/pilih.php"
                            class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-vote-yea mr-2"></i>
                            Mulai Voting
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include(BASE_PATH . '/views/includes/footer.php'); ?>