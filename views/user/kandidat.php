<?php
require_once(__DIR__ . '/../../controllers/AuthController.php');
require_once(__DIR__ . '/../../controllers/CandidateController.php');

// Check if user is logged in
AuthController::cekLoginUser();

// Initialize controller
$candidateController = new CandidateController();
$candidates = $candidateController->getDaftarKandidat();

// Set page title
$pageTitle = "Daftar Kandidat";
include('../includes/header.php');
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
                    <img src="/voting-system/assets/uploads/kandidat/<?php echo htmlspecialchars($kandidat['foto']); ?>"
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
                        <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($kandidat['visi'])); ?></p>
                    </div>

                    <!-- Misi -->
                    <div class="mb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Misi:</h4>
                        <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($kandidat['misi'])); ?></p>
                    </div>

                    <!-- View Detail Button -->
                    <button onclick="showDetail(<?php echo $kandidat['id']; ?>)"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors duration-300">
                        Lihat Detail
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal for Candidate Detail -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6" id="modalContent">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<script>
    function showDetail(id) {
        const modal = document.getElementById('detailModal');
        const content = document.getElementById('modalContent');

        // Show loading state
        content.innerHTML = '<div class="flex justify-center"><div class="loading-spinner"></div></div>';
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Fetch candidate details
        fetch('/voting-system/controllers/CandidateController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_detail&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.sukses) {
                    content.innerHTML = `
                <div class="text-right mb-4">
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex flex-col md:flex-row gap-6">
                    <img src="/voting-system/assets/uploads/kandidat/${data.kandidat.foto}" 
                         alt="Foto ${data.kandidat.nama}"
                         class="w-full md:w-1/3 rounded-lg object-cover">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold mb-4">${data.kandidat.nama}</h2>
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Visi:</h3>
                                <p class="text-gray-600">${data.kandidat.visi}</p>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Misi:</h3>
                                <p class="text-gray-600">${data.kandidat.misi}</p>
                            </div>
                        </div>
                    </div>
                </div>`;
                }
            });
    }

    function closeModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>

<?php include('../includes/footer.php'); ?>