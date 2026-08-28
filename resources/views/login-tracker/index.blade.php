@extends('layouts.app')
@section('title', 'Login Tracker - PT MCA')

@section('content')
<div class="container">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold" style="color: #1a1a2e;">
                <i class="bi bi-activity text-warning me-2"></i>Login Tracker
            </h2>
            <p class="text-muted mb-0" style="font-size: 14px;">
                Pantau aktivitas login seluruh pengguna sistem
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('login-tracker.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise"></i> Reset Filter
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        @php
            $todayCount = \App\Models\LoginLog::whereDate('logged_in_at', today())->count();
            $weekCount = \App\Models\LoginLog::where('logged_in_at', '>=', now()->startOfWeek())->count();
            $totalUsers = \App\Models\User::count();
            $activeToday = \App\Models\LoginLog::whereDate('logged_in_at', today())
                ->distinct('user_id')->count('user_id');
        @endphp
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:42px;height:42px;background:linear-gradient(135deg,#ffc107,#ff9800);flex-shrink:0;">
                            <i class="bi bi-box-arrow-in-right text-dark" style="font-size:18px;"></i>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Login Hari Ini</div>
                            <div class="fw-bold" style="font-size:22px;color:#1a1a2e;">{{ $todayCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #4fc3f7 !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:42px;height:42px;background:linear-gradient(135deg,#4fc3f7,#29b6f6);flex-shrink:0;">
                            <i class="bi bi-people-fill text-white" style="font-size:18px;"></i>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">User Aktif Hari Ini</div>
                            <div class="fw-bold" style="font-size:22px;color:#1a1a2e;">{{ $activeToday }} <small class="text-muted fw-normal" style="font-size:12px;">/ {{ $totalUsers }}</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #66bb6a !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:42px;height:42px;background:linear-gradient(135deg,#66bb6a,#43a047);flex-shrink:0;">
                            <i class="bi bi-calendar-week text-white" style="font-size:18px;"></i>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Minggu Ini</div>
                            <div class="fw-bold" style="font-size:22px;color:#1a1a2e;">{{ $weekCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ab47bc !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:42px;height:42px;background:linear-gradient(135deg,#ab47bc,#8e24aa);flex-shrink:0;">
                            <i class="bi bi-database text-white" style="font-size:18px;"></i>
                        </div>
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Total Log</div>
                            <div class="fw-bold" style="font-size:22px;color:#1a1a2e;">{{ $logs->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('login-tracker.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3 col-6">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:#6c757d;">
                        <i class="bi bi-person me-1"></i>User
                    </label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua User</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ ucfirst(str_replace('_', ' ', $u->role)) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:#6c757d;">
                        <i class="bi bi-calendar-event me-1"></i>Dari Tanggal
                    </label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:#6c757d;">
                        <i class="bi bi-calendar-event me-1"></i>Sampai Tanggal
                    </label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 col-6">
                    <button type="submit" class="btn btn-warning btn-sm w-100 fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold" style="color: #1a1a2e;">
                <i class="bi bi-list-ul me-2 text-warning"></i>Riwayat Login
            </h6>
            <span class="badge bg-dark">{{ $logs->total() }} record</span>
        </div>
        <div class="card-body p-0">
            @if($logs->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-shield-lock" style="font-size:48px;color:#dee2e6;"></i>
                    <p class="text-muted mt-3 mb-0">Tidak ada data login ditemukan.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="loginTrackerTable">
                        <thead>
                            <tr style="background:#f8f9fa;">
                                <th class="ps-4" style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#6c757d;font-weight:700;">No</th>
                                <th style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#6c757d;font-weight:700;">User</th>
                                <th style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#6c757d;font-weight:700;">Role</th>
                                <th style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#6c757d;font-weight:700;">Waktu Login</th>
                                <th style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#6c757d;font-weight:700;">IP Address</th>
                                <th style="font-size:11px;text-transform:uppercase;letter-spacing:0.5px;color:#6c757d;font-weight:700;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $index => $log)
                                @php
                                    $roleBadges = [
                                        'owner' => ['bg' => 'linear-gradient(135deg,#ffc107,#ff9800)', 'text' => '#1a1a2e'],
                                        'admin' => ['bg' => 'linear-gradient(135deg,#4fc3f7,#29b6f6)', 'text' => '#0d2137'],
                                        'karyawan_gudang' => ['bg' => 'linear-gradient(135deg,#66bb6a,#43a047)', 'text' => '#fff'],
                                        'karyawan_marketing' => ['bg' => 'linear-gradient(135deg,#ab47bc,#8e24aa)', 'text' => '#fff'],
                                    ];
                                    $roleLabels = [
                                        'owner' => 'Owner',
                                        'admin' => 'Admin',
                                        'karyawan_gudang' => 'Karyawan Gudang',
                                        'karyawan_marketing' => 'Karyawan Marketing',
                                    ];
                                    $role = $log->user->role ?? 'admin';
                                    $badge = $roleBadges[$role] ?? $roleBadges['admin'];
                                    $isToday = $log->logged_in_at->isToday();
                                @endphp
                                <tr style="{{ $isToday ? 'background-color: #fffde7;' : '' }}">
                                    <td class="ps-4 text-muted" style="font-size:13px;">{{ $logs->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                 style="width:34px;height:34px;background:{{ $badge['bg'] }};color:{{ $badge['text'] }};font-size:13px;flex-shrink:0;">
                                                {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold" style="font-size:13px;color:#1a1a2e;">{{ $log->user->name ?? 'Unknown' }}</div>
                                                <div class="text-muted" style="font-size:11px;">{{ $log->user->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill"
                                              style="background:{{ $badge['bg'] }};color:{{ $badge['text'] }};font-size:10px;font-weight:700;letter-spacing:0.3px;padding:5px 10px;">
                                            {{ $roleLabels[$role] ?? $role }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-size:13px;font-weight:600;color:#1a1a2e;">
                                            <i class="bi bi-clock text-warning me-1" style="font-size:11px;"></i>
                                            {{ $log->logged_in_at->format('H:i:s') }}
                                        </div>
                                        <div class="text-muted" style="font-size:11px;">
                                            {{ $log->logged_in_at->translatedFormat('l, d F Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <code style="font-size:12px;background:#f1f3f5;padding:3px 8px;border-radius:4px;color:#495057;">
                                            {{ $log->ip_address ?? '-' }}
                                        </code>
                                    </td>
                                    <td>
                                        <span class="text-muted" style="font-size:12px;">
                                            {{ $log->logged_in_at->diffForHumans() }}
                                        </span>
                                        @if($isToday)
                                            <span class="badge bg-success ms-1" style="font-size:9px;">Hari ini</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
