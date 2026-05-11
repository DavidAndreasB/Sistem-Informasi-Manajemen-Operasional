@extends('layouts.sbadmin')

@push('styles')
<style>
:root {
    --vt-primary: #1a56db;
    --vt-primary-dark: #1342a8;
    --vt-primary-light: #e8effc;
    --vt-accent: #2563eb;
    --vt-bg: #f0f2f5;
    --vt-card: #ffffff;
    --vt-text: #1e293b;
    --vt-text-muted: #64748b;
    --vt-success: #059669;
    --vt-warning: #d97706;
    --vt-danger: #dc2626;
    --vt-info: #0891b2;
    --vt-border: #b0bec5;
    --vt-shadow: 0 2px 6px rgba(0,0,0,.08), 0 4px 14px rgba(0,0,0,.06);
    --vt-shadow-lg: 0 6px 20px rgba(26,86,219,.15), 0 8px 32px rgba(0,0,0,.08);
    --vt-radius: 16px;
}
.dashboard-wrap { font-family: 'Inter', 'Nunito', sans-serif; background: var(--vt-bg); min-height: 100vh; }
.dashboard-wrap * { font-family: 'Inter', 'Nunito', sans-serif; }
.dashboard-wrap .fas, .dashboard-wrap .far, .dashboard-wrap .fab, .dashboard-wrap .fa { font-family: 'Font Awesome 5 Free' !important; }
.dashboard-wrap .fab { font-family: 'Font Awesome 5 Brands' !important; }

/* Header */
.dash-header { background: linear-gradient(135deg, #1a56db 0%, #1e40af 50%, #1e3a8a 100%); border-radius: var(--vt-radius); padding: 2rem 2.2rem; margin-bottom: 1.8rem; position: relative; overflow: hidden; }
.dash-header::before { content: ''; position: absolute; top: -40%; right: -10%; width: 300px; height: 300px; background: rgba(255,255,255,.06); border-radius: 50%; }
.dash-header::after { content: ''; position: absolute; bottom: -50%; left: 20%; width: 200px; height: 200px; background: rgba(255,255,255,.04); border-radius: 50%; }
.dash-header h1 { color: #fff; font-size: 1.6rem; font-weight: 700; margin: 0; position: relative; z-index: 1; }
.dash-header p { color: rgba(255,255,255,.8); font-size: .9rem; margin: .4rem 0 0; position: relative; z-index: 1; }
.dash-header .date-badge { background: rgba(255,255,255,.15); backdrop-filter: blur(10px); color: #fff; padding: .45rem 1rem; border-radius: 50px; font-size: .8rem; font-weight: 500; display: inline-flex; align-items: center; gap: .4rem; position: relative; z-index: 1; }

/* Stat Cards */
.stat-card { background: var(--vt-card); border-radius: var(--vt-radius); padding: 1.5rem; border: 1.5px solid var(--vt-border); box-shadow: var(--vt-shadow); transition: all .3s cubic-bezier(.4,0,.2,1); position: relative; overflow: hidden; }
.stat-card:hover { transform: translateY(-4px); box-shadow: var(--vt-shadow-lg); }
.stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.stat-card .stat-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--vt-text-muted); margin-bottom: .35rem; }
.stat-card .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--vt-text); line-height: 1; }
.stat-card .stat-bar { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; }

