@extends('layouts.gudang')

@section('title', 'PROShop - Penerimaan Stok')

@section('content')
<div id="dynamic-content">
    <div class="flex justify-center items-center h-64">
        <div class="text-center">
            <i class="fas fa-spinner animate-spin text-4xl text-indigo-600 mb-4"></i>
            <p class="text-slate-500">Memuat halaman penerimaan...</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof showPage === 'function') {
            showPage('penerimaan');
        }
    });
</script>
@endsection