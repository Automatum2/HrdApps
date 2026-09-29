@extends('layouts.admin')

@section('title', 'Master Departemen - HRDApps')
@section('page_title', 'Master Data Departemen')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative flex items-center gap-2">
        <span class="material-symbols-outlined text-green-500">check_circle</span>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative flex items-center gap-2">
        <span class="material-symbols-outlined text-red-500">error</span>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative">
        <div class="flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-red-500">error</span>
            <span class="text-sm font-bold">Terjadi Kesalahan:</span>
        </div>
        <ul class="list-disc list-inside text-sm pl-6">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Departemen</span>
        </nav>
        <p class="text-on-surface-variant text-sm max-w-2xl">Kelola struktur departemen, kode divisi, dan penanggung jawab manager.</p>
    </div>
    <button class="bg-primary hover:bg-primary-container text-white font-semibold text-sm px-4 py-2.5 rounded-lg flex items-center gap-2 transition-all shadow-sm active:scale-95 whitespace-nowrap cursor-pointer" onclick="openModal('modal-tambah-departemen')">
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span>Tambah Departemen</span>
    </button>
</div>

<!-- Data Table Card -->
<div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
    <!-- Table Toolbar -->
    <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-slate-50/50">
        <div class="flex items-center gap-3">
            <div class="font-bold text-on-surface text-base">Daftar Departemen</div>
            <span class="bg-primary/10 text-primary font-semibold px-3 py-0.5 rounded-full text-xs" id="total-count-badge">{{ $departments->count() }} Total</span>
        </div>
        <div class="relative focus-within:ring-2 focus-within:ring-primary/20 rounded-lg">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input class="pl-10 pr-4 py-2 bg-white border border-slate-300 rounded-lg text-xs outline-none focus:border-primary w-48 sm:w-64 transition-all text-slate-800 placeholder:text-slate-400" placeholder="Cari departemen atau kode..." type="text" id="search-dept">
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-outline-variant text-slate-500 uppercase tracking-wider text-xs font-bold">
                    <th class="py-4 px-6 w-16">No</th>
                    <th class="py-4 px-6">Kode</th>
                    <th class="py-4 px-6">Nama Departemen</th>
                    <th class="py-4 px-6">Manager Departemen</th>
                    <th class="py-4 px-6">Jumlah Karyawan</th>
                    <th class="py-4 px-6">Deskripsi</th>
                    <th class="py-4 px-6 w-32 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm font-medium text-slate-700 divide-y divide-slate-100" id="dept-table-body">
                @forelse($departments as $index => $dept)
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="py-4 px-6 text-on-surface-variant font-mono">{{ $index + 1 }}</td>
                    <td class="py-4 px-6">
                        <span class="px-2.5 py-1 bg-primary/10 text-primary rounded-md font-mono text-xs font-bold">
                            {{ $dept->kode_department ?? '-' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 font-bold text-slate-800">{{ $dept->nama_department }}</td>
                    <td class="py-4 px-6 text-slate-600">
                        @if($dept->manager)
                            <div class="flex items-center gap-1.5 font-semibold text-slate-800">
                                <span class="material-symbols-outlined text-primary text-base">manage_accounts</span>
                                <span>{{ $dept->manager->nama_lengkap }}</span>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">Belum Ditentukan</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center gap-1 font-semibold px-2.5 py-1 rounded-full text-xs {{ $dept->employees_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            <span class="material-symbols-outlined text-[14px]">groups</span>
                            <span>{{ $dept->employees_count }} Karyawan</span>
                        </span>
                    </td>
                    <td class="py-4 px-6 text-slate-500 text-xs max-w-xs truncate" title="{{ $dept->deskripsi }}">
                        {{ $dept->deskripsi ?: '-' }}
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openEditModal({{ $dept->id }}, '{{ addslashes($dept->nama_department) }}', '{{ addslashes($dept->kode_department ?? '') }}', '{{ addslashes($dept->deskripsi ?? '') }}')" class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors cursor-pointer" title="Edit Departemen">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </button>
                            <form action="{{ route('backoffice.departemen.destroy', $dept->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus departemen \'{{ addslashes($dept->nama_department) }}\'?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus Departemen">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-500 italic">
                        Belum ada data departemen.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Tambah Departemen -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-tambah-departemen" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[90vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 480px; min-width: 280px; display: flex; flex-direction: column;">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-base">Tambah Departemen Baru</h3>
            <button type="button" onclick="closeModal('modal-tambah-departemen')" class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('backoffice.departemen.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Kode Departemen <span class="text-red-500">*</span></label>
                <input type="text" name="kode_department" required placeholder="Contoh: IT, HRD, FIN, MKT" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm uppercase font-mono outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nama Departemen <span class="text-red-500">*</span></label>
                <input type="text" name="nama_department" required placeholder="Contoh: Information Technology" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" rows="3" placeholder="Keterangan singkat fungsi departemen..." class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-tambah-departemen')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-all cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-container text-white rounded-lg text-sm font-semibold shadow transition-all cursor-pointer">Simpan Departemen</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Departemen -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-edit-departemen" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[90vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 480px; min-width: 280px; display: flex; flex-direction: column;">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-base">Edit Departemen</h3>
            <button type="button" onclick="closeModal('modal-edit-departemen')" class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="form-edit-departemen" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Kode Departemen <span class="text-red-500">*</span></label>
                <input type="text" id="edit_kode_department" name="kode_department" required class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm uppercase font-mono outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nama Departemen <span class="text-red-500">*</span></label>
                <input type="text" id="edit_nama_department" name="nama_department" required class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Deskripsi (Opsional)</label>
                <textarea id="edit_deskripsi" name="deskripsi" rows="3" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeModal('modal-edit-departemen')" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 text-sm font-semibold hover:bg-slate-100 transition-all cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-container text-white rounded-lg text-sm font-semibold shadow transition-all cursor-pointer">Perbarui Departemen</button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function openEditModal(id, nama, kode, deskripsi) {
        document.getElementById('edit_nama_department').value = nama;
        document.getElementById('edit_kode_department').value = kode;
        document.getElementById('edit_deskripsi').value = deskripsi;
        
        const form = document.getElementById('form-edit-departemen');
        form.action = `/backoffice/departemen/${id}`;
        
        openModal('modal-edit-departemen');
    }

    // Live search filter
    document.getElementById('search-dept')?.addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#dept-table-body tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
</script>
@endpush
