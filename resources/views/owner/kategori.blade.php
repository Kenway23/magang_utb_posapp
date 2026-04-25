@extends('layouts.owner')

@section('title', 'Kategori Barang - PROShop')
@section('header-title', 'Kategori Barang')
@section('header-subtitle', 'Kelola kategori produk yang tersedia di toko')

@section('content')
    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-semibold"><i class="fas fa-tags text-indigo-600 mr-2"></i>Daftar Kategori Barang</h3>
            <button onclick="showTambahKategori()"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Kategori
            </button>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="kategoriGrid">
                <!-- Data dari JS -->
            </div>
        </div>
    </div>

    {{-- Modal Tambah Kategori --}}
    <div id="modalTambahKategori" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div
                class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white rounded-t-2xl flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-plus-circle text-indigo-600 mr-2"></i>Tambah Kategori</h3>
                <button onclick="closeModal('modalTambahKategori')" class="text-slate-400 hover:text-slate-600"><i
                        class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div><label class="block text-sm font-medium mb-1">Nama Kategori</label><input type="text"
                        id="namaKategori" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
                        placeholder="Contoh: Makanan Ringan"></div>
                <div><label class="block text-sm font-medium mb-1">Icon</label><select id="iconKategori"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="fa-box">📦 Box</option>
                        <option value="fa-tag">🏷️ Tag</option>
                        <option value="fa-store">🏪 Store</option>
                        <option value="fa-utensils">🍽️ Makanan</option>
                        <option value="fa-wine-bottle">🍾 Minuman</option>
                        <option value="fa-soap">🧼 Sabun</option>
                    </select></div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                <button onclick="closeModal('modalTambahKategori')"
                    class="px-4 py-2 border rounded-lg text-slate-700 hover:bg-slate-50">Batal</button>
                <button onclick="tambahKategori()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Tambah</button>
            </div>
        </div>
    </div>

    <script>
        let categories = [{
                id: 1,
                nama: "Kabagari",
                icon: "fa-box",
                produk: 12,
                warna: "indigo"
            },
            {
                id: 2,
                nama: "Sabun",
                icon: "fa-soap",
                produk: 8,
                warna: "green"
            },
            {
                id: 3,
                nama: "Makanan Ringan",
                icon: "fa-cookie",
                produk: 15,
                warna: "amber"
            },
            {
                id: 4,
                nama: "Minuman",
                icon: "fa-wine-bottle",
                produk: 10,
                warna: "blue"
            },
            {
                id: 5,
                nama: "Kebersihan",
                icon: "fa-broom",
                produk: 6,
                warna: "teal"
            },
            {
                id: 6,
                nama: "Kesehatan",
                icon: "fa-heartbeat",
                produk: 9,
                warna: "red"
            }
        ];
        let nextId = 7;

        function renderKategori() {
            const grid = document.getElementById('kategoriGrid');
            grid.innerHTML = categories.map(cat => `
        <div class="bg-white border border-slate-200 rounded-xl p-4 hover:shadow-lg transition group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-${cat.warna}-100 rounded-xl flex items-center justify-center">
                    <i class="fas ${cat.icon} text-${cat.warna}-600 text-xl"></i>
                </div>
                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                    <button onclick="editKategori(${cat.id})" class="text-indigo-600 hover:text-indigo-800"><i class="fas fa-edit"></i></button>
                    <button onclick="hapusKategori(${cat.id})" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <h4 class="font-semibold text-slate-800">${cat.nama}</h4>
            <p class="text-xs text-slate-400">ID: KTG-00${cat.id}</p>
            <div class="mt-3 pt-3 border-t border-slate-100"><p class="text-xs"><i class="fas fa-boxes mr-1"></i> ${cat.produk} Produk</p></div>
        </div>
    `).join('');
        }

        function showTambahKategori() {
            showModal('modalTambahKategori');
        }

        function tambahKategori() {
            const nama = document.getElementById('namaKategori').value.trim();
            const icon = document.getElementById('iconKategori').value;
            if (!nama) {
                showWarning('Nama kategori harus diisi!');
                return;
            }

            categories.push({
                id: nextId++,
                nama: nama,
                icon: icon,
                produk: 0,
                warna: ['indigo', 'green', 'amber', 'blue', 'teal', 'red'][Math.floor(Math.random() * 6)]
            });
            renderKategori();
            closeModal('modalTambahKategori');
            document.getElementById('namaKategori').value = '';
            showSuccess(`Kategori "${nama}" berhasil ditambahkan!`);
        }

        function hapusKategori(id) {
            const kategori = categories.find(c => c.id === id);
            showConfirmDelete(`Apakah Anda yakin ingin menghapus kategori "${kategori.nama}"?`, () => {
                categories = categories.filter(c => c.id !== id);
                renderKategori();
                showSuccess(`Kategori "${kategori.nama}" berhasil dihapus!`);
            });
        }

        function editKategori(id) {
            showInfo('Fitur edit kategori akan segera tersedia');
        }

        renderKategori();
    </script>
@endsection
