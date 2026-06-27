<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $units = UnitKerja::withCount(['users', 'pengajuans'])->latest()->paginate(10);
        return view('kasium.settings.units.index', compact('units'));
    }

    public function create()
    {
        return view('kasium.settings.units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_unit' => 'required|string|max:100',
            'kode_unit' => 'required|string|max:20|unique:unit_kerjas,kode_unit',
        ], [
            'nama_unit.required' => 'Nama unit wajib diisi.',
            'kode_unit.required' => 'Kode unit wajib diisi.',
            'kode_unit.unique' => 'Kode unit sudah digunakan.',
        ]);

        UnitKerja::create($validated);

        return redirect()->route('kasium.settings.units.index')
            ->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $unit = UnitKerja::findOrFail($id);
        return view('kasium.settings.units.edit', compact('unit'));
    }

    public function update(Request $request, $id)
    {
        $unit = UnitKerja::findOrFail($id);

        $validated = $request->validate([
            'nama_unit' => 'required|string|max:100',
            'kode_unit' => ['required', 'string', 'max:20', Rule::unique('unit_kerjas', 'kode_unit')->ignore($unit->id)],
        ], [
            'nama_unit.required' => 'Nama unit wajib diisi.',
            'kode_unit.required' => 'Kode unit wajib diisi.',
            'kode_unit.unique' => 'Kode unit sudah digunakan.',
        ]);

        $unit->update($validated);

        return redirect()->route('kasium.settings.units.index')
            ->with('success', 'Unit kerja berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unit = UnitKerja::findOrFail($id);

        if ($unit->users()->exists() || $unit->pengajuans()->exists()) {
            return back()->with('error', 'Unit kerja tidak dapat dihapus karena masih memiliki user/pengajuan terkait.');
        }

        $unit->delete();

        return back()->with('success', 'Unit kerja berhasil dihapus.');
    }
}
