@extends('layouts.admin')

@section('title', 'Master Jabatan - HRDApps')
@section('page_title', 'Master Data Jabatan')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Dashboard</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Jabatan</span>
        </nav>
        <p class="text-on-surface-variant text-sm">Kelola daftar jabatan dan standar tunjangan jabatan.</p>
    </div>
    <button onclick="alert('Fitur Tambah Jabatan dinonaktifkan sementara untuk perbaikan bug tampilan layar.')" class="bg-primary/50 text-white font-semibold text-sm px-4 py-2 rounded-lg flex items-center gap-2 transition-all shadow-sm cursor-not-allowed" title="Fitur dinonaktifkan sementara">
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span>Tambah Jabatan</span>
    </button>
</div>

@if($errors->any())
<div class="bg-red-50 border-l-4 border-error text-error p-4 mb-6 rounded shadow-sm">
    <div class="flex items-center gap-2 font-bold mb-2">
        <span class="material-symbols-outlined">error</span>
        <span>Terjadi Kesalahan:</span>
    </div>
    <ul class="list-disc ml-5 text-sm">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session('success'))
<div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
    <div class="flex items-center gap-2 font-bold">
        <span class="material-symbols-outlined">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
</div>
@endif

<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant text-sm font-semibold border-b border-outline-variant">
                    <th class="px-6 py-4 w-16 text-center">No</th>
                    <th class="px-6 py-4">Nama Jabatan</th>
                    <th class="px-6 py-4">Level</th>
                    <th class="px-6 py-4 text-right">Tunjangan Jabatan (Rp)</th>
                    <th class="px-6 py-4 w-32 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                @forelse($positions as $index => $posisi)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-center text-sm">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-800">{{ $posisi->nama_jabatan }}</td>
                    <td class="px-6 py-4 text-sm">{{ $posisi->level }}</td>
                    <td class="px-6 py-4 text-sm font-mono text-right text-primary font-medium">{{ number_format($posisi->tunjangan_jabatan, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="alert('Fitur Edit Jabatan dinonaktifkan sementara untuk perbaikan bug tampilan layar.')" class="p-2 text-amber-500/50 cursor-not-allowed rounded-lg transition-colors" title="Fitur dinonaktifkan sementara">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <form action="{{ route('backoffice.posisi.destroy', $posisi->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500 italic">Belum ada data jabatan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Jabatan -->
<div id="modal-tambah-posisi" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden relative">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800">Tambah Jabatan</h3>
            <button type="button" onclick="closeModalPortal('modal-tambah-posisi')" class="text-slate-400 hover:text-slate-600 transition-colors material-symbols-outlined cursor-pointer">close</button>
        </div>
        <form action="{{ route('backoffice.posisi.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Jabatan</label>
                    <input type="text" name="nama_jabatan" required class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Level</label>
                    <select name="level" required class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                        <option value="">-- Pilih Level --</option>
                        <option value="staff">Staff</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="manager">Manager</option>
                        <option value="director">Director</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tunjangan Jabatan (Rp)</label>
                    <input type="text" name="tunjangan_jabatan" required placeholder="Contoh: 1500000 atau 1.500.000" class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModalPortal('modal-tambah-posisi')" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-container rounded-lg transition-colors shadow-sm cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Jabatan -->
<div id="modal-edit-posisi" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden relative">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800">Edit Jabatan</h3>
            <button type="button" onclick="closeModalPortal('modal-edit-posisi')" class="text-slate-400 hover:text-slate-600 transition-colors material-symbols-outlined cursor-pointer">close</button>
        </div>
        <form id="form-edit-posisi" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Jabatan</label>
                    <input type="text" id="edit_nama_jabatan" name="nama_jabatan" required class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Level</label>
                    <select id="edit_level" name="level" required class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                        <option value="">-- Pilih Level --</option>
                        <option value="staff">Staff</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="manager">Manager</option>
                        <option value="director">Director</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tunjangan Jabatan (Rp)</label>
                    <input type="text" id="edit_tunjangan_jabatan" name="tunjangan_jabatan" required placeholder="Contoh: 1500000 atau 1.500.000" class="w-full rounded-lg border-slate-300 focus:border-primary focus:ring focus:ring-primary/20 text-sm">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModalPortal('modal-edit-posisi')" class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-container rounded-lg transition-colors shadow-sm cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, nama, level, tunjangan) {
        document.getElementById('form-edit-posisi').action = `/backoffice/posisi/${id}`;
        document.getElementById('edit_nama_jabatan').value = nama;
        document.getElementById('edit_level').value = level;
        document.getElementById('edit_tunjangan_jabatan').value = tunjangan;
        openModalPortal('modal-edit-posisi');
    }
</script>
@endsection
