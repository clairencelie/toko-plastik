@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Ganti Password</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">Ganti Password</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                @if($errors->any())
                    <div class="alert alert-danger border-0 mb-4">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Lama</label>
                        <input type="password" name="password_lama" class="form-control @error('password_lama') is-invalid @enderror" required autofocus>
                        @error('password_lama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="password_baru" class="form-control @error('password_baru') is-invalid @enderror" required>
                        @error('password_baru')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_baru_confirmation" class="form-control" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-key me-2"></i> Simpan Password Baru
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
