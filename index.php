<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: views/admin/dashboard.php");
        exit();
    } else if ($_SESSION['user_type'] === 'user') {
        header("Location: views/user/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Voting Online - Beranda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="mb-6">
                <i class="fas fa-vote-yea text-6xl text-white mb-4"></i>
            </div>
            <h1 class="text-5xl font-bold text-white mb-4">🗳️ Sistem Voting Online</h1>
            <p class="text-xl text-blue-100 mb-2">Pemilihan Ketua OSIS 2025</p>
            <p class="text-lg text-blue-200">SMA Negeri 1 Jakarta</p>
        </div>

        <!-- Main Card -->
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-2xl p-8 backdrop-blur-sm bg-opacity-95">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">Selamat Datang</h2>
                <p class="text-gray-600">Silakan pilih jenis login Anda:</p>
            </div>

            <div class="space-y-4">
                <!-- Login Pemilih -->
                <a href="views/auth/login.php?type=user"
                    class="group w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-6 rounded-xl block text-center transition duration-300 transform hover:scale-105 shadow-lg">
                    <div class="flex items-center justify-center space-x-3">
                        <i class="fas fa-user text-xl group-hover:animate-pulse"></i>
                        <span class="text-lg">Login sebagai Pemilih</span>
                    </div>
                    <p class="text-blue-100 text-sm mt-1">Masuk untuk memberikan suara</p>
                </a>

                <!-- Login Admin -->
                <a href="views/auth/login.php?type=admin"
                    class="group w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-4 px-6 rounded-xl block text-center transition duration-300 transform hover:scale-105 shadow-lg">
                    <div class="flex items-center justify-center space-x-3">
                        <i class="fas fa-user-shield text-xl group-hover:animate-pulse"></i>
                        <span class="text-lg">Login sebagai Admin</span>
                    </div>
                    <p class="text-green-100 text-sm mt-1">Kelola sistem voting</p>
                </a>

                <!-- Divider -->
                <div class="flex items-center my-6">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="px-4 text-gray-500 font-medium">atau</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <!-- Register -->
                <a href="views/auth/register.php"
                    class="group w-full bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-bold py-4 px-6 rounded-xl block text-center transition duration-300 transform hover:scale-105 shadow-lg">
                    <div class="flex items-center justify-center space-x-3">
                        <i class="fas fa-user-plus text-xl group-hover:animate-pulse"></i>
                        <span class="text-lg">Daftar sebagai Pemilih</span>
                    </div>
                    <p class="text-purple-100 text-sm mt-1">Belum punya akun? Daftar sekarang</p>
                </a>
            </div>

            <!-- Info Status -->
            <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    <span class="text-blue-800 font-semibold">Status Voting:</span>
                </div>
                <p id="statusVoting" class="text-blue-700 mt-1">Memuat status...</p>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-8">
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 max-w-2xl mx-auto">
                <h3 class="text-white font-bold mb-2">📋 Informasi Penting:</h3>
                <ul class="text-blue-100 text-sm space-y-1">
                    <li>• Setiap pemilih hanya dapat memberikan suara satu kali</li>
                    <li>• Akun pemilih baru perlu diaktifkan oleh admin</li>
                    <li>• Pastikan data yang dimasukkan benar dan valid</li>
                    <li>• Voting dilakukan secara rahasia dan aman</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Script untuk cek status voting -->
    <script>
        // Fungsi untuk cek status voting
        async function cekStatusVoting() {
            try {
                const response = await fetch('controllers/VoteController.php?action=cek_status');
                const data = await response.json();

                const statusElement = document.getElementById('statusVoting');
                if (data.voting_aktif) {
                    statusElement.innerHTML = '<span class="text-green-600 font-bold">🟢 Voting Sedang Berlangsung</span>';
                } else {
                    statusElement.innerHTML = '<span class="text-red-600 font-bold">🔴 Voting Belum Dimulai / Sudah Berakhir</span>';
                }
            } catch (error) {
                document.getElementById('statusVoting').innerHTML = '<span class="text-gray-600">❓ Status tidak dapat dimuat</span>';
            }
        }

        // Jalankan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            cekStatusVoting();

            // Update status setiap 30 detik
            setInterval(cekStatusVoting, 30000);
        });
    </script>
</body>

</html>