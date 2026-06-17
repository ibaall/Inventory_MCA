@extends('layouts.app')
@section('title', 'Edit Akun - ' . $user->name)

@section('content')
<div class="container" style="max-width: 600px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2 class="mb-0">✏️ Edit Akun: {{ $user->name }}</h2>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf @method('PUT')

                {{-- Nama --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="mb-3">
                    <label for="role" class="form-label fw-semibold">Role / Jabatan</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        @php $cr = old('role', $user->role); @endphp
                        <option value="owner" {{ $cr === 'owner' ? 'selected' : '' }}>👑 Owner (Akses Penuh + Kelola Akun)</option>
                        <option value="admin" {{ $cr === 'admin' ? 'selected' : '' }}>🔧 Admin (Akses Penuh + Kelola Akun)</option>
                        <option value="marketing" {{ $cr === 'marketing' ? 'selected' : '' }}>📊 Marketing (Semua Fitur, Tanpa Aksi Keranjang)</option>
                        <option value="karyawan" {{ $cr === 'karyawan' ? 'selected' : '' }}>👤 Karyawan (Akses Terbatas)</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password (opsional) --}}
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password Baru <small class="text-muted">(opsional, kosongkan jika tidak ingin mengubah)</small></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation">
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
