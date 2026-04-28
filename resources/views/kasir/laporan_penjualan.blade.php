@extends('layouts.kasir')

@section('title', 'Laporan Penjualan - PROShop')
@section('header-title', 'Laporan Penjualan')
@section('header-subtitle', 'Statistik dan ringkasan penjualan Anda')

@section('content')
    <div class="space-y-6">
        {{-- Filter & Export --}}
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" id="dariTanggal"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" id="sampaiTanggal"
                        class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <button onclick="loadData()"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        <i class="fas fa-search text-xs"></i> Tampilkan
                    </button>
                </div>
                <div>
                    <button onclick="resetFilter()"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        <i class="fas fa-undo-alt text-xs"></i> Reset
                    </button>
                </div>
                <div>
                    <button onclick="exportToPDF()"
                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        <i class="fas fa-file-pdf text-xs"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>

        {{-- Statistik --}}
        <div id="statistikContainer" class="grid grid-cols-1 md:grid-cols-3 gap-4 hidden">
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-500">
                <p class="text-slate-500 text-sm">Total Transaksi</p>
                <p class="text-2xl font-bold text-indigo-600" id="totalTransaksi">0</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
                <p class="text-slate-500 text-sm">Total Item Terjual</p>
                <p class="text-2xl font-bold text-blue-600" id="totalItem">0</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                <p class="text-slate-500 text-sm">Total Pendapatan</p>
                <p class="text-2xl font-bold text-green-600" id="totalPendapatan">Rp 0</p>
            </div>
        </div>

        {{-- Loading --}}
        <div id="loading" class="text-center py-8 hidden">
            <div class="inline-flex flex-col items-center">
                <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                <p class="mt-2 text-sm text-slate-500">Memuat data...</p>
            </div>
        </div>

        {{-- Produk Terlaris --}}
        <div id="produkContainer" class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-100 hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-semibold text-slate-800 text-sm">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>Top 10 Produk Terlaris
                </h3>
                <span class="text-xs text-slate-400 bg-white px-2 py-1 rounded-full" id="totalProdukCount">0</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">No</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500">Produk</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-slate-500">Qty Terjual</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-500">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody" class="divide-y divide-slate-100"></tbody>
                    <tfoot class="bg-slate-50">
                        <tr class="font-semibold">
                            <td colspan="2" class="px-4 py-2 text-right text-xs">Total:</td>
                            <td id="totalProductQty" class="px-4 py-2 text-center text-xs font-bold text-indigo-600">0</td>
                            <td id="totalProductRevenue" class="px-4 py-2 text-right text-xs font-bold text-green-600">Rp 0
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Grafik Penjualan Harian --}}
        <div id="chartContainer" class="bg-white rounded-xl shadow-sm p-4 hidden">
            <h3 class="font-semibold text-slate-800 text-sm mb-3">
                <i class="fas fa-chart-line text-indigo-500 mr-2"></i>Grafik Penjualan Harian
            </h3>
            <canvas id="dailyChart" height="80"></canvas>
        </div>
    </div>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        .status-approved {
            background: #d1fae5;
            color: #059669;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }
    </style>

    <!-- Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        // Global variables
        let topProducts = [];
        let dailyData = [];
        let statistik = {};
        let myChart = null;

        function formatRp(angka) {
            return 'Rp ' + (angka || 0).toLocaleString('id-ID');
        }

        async function loadData() {
            const loading = document.getElementById('loading');
            const containers = ['statistikContainer', 'produkContainer', 'chartContainer'];
            containers.forEach(c => {
                const el = document.getElementById(c);
                if (el) el.classList.add('hidden');
            });
            loading.classList.remove('hidden');

            const dariTanggal = document.getElementById('dariTanggal').value;
            const sampaiTanggal = document.getElementById('sampaiTanggal').value;

            let url = `/kasir/laporan-penjualan-kasir/data?`;
            if (dariTanggal) url += `dari_tanggal=${dariTanggal}&`;
            if (sampaiTanggal) url += `sampai_tanggal=${sampaiTanggal}`;

            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    topProducts = result.data.top_products || [];
                    dailyData = result.data.daily_data || [];
                    statistik = result.data.statistik || {};

                    // Update statistik
                    document.getElementById('totalTransaksi').innerText = statistik.total_transaksi || 0;
                    document.getElementById('totalItem').innerText = statistik.total_item || 0;
                    document.getElementById('totalPendapatan').innerHTML = formatRp(statistik.total_pendapatan || 0);

                    // Render tabel produk
                    renderProductTable();

                    // Update total produk count
                    document.getElementById('totalProdukCount').innerText = topProducts.length;

                    // Render grafik
                    renderChart();

                    // Tampilkan container
                    containers.forEach(c => {
                        const el = document.getElementById(c);
                        if (el) el.classList.remove('hidden');
                    });
                } else {
                    showNotification(result.message || 'Gagal memuat data', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat memuat data', 'error');
            } finally {
                loading.classList.add('hidden');
            }
        }

        function renderProductTable() {
            const tbody = document.getElementById('productTableBody');
            if (!tbody) return;

            if (topProducts.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada data penjualan</td><tr>`;
                document.getElementById('totalProductQty').innerText = '0';
                document.getElementById('totalProductRevenue').innerHTML = 'Rp 0';
                return;
            }

            const medals = ['🥇', '🥈', '🥉'];
            tbody.innerHTML = topProducts.map((item, idx) => {
                const rankIcon = idx < 3 ? `<span class="text-lg">${medals[idx]}</span>` : `${idx + 1}`;
                return `
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-center font-medium">${rankIcon}</td>
                        <td class="px-4 py-2 font-semibold text-slate-700">${escapeHtml(item.name)}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">${item.qty} pcs</span>
                        </td>
                        <td class="px-4 py-2 text-right font-semibold text-green-600">${formatRp(item.revenue)}</td>
                    </tr>
                `;
            }).join('');

            const totalQty = topProducts.reduce((sum, p) => sum + p.qty, 0);
            const totalRevenue = topProducts.reduce((sum, p) => sum + p.revenue, 0);

            document.getElementById('totalProductQty').innerText = totalQty;
            document.getElementById('totalProductRevenue').innerHTML = formatRp(totalRevenue);
        }

        function renderChart() {
            const ctx = document.getElementById('dailyChart');
            if (!ctx) return;

            if (myChart) {
                myChart.destroy();
            }

            if (dailyData.length === 0) {
                ctx.style.display = 'none';
                return;
            }
            ctx.style.display = 'block';

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dailyData.map(d => d.date),
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: dailyData.map(d => d.total),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `Rp ${ctx.raw.toLocaleString('id-ID')}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (v) => `Rp ${v.toLocaleString('id-ID')}`
                            }
                        }
                    }
                }
            });
        }

        function resetFilter() {
            document.getElementById('dariTanggal').value = '';
            document.getElementById('sampaiTanggal').value = '';
            loadData();
        }

        function setDefaultDate() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            document.getElementById('dariTanggal').value = firstDay.toISOString().split('T')[0];
            document.getElementById('sampaiTanggal').value = today.toISOString().split('T')[0];
        }

        // ==================== EXPORT PDF ====================
        async function exportToPDF() {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
            loadingDiv.innerHTML = `
                <div class="bg-white rounded-xl p-6 text-center min-w-[250px]">
                    <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-3"></div>
                    <p class="text-slate-600 text-sm">Membuat PDF...</p>
                </div>
            `;
            document.body.appendChild(loadingDiv);

            try {
                const dariTanggal = document.getElementById('dariTanggal').value || 'Semua';
                const sampaiTanggal = document.getElementById('sampaiTanggal').value || 'Semua';
                const now = new Date();

                // Buat container untuk PDF
                const pdfContainer = document.createElement('div');
                pdfContainer.style.padding = '20px';
                pdfContainer.style.fontFamily = 'Arial, sans-serif';
                pdfContainer.style.backgroundColor = 'white';
                pdfContainer.style.width = '800px';
                pdfContainer.style.position = 'absolute';
                pdfContainer.style.left = '-9999px';
                pdfContainer.style.top = '0';

                // Hitung total dari topProducts
                const totalQty = topProducts.reduce((sum, p) => sum + p.qty, 0);
                const totalRevenue = topProducts.reduce((sum, p) => sum + p.revenue, 0);

                // Buat HTML untuk PDF
                pdfContainer.innerHTML = `
                    <div style="padding: 20px;">
                        <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 15px;">
                            <h2 style="margin: 0; color: #1e293b;">PROShop</h2>
                            <p style="margin: 5px 0; color: #64748b;">Laporan Penjualan Kasir</p>
                            <p style="margin: 5px 0; font-size: 11px; color: #94a3b8;">Periode: ${dariTanggal} s/d ${sampaiTanggal}</p>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 10px; background: #f0fdf4; border: 1px solid #dcfce7; text-align: center;">
                                        <strong style="color: #166534;">Total Transaksi</strong><br>
                                        <span style="font-size: 18px; font-weight: bold;">${statistik.total_transaksi || 0}</span>
                                    </td>
                                    <td style="padding: 10px; background: #ecfdf5; border: 1px solid #d1fae5; text-align: center;">
                                        <strong style="color: #065f46;">Total Item</strong><br>
                                        <span style="font-size: 18px; font-weight: bold;">${statistik.total_item || 0}</span>
                                    </td>
                                    <td style="padding: 10px; background: #eff6ff; border: 1px solid #dbeafe; text-align: center;">
                                        <strong style="color: #1e40af;">Total Pendapatan</strong><br>
                                        <span style="font-size: 18px; font-weight: bold;">${formatRp(statistik.total_pendapatan || 0)}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 13px; color: #334155;">🏆 Top 10 Produk Terlaris</h3>
                            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <thead>
                                    <tr style="background-color: #f1f5f9;">
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">No</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: left;">Produk</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">Qty</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: right;">Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${topProducts.map((item, idx) => `
                                                        <tr>
                                                            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">${idx + 1}</td>
                                                            <td style="border: 1px solid #cbd5e1; padding: 6px;">${escapeHtml(item.name)}</td>
                                                            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">${item.qty}</td>
                                                            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right;">${formatRp(item.revenue)}</td>
                                                        </tr>
                                                    `).join('')}
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f8fafc;">
                                        <td colspan="2" style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold;">Total:
                                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: bold;">${totalQty}</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold; color: #059669;">${formatRp(totalRevenue)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                            Dicetak pada: ${now.toLocaleString('id-ID')}
                        </div>
                    </div>
                `;

                document.body.appendChild(pdfContainer);

                const canvas = await html2canvas(pdfContainer, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false,
                    useCORS: true
                });

                const imgData = canvas.toDataURL('image/png');
                const {
                    jsPDF
                } = window.jspdf;
                const imgWidth = 210;
                const pageHeight = 297;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                const pdf = new jsPDF('p', 'mm', 'a4');
                pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);

                let heightLeft = imgHeight - pageHeight;
                let position = -pageHeight;
                while (heightLeft > 0) {
                    position = position - pageHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                const fileName = `laporan_penjualan_${now.getFullYear()}-${now.getMonth()+1}-${now.getDate()}.pdf`;
                pdf.save(fileName);

                showNotification('PDF berhasil diunduh!', 'success');

            } catch (error) {
                console.error('PDF Error:', error);
                showNotification('Gagal membuat PDF: ' + error.message, 'error');
            } finally {
                const pdfContainerElem = document.querySelector('div[style*="left: -9999px"]');
                if (pdfContainerElem) pdfContainerElem.remove();
                loadingDiv.remove();
            }
        }

        function showNotification(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-5 right-5 z-50 px-3 py-2 rounded-lg shadow-lg text-xs font-medium transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            toast.innerHTML =
                `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-1"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        document.addEventListener('DOMContentLoaded', () => {
            setDefaultDate();
            loadData();
        });
    </script>
@endsection
