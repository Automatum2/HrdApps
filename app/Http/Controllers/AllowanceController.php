<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Allowance;
use App\Models\Employee;

class AllowanceController extends Controller
{
    public function store(Request $request, $employeeId)
    {
        $request->validate([
            'nama_tunjangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tipe' => 'required|in:tetap,tidak_tetap',
        ]);

        $employee = Employee::findOrFail($employeeId);

        if (auth()->check() && !auth()->user()->can('manage-allowance', $employee)) {
            abort(403, 'Anda tidak memiliki hak akses untuk menambah tunjangan pada karyawan ini.');
        }

        Allowance::create([
            'employee_id' => $employee->id,
            'nama_tunjangan' => $request->nama_tunjangan,
            'jumlah' => $request->jumlah,
            'tipe' => $request->tipe,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Tunjangan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $allowance = Allowance::with('employee')->findOrFail($id);
        
        if (auth()->check() && !auth()->user()->can('manage-allowance', $allowance->employee)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah tunjangan pada karyawan ini.');
        }
        
        $request->validate([
            'nama_tunjangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tipe' => 'required|in:tetap,tidak_tetap',
            'is_active' => 'boolean'
        ]);

        $allowance->update([
            'nama_tunjangan' => $request->nama_tunjangan,
            'jumlah' => $request->jumlah,
            'tipe' => $request->tipe,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Tunjangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $allowance = Allowance::with('employee')->findOrFail($id);
        
        if (auth()->check() && !auth()->user()->can('manage-allowance', $allowance->employee)) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus tunjangan pada karyawan ini.');
        }
        
        $allowance->delete();

        return redirect()->back()->with('success', 'Tunjangan berhasil dihapus.');
    }
}
