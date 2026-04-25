@extends('layouts.owner')

@section('title', 'Pengguna - PROShop')
@section('header-title', 'Pengguna')
@section('header-subtitle', 'Kelola data pengguna sistem')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-end"><button onclick="showTambahModal()"
                class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2"><i
                    class="fas fa-plus"></i>Tambah Pengguna Baru</button></div>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-white">
                <h3 class="font-semibold"><i class="fas fa-users text-indigo-600 mr-2"></i>Daftar Pengguna</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs">Nama Pengguna</th>
                            <th class="px-6 py-3 text-left text-xs">Role</th>
                            <th class="px-6 py-3 text-left text-xs">Username</th>
                            <th class="px-6 py-3 text-left text-xs">Password</th>
                            <th class="px-6 py-3 text-left text-xs">Waktu Login</th>
                            <th class="px-6 py-3 text-left text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div id="modalTambah" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-white rounded-t-2xl flex justify-between">
                <h3 class="font-semibold"><i class="fas fa-user-plus text-indigo-600 mr-2"></i>Tambah Pengguna Baru</h3>
                <button onclick="closeModal('modalTambah')"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div><label class="text-sm font-medium">Nama Pengguna</label><input type="text" id="tambahNama"
                        class="w-full px-4 py-2 border rounded-lg"></div>
                <div><label class="text-sm font-medium">Role</label><select id="tambahRole"
                        class="w-full px-4 py-2 border rounded-lg">
                        <option>Kasir</option>
                        <option>Gudang</option>
                    </select></div>
                <div><label class="text-sm font-medium">Username</label><input type="text" id="tambahUsername"
                        class="w-full px-4 py-2 border rounded-lg"></div>
                <div><label class="text-sm font-medium">Password</label><input type="password" id="tambahPassword"
                        class="w-full px-4 py-2 border rounded-lg"></div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3"><button onclick="closeModal('modalTambah')"
                    class="px-4 py-2 border rounded-lg">Batal</button><button onclick="tambahPengguna()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Tambah</button></div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modalEdit" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 animate-modal">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-white rounded-t-2xl flex justify-between">
                <h3 class="font-semibold"><i class="fas fa-user-edit text-indigo-600 mr-2"></i>Edit Pengguna</h3><button
                    onclick="closeModal('modalEdit')"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" id="editId">
                <div><label class="text-sm font-medium">Nama Pengguna</label><input type="text" id="editNama"
                        class="w-full px-4 py-2 border rounded-lg"></div>
                <div><label class="text-sm font-medium">Role</label><select id="editRole"
                        class="w-full px-4 py-2 border rounded-lg">
                        <option>Kasir</option>
                        <option>Gudang</option>
                        <option>Owner</option>
                        <option>Admin</option>
                    </select></div>
                <div><label class="text-sm font-medium">Username</label><input type="text" id="editUsername"
                        class="w-full px-4 py-2 border rounded-lg"></div>
                <div><label class="text-sm font-medium">Password</label><input type="password" id="editPassword"
                        class="w-full px-4 py-2 border rounded-lg" placeholder="Kosongkan jika tidak ingin mengubah"></div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3"><button onclick="closeModal('modalEdit')"
                    class="px-4 py-2 border rounded-lg">Batal</button><button onclick="updatePengguna()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Edit</button></div>
        </div>
    </div>

    <script>
        let users = [{
            id: 1,
            nama: "Kasir 1",
            role: "Kasir",
            username: "K@gmail_1",
            password: "********",
            waktu_login: "10/04/2023 11:20"
        }, {
            id: 2,
            nama: "Gudang 1",
            role: "Gudang",
            username: "@adrian01",
            password: "********",
            waktu_login: "10/04/2023 11:20"
        }];
        let nextId = 3;

        function renderUserTable() {
            document.getElementById('userTableBody').innerHTML = users.map(u =>
                `<tr><td class="px-6 py-3">${u.nama}</td><td class="px-6 py-3"><span class="px-2 py-1 rounded-full text-xs ${u.role==='Owner'?'bg-purple-100 text-purple-700':u.role==='Kasir'?'bg-green-100 text-green-700':'bg-blue-100 text-blue-700'}">${u.role}</span></td><td class="px-6 py-3">${u.username}</td><td class="px-6 py-3 font-mono">********</td><td class="px-6 py-3">${u.waktu_login}</td><td class="px-6 py-3"><button onclick="showEditModal(${u.id})" class="text-indigo-600 mr-3"><i class="fas fa-edit"></i> Edit</button><button onclick="hapusPengguna(${u.id})" class="text-red-600"><i class="fas fa-trash"></i> Hapus</button></td></tr>`
                ).join('');
        }

        function showTambahModal() {
            showModal('modalTambah');
        }

        function tambahPengguna() {
            const nama = document.getElementById('tambahNama').value.trim(),
                role = document.getElementById('tambahRole').value,
                username = document.getElementById('tambahUsername').value.trim(),
                password = document.getElementById('tambahPassword').value;
            if (!nama || !username || !password) {
                showWarning('Semua field harus diisi!');
                return;
            }
            if (users.find(u => u.username === username)) {
                showWarning('Username sudah digunakan!');
                return;
            }
            users.push({
                id: nextId++,
                nama,
                role,
                username,
                password: '********',
                waktu_login: '-'
            });
            renderUserTable();
            closeModal('modalTambah');
            showSuccess(`Pengguna "${nama}" berhasil ditambahkan!`);
            document.getElementById('tambahNama').value = '';
            document.getElementById('tambahUsername').value = '';
            document.getElementById('tambahPassword').value = '';
        }

        function showEditModal(id) {
            const u = users.find(x => x.id === id);
            if (u) {
                document.getElementById('editId').value = u.id;
                document.getElementById('editNama').value = u.nama;
                document.getElementById('editRole').value = u.role;
                document.getElementById('editUsername').value = u.username;
                document.getElementById('editPassword').value = '';
                showModal('modalEdit');
            }
        }

        function updatePengguna() {
            const id = parseInt(document.getElementById('editId').value),
                nama = document.getElementById('editNama').value.trim(),
                role = document.getElementById('editRole').value,
                username = document.getElementById('editUsername').value.trim(),
                password = document.getElementById('editPassword').value;
            if (!nama || !username) {
                showWarning('Nama dan Username harus diisi!');
                return;
            }
            const idx = users.findIndex(u => u.id === id);
            if (idx !== -1) {
                users[idx].nama = nama;
                users[idx].role = role;
                users[idx].username = username;
                if (password) users[idx].password = '********';
            }
            renderUserTable();
            closeModal('modalEdit');
            showSuccess('Data pengguna berhasil diupdate!');
        }

        function hapusPengguna(id) {
            const user = users.find(u => u.id === id);
            showConfirmDelete(`Yakin hapus pengguna "${user.nama}"?`, () => {
                users = users.filter(u => u.id !== id);
                renderUserTable();
                showSuccess(`Pengguna "${user.nama}" dihapus`);
            });
        }
        renderUserTable();
    </script>
@endsection
