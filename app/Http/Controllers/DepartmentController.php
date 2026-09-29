<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('employees')->with('manager')->orderBy('nama_department', 'asc')->get();
        return view('backoffice.departemen', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_department' => 'required|string|max:255|unique:departments,nama_department',
            'kode_department' => 'required|string|max:50|unique:departments,kode_department',
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'nama_department.required' => 'Nama departemen wajib diisi.',
            'nama_department.unique' => 'Nama departemen sudah terdaftar.',
            'kode_department.required' => 'Kode departemen wajib diisi.',
            'kode_department.unique' => 'Kode departemen sudah digunakan.',
        ]);

        Department::create([
            'nama_department' => trim($request->nama_department),
            'kode_department' => strtoupper(trim($request->kode_department)),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Departemen baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $dept = Department::findOrFail($id);

        $request->validate([
            'nama_department' => [
                'required', 'string', 'max:255',
                Rule::unique('departments', 'nama_department')->ignore($dept->id),
            ],
            'kode_department' => [
                'required', 'string', 'max:50',
                Rule::unique('departments', 'kode_department')->ignore($dept->id),
            ],
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'nama_department.required' => 'Nama departemen wajib diisi.',
            'nama_department.unique' => 'Nama departemen sudah terdaftar.',
            'kode_department.required' => 'Kode departemen wajib diisi.',
            'kode_department.unique' => 'Kode departemen sudah digunakan.',
        ]);

        $dept->update([
            'nama_department' => trim($request->nama_department),
            'kode_department' => strtoupper(trim($request->kode_department)),
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->back()->with('success', 'Data departemen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dept = Department::findOrFail($id);

        if ($dept->employees()->count() > 0) {
            return redirect()->back()->with('error', 'Departemen "' . $dept->nama_department . '" tidak dapat dihapus karena masih memiliki ' . $dept->employees()->count() . ' karyawan.');
        }

        $dept->delete();

        return redirect()->back()->with('success', 'Departemen berhasil dihapus.');
    }
}
