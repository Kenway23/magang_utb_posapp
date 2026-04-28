@extends('layouts.gudang')

@section('title', 'PROShop - Laporan Stok Gudang')
@section('page-title', 'Laporan Stok Gudang')
@section('page-subtitle', 'Laporan pergerakan stok gudang')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-slate-800">
                <i class="fas fa-file-alt text-indigo-600 mr-2"></i>Laporan Pergerakan Stok
            </h3>
            <div class="flex gap-2">
                <button onclick="exportToPDF()"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
                <button onclick="exportToExcel()"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
                <button onclick="printReport()"
                    class="bg-slate-600 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="mb-6 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" id="dariTanggal" onchange="loadData()"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="sampaiTanggal" onchange="loadData()"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                <select id="kategoriFilter" onchange="loadData()"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <option value="all">Semua Kategori</option>
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->kategori_id }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <button onclick="resetFilter()"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-undo mr-1"></i> Reset
            </button>
        </div>

        <!-- Loading -->
        <div id="loading" class="text-center py-8 hidden">
            <div class="inline-flex flex-col items-center">
                <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                <p class="mt-2 text-sm text-slate-500">Memuat data...</p>
            </div>
        </div>

        <!-- Tabel -->
        <div id="tableContainer" class="hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="laporanTable">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-3 text-left">No</th>
                            <th class="p-3 text-left">Nama Produk</th>
                            <th class="p-3 text-left">Kategori</th>
                            <th class="p-3 text-center">Stok Awal</th>
                            <th class="p-3 text-center">Penerimaan</th>
                            <th class="p-3 text-center">Pengeluaran</th>
                            <th class="p-3 text-center">Penyesuaian</th>
                            <th class="p-3 text-center">Stok Akhir</th>
                            <th class="p-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="laporanTableBody"></tbody>
                    <tfoot class="bg-slate-100 font-semibold" id="laporanTableFooter"></tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6" id="statistikCards"></div>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-aman {
            background: #d1fae5;
            color: #059669;
        }

        .status-menipis {
            background: #fef3c7;
            color: #d97706;
        }

        .status-kritis {
            background: #fee2e2;
            color: #dc2626;
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        @media print {

            button,
            .no-print {
                display: none;
            }

            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        let laporanData = [];
        let summaryData = {};

        function formatRp(angka) {
            return 'Rp ' + (angka || 0).toLocaleString('id-ID');
        }

        async function loadData() {
            const loading = document.getElementById('loading');
            const tableContainer = document.getElementById('tableContainer');
            const statCards = document.getElementById('statistikCards');

            loading.classList.remove('hidden');
            tableContainer.classList.add('hidden');
            statCards.classList.add('hidden');

            const dariTanggal = document.getElementById('dariTanggal').value;
            const sampaiTanggal = document.getElementById('sampaiTanggal').value;
            const kategoriId = document.getElementById('kategoriFilter').value;

            let url = `/gudang/stok/laporan-gudang/data?`;
            if (dariTanggal) url += `dari_tanggal=${dariTanggal}&`;
            if (sampaiTanggal) url += `sampai_tanggal=${sampaiTanggal}&`;
            if (kategoriId && kategoriId !== 'all') url += `kategori_id=${kategoriId}`;

            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    laporanData = result.data;
                    summaryData = result.summary;
                    renderTable();
                    renderStatistik();
                    tableContainer.classList.remove('hidden');
                    statCards.classList.remove('hidden');
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

        function renderTable() {
            const tbody = document.getElementById('laporanTableBody');
            const tfoot = document.getElementById('laporanTableFooter');

            if (laporanData.length === 0) {
                tbody.innerHTML =
                    `<tr><td colspan="9" class="text-center py-8 text-slate-400">Tidak ada数据\n                </td></td>`;
                tfoot.innerHTML = '';
                return;
            }

            tbody.innerHTML = laporanData.map((item, idx) => {
                let statusClass = '';
                if (item.status === 'Aman') statusClass = 'status-aman';
                else if (item.status === 'Menipis') statusClass = 'status-menipis';
                else statusClass = 'status-kritis';

                return `
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="p-3">${idx + 1}</td>
                        <td class="p-3 font-medium">${item.nama_produk}</td>
                        <td class="p-3">${item.kategori}</td>
                        <td class="p-3 text-center">${item.stok_awal} ${item.satuan}</td>
                        <td class="p-3 text-center text-green-600">+${item.penerimaan} ${item.satuan}</td>
                        <td class="p-3 text-center text-red-600">-${item.pengeluaran} ${item.satuan}</td>
                        <td class="p-3 text-center ${item.penyesuaian >= 0 ? 'text-green-600' : 'text-red-600'}">
                            ${item.penyesuaian >= 0 ? '+' : ''}${item.penyesuaian} ${item.satuan}
                        </td>
                        <td class="p-3 text-center font-bold">${item.stok_akhir} ${item.satuan}</td>
                        <td class="p-3 text-center"><span class="status-badge ${statusClass}">${item.status}</span></td>
                    </tr>
                `;
            }).join('');

            tfoot.innerHTML = `
                <tr>
                    <td colspan="3" class="p-3 text-right font-bold">Total:</td>
                    <td class="p-3 text-center font-bold">${summaryData.total_awal} pcs</td>
                    <td class="p-3 text-center font-bold text-green-600">+${summaryData.total_masuk} pcs</td>
                    <td class="p-3 text-center font-bold text-red-600">-${summaryData.total_keluar} pcs</td>
                    <td class="p-3 text-center font-bold">${summaryData.total_penyesuaian >= 0 ? '+' : ''}${summaryData.total_penyesuaian} pcs</td>
                    <td class="p-3 text-center font-bold">${summaryData.total_akhir} pcs</td>
                    <td class="p-3"></td>
                </tr>
            `;
        }

        function renderStatistik() {
            const container = document.getElementById('statistikCards');
            container.innerHTML = `
                <div class="bg-white rounded-xl shadow-sm p-5 card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Penerimaan</p>
                            <p class="text-2xl font-bold text-slate-800">${summaryData.total_masuk}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-arrow-up text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Pengeluaran</p>
                            <p class="text-2xl font-bold text-slate-800">${summaryData.total_keluar}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Stok Akhir</p>
                            <p class="text-2xl font-bold text-slate-800">${summaryData.total_akhir}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 card">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500">Total Nilai Inventaris</p>
                            <p class="text-2xl font-bold text-slate-800">${formatRp(summaryData.total_nilai)}</p>
                        </div>
                    </div>
                </div>
            `;
        }

        function resetFilter() {
            document.getElementById('dariTanggal').value = '';
            document.getElementById('sampaiTanggal').value = '';
            document.getElementById('kategoriFilter').value = 'all';
            loadData();
        }

        function setDefaultDate() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            document.getElementById('dariTanggal').value = firstDay.toISOString().split('T')[0];
            document.getElementById('sampaiTanggal').value = today.toISOString().split('T')[0];
        }

        function showNotification(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-5 right-5 z-50 px-3 py-2 rounded-lg shadow-lg text-xs font-medium transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
            }`;
            toast.innerHTML =
                `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-1"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

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

                const pdfContainer = document.createElement('div');
                pdfContainer.style.padding = '20px';
                pdfContainer.style.fontFamily = 'Arial, sans-serif';
                pdfContainer.style.backgroundColor = 'white';
                pdfContainer.style.width = '800px';
                pdfContainer.style.position = 'absolute';
                pdfContainer.style.left = '-9999px';
                pdfContainer.style.top = '0';

                let tableRows = '';
                laporanData.forEach((item, idx) => {
                    tableRows += `
                        <tr>
                            <td style="border: 1px solid #cbd5e1; padding: 6px;">${idx + 1}</td>
                            <td style="border: 1px solid #cbd5e1; padding: 6px;">${item.nama_produk}</td>
                            <td style="border: 1px solid #cbd5e1; padding: 6px;">${item.kategori}</td>
                            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">${item.stok_awal}</td>
                            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">+${item.penerimaan}</td>
                            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">-${item.pengeluaran}</td>
                            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center;">${item.penyesuaian >= 0 ? '+' : ''}${item.penyesuaian}</td>
                            <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: bold;">${item.stok_akhir}</td>
                        </tr>
                    `;
                });

                pdfContainer.innerHTML = `
                    <div style="padding: 20px;">
                        <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 15px;">
                            <h2 style="margin: 0; color: #1e293b;">PROShop - Laporan Stok Gudang</h2>
                            <p style="margin: 5px 0; color: #64748b;">Periode: ${dariTanggal} s/d ${sampaiTanggal}</p>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                <thead>
                                    <tr style="background-color: #f1f5f9;">
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">No</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: left;">Produk</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: left;">Kategori</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">Awal</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">Masuk</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">Keluar</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">Penyesuaian</th>
                                        <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">Akhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableRows}
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #f8fafc;">
                                        <td colspan="3" style="border: 1px solid #cbd5e1; padding: 6px; text-align: right; font-weight: bold;">Total:
                                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: bold;">${summaryData.total_awal}</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: bold;">+${summaryData.total_masuk}</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: bold;">-${summaryData.total_keluar}</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: bold;">${summaryData.total_penyesuaian >= 0 ? '+' : ''}${summaryData.total_penyesuaian}</td>
                                        <td style="border: 1px solid #cbd5e1; padding: 6px; text-align: center; font-weight: bold;">${summaryData.total_akhir}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div style="text-align: center; margin-top: 20px; font-size: 9px; color: #94a3b8;">
                            Dicetak pada: ${now.toLocaleString('id-ID')}
                        </div>
                    </div>
                `;

                document.body.appendChild(pdfContainer);

                const canvas = await html2canvas(pdfContainer, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    logging: false
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

                pdf.save(`laporan_stok_gudang_${now.getFullYear()}-${now.getMonth()+1}-${now.getDate()}.pdf`);
                showNotification('PDF berhasil diunduh!', 'success');

            } catch (error) {
                console.error('PDF Error:', error);
                showNotification('Gagal membuat PDF', 'error');
            } finally {
                loadingDiv.remove();
                const pdfContainerElem = document.querySelector('div[style*="left: -9999px"]');
                if (pdfContainerElem) pdfContainerElem.remove();
            }
        }

        function exportToExcel() {
            let csvContent = "No,Nama Produk,Kategori,Stok Awal,Penerimaan,Pengeluaran,Penyesuaian,Stok Akhir,Status\n";
            laporanData.forEach((item, idx) => {
                csvContent +=
                    `${idx + 1},${item.nama_produk},${item.kategori},${item.stok_awal},${item.penerimaan},${item.pengeluaran},${item.penyesuaian},${item.stok_akhir},${item.status}\n`;
            });
            csvContent +=
                `\nTotal,,,${summaryData.total_awal},${summaryData.total_masuk},${summaryData.total_keluar},${summaryData.total_penyesuaian},${summaryData.total_akhir},\n`;

            const blob = new Blob(["\uFEFF" + csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', `laporan_stok_gudang_${new Date().toISOString().slice(0,19)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            showNotification('File Excel berhasil diunduh!', 'success');
        }

        function printReport() {
            window.print();
        }

        document.addEventListener('DOMContentLoaded', () => {
            setDefaultDate();
            loadData();
        });
    </script>
@endsection
