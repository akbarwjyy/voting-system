<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistem E-Voting'; ?> - SMA Negeri 1 Jakarta</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">

    <!-- Optional: Custom page specific CSS -->
    <?php if (isset($customCSS)) echo $customCSS; ?>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo isset($_SESSION['user_type']) ? ($_SESSION['user_type'] === 'admin' ? '/voting-system/views/admin/dashboard.php' : '/voting-system/views/user/dashboard.php') : '/voting-system/index.php'; ?>"
                        class="text-2xl font-semibold text-gray-800">
                        <i class="fas fa-vote-yea text-blue-600 mr-2"></i>
                        <?php echo isset($_SESSION['user_type']) ? ($_SESSION['user_type'] === 'admin' ? 'Admin Panel' : 'Panel Pemilih') : 'E-Voting'; ?>
                    </a>
                </div>

                <?php if (isset($_SESSION['user_type'])): ?>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">
                            <i class="fas fa-user mr-2"></i>
                            <?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : $_SESSION['user_nama']; ?>
                        </span>
                        <a href="/voting-system/logout.php" class="text-red-600 hover:text-red-700">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Logout
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="<?php echo $_SESSION['flash_type'] === 'success' ? 'alert-success' : 'alert-error'; ?> p-4 rounded-lg">
                <?php echo $_SESSION['flash_message']; ?>
            </div>
        </div>
    <?php
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    endif;
    ?>

    <!-- Main Content Container -->
    <main class="flex-grow">