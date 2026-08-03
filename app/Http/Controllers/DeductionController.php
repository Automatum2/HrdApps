<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deduction;
use App\Models\Employee;

class DeductionController extends Controller
{
    public function store(Request $request, $employeeId)
    {
        $request->validate([
            'nama_potongan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tipe' => 'required|in:tetap,tidak_tetap',
        ]);

        $employee = Employee::findOrFail($employeeId);

        Deduction::create([
            'employee_id' => $employee->id,
            'nama_potongan' => $request->nama_potongan,
            'jumlah' => $request->jumlah,
            'tipe' => $request->tipe,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Potongan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $deduction = Deduction::findOrFail($id);
        
        $request->validate([
            'nama_potongan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tipe' => 'required|in:tetap,tidak_tetap',
            'is_active' => 'boolean'
        ]);

        $deduction->update([
            'nama_potongan' => $request->nama_potongan,
            'jumlah' => $request->jumlah,
            'tipe' => $request->tipe,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Potongan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $deduction = Deduction::findOrFail($id);
        $deduction->delete();

        return redirect()->back()->with('success', 'Potongan berhasil dihapus.');
    }
}
