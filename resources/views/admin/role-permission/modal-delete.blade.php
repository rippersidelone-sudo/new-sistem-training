{{-- resources/views/admin/role-permission/modal-delete.blade.php --}}

<div x-show="openDeleteUser" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div @click.outside="openDeleteUser = false" class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8">

        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="text-red-600">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 9v4" />
                    <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                    <path d="M12 16h.01" />
                </svg>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-3 text-center">Hapus User?</h2>
        <p class="text-gray-600 mb-6 text-center">
            Yakin ingin menghapus <span class="font-semibold text-gray-900" x-text="deleteUserName"></span>
            sebagai <span class="font-semibold text-gray-900" x-text="deleteUserRole"></span>?
        </p>

        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-6">
            <p class="text-sm text-red-700 flex items-start gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" class="flex-shrink-0 mt-0.5">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                    <path d="M12 9v4" />
                    <path d="M12 16v.01" />
                </svg>
                <span>Data user yang dihapus tidak dapat dikembalikan. Pastikan user tidak memiliki data terkait.</span>
            </p>
        </div>

        <div class="flex gap-3">
            <button @click="openDeleteUser = false"
                    class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                Batal
            </button>

            <form id="deleteUserForm" method="POST" action="" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium shadow-lg shadow-red-600/30">
                    <span class="flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 7l16 0" />
                            <path d="M10 11l0 6" />
                            <path d="M14 11l0 6" />
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        </svg>
                        Hapus User
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-delete-id]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const userId = this.dataset.deleteId;
                document.getElementById('deleteUserForm').action = '/admin/users/' + userId;
            });
        });
    });
</script>