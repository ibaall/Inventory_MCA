<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    /**
     * Daftar semua karyawan.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        return view('users.index', compact('users'));
    }

    /**
     * Form tambah karyawan baru.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Simpan karyawan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:owner,admin,marketing,karyawan'],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Akun berhasil ditambahkan!');
    }

    /**
     * Form edit karyawan.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update data karyawan.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'in:owner,admin,marketing,karyawan'],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Akun berhasil diperbarui!');
    }

    /**
     * Hapus karyawan.
     */
    public function destroy(User $user)
    {
        // Jangan izinkan menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus!');
    }

    /**
     * Reset password karyawan dari halaman Kelola Akun.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Password ' . $user->name . ' berhasil direset!');
    }
}
