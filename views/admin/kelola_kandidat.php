<?php
require_once(__DIR__ . '/../../controllers/AuthController.php');
require_once(__DIR__ . '/../../controllers/CandidateController.php');

// Cek apakah user sudah login sebagai admin
AuthController::cekLoginAdmin();

// Inisialisasi controller
$candidateController = new CandidateController();

// Handle form submission
$pesan = '';
$tipe_pesan = '';

// Cek pesan dari redirect
if (isset($_GET['status']) && isset($_GET['message'])) {
    $tipe_pesan = $_GET['status'];
    $pesan = urldecode($_GET['message']);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $hasil = $candidateController->handleFormSubmission(
        $_POST['action'],
        $_POST,
        $_FILES
    );

    if ($hasil['sukses']) {
        // Redirect untuk menghindari resubmission
        header('Location: kelola_kandidat.php?status=success&message=' . urlencode($hasil['pesan']));
        exit;
    } else {
        $pesan = $hasil['pesan'];
        $tipe_pesan = 'error';
    }
}

// Set page title
$pageTitle = "Kelola Kandidat";

// Custom CSS
$customCSS = '<style>
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
    }
    .message {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 0.5rem;
    }
    .message.success {
        background-color: #DEF7EC;
        color: #03543F;
    }
    .message.error {
        background-color: #FDE8E8;
        color: #9B1C1C;
    }
</style>';

include('../includes/header.php');
?>

<div class="max-w-7xl mx-auto py-6 px-4">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Kandidat</h1>
        <a href="?action=tambah" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Tambah Kandidat
        </a>
    </div>

    <!-- Message Display -->
    <?php if ($pesan): ?>
        <div class="message <?php echo $tipe_pesan; ?>">
            <?php echo $pesan; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['action']) && ($_GET['action'] === 'tambah' || $_GET['action'] === 'edit')): ?>
        <!-- Form Tambah/Edit Kandidat -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">
                <?php echo $_GET['action'] === 'tambah' ? 'Tambah Kandidat' : 'Edit Kandidat'; ?>
            </h2>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
                <?php if (isset($_GET['id'])): ?>
                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                <?php endif; ?>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kandidat</label>
                    <input type="text" name="nama" required
                        value="<?php echo isset($kandidat['nama']) ? htmlspecialchars($kandidat['nama']) : ''; ?>"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Urut</label>
                    <input type="number" name="no_urut" required
                        value="<?php echo isset($kandidat['no_urut']) ? htmlspecialchars($kandidat['no_urut']) : ''; ?>"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Visi</label>
                    <textarea name="visi" required rows="3"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"><?php echo isset($kandidat['visi']) ? htmlspecialchars($kandidat['visi']) : ''; ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Misi</label>
                    <textarea name="misi" required rows="4"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"><?php echo isset($kandidat['misi']) ? htmlspecialchars($kandidat['misi']) : ''; ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Foto</label>
                    <input type="file" name="foto" <?php echo $_GET['action'] === 'tambah' ? 'required' : ''; ?>
                        accept="image/*" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <?php if (isset($kandidat['foto'])): ?>
                        <div class="mt-2">
                            <img src="/voting-system/assets/uploads/kandidat/<?php echo $kandidat['foto']; ?>"
                                alt="Foto kandidat" class="image-preview">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="kelola_kandidat.php"
                        class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    <?php else: ?> <!-- Daftar Kandidat -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $kandidat_list = $candidateController->getDaftarKandidat();
            foreach ($kandidat_list as $k):
            ?> <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <!-- Image Container -->
                    <div class="relative overflow-hidden h-72">
                        <img src="/voting-system/assets/uploads/kandidat/<?php echo htmlspecialchars($k['foto']); ?>"
                            alt="Foto <?php echo htmlspecialchars($k['nama']); ?>"
                            class="w-full h-full object-cover object-center transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <!-- Nomor Urut Badge -->
                        <span class="absolute top-4 right-4 bg-indigo-600 text-white px-4 py-1 rounded-full text-sm font-semibold shadow-lg transform -translate-y-2 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                            No. <?php echo htmlspecialchars($k['no_urut']); ?>
                        </span>
                    </div>

                    <div class="p-6">
                        <!-- Header with Name -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors duration-300">
                                <?php echo htmlspecialchars($k['nama']); ?>
                            </h3>
                        </div>

                        <!-- Visi & Misi -->
                        <div class="space-y-4">
                            <div class="bg-indigo-50 rounded-lg p-4 transform group-hover:-translate-y-1 transition-transform duration-300">
                                <h4 class="font-semibold text-indigo-800 mb-2">Visi:</h4>
                                <p class="text-gray-700 text-sm line-clamp-3 group-hover:line-clamp-none transition-all duration-300">
                                    <?php echo nl2br(htmlspecialchars($k['visi'])); ?>
                                </p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4 transform group-hover:-translate-y-1 transition-transform duration-300">
                                <h4 class="font-semibold text-purple-800 mb-2">Misi:</h4>
                                <p class="text-gray-700 text-sm line-clamp-3 group-hover:line-clamp-none transition-all duration-300">
                                    <?php echo nl2br(htmlspecialchars($k['misi'])); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="?action=edit&id=<?php echo $k['id']; ?>"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-300">
                                <i class="fas fa-edit mr-2"></i> Edit
                            </a>
                            <form method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kandidat ini?');">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?php echo $k['id']; ?>">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-300">
                                    <i class="fas fa-trash mr-2"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>