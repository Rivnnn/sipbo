<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\{User, UnitKerja};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Settings/UserController.php — index() pass $units & $roles untuk modal
    public function index()
    {
        $users = User::with(['unitKerja', 'roles'])->latest()->paginate(10);
        $units = UnitKerja::all();
        $roles = ['staf_unit' => 'Staf Unit', 'kasium' => 'KASIUM', 'pimpinan' => 'Pimpinan'];

        return view('kasium.settings.users.index', compact('users', 'units', 'roles'));
    }

    public function create()
    {
        $units = UnitKerja::all();
        $roles = ['staf_unit' => 'Staf Unit', 'kasium' => 'KASIUM', 'pimpinan' => 'Pimpinan'];
        return view('kasium.settings.users.create', compact('units', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:30|unique:users,username',
            'password' => 'required|string|min:6',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'role' => 'required|in:staf_unit,kasium,pimpinan',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'unit_kerja_id' => $validated['unit_kerja_id'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('kasium.settings.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $units = UnitKerja::all();
        $roles = ['staf_unit' => 'Staf Unit', 'kasium' => 'KASIUM', 'pimpinan' => 'Pimpinan'];
        return view('kasium.settings.users.edit', compact('user', 'units', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => ['required', 'string', 'max:30', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'unit_kerja_id' => 'nullable|exists:unit_kerjas,id',
            'role' => 'required|in:staf_unit,kasium,pimpinan',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->unit_kerja_id = $validated['unit_kerja_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()->route('kasium.settings.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
