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
$isVotingActive = $statusVoting['sukses'] && $statusVoting['voting_aktif'];

if (!$isVotingActive) {
    $_SESSION['flash_message'] = $statusVoting['pesan'] ?? 'Voting sedang tidak aktif!';
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

<!-- Proses form jika ada POST request -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kandidat_id'], $_POST['konfirmasi'])) {
    $result = $voteController->prosesVoting($_SESSION['user_id'], $_POST['kandidat_id']);
    if ($result['sukses']) {
        $_SESSION['flash_message'] = $result['pesan'];
        $_SESSION['flash_type'] = 'success';
        header('Location: ../views/user/dashboard.php');
        exit();
    } else {
        $_SESSION['flash_message'] = $result['pesan'];
        $_SESSION['flash_type'] = 'error';
    }
}
?>

<!-- Main Content -->
<div class="max-w-7xl mx-auto py-6 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Pilih Kandidat</h1>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mb-4 p-4 rounded-lg <?php echo $_SESSION['flash_type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php
            echo $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $kandidat = $candidateController->getDaftarKandidat();
        foreach ($kandidat as $k):
        ?>
            <div class="kandidat-card bg-white rounded-lg shadow-md overflow-hidden">
                <img src="/voting-system/assets/uploads/kandidat/<?php echo $k['foto']; ?>"
                    alt="<?php echo $k['nama']; ?>"
                    class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="text-xl font-semibold text-gray-800"><?php echo $k['nama']; ?></h3>
                    <p class="text-gray-600 mt-2"><?php echo $k['visi']; ?></p>

                    <!-- Form untuk memilih kandidat -->
                    <form method="POST" action="" class="mt-4">
                        <input type="hidden" name="kandidat_id" value="<?php echo $k['id']; ?>">
                        <input type="hidden" name="konfirmasi" value="1">

                        <!-- Tombol konfirmasi -->
                        <button type="submit"
                            onclick="return confirm('Apakah Anda yakin memilih kandidat ini? Pilihan tidak dapat diubah setelah dikonfirmasi.');"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i>Pilih Kandidat
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include('../views/includes/footer.php'); ?>