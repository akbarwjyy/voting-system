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
    </div> <!-- Candidates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($candidates as $kandidat): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <!-- Image Container -->
                <div class="relative overflow-hidden h-72">
                    <img src="<?php echo ROOT_PATH; ?>/assets/uploads/kandidat/<?php echo htmlspecialchars($kandidat['foto']); ?>"
                        alt="Foto <?php echo htmlspecialchars($kandidat['nama']); ?>"
                        class="w-full h-full object-cover object-center transform group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <!-- Nomor Urut Badge -->
                    <span class="absolute top-4 right-4 bg-indigo-600 text-white px-4 py-1 rounded-full text-sm font-semibold shadow-lg transform -translate-y-2 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                        No. <?php echo htmlspecialchars($kandidat['no_urut']); ?>
                    </span>
                </div>

                <div class="p-6">
                    <!-- Header with Name -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors duration-300">
                            <?php echo htmlspecialchars($kandidat['nama']); ?>
                        </h3>
                    </div>

                    <!-- Visi & Misi -->
                    <div class="space-y-4">
                        <div class="bg-indigo-50 rounded-lg p-4 transform group-hover:-translate-y-1 transition-transform duration-300">
                            <h4 class="font-semibold text-indigo-800 mb-2">Visi:</h4>
                            <p class="text-gray-700 text-sm line-clamp-3 group-hover:line-clamp-none transition-all duration-300">
                                <?php echo nl2br(htmlspecialchars($kandidat['visi'])); ?>
                            </p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 transform group-hover:-translate-y-1 transition-transform duration-300">
                            <h4 class="font-semibold text-purple-800 mb-2">Misi:</h4>
                            <p class="text-gray-700 text-sm line-clamp-3 group-hover:line-clamp-none transition-all duration-300">
                                <?php echo nl2br(htmlspecialchars($kandidat['misi'])); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-6">
                        <a href="<?php echo ROOT_PATH; ?>/views/user/detail_kandidat.php?id=<?php echo $kandidat['id']; ?>"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-300">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include(BASE_PATH . '/views/includes/footer.php'); ?>