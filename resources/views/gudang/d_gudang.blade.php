@extends('layouts.gudang')

@section('title', 'PROShop - Dashboard Gudang')

@section('content')z
    <!-- Konten sama seperti di atas karena sudah di-handle oleh JavaScript -->
    <!-- File ini hanya sebagai placeholder, konten utama di-handle oleh JavaScript di layout -->
    <div id="dynamic-content">
        <div class="flex justify-center items-center h-64">
            <div class="text-center">
                <i class="fas fa-spinner animate-spin text-4xl text-indigo-600 mb-4"></i>
                <p class="text-slate-500">Memuat dashboard...</p>
            </div>
        </div>
    </div>

    <script>
        // Panggil showPage saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof showPage === 'function') {
                showPage('beranda');
            }
        });
    </script>
@endsection
