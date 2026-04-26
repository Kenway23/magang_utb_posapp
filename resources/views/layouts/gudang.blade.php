<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PROShop - Gudang</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (tanpa defer agar langsung tersedia) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: #f1f5f9;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }

        .sidebar-item:hover {
            background: linear-gradient(90deg, #eef2ff 0%, #ffffff 100%);
            transform: translateX(4px);
        }

        .sidebar-item-active {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 500;
        }

        .submenu-item:hover {
            background: #f1f5f9;
            transform: translateX(4px);
        }

        .submenu-active {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 500;
        }

        .rotate-icon {
            transition: transform 0.3s ease;
        }

        .rotate-icon.rotate {
            transform: rotate(90deg);
        }

        .submenu-collapse {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .submenu-collapse.open {
            max-height: 500px;
            transition: max-height 0.3s ease-in;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-approved {
            background: #d1fae5;
            color: #059669;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-draft {
            background: #e0e7ff;
            color: #4f46e5;
        }

        @keyframes modalAnim {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-modal {
            animation: modalAnim 0.2s ease-out;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #64748b;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background-color: #475569;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-slate-100">

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-72 bg-white shadow-xl flex flex-col h-full overflow-y-auto custom-scroll">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center overflow-hidden bg-indigo-100">
                        <i class="fas fa-store text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">PROShop</h1>
                        <p class="text-xs text-slate-400">Gudang</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('gudang.dashboard') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gudang.dashboard') ? 'sidebar-item-active' : 'text-slate-600' }}">
                    <i class="fas fa-home w-5 h-5"></i> Beranda
                </a>

                <a href="{{ route('gudang.produk.index') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gudang.produk.index') ? 'sidebar-item-active' : 'text-slate-600' }}">
                    <i class="fas fa-box w-5 h-5"></i>
                    <span>Produk</span>
                </a>

                <!-- Stok Dropdown -->
                <div>
                    <button onclick="toggleSubmenu('stokSubmenu', 'stokIcon')"
                        class="sidebar-item flex items-center justify-between w-full px-4 py-3 rounded-xl transition-all duration-200 text-slate-600">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-warehouse w-5 h-5"></i>
                            <span>Stok</span>
                        </div>
                        <i id="stokIcon" class="fas fa-chevron-right text-xs rotate-icon"></i>
                    </button>
                    <div id="stokSubmenu" class="submenu-collapse ml-6 mt-1 space-y-1">

                        {{-- 🔥 REQUEST TAMBAH STOK (tanpa stok) --}}
                        <a href="{{ route('gudang.tambah_stok.index') }}"
                            class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('gudang.tambah_stok.*') ? 'submenu-active' : 'text-slate-600' }}">
                            <i class="fas fa-arrow-down w-4 h-4"></i> Request Tambah Stok
                        </a>

                        {{-- 🔥 REQUEST PENGIRIMAN (tanpa stok) --}}
                        <a href="{{ route('gudang.pengiriman.index') }}"
                            class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('gudang.pengiriman.*') ? 'submenu-active' : 'text-slate-600' }}">
                            <i class="fas fa-arrow-up w-4 h-4"></i> Request Kirim Stok
                        </a>

                        {{-- PENYESUAIAN STOK --}}
                        <a href="{{ route('gudang.stok.penyesuaian') }}"
                            class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('gudang.stok.penyesuaian') ? 'submenu-active' : 'text-slate-600' }}">
                            <i class="fas fa-sliders-h w-4 h-4"></i> Penyesuaian Stok
                        </a>

                        {{-- LAPORAN STOK --}}
                        <a href="{{ route('gudang.stok.laporan') }}"
                            class="submenu-item flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('gudang.stok.laporan') ? 'submenu-active' : 'text-slate-600' }}">
                            <i class="fas fa-file-alt w-4 h-4"></i> Laporan Stok
                        </a>
                    </div>
                </div>

                <a href="{{ route('gudang.riwayat_gudang') }}"
                    class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gudang.riwayat') ? 'sidebar-item-active' : 'text-slate-600' }}">
                    <i class="fas fa-history w-5 h-5"></i> Riwayat Pergerakan Gudang
                </a>
            </nav>

            <div class="p-4 border-t border-slate-200">
                <button onclick="showLogoutConfirm()"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-all duration-200 w-full">
                    <i class="fas fa-sign-out-alt w-5 h-5"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-auto custom-scroll">
            <div class="bg-white shadow-sm px-8 py-5 border-b border-slate-200 sticky top-0 z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-800">@yield('page-title')</h2>
                        <p class="text-sm text-slate-500 mt-0.5">@yield('page-subtitle')</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 pl-3 border-l border-slate-200">
                            <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="hidden md:block">
                                <p class="font-semibold text-slate-800 text-sm">{{ Auth::user()->name ?? 'Gudang' }}
                                </p>
                                <p class="text-xs text-slate-400">Online</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // ==================== HELPER FUNCTIONS ====================
        function toggleSubmenu(submenuId, iconId) {
            const submenu = document.getElementById(submenuId);
            const icon = document.getElementById(iconId);
            if (submenu && icon) {
                submenu.classList.toggle('open');
                icon.classList.toggle('rotate');
            }
        }

        // Cek status submenu dari URL
        function checkSubmenuActive() {
            const currentUrl = window.location.href;
            const stokSubmenu = document.getElementById('stokSubmenu');
            const stokIcon = document.getElementById('stokIcon');

            if (currentUrl.includes('/gudang/stok/')) {
                if (stokSubmenu && stokIcon) {
                    stokSubmenu.classList.add('open');
                    stokIcon.classList.add('rotate');
                }
            }
        }

        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        function showSuccess(message, title = 'Berhasil!') {
            const modal = document.getElementById('modalSuccess');
            if (modal) {
                document.getElementById('successTitle').innerText = title;
                document.getElementById('successMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => closeModal('modalSuccess'), 2500);
            }
        }

        function showError(message, title = 'Gagal!') {
            const modal = document.getElementById('modalError');
            if (modal) {
                document.getElementById('errorTitle').innerText = title;
                document.getElementById('errorMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function showWarning(message, title = 'Peringatan!') {
            const modal = document.getElementById('modalWarning');
            if (modal) {
                document.getElementById('warningTitle').innerText = title;
                document.getElementById('warningMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function showInfo(message, title = 'Informasi') {
            const modal = document.getElementById('modalInfo');
            if (modal) {
                document.getElementById('infoTitle').innerText = title;
                document.getElementById('infoMessage').innerHTML = message;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => closeModal('modalInfo'), 2000);
            }
        }

        function showConfirmDelete(message, onConfirm) {
            const modal = document.getElementById('modalConfirmDelete');
            if (modal) {
                document.getElementById('confirmDeleteMessage').innerHTML = message;
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                const newBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
                newBtn.onclick = () => {
                    closeModal('modalConfirmDelete');
                    if (onConfirm) onConfirm();
                };
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function showLogoutConfirm() {
            const modal = document.getElementById('modalLogoutConfirm');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function confirmLogout() {
            closeModal('modalLogoutConfirm');
            showInfo('Logging out...', 'Proses');
            setTimeout(() => {
                document.getElementById('logout-form')?.submit();
            }, 1000);
        }

        function showLoading(message) {
            const modal = document.getElementById('modalLoading');
            if (modal) {
                document.getElementById('loadingMessage').innerHTML = message || 'Memproses...';
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function hideLoading() {
            const modal = document.getElementById('modalLoading');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        function exportToExcel() {
            showInfo('Fitur export Excel akan segera hadir', 'Informasi');
        }

        function printReport() {
            window.print();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            checkSubmenuActive();
        });
    </script>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- MODALS -->
    <div id="modalSuccess" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center animate-modal">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2" id="successTitle">Berhasil!</h3>
            <p class="text-slate-500" id="successMessage">Data berhasil disimpan</p>
            <button onclick="closeModal('modalSuccess')"
                class="mt-6 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Tutup</button>
        </div>
    </div>

    <div id="modalError" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center animate-modal">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times-circle text-red-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2" id="errorTitle">Gagal!</h3>
            <p class="text-slate-500" id="errorMessage">Terjadi kesalahan</p>
            <button onclick="closeModal('modalError')"
                class="mt-6 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Tutup</button>
        </div>
    </div>

    <div id="modalWarning" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center animate-modal">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2" id="warningTitle">Peringatan!</h3>
            <p class="text-slate-500" id="warningMessage"></p>
            <button onclick="closeModal('modalWarning')"
                class="mt-6 px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">Tutup</button>
        </div>
    </div>

    <div id="modalInfo" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6 text-center animate-modal">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-info-circle text-blue-600 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2" id="infoTitle">Informasi</h3>
            <p class="text-slate-500" id="infoMessage"></p>
            <button onclick="closeModal('modalInfo')"
                class="mt-6 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
        </div>
    </div>

    <div id="modalConfirmDelete" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-trash-alt text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-800">Konfirmasi Hapus</h3>
                </div>
                <p class="text-slate-500 mb-6" id="confirmDeleteMessage">Apakah Anda yakin ingin menghapus data ini?
                </p>
                <div class="flex justify-end gap-3">
                    <button onclick="closeModal('modalConfirmDelete')"
                        class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition">Batal</button>
                    <button id="confirmDeleteBtn"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalLoading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xs mx-4 p-6 text-center animate-modal">
            <div
                class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3">
            </div>
            <p class="text-slate-600" id="loadingMessage">Memproses...</p>
        </div>
    </div>

    <div id="modalLogoutConfirm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800">Konfirmasi Keluar</h3>
                        <p class="text-sm text-slate-500">Apakah Anda yakin ingin keluar?</p>
                    </div>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-amber-800 font-medium">Perhatian!</p>
                            <p class="text-xs text-amber-700 mt-1">Anda akan keluar dari sistem. Pastikan semua data
                                telah tersimpan sebelum keluar.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button onclick="closeModal('modalLogoutConfirm')"
                        class="px-5 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition font-medium">
                        <i class="fas fa-times mr-2"></i> Batal
                    </button>
                    <button onclick="confirmLogout()"
                        class="px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
