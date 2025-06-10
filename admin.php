<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: views/admin/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - E-Voting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 relative overflow-x-hidden">
    <!-- Background Patterns -->
    <div class="fixed inset-0 z-0">
        <div class="absolute top-0 left-0 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <div class="container mx-auto px-4 py-8 relative z-10">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-6 transform hover:scale-110 transition-transform duration-300">
                <i class="fas fa-user-shield text-6xl text-white mb-4 animate-bounce"></i>
            </div>
            <h1 class="text-5xl font-bold text-white mb-4 tracking-tight hover:tracking-wide transition-all duration-300">
                👨‍💼 Admin Panel
            </h1>
            <p class="text-xl text-blue-100 mb-2 hover:text-purple-200 transition-colors duration-300">
                E-Voting OSIS
            </p>
            <!-- <p class="text-lg text-blue-200 hover:text-purple-300 transition-colors duration-300">
                SMA Negeri 1 Jakarta
            </p> -->
        </div>

        <!-- Main Card -->
        <div class="max-w-md mx-auto bg-white/95 rounded-2xl shadow-2xl p-8 backdrop-blur-sm hover:bg-white/100 transition-all duration-300 transform hover:-translate-y-1">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-3">
                    Area Admin
                </h2>
                <p class="text-gray-600">Silakan masuk untuk mengelola sistem</p>
            </div> <!-- Login Admin -->
            <a href="views/auth/login.php?type=admin"
                class="group w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold py-4 px-6 rounded-xl block text-center transition-all duration-300 transform hover:scale-105 hover:shadow-xl shadow-lg relative overflow-hidden">
                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="flex items-center justify-center space-x-3 relative z-10">
                    <i class="fas fa-user-shield text-xl group-hover:animate-bounce"></i>
                    <span class="text-lg">Masuk ke Admin Panel</span>
                </div>
                <p class="text-indigo-100 text-sm mt-1 relative z-10">Akses panel administrasi</p>
            </a>

            <!-- Back to Homepage -->
            <div class="mt-8 text-center">
                <a href="index.php"
                    class="inline-flex items-center text-indigo-600 hover:text-purple-700 font-medium transition-all duration-300 hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div> <!-- Info Box -->
            <div class="mt-8 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-100 transform transition-all duration-300 hover:scale-[1.02] hover:shadow-lg">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-indigo-600 animate-pulse"></i>
                    <span class="text-indigo-800 font-semibold">Informasi:</span>
                </div>
                <p class="text-indigo-700 mt-1 font-medium">
                    Halaman ini khusus untuk administrator sistem. Jika Anda seorang pemilih, silakan kembali ke halaman beranda.
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</body>

</html>