@extends('layouts.owner')

@section('title', 'Satuan Barang - PROShop')
@section('header-title', 'Satuan Barang')
@section('header-subtitle', 'Kelola semua produk satuan')

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-4">
            <div class="flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-2" id="filterButtons">
                    <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white"
                        data-filter="all">Semua</button>
                    <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100"
                        data-filter="Makanan">Makanan</button>
                    <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100"
                        data-filter="Minuman">Minuman</button>
                    <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100"
                        data-filter="Kebersihan">Kebersihan</button>
                    <button class="filter-btn px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100"
                        data-filter="Kesehatan">Kesehatan</button>
                </div>
                <div class="relative"><input type="text" id="searchInput" placeholder="Cari produk..."
                        class="pl-10 pr-4 py-2 border rounded-lg w-64"><i
                        class="fas fa-search absolute left-3 top-3 text-slate-400 text-sm"></i></div>
            </div>
        </div>

        <div class="flex justify-end"><button onclick="showTambahProduk()"
                class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2"><i
                    class="fas fa-plus"></i> Tambah Produk</button></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" id="productGrid"></div>
    </div>

    {{-- Modal Tambah Produk --}}
    <div id="modalTambahProduk" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div
                class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white rounded-t-2xl flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-plus-circle text-indigo-600 mr-2"></i>Tambah Produk</h3>
                <button onclick="closeModal('modalTambahProduk')" class="text-slate-400 hover:text-slate-600"><i
                        class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div><label class="block text-sm font-medium mb-1">Nama Produk</label><input type="text" id="produkNama"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></div>
                <div><label class="block text-sm font-medium mb-1">Kategori</label><select id="produkKategori"
                        class="w-full px-4 py-2 border rounded-lg">
                        <option>Makanan</option>
                        <option>Minuman</option>
                        <option>Kebersihan</option>
                        <option>Kesehatan</option>
                    </select></div>
                <div><label class="block text-sm font-medium mb-1">Harga</label><input type="number" id="produkHarga"
                        class="w-full px-4 py-2 border rounded-lg"></div>
                <div><label class="block text-sm font-medium mb-1">Stok</label><input type="number" id="produkStok"
                        class="w-full px-4 py-2 border rounded-lg"></div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                <button onclick="closeModal('modalTambahProduk')" class="px-4 py-2 border rounded-lg">Batal</button>
                <button onclick="tambahProduk()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan</button>
            </div>
        </div>
    </div>

    <script>
        let products = [{
                name: "Sunlight 690ml",
                category: "Kebersihan",
                price: 15000,
                stock: 40,
                icon: "fa-pump-soap",
                bg: "yellow"
            },
            {
                name: "Tolak Angin",
                category: "Kesehatan",
                price: 4700,
                stock: 20,
                icon: "fa-capsules",
                bg: "blue"
            },
            {
                name: "Rinso Bubuk",
                category: "Kebersihan",
                price: 12500,
                stock: 5,
                icon: "fa-box",
                bg: "green"
            },
            {
                name: "Kayu Putih",
                category: "Kesehatan",
                price: 8900,
                stock: 7,
                icon: "fa-leaf",
                bg: "emerald"
            },
            {
                name: "Japota Honey",
                category: "Makanan",
                price: 10900,
                stock: 70,
                icon: "fa-jar",
                bg: "amber"
            },
            {
                name: "Pocky Coklat",
                category: "Makanan",
                price: 7900,
                stock: 75,
                icon: "fa-cookie-bite",
                bg: "amber"
            }
        ];

        function renderProducts() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const activeFilter = document.querySelector('.filter-btn.bg-indigo-600.text-white')?.dataset.filter || 'all';
            const filtered = products.filter(p => (activeFilter === 'all' || p.category === activeFilter) && p.name
                .toLowerCase().includes(search));
            const grid = document.getElementById('productGrid');
            if (filtered.length === 0) {
                grid.innerHTML =
                    '<div class="col-span-full text-center py-12"><i class="fas fa-box-open text-5xl text-slate-300 mb-3"></i><p>Tidak ada produk</p></div>';
                return;
            }
            grid.innerHTML = filtered.map(p =>
                `<div class="bg-white rounded-2xl shadow-md overflow-hidden border hover:shadow-xl transition group"><div class="bg-${p.bg}-100 h-32 flex items-center justify-center relative"><i class="fas ${p.icon} text-5xl text-${p.bg}-600"></i><div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100"><button onclick="editProduk('${p.name}')" class="bg-black/20 p-1.5 rounded-full"><i class="fas fa-edit text-white text-xs"></i></button></div></div><div class="p-4"><h4 class="font-semibold text-lg">${p.name}</h4><p class="text-xs text-slate-400 mb-2">${p.category}</p><div class="flex justify-between mb-1"><span class="text-sm">Harga</span><span class="font-bold text-green-600">Rp ${p.price.toLocaleString()}</span></div><div class="flex justify-between"><span class="text-sm">Stok</span><span class="font-semibold ${p.stock<10?'text-red-600':p.stock<20?'text-orange-600':'text-slate-700'}">${p.stock}</span></div><div class="mt-3 pt-3 border-t flex gap-2"><button onclick="editProduk('${p.name}')" class="flex-1 bg-indigo-50 text-indigo-600 py-1 rounded-lg text-sm">Edit</button><button onclick="hapusProduk('${p.name}')" class="flex-1 bg-red-50 text-red-500 py-1 rounded-lg text-sm">Hapus</button></div></div></div>`
                ).join('');
        }

        function showTambahProduk() {
            showModal('modalTambahProduk');
        }

        function tambahProduk() {
            const nama = document.getElementById('produkNama').value.trim(),
                kategori = document.getElementById('produkKategori').value,
                harga = parseInt(document.getElementById('produkHarga').value),
                stok = parseInt(document.getElementById('produkStok').value);
            if (!nama || !harga || !stok) {
                showWarning('Semua field harus diisi!');
                return;
            }
            products.push({
                name: nama,
                category: kategori,
                price: harga,
                stock: stok,
                icon: "fa-box",
                bg: "gray"
            });
            renderProducts();
            closeModal('modalTambahProduk');
            showSuccess(`Produk "${nama}" berhasil ditambahkan!`);
            document.getElementById('produkNama').value = '';
            document.getElementById('produkHarga').value = '';
            document.getElementById('produkStok').value = '';
        }

        function hapusProduk(nama) {
            showConfirmDelete(`Yakin hapus "${nama}"?`, () => {
                products = products.filter(p => p.name !== nama);
                renderProducts();
                showSuccess(`"${nama}" dihapus`);
            });
        }

        function editProduk(nama) {
            showInfo(`Edit produk "${nama}"`);
        }

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.onclick = () => {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('bg-indigo-600', 'text-white');
                    b.classList.add('text-slate-600');
                });
                btn.classList.add('bg-indigo-600', 'text-white');
                renderProducts();
            };
        });
        document.getElementById('searchInput').addEventListener('input', renderProducts);
        renderProducts();
    </script>
@endsection
