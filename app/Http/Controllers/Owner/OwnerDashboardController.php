<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\Pengiriman;
use App\Models\TambahStock;
use App\Models\StockAdjustment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        // Total Produk
        $totalProduk = Produk::where('status', 'approved')->count();

        // Total Pendapatan (hanya transaksi COMPLETED)
        $totalPendapatan = Transaksi::where('status', 'COMPLETED')->sum('total_harga');

        // Total Transaksi
        $totalTransaksi = Transaksi::count();

        // Produk Stok Rendah (<=10)
        $produkStokRendah = Produk::with('kategori')
            ->where('status', 'approved')
            ->where('stok_gudang', '<=', 10)
            ->orderBy('stok_gudang', 'asc')
            ->limit(5)
            ->get();

        // Aktivitas Terakhir (gabungan dari berbagai sumber)
        $aktivitasTerakhir = $this->getRecentActivities();

        // Penjualan Terbaik (Top 5 produk berdasarkan jumlah terjual)
        $penjualanTerbaik = $this->getTopSellingProducts();

        // Pengguna Terbaru
        $penggunaTerbaru = User::with('role')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistik untuk grafik (opsional)
        $statistik = [
            'total_produk' => $totalProduk,
            'total_pendapatan' => $totalPendapatan,
            'total_transaksi' => $totalTransaksi,
            'pending_produk' => Produk::where('status', 'pending')->count(),
            'pending_pengiriman' => Pengiriman::where('status', 'pending')->count(),
            'stok_menipis' => Produk::where('status', 'approved')->where('stok_gudang', '<=', 10)->count(),
        ];

        return view('owner.d_owner', compact(
            'totalProduk',
            'totalPendapatan',
            'totalTransaksi',
            'produkStokRendah',
            'aktivitasTerakhir',
            'penjualanTerbaik',
            'penggunaTerbaru',
            'statistik'
        ));
    }

    private function getRecentActivities()
    {
        $activities = [];

        // Transaksi terbaru
        $transaksi = Transaksi::with('kasir')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'transaksi',
                    'icon' => 'fa-cart-shopping',
                    'icon_color' => 'bg-green-100 text-green-600',
                    'title' => 'Transaksi Baru',
                    'description' => "No. " . ($item->kode_transaksi ?? 'TRX-' . $item->transaksi_id),
                    'time' => $item->created_at->diffForHumans(),
                    'user' => $item->kasir->name ?? 'Kasir'
                ];
            });

        // Penerimaan stok (Tambah Stock yang approved)
        $penerimaan = TambahStock::with('produk', 'requester')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'penerimaan',
                    'icon' => 'fa-plus-circle',
                    'icon_color' => 'bg-blue-100 text-blue-600',
                    'title' => 'Penerimaan Stok',
                    'description' => ($item->produk->nama_produk ?? 'Produk') . " +{$item->jumlah_request} pcs",
                    'time' => $item->created_at->diffForHumans(),
                    'user' => $item->requester->name ?? 'Gudang'
                ];
            });

        // Pengeluaran stok (Pengiriman yang approved)
        $pengeluaran = Pengiriman::with('produk', 'requester')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'pengeluaran',
                    'icon' => 'fa-minus-circle',
                    'icon_color' => 'bg-red-100 text-red-600',
                    'title' => 'Pengeluaran Stok',
                    'description' => ($item->produk->nama_produk ?? 'Produk') . " -{$item->jumlah} pcs",
                    'time' => $item->created_at->diffForHumans(),
                    'user' => $item->requester->name ?? 'Gudang'
                ];
            });

        // Penyesuaian stok (Stock Adjustment yang approved)
        $penyesuaian = StockAdjustment::with('produk', 'creator')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                $perubahanText = $item->perubahan >= 0 ? '+' . $item->perubahan : (string) $item->perubahan;
                return [
                    'type' => 'penyesuaian',
                    'icon' => 'fa-sliders-h',
                    'icon_color' => 'bg-purple-100 text-purple-600',
                    'title' => 'Penyesuaian Stok',
                    'description' => ($item->produk->nama_produk ?? 'Produk') . " {$perubahanText} pcs",
                    'time' => $item->created_at->diffForHumans(),
                    'user' => $item->creator->name ?? 'Gudang'
                ];
            });

        // Gabungkan dan urutkan
        $activities = collect()
            ->concat($transaksi)
            ->concat($penerimaan)
            ->concat($pengeluaran)
            ->concat($penyesuaian)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        return $activities;
    }

    private function getTopSellingProducts()
    {
        // Ambil dari detail transaksi
        $topProducts = DB::table('detail_transaksis')
            ->join('produks', 'detail_transaksis.produk_id', '=', 'produks.produk_id')
            ->select(
                'produks.nama_produk',
                DB::raw('SUM(detail_transaksis.jumlah) as total_terjual'),
                DB::raw('SUM(detail_transaksis.subtotal) as total_pendapatan')
            )
            ->groupBy('produks.produk_id', 'produks.nama_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        return $topProducts;
    }
}