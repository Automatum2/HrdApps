<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::all();
        return view('backoffice.posisi', compact('positions'));
    }

    public function store(Request $request)
    {
        if ($request->has('tunjangan_jabatan')) {
            $request->merge([
                'tunjangan_jabatan' => str_replace('.', '', $request->tunjangan_jabatan)
            ]);
        }

        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'level' => 'required|in:staff,supervisor,manager,director',
            'tunjangan_jabatan' => 'required|numeric|min:0',
        ]);

        Position::create($request->all());

        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $position = Position::findOrFail($id);

        if ($request->has('tunjangan_jabatan')) {
            $request->merge([
                'tunjangan_jabatan' => str_replace('.', '', $request->tunjangan_jabatan)
            ]);
        }

        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
            'level' => 'required|in:staff,supervisor,manager,director',
            'tunjangan_jabatan' => 'required|numeric|min:0',
        ]);

        $position->update($request->all());

        return redirect()->back()->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        
        if ($position->employees()->count() > 0) {
            return redirect()->back()->with('error', 'Jabatan tidak bisa dihapus karena masih ada karyawan yang menggunakannya.');
        }
        
        $position->delete();

        return redirect()->back()->with('success', 'Jabatan berhasil dihapus.');
    }
}
