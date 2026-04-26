@extends('layouts.owner')

@section('title', 'Pengguna - PROShop')
@section('header-title', 'Pengguna')
@section('header-subtitle', 'Kelola data pengguna sistem')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-end">
            <button onclick="showTambahModal()"
                class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i>Tambah Pengguna Baru
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-slate-100">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-white">
                <h3 class="font-semibold"><i class="fas fa-users text-indigo-600 mr-2"></i>Daftar Pengguna</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pengguna</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Password</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu Login</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        @foreach ($users as $u)
                            <tr id="user-row-{{ $u->user_id }}">
                                <td class="px-6 py-3">{{ $u->name }}</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                        {{ $u->role->nama_role }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">{{ $u->username }}</td>
                                <td class="px-6 py-3">********</td>
                                <td class="px-6 py-3">{{ $u->created_at }}</td>
                                <td class="px-6 py-3">
                                    <button onclick="showEditModal({{ $u->user_id }})"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="hapusPengguna({{ $u->user_id }})"
                                        class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div id="modalTambah" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
            <div
                class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-white rounded-t-2xl flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-user-plus text-indigo-600 mr-2"></i>Tambah Pengguna Baru</h3>
                <button onclick="closeModal('modalTambah')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formTambah" action="{{ route('owner.pengguna.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm font-medium block mb-2">Nama</label>
                        <input type="text" name="name" id="tambahNama" required
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="text-sm font-medium block mb-2">Role</label>
                        <select name="role_id" id="tambahRole" required
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($roles as $role)
                                <option value="{{ $role->role_id }}">{{ $role->nama_role }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium block mb-2">Username</label>
                        <input type="text" name="username" id="tambahUsername" required
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="text-sm font-medium block mb-2">Password</label>
                        <input type="password" name="password" id="tambahPassword" required
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button" onclick="closeModal('modalTambah')"
                        class="border px-4 py-2 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modalEdit" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
            <div
                class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-white rounded-t-2xl flex justify-between items-center">
                <h3 class="font-semibold"><i class="fas fa-user-edit text-indigo-600 mr-2"></i>Edit Pengguna</h3>
                <button onclick="closeModal('modalEdit')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <input type="hidden" id="editId" name="id">
                    <div>
                        <label class="text-sm font-medium block mb-2">Nama Lengkap</label>
                        <input type="text" id="editNama" name="name" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium block mb-2">Role</label>
                        <select id="editRole" name="role_id" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($roles as $role)
                                <option value="{{ $role->role_id }}">{{ $role->nama_role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium block mb-2">Username</label>
                        <input type="text" id="editUsername" name="username" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium block mb-2">Password</label>
                        <input type="password" id="editPassword" name="password"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Kosongkan jika tidak ingin mengubah">
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah password</p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button" onclick="closeModal('modalEdit')"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div id="modalHapus" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-lg mb-2">Konfirmasi Hapus</h3>
                <p class="text-gray-600" id="hapusMessage">Apakah Anda yakin ingin menghapus pengguna ini?</p>
            </div>
            <div class="px-6 py-4 border-t flex justify-center gap-3">
                <button type="button" onclick="closeModal('modalHapus')"
                    class="px-5 py-2 border rounded-lg hover:bg-gray-50">
                    <i class="fas fa-times mr-1"></i> Batal
                </button>
                <button type="button" id="confirmHapusBtn"
                    class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="toast" class="fixed top-5 right-5 z-50 hidden transition-all duration-300">
        <div class="bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4" id="toastContent">
            <div class="flex items-center gap-3">
                <div id="toastIcon"></div>
                <div class="flex-1">
                    <p id="toastMessage" class="text-sm font-medium"></p>
                </div>
                <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Toast Notification Functions
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastContent = document.getElementById('toastContent');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');

            // Set border color and icon based on type
            if (type === 'success') {
                toastContent.className = 'bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4 border-green-500';
                toastIcon.innerHTML = '<i class="fas fa-check-circle text-green-500 text-xl"></i>';
            } else if (type === 'error') {
                toastContent.className = 'bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4 border-red-500';
                toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-red-500 text-xl"></i>';
            } else if (type === 'warning') {
                toastContent.className = 'bg-white rounded-lg shadow-lg p-4 min-w-[300px] border-l-4 border-yellow-500';
                toastIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>';
            }

            toastMessage.textContent = message;
            toast.classList.remove('hidden');

            // Auto hide after 3 seconds
            setTimeout(() => {
                hideToast();
            }, 3000);
        }

        function hideToast() {
            document.getElementById('toast').classList.add('hidden');
        }

        // Modal Functions
        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function showTambahModal() {
            document.getElementById('formTambah').reset();
            showModal('modalTambah');
        }

        // Handle Tambah Form Submission
        document.getElementById('formTambah').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Pengguna berhasil ditambahkan!', 'success');
                        closeModal('modalTambah');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast(data.message || 'Gagal menambahkan pengguna', 'error');
                    }
                })
                .catch(error => {
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        });

        // Show Edit Modal with User Data
        function showEditModal(id) {
            fetch(`/owner/pengguna/${id}/edit`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const user = data.data;

                        // karena PK kamu user_id
                        document.getElementById('editId').value = user.user_id;
                        document.getElementById('editNama').value = user.name;
                        document.getElementById('editRole').value = user.role_id;
                        document.getElementById('editUsername').value = user.username;
                        document.getElementById('editPassword').value = '';

                        // 🔥 FIX UTAMA DI SINI
                        document.getElementById('formEdit').action = `/owner/pengguna/${user.user_id}`;

                        showModal('modalEdit');
                    } else {
                        showToast('Gagal mengambil data pengguna', 'error');
                    }
                })
                .catch(error => {
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        }

        // Handle Edit Form Submission
        document.getElementById('formEdit').addEventListener('submit', function(e) {
            e.preventDefault();

            const id = document.getElementById('editId').value;
            const formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Pengguna berhasil diupdate!', 'success');
                        closeModal('modalEdit');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast(data.message || 'Gagal mengupdate pengguna', 'error');
                    }
                })
                .catch(error => {
                    showToast('Terjadi kesalahan pada server', 'error');
                });
        });

        // Delete User Function
        let deleteUserId = null;

        function hapusPengguna(id) {
            deleteUserId = id;
            document.getElementById('hapusMessage').textContent = 'Apakah Anda yakin ingin menghapus pengguna ini?';
            showModal('modalHapus');
        }

        // Handle Delete Confirmation
        document.getElementById('confirmHapusBtn').addEventListener('click', function() {
            if (!deleteUserId) return;

            fetch(`/owner/pengguna/${deleteUserId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Pengguna berhasil dihapus!', 'success');
                        // Remove row from table
                        document.getElementById(`user-row-${deleteUserId}`).remove();
                    } else {
                        showToast(data.message || 'Gagal menghapus pengguna', 'error');
                    }
                    closeModal('modalHapus');
                    deleteUserId = null;
                })
                .catch(error => {
                    showToast('Terjadi kesalahan pada server', 'error');
                    closeModal('modalHapus');
                    deleteUserId = null;
                });
        });

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('fixed')) {
                event.target.classList.add('hidden');
            }
        }
    </script>
@endsection
