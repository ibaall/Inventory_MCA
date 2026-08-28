@extends('layouts.app')
@section('title', 'Tambah No. Perkiraan')
@section('content')
<div class="container" style="max-width:600px">
    <h2 class="mb-4"><i class="bi bi-plus-circle"></i> Tambah No. Perkiraan</h2>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <div class="card">
        <div class="card-header bg-primary text-white fw-semibold"><i class="bi bi-journal-bookmark"></i> Data No. Perkiraan</div>
        <div class="card-body">
            <form action="{{ route('no-perkiraan.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Kode Perkiraan <span class="text-danger">*</span></label>
                    <input type="text" name="kode_perkiraan" class="form-control" value="{{ old('kode_perkiraan') }}" required placeholder="Contoh: 100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Perkiraan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_perkiraan" class="form-control" value="{{ old('nama_perkiraan') }}" required placeholder="Contoh: Kas">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('no-perkiraan.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
