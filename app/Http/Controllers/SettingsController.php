<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\EmployeeDocument;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('backoffice.pengaturan', compact('user'));
    }

    public function updateCv(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->employee) {
            $user->employee->cv_text = $request->input('cv_text');
            $user->employee->save();
            return redirect()->back()->with('success', 'Data Pribadi & Riwayat Hidup berhasil disimpan.');
        }
        return redirect()->back()->with('error', 'Gagal menyimpan data. Akun tidak memiliki profil karyawan.');
    }

    public function updateBank(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->employee) {
            // Cek apakah info bank sudah ada
            if (!empty($user->employee->nama_bank) || !empty($user->employee->no_rekening)) {
                return redirect()->back()->with('error', 'Informasi bank sudah terisi. Jika ingin mengubahnya, silakan hubungi HRD.');
            }

            $user->employee->nama_bank = $request->input('nama_bank');
            $user->employee->no_rekening = $request->input('no_rekening');
            $user->employee->save();
            return redirect()->back()->with('success', 'Informasi Perbankan berhasil disimpan.');
        }
        return redirect()->back()->with('error', 'Gagal menyimpan data. Akun tidak memiliki profil karyawan.');
    }

    public function updateNotification(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $preferences = [
                'laporan_absensi' => $request->has('notif_laporan_absensi'),
                'peringatan_penggajian' => $request->has('notif_peringatan_penggajian'),
                'permohonan_cuti' => $request->has('notif_permohonan_cuti'),
            ];
            $user->notification_preferences = json_encode($preferences);
            $user->save();
            return redirect()->back()->with('success', 'Preferensi pemberitahuan berhasil diperbarui.');
        }
        return redirect()->back()->with('error', 'Gagal menyimpan preferensi notifikasi.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Kata sandi saat ini tidak sesuai.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function uploadDocument(Request $request)
    {
        Log::info('Upload document route hit. Files:', $_FILES);
        Log::info('Request All:', $request->all());
        
        $request->validate([
            'document' => 'required|array',
            'document.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'document.required' => 'Pilih setidaknya satu file dokumen terlebih dahulu.',
            'document.*.required' => 'Ada file dokumen yang kosong atau gagal diunggah.',
            'document.*.file' => 'File yang diunggah tidak valid.',
            'document.*.mimes' => 'Format file tidak didukung. Harap gunakan format PDF, JPG, atau PNG.',
            'document.*.max' => 'Ukuran salah satu file melebihi 5 MB.',
        ]);

        $user = Auth::user();
        if (!$user || !$user->employee) {
            return redirect()->back()->with('error', 'Akses ditolak, akun Anda tidak tertaut dengan data Karyawan.');
        }

        if ($request->hasFile('document')) {
            $files = $request->file('document');
            $count = 0;
            
            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $sizeKb = round($file->getSize() / 1024);
                $extension = $file->getClientOriginalExtension();
                
                $path = $file->store('documents', 'public');
                
                EmployeeDocument::create([
                    'employee_id' => $user->employee->id,
                    'file_name' => $originalName,
                    'file_path' => $path,
                    'file_size' => $sizeKb,
                    'file_type' => strtolower($extension),
                ]);
                $count++;
            }
            
            return redirect()->back()->with('success', "$count Dokumen berhasil diunggah.");
        }

        return redirect()->back()->with('error', 'Gagal mengunggah dokumen.');
    }

    public function deleteDocument($id)
    {
        $user = Auth::user();
        if (!$user || !$user->employee) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $document = EmployeeDocument::where('id', $id)
            ->where('employee_id', $user->employee->id)
            ->first();

        if ($document) {
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->delete();
            return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'photo.required' => 'Pilih foto profil terlebih dahulu sebelum mengunggah.',
            'photo.image' => 'File yang diunggah harus berupa gambar.',
            'photo.mimes' => 'Format foto tidak didukung. Harap gunakan format JPG atau PNG.',
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 5 MB.',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            
            $user = Auth::user();
            if ($user && $user->employee) {
                $user->employee->foto = $path;
                $user->employee->save();
            }
            
            session(['user_photo' => asset('storage/' . $path)]);
            return redirect()->back()->with('success', 'Foto profil berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah foto profil.');
    }

    public function deleteDocumentSession($index)
    {
        $documents = session('user_documents', []);
        if (isset($documents[$index])) {
            unset($documents[$index]);
            session(['user_documents' => array_values($documents)]);
            return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
    }
}
