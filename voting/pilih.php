<?php
require_once(__DIR__ . '/../controllers/AuthController.php');
require_once(__DIR__ . '/../controllers/VoteController.php');
require_once(__DIR__ . '/../controllers/CandidateController.php');

// Cek apakah user sudah login
AuthController::cekLoginUser();

// Inisialisasi controller
$voteController = new VoteController();
$candidateController = new CandidateController();

// Cek status voting
$statusVoting = $voteController->cekStatusVoting();
if (!$statusVoting['voting_aktif']) {
    $_SESSION['flash_message'] = 'Voting sedang tidak aktif!';
    $_SESSION['flash_type'] = 'error';
    header('Location: ../views/user/dashboard.php');
    exit();
}

// Set page title
$pageTitle = "Pilih Kandidat";

// Custom CSS
$customCSS = '<style>
    .kandidat-card.selected {
        border: 2px solid #4f46e5;
        transform: translateY(-5px);
    }
</style>';

include('../views/includes/header.php');
?>

<!-- Main Content -->
<div class="max-w-7xl mx-auto py-6 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Pilih Kandidat</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $kandidat = $candidateController->getDaftarKandidat();
        foreach ($kandidat as $k):
        ?>
            <div class="kandidat-card bg-white rounded-lg shadow-md overflow-hidden cursor-pointer"
                onclick="pilihKandidat(<?php echo $k['id']; ?>)">
                <img src="/voting-system/assets/uploads/kandidat/<?php echo $k['foto']; ?>"
                    alt="<?php echo $k['nama']; ?>"
                    class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold text-gray-800"><?php echo $k['nama']; ?></h3>
                    <p class="text-gray-600 mt-2"><?php echo $k['visi']; ?></p>
                    <button class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>Pilih Kandidat
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal konfirmasi voting -->
<div id="konfirmasiModal" class="modal-backdrop hidden fixed inset-0 flex items-center justify-center z-50">
    <div class="modal-content bg-white rounded-lg shadow-xl p-6 max-w-md mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Pilihan</h3>
        <p class="text-gray-600">Apakah Anda yakin dengan pilihan Anda? Pilihan tidak dapat diubah setelah dikonfirmasi.</p>
        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="batalPilih()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                Batal
            </button>
            <button onclick="konfirmasiPilih()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                Ya, Saya Yakin
            </button>
        </div>
    </div>
</div>

<?php
$customJS = '<script src="/voting-system/assets/js/voting.js"></script>';
include('../views/includes/footer.php');
?>