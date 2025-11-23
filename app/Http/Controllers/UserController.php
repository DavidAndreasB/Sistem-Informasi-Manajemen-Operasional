<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Middleware: Pastikan semua aksi di controller ini HANYA untuk Admin.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('admin'), 
        ];
    }

    /**
     * Tampilkan daftar user
     */
    public function index()
    {
        $users = User::latest()->get();
        return view('user.index', compact('users')); 
    }

    /**
     * Form tambah user (Sebenarnya kita pakai /register, tapi ini opsi CRUD standar)
     */
    public function create()
    {
        // Karena kita menggunakan alur /register khusus admin yang sudah dibuat sebelumnya,
        // method ini bisa kita redirect ke sana atau biarkan jika Anda ingin view terpisah.
        return redirect()->route('register');
    }
    
    /**
     * Simpan user (Ditangani oleh RegisterController logic atau custom store di sini)
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'integer', Rule::in([User::ROLE_SUPER_ADMIN, User::ROLE_QUALITY_CONTROL, User::ROLE_OPERATOR])],
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => (int) $request->role,
        ]);

        return redirect()->route('user.index')->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan Form Edit User
     */
    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    /**
     * Proses Update User
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            // Username harus unik, KECUALI punya user ini sendiri
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'integer', Rule::in([User::ROLE_SUPER_ADMIN, User::ROLE_QUALITY_CONTROL, User::ROLE_OPERATOR])],
            // Password opsional (nullable). Jika diisi, minimal 8 karakter.
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Data dasar yang akan diupdate
        $data = [
            'username' => $request->username,
            'role' => (int) $request->role,
        ];

        // Cek apakah password diisi? Jika ya, hash dan masukkan ke data.
        // Jika kosong, berarti admin tidak ingin mengubah password user tersebut.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Hapus User
     */
    public function destroy(User $user)
    {
        // Proteksi: Jangan biarkan Admin menghapus dirinya sendiri saat sedang login
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $user->delete();
        return redirect()->route('user.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }
}