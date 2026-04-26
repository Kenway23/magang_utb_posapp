    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Login - PROShop</title>

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet">

        <style>
            * {
                font-family: 'Inter', sans-serif;
            }

            /* Animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-80px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(80px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes shake {

                0%,
                100% {
                    transform: translateX(0);
                }

                10%,
                30%,
                50%,
                70%,
                90% {
                    transform: translateX(-6px);
                }

                20%,
                40%,
                60%,
                80% {
                    transform: translateX(6px);
                }
            }

            @keyframes modalFadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.9) translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }

            @keyframes modalFadeOut {
                from {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }

                to {
                    opacity: 0;
                    transform: scale(0.9) translateY(20px);
                }
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(-15px);
                }
            }

            @keyframes pulseGlow {
                0% {
                    box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
                }
            }

            @keyframes gradientShift {
                0% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            @keyframes ripple {
                0% {
                    transform: scale(0);
                    opacity: 0.5;
                }

                100% {
                    transform: scale(4);
                    opacity: 0;
                }
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            .animate-fadeIn {
                animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .animate-slideInLeft {
                animation: slideInLeft 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .animate-slideInRight {
                animation: slideInRight 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .animate-shake {
                animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
            }

            .animate-modalFadeIn {
                animation: modalFadeIn 0.4s cubic-bezier(0.34, 1.2, 0.64, 1);
            }

            .animate-modalFadeOut {
                animation: modalFadeOut 0.3s ease-out forwards;
            }

            .animate-float {
                animation: float 4s ease-in-out infinite;
            }

            .animate-pulseGlow {
                animation: pulseGlow 2s infinite;
            }

            .btn-animate {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .btn-animate:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
            }

            .btn-animate:active {
                transform: translateY(1px);
            }

            .ripple-effect {
                position: absolute;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.7);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            }

            .input-animate {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .input-animate:focus {
                transform: scale(1.02);
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            }

            .modal-backdrop {
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(8px);
                transition: all 0.3s ease;
            }

            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
                background-size: 200% 200%;
                animation: gradientShift 10s ease infinite;
            }

            /* Floating particles */
            .particle {
                position: absolute;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                pointer-events: none;
                animation: float 6s ease-in-out infinite;
            }

            .card-hover {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .card-hover:hover {
                transform: translateY(-8px);
                box-shadow: 0 25px 40px -12px rgba(0, 0, 0, 0.3);
            }

            /* ========== CSS UNTUK PASSWORD - FIX TIDAK BERGERAK ========== */
            .form-group {
                margin-bottom: 24px;
            }

            .form-label {
                display: block;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
            }

            .form-label i {
                margin-right: 8px;
                color: #3b82f6;
            }

            .input-wrapper {
                position: relative;
                width: 100%;
                display: block;
            }

            /* Style untuk input biasa */
            .input-username {
                width: 100%;
                padding: 12px 16px;
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                font-size: 16px;
                transition: all 0.3s ease;
                background-color: #f9fafb;
                box-sizing: border-box;
            }

            .input-username:focus {
                outline: none;
                border-color: #3b82f6;
                background-color: #ffffff;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            }

            /* Style untuk input password - dengan padding kanan lebih besar */
            .input-password {
                width: 100%;
                padding: 12px 45px 12px 16px;
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                font-size: 16px;
                transition: all 0.3s ease;
                background-color: #f9fafb;
                box-sizing: border-box;
            }

            .input-password:focus {
                outline: none;
                border-color: #3b82f6;
                background-color: #ffffff;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            }

            /* Tombol mata password - POSISI ABSOLUTE TIDAK BERUBAH */
            .btn-password-toggle {
                position: absolute !important;
                right: 12px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                background: transparent !important;
                border: none !important;
                cursor: pointer !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 24px !important;
                height: 24px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                z-index: 10 !important;
            }

            .btn-password-toggle i {
                font-size: 18px;
                color: #9ca3af;
                transition: color 0.3s ease;
                pointer-events: none;
            }

            .btn-password-toggle:hover i {
                color: #3b82f6;
            }

            .btn-password-toggle:focus {
                outline: none;
            }

            /* Menghilangkan outline pada button saat di klik */
            .btn-password-toggle:active {
                outline: none;
                transform: translateY(-50%) !important;
            }
        </style>
    </head>

    <body class="font-sans antialiased relative overflow-x-hidden">
        <!-- Floating Particles -->
        <div id="particles"></div>

        <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
            <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full overflow-hidden card-hover animate-fadeIn">
                <div class="flex flex-col md:flex-row">
                    <!-- Bagian Kiri - Logo -->
                    <div
                        class="bg-gradient-to-br from-blue-600 via-purple-600 to-purple-700 md:w-1/2 p-10 flex flex-col items-center justify-center text-white relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -mt-32 -ml-32"></div>
                            <div class="absolute bottom-0 right-0 w-64 h-64 bg-white rounded-full -mb-32 -mr-32"></div>
                        </div>

                        <div class="text-center relative z-10 animate-float">
                            <div class="mb-1 group">
                                <div
                                    class="rounded-2xl p-2 inline-block transform group-hover:scale-110 transition-all duration-300">
                                    <img src="{{ asset('images/logo.png') }}" alt="PROShop Logo"
                                        class="w-24 h-24 md:w-32 md:h-32 object-contain drop-shadow-lg">
                                </div>
                            </div>
                            <h1
                                class="text-4xl md:text-5xl font-extrabold mb-1 tracking-tight bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
                                PROShop
                            </h1>
                            <p class="text-blue-100 text-base md:text-lg font-medium">
                                Sistem Point of Sale
                            </p>

                            <div class="mt-8 flex justify-center space-x-4">
                                <i
                                    class="fas fa-chart-line text-2xl md:text-3xl opacity-60 hover:opacity-100 transition-all hover:scale-125 duration-300 cursor-pointer"></i>
                                <i
                                    class="fas fa-shopping-cart text-2xl md:text-3xl opacity-60 hover:opacity-100 transition-all hover:scale-125 duration-300 cursor-pointer"></i>
                                <i
                                    class="fas fa-users text-2xl md:text-3xl opacity-60 hover:opacity-100 transition-all hover:scale-125 duration-300 cursor-pointer"></i>
                                <i
                                    class="fas fa-chart-pie text-2xl md:text-3xl opacity-60 hover:opacity-100 transition-all hover:scale-125 duration-300 cursor-pointer"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Kanan - Form Login -->
                    <div class="md:w-1/2 p-10 bg-white">
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-3xl font-bold text-gray-800">Selamat Datang</h2>
                            </div>
                            <p class="text-gray-500 text-sm">Silakan masukkan username dan password Anda</p>
                            <div class="w-20 h-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full mt-3"></div>
                        </div>

                        <form action="#" method="POST" id="loginForm">
                            @csrf

                            <!-- Username Field -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>
                                    Username
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" id="username" name="username" placeholder="Masukkan username"
                                        class="input-username" autocomplete="off">
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Password
                                </label>
                                <div class="input-wrapper">
                                    <input type="password" id="password" name="password"
                                        placeholder="Masukkan password" class="input-password" autocomplete="off">
                                    <button type="button" id="togglePasswordBtn" class="btn-password-toggle">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Login Button -->
                            <button type="submit" id="loginBtn"
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 px-4 rounded-xl btn-animate relative overflow-hidden shadow-lg hover:shadow-xl text-base mt-8">
                                <span class="relative z-10 flex items-center justify-center">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Masuk
                                </span>
                                <span
                                    class="absolute inset-0 bg-gradient-to-r from-blue-700 to-purple-700 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== MODAL VALIDASI & KONFIRMASI ==================== -->

        <!-- Modal Error Username Kosong -->
        <div id="modalUsernameEmpty" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop"
            style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 animate-modalFadeIn shadow-2xl">
                <div class="relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                        <div class="bg-red-500 rounded-full p-3 shadow-lg">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="pt-10 pb-6 px-6 text-center">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Validasi Gagal</h3>
                        <p class="text-gray-600">Username tidak boleh kosong!</p>
                        <p class="text-sm text-gray-500 mt-2">Silakan masukkan username Anda terlebih dahulu.</p>
                    </div>
                    <div class="border-t border-gray-100 p-4 flex justify-center">
                        <button onclick="closeModal('modalUsernameEmpty')"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all btn-animate">
                            <i class="fas fa-check mr-1"></i> Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Error Username Tidak Valid -->
        <div id="modalUsernameInvalid" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop"
            style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 animate-modalFadeIn shadow-2xl">
                <div class="relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                        <div class="bg-orange-500 rounded-full p-3 shadow-lg">
                            <i class="fas fa-user-slash text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="pt-10 pb-6 px-6 text-center">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Username Tidak Valid</h3>
                        <p class="text-gray-600">Username hanya boleh mengandung huruf, angka, dan underscore (_)!</p>
                        <p class="text-sm text-gray-500 mt-2">Minimal 3 karakter, tanpa spasi.</p>
                    </div>
                    <div class="border-t border-gray-100 p-4 flex justify-center">
                        <button onclick="closeModal('modalUsernameInvalid')"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all btn-animate">
                            <i class="fas fa-check mr-1"></i> Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Error Password Kosong -->
        <div id="modalPasswordEmpty" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop"
            style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 animate-modalFadeIn shadow-2xl">
                <div class="relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                        <div class="bg-yellow-500 rounded-full p-3 shadow-lg">
                            <i class="fas fa-lock text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="pt-10 pb-6 px-6 text-center">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Validasi Gagal</h3>
                        <p class="text-gray-600">Password tidak boleh kosong!</p>
                        <p class="text-sm text-gray-500 mt-2">Silakan masukkan password Anda.</p>
                    </div>
                    <div class="border-t border-gray-100 p-4 flex justify-center">
                        <button onclick="closeModal('modalPasswordEmpty')"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all btn-animate">
                            <i class="fas fa-check mr-1"></i> Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Error Username dan Password Kosong -->
        <div id="modalUsernamePasswordEmpty"
            class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop" style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 animate-modalFadeIn shadow-2xl">
                <div class="relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                        <div class="bg-red-500 rounded-full p-3 shadow-lg">
                            <i class="fas fa-lock text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="pt-10 pb-6 px-6 text-center">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Validasi Gagal</h3>
                        <p class="text-gray-600">Username dan password tidak boleh kosong!</p>
                        <p class="text-sm text-gray-500 mt-2">Silakan masukkan username dan password Anda.</p>
                    </div>
                    <div class="border-t border-gray-100 p-4 flex justify-center">
                        <button onclick="closeModal('modalUsernamePasswordEmpty')"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all btn-animate">
                            <i class="fas fa-check mr-1"></i> Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Login -->
        <div id="modalConfirmLogin" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop"
            style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 animate-modalFadeIn shadow-2xl">
                <div class="relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                        <div class="bg-blue-500 rounded-full p-3 shadow-lg">
                            <i class="fas fa-question-circle text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="pt-10 pb-6 px-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Konfirmasi Login</h3>
                        <p class="text-gray-600 text-center mb-4">Apakah Anda yakin ingin login dengan data berikut?
                        </p>
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-600 font-medium">Username:</span>
                                <span id="confirmUsername" class="text-blue-600 font-semibold"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 font-medium">Password:</span>
                                <span class="text-gray-500">••••••••</span>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 p-4 flex justify-center space-x-3">
                        <button onclick="closeModal('modalConfirmLogin')"
                            class="px-5 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all btn-animate">
                            <i class="fas fa-times mr-1"></i> Batal
                        </button>
                        <button onclick="proceedLogin()"
                            class="px-5 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all btn-animate">
                            <i class="fas fa-check mr-1"></i> Ya, Login
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Loading Proses Login -->
        <div id="modalLoading" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop"
            style="display: none;">
            <div class="bg-white rounded-2xl max-w-sm w-full mx-4 p-8 animate-modalFadeIn text-center shadow-2xl">
                <div class="flex justify-center mb-4">
                    <div class="relative">
                        <div class="animate-spin rounded-full h-14 w-14 border-4 border-gray-200"></div>
                        <div
                            class="animate-spin rounded-full h-14 w-14 border-4 border-blue-600 border-t-transparent absolute top-0 left-0">
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Memproses Login</h3>
                <p class="text-gray-500 text-sm">Sedang memverifikasi data Anda...</p>
            </div>
        </div>

        <!-- Modal Sukses Login -->
        <div id="modalSuccess" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop"
            style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 animate-slideDown shadow-2xl">
                <div class="relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                        <div class="bg-green-500 rounded-full p-3 shadow-lg animate-pulseGlow">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="pt-10 pb-6 px-6 text-center">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Login Berhasil!</h3>
                        <div class="flex justify-center mb-3">
                            <i class="fas fa-store text-6xl text-green-500"></i>
                        </div>
                        <p class="text-gray-600">Selamat datang kembali di PROShop!</p>
                        <p class="text-gray-700 font-semibold mt-2" id="successUsername"></p>
                        <p class="text-sm text-gray-500 mt-3">Anda akan diarahkan ke dashboard dalam 2 detik.</p>
                    </div>
                    <div class="border-t border-gray-100 p-4">
                        <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div id="successProgress"
                                class="h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full"
                                style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Error Login Gagal -->
        <div id="modalError" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop"
            style="display: none;">
            <div class="bg-white rounded-2xl max-w-md w-full mx-4 animate-modalFadeIn shadow-2xl">
                <div class="relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                        <div class="bg-red-500 rounded-full p-3 shadow-lg">
                            <i class="fas fa-times-circle text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="pt-10 pb-6 px-6 text-center">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Login Gagal</h3>
                        <p class="text-gray-600" id="errorMessage">Username atau password yang Anda masukkan salah!
                        </p>
                        <p class="text-sm text-gray-500 mt-2">Silakan coba kembali dengan data yang benar.</p>
                    </div>
                    <div class="border-t border-gray-100 p-4 flex justify-center">
                        <button onclick="closeModal('modalError')"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all btn-animate">
                            <i class="fas fa-redo mr-1"></i> Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Create floating particles
            function createParticles() {
                const particlesContainer = document.getElementById('particles');
                if (!particlesContainer) return;

                for (let i = 0; i < 30; i++) {
                    const particle = document.createElement('div');
                    particle.classList.add('particle');
                    const size = Math.random() * 8 + 2;
                    particle.style.width = size + 'px';
                    particle.style.height = size + 'px';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 5 + 's';
                    particle.style.animationDuration = Math.random() * 4 + 4 + 's';
                    particlesContainer.appendChild(particle);
                }
            }
            createParticles();

            // ========== TOGGLE PASSWORD - FIX TOTAL (TIDAK BERGERAK SAMA SEKALI) ==========
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Cek tipe saat ini
                    const currentType = passwordInput.getAttribute('type');
                    const newType = currentType === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', newType);

                    // Ganti icon
                    const icon = toggleBtn.querySelector('i');
                    if (icon) {
                        if (icon.classList.contains('fa-eye-slash')) {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    }
                });
            }

            // Function to open modal
            function openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'flex';
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }

            // Function to close modal
            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    const modalContent = modal.querySelector('.bg-white');
                    if (modalContent) {
                        modalContent.classList.add('animate-modalFadeOut');
                        setTimeout(() => {
                            modal.style.display = 'none';
                            modal.classList.add('hidden');
                            if (modalContent) {
                                modalContent.classList.remove('animate-modalFadeOut');
                            }
                            document.body.style.overflow = 'auto';
                        }, 300);
                    } else {
                        modal.style.display = 'none';
                        modal.classList.add('hidden');
                        document.body.style.overflow = 'auto';
                    }
                }
            }

            // Get form elements
            const form = document.getElementById('loginForm');
            const usernameInput = document.getElementById('username');
            const passwordField = document.getElementById('password');

            // Store username for confirmation
            let currentUsername = '';

            // Validate username format
            function isValidUsername(username) {
                const usernameRegex = /^[a-zA-Z0-9_]{3,}$/;
                return usernameRegex.test(username);
            }

            // Handle form submit
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const username = usernameInput ? usernameInput.value.trim() : '';
                    const password = passwordField ? passwordField.value.trim() : '';

                    if (!username && !password) {
                        openModal('modalUsernamePasswordEmpty');
                        if (usernameInput) {
                            usernameInput.classList.add('animate-shake');
                            setTimeout(() => usernameInput.classList.remove('animate-shake'), 500);
                        }
                        if (passwordField) {
                            passwordField.classList.add('animate-shake');
                            setTimeout(() => passwordField.classList.remove('animate-shake'), 500);
                        }
                        return;
                    }

                    if (!username) {
                        openModal('modalUsernameEmpty');
                        if (usernameInput) {
                            usernameInput.classList.add('animate-shake');
                            setTimeout(() => usernameInput.classList.remove('animate-shake'), 500);
                        }
                        return;
                    }

                    if (!isValidUsername(username)) {
                        openModal('modalUsernameInvalid');
                        if (usernameInput) {
                            usernameInput.classList.add('animate-shake');
                            setTimeout(() => usernameInput.classList.remove('animate-shake'), 500);
                        }
                        return;
                    }

                    if (!password) {
                        openModal('modalPasswordEmpty');
                        if (passwordField) {
                            passwordField.classList.add('animate-shake');
                            setTimeout(() => passwordField.classList.remove('animate-shake'), 500);
                        }
                        return;
                    }

                    currentUsername = username;
                    const confirmUsernameSpan = document.getElementById('confirmUsername');
                    if (confirmUsernameSpan) {
                        confirmUsernameSpan.textContent = username;
                    }
                    openModal('modalConfirmLogin');
                });
            }

            // Proceed login
            function proceedLogin() {
                closeModal('modalConfirmLogin');
                openModal('modalLoading');

                const username = currentUsername;
                const password = passwordField ? passwordField.value.trim() : '';

                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);

                fetch("{{ route('login.post') }}", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        closeModal('modalLoading');

                        console.log(data); // DEBUG WAJIB

                        if (data.success) {

                            const successUsernameSpan = document.getElementById('successUsername');
                            if (successUsernameSpan) {
                                successUsernameSpan.innerHTML =
                                    '<i class="fas fa-user-circle mr-1"></i> ' + data.user;
                            }

                            openModal('modalSuccess');

                            let width = 0;
                            const progressBar = document.getElementById('successProgress');

                            const interval = setInterval(() => {
                                if (width >= 100) {
                                    clearInterval(interval);
                                    closeModal('modalSuccess');

                                    // 🔥 FIX UTAMA REDIRECT
                                    window.location.href = data.redirect;
                                } else {
                                    width += 10;
                                    progressBar.style.width = width + '%';
                                }
                            }, 200);

                        } else {
                            const errorMessageSpan = document.getElementById('errorMessage');
                            errorMessageSpan.innerHTML = data.message || 'Login gagal';
                            openModal('modalError');
                        }
                    })
                    .catch(error => {
                        closeModal('modalLoading');
                        console.error(error);
                        openModal('modalError');
                    });
            }

            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                const modals = document.querySelectorAll('[id^="modal"]');
                modals.forEach(modal => {
                    if (e.target === modal) {
                        const modalId = modal.id;
                        closeModal(modalId);
                    }
                });
            });

            // Add ripple effect to buttons
            document.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function(e) {
                    const rect = button.getBoundingClientRect();
                    const ripple = document.createElement('span');
                    ripple.className = 'ripple-effect';
                    ripple.style.left = (e.clientX - rect.left) + 'px';
                    ripple.style.top = (e.clientY - rect.top) + 'px';
                    button.style.position = 'relative';
                    button.style.overflow = 'hidden';
                    button.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        </script>
    </body>

    </html>