.icon-blue { background: var(--vt-primary-light); color: var(--vt-primary); }
.icon-warning { background: #fef3c7; color: var(--vt-warning); }
.icon-success { background: #d1fae5; color: var(--vt-success); }
.icon-danger { background: #fee2e2; color: var(--vt-danger); }
.bar-blue { background: linear-gradient(90deg, var(--vt-primary), var(--vt-accent)); }
.bar-warning { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.bar-success { background: linear-gradient(90deg, #059669, #34d399); }
.bar-danger { background: linear-gradient(90deg, #dc2626, #f87171); }

/* Cards */
.vt-card { background: var(--vt-card); border-radius: var(--vt-radius); border: 1.5px solid var(--vt-border); box-shadow: var(--vt-shadow); overflow: hidden; transition: box-shadow .3s ease; }
.vt-card:hover { box-shadow: var(--vt-shadow-lg); }
.vt-card-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--vt-border); display: flex; align-items: center; justify-content: space-between; }
.vt-card-header h6 { font-size: .85rem; font-weight: 700; color: var(--vt-text); margin: 0; display: flex; align-items: center; gap: .5rem; }
.vt-card-header h6 i { color: var(--vt-primary); }
.vt-card-body { padding: 1.5rem; }

/* Completion Ring */
.completion-ring { width: 100px; height: 100px; position: relative; margin: 0 auto; }
.completion-ring svg { transform: rotate(-90deg); }
.completion-ring .ring-text { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 800; color: var(--vt-primary); }

/* Table */
.vt-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.vt-table thead th { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: var(--vt-text-muted); padding: .8rem 1rem; border-bottom: 2px solid var(--vt-border); background: #f8fafc; }
.vt-table tbody td { padding: .85rem 1rem; font-size: .85rem; color: var(--vt-text); border-bottom: 1px solid var(--vt-border); vertical-align: middle; }
.vt-table tbody tr { transition: background .2s; }
.vt-table tbody tr:hover { background: #f8fafc; }
.vt-table tbody tr:last-child td { border-bottom: none; }

/* Badges */
.badge-pill-vt { padding: .35rem .85rem; border-radius: 50px; font-size: .7rem; font-weight: 600; letter-spacing: .3px; }
.badge-vt-success { background: #d1fae5; color: #065f46; }
.badge-vt-warning { background: #fef3c7; color: #92400e; }
.badge-vt-danger { background: #fee2e2; color: #991b1b; }
.badge-vt-primary { background: var(--vt-primary-light); color: var(--vt-primary-dark); }

/* Timeline */
.timeline-item { display: flex; gap: 1rem; padding-bottom: 1.2rem; position: relative; }
.timeline-item:not(:last-child)::after { content: ''; position: absolute; left: 17px; top: 40px; bottom: 0; width: 2px; background: var(--vt-border); }
.timeline-dot { width: 36px; height: 36px; border-radius: 50%; background: var(--vt-primary-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--vt-primary); font-size: .75rem; z-index: 1; }
.timeline-content { flex: 1; }
.timeline-content .tl-user { font-weight: 600; font-size: .85rem; color: var(--vt-text); }
.timeline-content .tl-desc { font-size: .8rem; color: var(--vt-text-muted); margin-top: .15rem; }
.timeline-content .tl-time { font-size: .7rem; color: #94a3b8; margin-top: .2rem; display: flex; align-items: center; gap: .3rem; }

/* Quick Actions */
.quick-action { display: flex; align-items: center; gap: .8rem; padding: .9rem 1.1rem; border-radius: 12px; border: 1.5px solid var(--vt-border); background: var(--vt-card); transition: all .25s ease; text-decoration: none; color: var(--vt-text); }
.quick-action:hover { border-color: var(--vt-primary); background: var(--vt-primary-light); transform: translateX(4px); text-decoration: none; color: var(--vt-primary); }
.quick-action .qa-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .9rem; }
.quick-action .qa-text { font-size: .82rem; font-weight: 600; }

/* Operator Rank */
.operator-item { display: flex; align-items: center; gap: .8rem; padding: .7rem 0; }
.operator-item:not(:last-child) { border-bottom: 1px solid var(--vt-border); }
.rank-badge { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .7rem; font-weight: 800; flex-shrink: 0; }
.rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #fff; }
.rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: #fff; }
.rank-3 { background: linear-gradient(135deg, #d97706, #b45309); color: #fff; }
.operator-name { font-weight: 600; font-size: .85rem; color: var(--vt-text); }
.operator-jobs { font-size: .75rem; color: var(--vt-text-muted); }

/* Chart Container */
.chart-container { position: relative; width: 100%; }

/* View All Btn */
.btn-view-all { display: block; text-align: center; padding: .6rem; border-radius: 10px; background: #f8fafc; color: var(--vt-primary); font-size: .8rem; font-weight: 600; text-decoration: none; transition: all .2s; border: 1px solid transparent; }
.btn-view-all:hover { background: var(--vt-primary-light); border-color: var(--vt-primary); text-decoration: none; color: var(--vt-primary-dark); }

/* Jam Kerja */
.jam-kerja-value { font-size: 2.2rem; font-weight: 800; color: var(--vt-primary); }
.jam-kerja-label { font-size: .75rem; color: var(--vt-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }

/* Hide redundant topbar on dashboard */
.topbar { display: none !important; }
#content { padding-top: 0 !important; }

@media (max-width: 768px) {
    .dash-header { padding: 1.4rem; }
    .dash-header h1 { font-size: 1.2rem; }
    .stat-card .stat-value { font-size: 1.4rem; }
}
</style>
@endpush

@section('content')
<div class="dashboard-wrap">
<div class="container-fluid px-4 py-3">

    {{-- HEADER --}}
    <div class="dash-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-tachometer-alt mr-2"></i>Dashboard Operasional</h1>
                <p>Selamat datang kembali, <strong>{{ Auth::user()->username }}</strong> — pantau seluruh operasional Anda.</p>
            </div>
            <div class="date-badge mt-2 mt-md-0">
                <i class="far fa-calendar-alt"></i>
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total SPK</div>
                        <div class="stat-value">{{ $totalSpk }}</div>
                    </div>
                    <div class="stat-icon icon-blue"><i class="fas fa-file-contract"></i></div>
                </div>
                <div class="stat-bar bar-blue"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Sedang Diproses</div>
                        <div class="stat-value">{{ $spkDiproses }}</div>
                    </div>
                    <div class="stat-icon icon-warning"><i class="fas fa-cogs"></i></div>
                </div>
                <div class="stat-bar bar-warning"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Proyek Selesai</div>
                        <div class="stat-value">{{ $spkSelesai }}</div>
                    </div>
                    <div class="stat-icon icon-success"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="stat-bar bar-success"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Pending QC</div>
                        <div class="stat-value">{{ $pendingQc }}</div>
                    </div>
                    <div class="stat-icon icon-danger"><i class="fas fa-clipboard-check"></i></div>
                </div>
                <div class="stat-bar bar-danger"></div>
            </div>
        </div>
    </div>

    {{-- CHARTS ROW --}}
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="vt-card h-100">
                <div class="vt-card-header">
                    <h6><i class="fas fa-chart-area"></i> Tren SPK (6 Bulan Terakhir)</h6>
                </div>
                <div class="vt-card-body">
                    <div class="chart-container" style="height:280px;">
                        <canvas id="spkTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="vt-card h-100">
                <div class="vt-card-header">
                    <h6><i class="fas fa-chart-pie"></i> Distribusi Status</h6>
                </div>
                <div class="vt-card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="chart-container" style="height:200px; max-width:220px;">
                        <canvas id="statusDonutChart"></canvas>
                    </div>
                    <div class="d-flex gap-3 mt-3 flex-wrap justify-content-center" style="gap:.8rem;">
                        <span class="d-flex align-items-center" style="gap:.3rem; font-size:.75rem; color:var(--vt-text-muted);">
                            <span style="width:10px;height:10px;border-radius:50%;background:var(--vt-warning);display:inline-block;"></span> Diproses
                        </span>
                        <span class="d-flex align-items-center" style="gap:.3rem; font-size:.75rem; color:var(--vt-text-muted);">
                            <span style="width:10px;height:10px;border-radius:50%;background:var(--vt-success);display:inline-block;"></span> Selesai
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT ROW --}}
    <div class="row mb-4">
        {{-- Recent Projects --}}
        <div class="col-lg-8 mb-3">
            <div class="vt-card">
                <div class="vt-card-header">
                    <h6><i class="fas fa-folder-open"></i> Proyek Terakhir Masuk</h6>
                    <a href="{{ route('spk.index') }}" style="font-size:.75rem; font-weight:600; color:var(--vt-primary); text-decoration:none;">Lihat Semua →</a>
                </div>
                <div style="padding:0;">
                    <table class="vt-table">
                        <thead>
                            <tr>
                                <th>No SPK</th>
                                <th>Judul Proyek</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSpk as $spk)
                            <tr>
                                <td><a href="{{ route('spk.show', $spk->id) }}" style="color:var(--vt-primary); font-weight:600; text-decoration:none;">{{ $spk->no_spk }}</a></td>
                                <td>{{ $spk->judul_proyek }}</td>
                                <td>
                                    @if ($spk->status == 'Selesai')
                                        <span class="badge-pill-vt badge-vt-success">Selesai</span>
                                    @else
                                        <span class="badge-pill-vt badge-vt-warning">Diproses</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center" style="color:var(--vt-text-muted); padding:2rem;">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4 mb-3">
            {{-- Completion + Jam Kerja --}}
            <div class="vt-card mb-3">
                <div class="vt-card-body">
                    <div class="d-flex align-items-center justify-content-around">
                        <div class="text-center">
                            <div class="completion-ring">
                                <svg width="100" height="100" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="42" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                                    <circle cx="50" cy="50" r="42" fill="none" stroke="var(--vt-primary)" stroke-width="8" stroke-linecap="round" stroke-dasharray="{{ 2 * 3.14159 * 42 }}" stroke-dashoffset="{{ 2 * 3.14159 * 42 * (1 - $completionRate / 100) }}"/>
                                </svg>
                                <div class="ring-text">{{ $completionRate }}%</div>
                            </div>
                            <div style="font-size:.7rem; font-weight:600; color:var(--vt-text-muted); text-transform:uppercase; letter-spacing:.5px; margin-top:.5rem;">Completion</div>
                        </div>
                        <div class="text-center">
                            <div class="jam-kerja-value">{{ number_format($totalJamBulanIni, 1) }}</div>
                            <div class="jam-kerja-label">Jam Kerja<br>Bulan Ini</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Operators --}}
            <div class="vt-card mb-3">
                <div class="vt-card-header">
                    <h6><i class="fas fa-trophy"></i> Top Operator</h6>
                </div>
                <div class="vt-card-body" style="padding-top:.8rem; padding-bottom:.8rem;">
                    @forelse($topOperators as $i => $op)
                    <div class="operator-item">
                        <div class="rank-badge rank-{{ $i + 1 }}">{{ $i + 1 }}</div>
                        <div class="flex-grow-1">
                            <div class="operator-name">{{ $op->operator->username ?? 'User' }}</div>
                            <div class="operator-jobs">{{ $op->total_jobs }} pekerjaan</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center mb-0" style="color:var(--vt-text-muted); font-size:.8rem;">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- BOTTOM ROW --}}
    <div class="row mb-4">
        {{-- Activity Timeline --}}
        <div class="col-lg-8 mb-3">
            <div class="vt-card">
                <div class="vt-card-header">
                    <h6><i class="fas fa-history"></i> Aktivitas Terkini</h6>
                    <a href="{{ route('jobsheet.index') }}" style="font-size:.75rem; font-weight:600; color:var(--vt-primary); text-decoration:none;">Lihat Semua →</a>
                </div>
                <div class="vt-card-body">
                    @forelse($recentActivity as $job)
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-wrench"></i></div>
                        <div class="timeline-content">
                            <div class="tl-user">{{ $job->operator->username ?? 'User' }}</div>
                            <div class="tl-desc">
                                Mengerjakan <strong>{{ $job->jenis_pekerjaan }}</strong>
                                pada proyek <a href="{{ route('jobsheet.show', $job->spk_id) }}" style="color:var(--vt-primary); font-weight:600;">{{ $job->spk->no_spk ?? '-' }}</a>
                            </div>
                            <div class="tl-time"><i class="far fa-clock"></i> {{ $job->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center mb-0" style="color:var(--vt-text-muted);">Belum ada aktivitas pengerjaan.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-lg-4 mb-3">
            <div class="vt-card">
                <div class="vt-card-header">
                    <h6><i class="fas fa-bolt"></i> Aksi Cepat</h6>
                </div>
                <div class="vt-card-body d-flex flex-column" style="gap:.6rem;">
                    <a href="{{ route('spk.create') }}" class="quick-action">
                        <div class="qa-icon icon-blue"><i class="fas fa-plus"></i></div>
                        <div class="qa-text">Buat SPK Baru</div>
                    </a>
                    <a href="{{ route('jobsheet.index') }}" class="quick-action">
                        <div class="qa-icon icon-success"><i class="fas fa-clipboard-list"></i></div>
                        <div class="qa-text">Lihat Jobsheet</div>
                    </a>
                    <a href="{{ route('spk.index') }}" class="quick-action">
                        <div class="qa-icon icon-warning"><i class="fas fa-search"></i></div>
                        <div class="qa-text">Cari SPK</div>
                    </a>
                </div>
            </div>

            {{-- Panduan --}}
            <div class="vt-card mt-3">
                <div class="vt-card-header">
                    <h6><i class="fas fa-info-circle"></i> Panduan Singkat</h6>
                </div>
                <div class="vt-card-body" style="font-size:.82rem; color:var(--vt-text-muted);">
                    <p class="mb-2"><span style="color:var(--vt-primary); font-weight:700;">Admin:</span> Menu "SPK" untuk membuat perintah kerja baru.</p>
                    <p class="mb-2"><span style="color:var(--vt-success); font-weight:700;">Operator:</span> Menu "Jobsheet", pilih proyek, catat jam kerja.</p>
                    <p class="mb-0"><span style="color:var(--vt-danger); font-weight:700;">QC:</span> Detail SPK untuk validasi (OK/Reject) item.</p>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Area Chart - SPK Trend
    const trendCtx = document.getElementById('spkTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($monthLabels),
                datasets: [{
                    label: 'SPK Masuk',
                    data: @json($monthlySpk),
                    borderColor: '#1a56db',
                    backgroundColor: 'rgba(26,86,219,.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: .4,
                    pointBackgroundColor: '#1a56db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, font: { family: 'Inter', size: 11 } }, grid: { color: '#f0f0f0' } },
                    x: { ticks: { font: { family: 'Inter', size: 11 } }, grid: { display: false } }
                }
            }
        });
    }

    // Donut Chart - Status Distribution
    const donutCtx = document.getElementById('statusDonutChart');
    if (donutCtx) {
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Diproses', 'Selesai'],
                datasets: [{
                    data: [{{ $statusDistribution['Diproses'] }}, {{ $statusDistribution['Selesai'] }}],
                    backgroundColor: ['#d97706', '#059669'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
@endpush