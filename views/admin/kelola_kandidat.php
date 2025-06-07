<?php
require_once(__DIR__ . '/../../controllers/AuthController.php');
require_once(__DIR__ . '/../../controllers/CandidateController.php');

// Cek apakah user sudah login sebagai admin
AuthController::cekLoginAdmin();

// Inisialisasi controller
$candidateController = new CandidateController();

// Set page title
$pageTitle = "Kelola Kandidat";

// Custom CSS untuk upload preview
$customCSS = '<style>
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
    }
</style>';

include('../includes/header.php');
?>

<!-- Main Content -->
<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Kandidat</h1>
        <button onclick="showAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Tambah Kandidat
        </button>
    </div> <!-- Daftar Kandidat -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $kandidat = $candidateController->getDaftarKandidat();
        foreach ($kandidat as $k):
        ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="/voting-system/assets/uploads/kandidat/<?php echo $k['foto']; ?>"
                    alt="<?php echo $k['nama']; ?>"
                    class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold text-gray-800"><?php echo $k['nama']; ?></h3>
                    <p class="text-gray-600 mt-2"><?php echo $k['visi']; ?></p>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button onclick="editKandidat(<?php echo $k['id']; ?>)"
                            class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button onclick="deleteKandidat(<?php echo $k['id']; ?>)"
                            class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal tambah/edit kandidat akan ditambahkan di sini -->

<?php
$customJS = '<script src="/voting-system/assets/js/kandidat.js"></script>';
include('../includes/footer.php');
?>