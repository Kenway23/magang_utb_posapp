<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROShop - Point of Sale System</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Animasi fade out */
        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        /* Animasi fade in */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Animasi bounce untuk logo */
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        /* Animasi pulse untuk teks */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Animasi loading bar */
        @keyframes loading {
            0% {
                width: 0%;
            }

            100% {
                width: 100%;
            }
        }

        /* Animasi spin untuk icon */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out;
        }

        .animate-bounce-custom {
            animation: bounce 1s ease-in-out;
        }

        .animate-pulse-custom {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .animate-loading {
            animation: loading 3s ease-out forwards;
        }

        .animate-spin-custom {
            animation: spin 2s linear infinite;
        }

        .splash-fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }

        /* Background gradient dengan pattern */
        .splash-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        /* Efek partikel/kotak-kotak */
        .splash-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
        }

        /* Efek lingkaran floating */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            pointer-events: none;
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
        }

        .circle-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
        }

        .circle-3 {
            width: 150px;
            height: 150px;
            bottom: 30%;
            right: 10%;
        }
    </style>
</head>

<body>
    <div id="splashScreen" class="splash-bg min-h-screen flex items-center justify-center relative">
        <!-- Lingkaran dekorasi -->
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>

        <!-- Konten Splash Screen -->
        <div class="text-center text-white z-10 px-4 animate-fadeIn">
            <!-- Icon/Logo -->
            <div class="mb-6 animate-bounce-custom">
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-6 inline-block">
                    <i class="fas fa-store text-7xl md:text-8xl"></i>
                </div>
            </div>

            <!-- Teks PROShop -->
            <h1 class="text-5xl md:text-7xl font-bold mb-3 tracking-tight">
                PROShop
            </h1>

            <!-- Subtitle -->
            <p class="text-lg md:text-xl text-blue-100 mb-8">
                <i class="fas fa-chart-line mr-2"></i>
                Sistem Point of Sale
                <i class="fas fa-shopping-cart ml-2"></i>
            </p>

            <!-- Loading Bar -->
            <div class="max-w-xs mx-auto mt-8">
                <div class="h-1 bg-white/30 rounded-full overflow-hidden">
                    <div class="h-full bg-white rounded-full animate-loading"></div>
                </div>
            </div>

            <!-- Loading Text dengan animasi -->
            <p class="text-sm text-blue-100 mt-4 animate-pulse-custom">
                <i class="fas fa-spinner animate-spin-custom mr-2"></i>
                Memuat sistem...
            </p>

            <!-- Versi -->
            <p class="text-xs text-blue-200 mt-8 absolute bottom-4 left-0 right-0 text-center">
                PROShop v1.0 | Point of Sale System
            </p>
        </div>
    </div>

    <script>
        // Tunggu 3 detik lalu fade out dan redirect ke login
        setTimeout(function() {
            const splashScreen = document.getElementById('splashScreen');

            // Cek apakah elemen ada
            if (splashScreen) {
                // Tambah class fade out
                splashScreen.classList.add('splash-fade-out');

                // Setelah animasi fade out selesai, redirect ke login
                setTimeout(function() {
                    window.location.href = "{{ url('/login') }}"; // Gunakan url() вместо route()
                }, 500); // 500ms = durasi animasi fade out
            } else {
                // Langsung redirect jika elemen tidak ditemukan
                window.location.href = "{{ url('/login') }}";
            }
        }, 3000); // 3000ms = 3 detik tampil splash screen
    </script>
</body>

</html>
