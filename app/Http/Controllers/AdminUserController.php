<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar akun pengguna/administrator.
     */
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        $users = $query->latest('id')->paginate(10)->withQueryString();
        $roles = User::ROLES;

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Formulir tambah pengguna baru.
     */
    public function create(): View
    {
        $roles = User::ROLES;
        $permissions = User::PERMISSIONS;

        return view('admin.users.create', compact('roles', 'permissions'));
    }

    /**
     * Simpan pengguna baru ke basis data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(6)],
            'role' => ['nullable', 'string', 'in:'.implode(',', array_keys(User::ROLES))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', array_keys(User::PERMISSIONS))],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar sebagai akun lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal berjumlah 6 karakter.',
            'role.in' => 'Pilihan peran tidak valid.',
        ]);

        $role = $validated['role'] ?? User::ROLE_ADMIN;
        $permissions = null;

        if ($role === User::ROLE_PETUGAS_WILAYAH) {
            $permissions = ['wilayah'];
        } elseif ($role === User::ROLE_CUSTOM) {
            $permissions = $validated['permissions'] ?? [];
        }

        User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'permissions' => $permissions,
        ]);

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna {$validated['name']} ({$validated['email']}) berhasil ditambahkan!");
    }

    /**
     * Formulir edit akun pengguna.
     */
    public function edit(int $id): View
    {
        $user = User::findOrFail($id);
        $roles = User::ROLES;
        $permissions = User::PERMISSIONS;

        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Perbarui akun pengguna.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'confirmed', Password::min(6)],
            'role' => ['nullable', 'string', 'in:'.implode(',', array_keys(User::ROLES))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', array_keys(User::PERMISSIONS))],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal berjumlah 6 karakter.',
            'role.in' => 'Pilihan peran tidak valid.',
        ]);

        $role = $validated['role'] ?? $user->role;

        // Pencegahan: Akun sendiri tidak boleh mengubah rolenya sendiri jika itu admin
        if (auth()->id() === $user->id && $user->isAdmin() && $role !== User::ROLE_ADMIN) {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri dari Administrator.');
        }

        // Pencegahan: Jika ini satu-satunya administrator, tidak boleh diturunkan rolenya
        if ($user->isAdmin() && $role !== User::ROLE_ADMIN && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('error', 'Tidak dapat mengubah peran karena sistem memerlukan minimal satu Administrator aktif.');
        }

        $permissions = $user->permissions;

        if (array_key_exists('role', $validated)) {
            if ($role === User::ROLE_PETUGAS_WILAYAH) {
                $permissions = ['wilayah'];
            } elseif ($role === User::ROLE_CUSTOM) {
                $permissions = $validated['permissions'] ?? [];
            } else {
                $permissions = null;
            }
        }

        $updates = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role' => $role,
            'permissions' => $permissions,
        ];

        if (! empty($validated['password'])) {
            $updates['password'] = Hash::make($validated['password']);
        }

        $user->update($updates);

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna {$user->name} berhasil diperbarui!");
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Pencegahan menghapus akun sendiri yang sedang login
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        // Pencegahan menghapus jika hanya tersisa 1 administrator
        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus akun karena sistem memerlukan minimal satu administrator aktif.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Akun pengguna {$userName} berhasil dihapus.");
    }
}
