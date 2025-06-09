<?php
// Include constants file first
require_once(__DIR__ . '/../../config/constants.php');

require_once(BASE_PATH . '/controllers/AuthController.php');
require_once(BASE_PATH . '/controllers/AdminController.php');

// Cek apakah user sudah login sebagai admin
AuthController::cekLoginAdmin();

// Inisialisasi controller
$adminController = new AdminController();

// Handle voting status update
if (isset($_POST['update_voting_status'])) {
    $data = [
        'judul_voting' => $_POST['judul_voting'] ?? 'Pemilihan Ketua',
        'waktu_mulai' => $_POST['waktu_mulai'] ?? null,
        'waktu_selesai' => $_POST['waktu_selesai'] ?? null
    ];

    $result = $adminController->updateStatusVoting($data);
    $_SESSION['flash_message'] = $result['pesan'];
    $_SESSION['flash_type'] = $result['sukses'] ? 'success' : 'error';
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil statistik untuk dashboard
$statistik = $adminController->getStatistikDashboard();
$data = $statistik['data'];

// Get voting status
$votingStatus = $adminController->getStatusVoting();

// Set page title
$pageTitle = "Dashboard Admin";
include(BASE_PATH . '/views/includes/header.php');
?>

<!-- Main Content -->
<div class="max-w-7xl mx-auto py-6 px-4">
    <!-- Welcome Message -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Selamat Datang, <?php echo $_SESSION['admin_username']; ?>!
        </h1>
        <p class="text-gray-600">
            Kelola sistem voting dengan mudah melalui dashboard admin.
        </p>
    </div> <!-- Voting Status Control -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Pengaturan Jadwal Voting</h2>

        <form method="POST" class="space-y-6">
            <!-- Status Voting -->
            <div class="flex items-center justify-between pb-4 border-b">
                <div>
                    <h3 class="font-medium text-gray-900">Status Voting</h3>
                    <p class="text-gray-500 text-sm">Status berdasarkan waktu yang ditentukan</p>
                </div>
                <div class="flex flex-col items-end">
                    <?php
                    $votingAktif = isset($votingStatus['status']['voting_aktif']) ? $votingStatus['status']['voting_aktif'] : false;
                    $dalamPeriodeWaktu = isset($votingStatus['status']['dalam_periode_waktu']) ? $votingStatus['status']['dalam_periode_waktu'] : true;
                    ?>

                    <!-- Status Indicator -->
                    <div class="flex items-center">
                        <span class="<?php echo $votingAktif ? 'text-green-600' : 'text-red-600'; ?> font-medium">
                            <i class="fas fa-circle mr-2"></i>
                            <?php echo $votingAktif ? 'Voting Sedang Berlangsung' : 'Voting Tidak Aktif'; ?>
                        </span>
                    </div>

                    <!-- Time Status -->
                    <?php if (!$votingAktif): ?>
                        <div class="mt-2 text-sm text-gray-600">
                            <p>Periode Waktu: <?php echo $dalamPeriodeWaktu ? 'Valid' : 'Di luar jadwal' ?></p>
                            <p>Status: <?php
                                        if (!isset($votingStatus['status']['waktu_mulai']) || !isset($votingStatus['status']['waktu_selesai'])) {
                                            echo 'Jadwal belum diatur';
                                        } elseif (!$dalamPeriodeWaktu) {
                                            echo 'Di luar jadwal voting';
                                        } else {
                                            echo 'Menunggu waktu mulai';
                                        }
                                        ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Judul Voting -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Judul Voting</label>
                <input type="text" name="judul_voting" required
                    value="<?php echo $votingStatus['status']['judul_voting'] ?? ''; ?>"
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Waktu Voting -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                    <input type="datetime-local" name="waktu_mulai" required
                        value="<?php echo isset($votingStatus['status']['waktu_mulai']) ? date('Y-m-d\TH:i', strtotime($votingStatus['status']['waktu_mulai'])) : ''; ?>"
                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                    <input type="datetime-local" name="waktu_selesai" required
                        value="<?php echo isset($votingStatus['status']['waktu_selesai']) ? date('Y-m-d\TH:i', strtotime($votingStatus['status']['waktu_selesai'])) : ''; ?>"
                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Status Message -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="<?php echo $_SESSION['flash_type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> rounded-md p-4">
                    <?php echo $_SESSION['flash_message']; ?>
                </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" name="update_voting_status"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div> <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 bg-opacity-75">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Total Pemilih</h2>
                    <p class="text-2xl font-semibold text-gray-800"><?php echo $data['total_user']; ?></p>
                </div>
            </div>
            <div class="mt-4 flex justify-between items-center"> <span class="text-green-600 text-sm font-medium">
                    <?php echo $data['total_aktif']; ?> Aktif
                </span>
                <a href="kelola_user.php" class="text-blue-600 text-sm font-medium hover:text-blue-700">
                    Detail <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Total Voted Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 bg-opacity-75">
                    <i class="fas fa-vote-yea text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm font-medium text-gray-600">Status Partisipasi</h2>
                    <p class="text-2xl font-semibold text-gray-800"><?php echo $data['total_voted']; ?> / <?php echo $data['total_aktif']; ?></p>
                </div>
            </div>
            <div class="mt-4 flex justify-between items-center"> <span class="text-blue-600 text-sm font-medium">
                    <?php echo $data['persentase_voted']; ?>% Partisipasi
                </span>
                <a href="../../voting/hasil.php" class="text-green-600 text-sm font-medium hover:text-green-700">
                    Detail <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div> <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Manage Users -->
        <a href="kelola_user.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <i class="fas fa-users-cog text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Kelola Pemilih</h3>
                    <p class="text-gray-600">Aktivasi dan manajemen akun pemilih</p>
                </div>
            </div>
        </a>

        <!-- Manage Candidates -->
        <a href="kelola_kandidat.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <i class="fas fa-user-tie text-2xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Kelola Kandidat</h3>
                    <p class="text-gray-600">Tambah dan edit data kandidat</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>