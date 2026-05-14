@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-bell"></i> Notifikasi</h2>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Daftar Notifikasi</h5>
                </div>
                <div class="card-body">
                    @forelse($notifikasi as $item)
                        <div class="card mb-3 {{ $item->is_read ? 'bg-light' : 'border-primary' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            @if(!$item->is_read)
                                                <span class="badge bg-danger">Baru</span>
                                            @endif
                                            {{ $item->judul }}
                                        </h6>
                                        <p class="mb-1">{{ $item->pesan }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ date('d F Y H:i', strtotime($item->created_at)) }}
                                        </small>
                                    </div>
                                    @if(!$item->is_read)
                                        <form action="/notifikasi/{{ $item->id_notifikasi }}/read" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Tandai Dibaca
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-bell-slash fa-3x mb-3"></i>
                            <p>Tidak ada notifikasi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection