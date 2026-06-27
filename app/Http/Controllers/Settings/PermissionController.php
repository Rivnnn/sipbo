<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\{Role, Permission};

class PermissionController extends Controller
{
    // Permission yang TIDAK boleh diubah dari UI (system-locked)
    private array $lockedPermissions = [
        'dashboard.lihat',      // semua role harus bisa login
    ];

    /*
    |--------------------------------------------------------------------------
    | PER ROLE
    |--------------------------------------------------------------------------
    */
    public function roleIndex()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);

        return view('kasium.settings.permissions.role', compact('roles', 'permissions'));
    }

    public function roleUpdate(Request $request, $roleName)
    {
        // KASIUM tidak boleh ubah permission role 'kasium' sendiri
        if ($roleName === 'kasium') {
            return back()->with('error', 'Permission role KASIUM tidak dapat diubah dari UI untuk alasan keamanan.');
        }

        $role = Role::findByName($roleName);
        $submitted = $request->input('permissions', []);

        // Pisahkan: permission yang dikunci tetap dipertahankan
        $lockedOwned = $role->permissions
            ->whereIn('name', $this->lockedPermissions)
            ->pluck('name')
            ->toArray();

        $finalPermissions = array_unique(array_merge($submitted, $lockedOwned));

        $role->syncPermissions($finalPermissions);

        return back()->with('success', "Permission role '{$role->name}' berhasil diperbarui.");
    }

    /*
    |--------------------------------------------------------------------------
    | PER USER (Override)
    |--------------------------------------------------------------------------
    */
    public function userIndex()
    {
        $users = User::with(['roles', 'permissions'])->latest()->paginate(10);

        return view('kasium.settings.permissions.user', compact('users'));
    }

    public function userShow($id)
    {
        $user = User::with(['roles.permissions', 'permissions'])->findOrFail($id);
        $allPermissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);

        // Permission dari role (bawaan)
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        // Permission override langsung ke user (tambah/kurang dari role)
        $directPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        return view('kasium.settings.permissions.user-detail', compact(
            'user',
            'allPermissions',
            'rolePermissions',
            'directPermissions'
        ));
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // KASIUM tidak bisa override permission dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mengubah permission akun Anda sendiri.');
        }

        $submitted = $request->input('permissions', []);

        // Sync direct permissions (override dari role)
        // Ini hanya set permission TAMBAHAN/DIKURANGI di level user
        $user->syncPermissions($submitted);

        return back()->with('success', "Permission user '{$user->name}' berhasil diperbarui.");
    }

    public function userReset($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat mereset permission akun Anda sendiri.');
        }

        // Hapus semua direct permission — kembali ke default role
        $user->syncPermissions([]);

        return back()->with('success', "Permission user '{$user->name}' direset ke default role.");
    }
}
