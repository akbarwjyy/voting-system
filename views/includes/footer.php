    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-inner mt-8">
        <div class="max-w-7xl mx-auto py-4 px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-center text-gray-600">
                        &copy; <?php echo date('Y'); ?> SMA Negeri 1 Jakarta. All rights reserved.
                    </p>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-question-circle"></i>
                        <span class="ml-1">Bantuan</span>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-shield-alt"></i>
                        <span class="ml-1">Privasi</span>
                    </a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-book"></i>
                        <span class="ml-1">Panduan</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Optional: Custom page specific JavaScript -->
    <?php if (isset($customJS)) echo $customJS; ?>

    <!-- Global JavaScript -->
    <script>
        // Function to show loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }

        // Function to hide loading overlay
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }

        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessage = document.querySelector('.alert-success, .alert-error');
            if (flashMessage) {
                setTimeout(() => {
                    flashMessage.style.transition = 'opacity 0.5s ease';
                    flashMessage.style.opacity = '0';
                    setTimeout(() => flashMessage.remove(), 500);
                }, 5000);
            }
        });

        // Add loading indicator to forms
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                showLoading();
            });
        });
    </script>
    </body>

    </html>