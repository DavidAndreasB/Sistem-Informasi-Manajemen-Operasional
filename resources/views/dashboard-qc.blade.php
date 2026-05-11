@extends('layouts.sbadmin')

@push('styles')
<style>
:root {
    --vt-primary: #1a56db;
    --vt-primary-light: #e8effc;
    --vt-bg: #f0f2f5;
    --vt-card: #ffffff;
    --vt-text: #1e293b;
    --vt-text-muted: #64748b;
    --vt-success: #059669;
    --vt-warning: #d97706;
    --vt-danger: #dc2626;
    --vt-border: #b0bec5;
    --vt-shadow: 0 2px 6px rgba(0,0,0,.08), 0 4px 14px rgba(0,0,0,.06);
    --vt-shadow-lg: 0 6px 20px rgba(26,86,219,.15), 0 8px 32px rgba(0,0,0,.08);
    --vt-radius: 16px;
    --qc-primary: #1a56db;
    --qc-primary-light: #e8effc;
}
.dashboard-wrap { font-family: 'Inter', 'Nunito', sans-serif; background: var(--vt-bg); min-height: 100vh; }
.dashboard-wrap * { font-family: 'Inter', 'Nunito', sans-serif; }
.dashboard-wrap .fas, .dashboard-wrap .far, .dashboard-wrap .fab, .dashboard-wrap .fa { font-family: 'Font Awesome 5 Free' !important; }
.dashboard-wrap .fab { font-family: 'Font Awesome 5 Brands' !important; }
.topbar { display: none !important; }
#content { padding-top: 0 !important; }

.dash-header { background: linear-gradient(135deg, #1a56db 0%, #1e40af 50%, #1e3a8a 100%); border-radius: var(--vt-radius); padding: 2rem 2.2rem; margin-bottom: 1.8rem; position: relative; overflow: hidden; }
.dash-header::before { content: ''; position: absolute; top: -40%; right: -10%; width: 300px; height: 300px; background: rgba(255,255,255,.06); border-radius: 50%; }
.dash-header h1 { color: #fff; font-size: 1.6rem; font-weight: 700; margin: 0; position: relative; z-index: 1; }
.dash-header p { color: rgba(255,255,255,.8); font-size: .9rem; margin: .4rem 0 0; position: relative; z-index: 1; }
.dash-header .date-badge { background: rgba(255,255,255,.15); backdrop-filter: blur(10px); color: #fff; padding: .45rem 1rem; border-radius: 50px; font-size: .8rem; font-weight: 500; display: inline-flex; align-items: center; gap: .4rem; position: relative; z-index: 1; }

.stat-card { background: var(--vt-card); border-radius: var(--vt-radius); padding: 1.5rem; border: 1.5px solid var(--vt-border); box-shadow: var(--vt-shadow); transition: all .3s cubic-bezier(.4,0,.2,1); position: relative; overflow: hidden; }
.stat-card:hover { transform: translateY(-4px); box-shadow: var(--vt-shadow-lg); }
.stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.stat-card .stat-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--vt-text-muted); margin-bottom: .35rem; }
.stat-card .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--vt-text); line-height: 1; }
.stat-card .stat-bar { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; }
.icon-purple { background: #e8effc; color: #1a56db; }
.icon-warning { background: #fef3c7; color: var(--vt-warning); }
.icon-success { background: #d1fae5; color: var(--vt-success); }
.icon-danger { background: #fee2e2; color: var(--vt-danger); }
.bar-purple { background: linear-gradient(90deg, #1a56db, #60a5fa); }
.bar-warning { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.bar-success { background: linear-gradient(90deg, #059669, #34d399); }
.bar-danger { background: linear-gradient(90deg, #dc2626, #f87171); }

.vt-card { background: var(--vt-card); border-radius: var(--vt-radius); border: 1.5px solid var(--vt-border); box-shadow: var(--vt-shadow); overflow: hidden; transition: box-shadow .3s ease; }
.vt-card:hover { box-shadow: var(--vt-shadow-lg); }
.vt-card-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--vt-border); display: flex; align-items: center; justify-content: space-between; }
.vt-card-header h6 { font-size: .85rem; font-weight: 700; color: var(--vt-text); margin: 0; display: flex; align-items: center; gap: .5rem; }
.vt-card-header h6 i { color: var(--qc-primary); }
.vt-card-body { padding: 1.5rem; }

.vt-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.vt-table thead th { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: var(--vt-text-muted); padding: .8rem 1rem; border-bottom: 2px solid var(--vt-border); background: #f8fafc; }
.vt-table tbody td { padding: .85rem 1rem; font-size: .85rem; color: var(--vt-text); border-bottom: 1px solid var(--vt-border); vertical-align: middle; }
.vt-table tbody tr { transition: background .2s; }
.vt-table tbody tr:hover { background: #f8fafc; }
.vt-table tbody tr:last-child td { border-bottom: none; }

.badge-pill-vt { padding: .35rem .85rem; border-radius: 50px; font-size: .7rem; font-weight: 600; }
.badge-vt-warning { background: #fef3c7; color: #92400e; }

.completion-ring { width: 120px; height: 120px; position: relative; margin: 0 auto; }
.completion-ring svg { transform: rotate(-90deg); }
.completion-ring .ring-text { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.completion-ring .ring-value { font-size: 1.6rem; font-weight: 800; color: #1a56db; }
.completion-ring .ring-label { font-size: .6rem; font-weight: 600; color: var(--vt-text-muted); text-transform: uppercase; letter-spacing: .5px; }

.chart-container { position: relative; width: 100%; }

.quick-action { display: flex; align-items: center; gap: .8rem; padding: .9rem 1.1rem; border-radius: 12px; border: 1.5px solid var(--vt-border); background: var(--vt-card); transition: all .25s ease; text-decoration: none; color: var(--vt-text); }
.quick-action:hover { border-color: var(--qc-primary); background: var(--qc-primary-light); transform: translateX(4px); text-decoration: none; color: var(--qc-primary); }
.quick-action .qa-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .9rem; }
.quick-action .qa-text { font-size: .82rem; font-weight: 600; }

.pending-item { display: flex; align-items: center; gap: .8rem; padding: .8rem 0; }
.pending-item:not(:last-child) { border-bottom: 1px solid var(--vt-border); }
.pending-count { background: #fef3c7; color: #92400e; font-size: .7rem; font-weight: 700; padding: .25rem .6rem; border-radius: 50px; }
</style>
@endpush

@section('content')
<div class="dashboard-wrap">
<div class="container-fluid px-4 py-3">

    {{-- HEADER --}}
    <div class="dash-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-clipboard-check mr-2"></i>Dashboard Quality Control</h1>
                <p>Halo, <strong>{{ Auth::user()->username }}</strong> — berikut ringkasan status QC.</p>
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
                        <div class="stat-label">Pending Review</div>
                        <div class="stat-value">{{ $pendingQc }}</div>
                    </div>
                    <div class="stat-icon icon-warning"><i class="fas fa-hourglass-half"></i></div>
                </div>
                <div class="stat-bar bar-warning"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Approved (OK)</div>
                        <div class="stat-value">{{ $approvedQc }}</div>
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
                        <div class="stat-label">Rejected</div>
                        <div class="stat-value">{{ $rejectedQc }}</div>
                    </div>
                    <div class="stat-icon icon-danger"><i class="fas fa-times-circle"></i></div>
                </div>
                <div class="stat-bar bar-danger"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total Item</div>
                        <div class="stat-value">{{ $totalItems }}</div>
                    </div>
                    <div class="stat-icon icon-purple"><i class="fas fa-boxes"></i></div>
                </div>
                <div class="stat-bar bar-purple"></div>
            </div>
        </div>
    </div>

    {{-- CHART + APPROVAL RATE --}}
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="vt-card h-100">
                <div class="vt-card-header">
                    <h6><i class="fas fa-chart-bar"></i> Item Direview per Bulan</h6>
                </div>
                <div class="vt-card-body">
                    <div class="chart-container" style="height:280px;">
                        <canvas id="qcChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="vt-card h-100">
                <div class="vt-card-header">
                    <h6><i class="fas fa-percentage"></i> Approval Rate</h6>
                </div>
                <div class="vt-card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="completion-ring mb-3">
                        <svg width="120" height="120" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#1a56db" stroke-width="10" stroke-linecap="round" stroke-dasharray="{{ 2 * 3.14159 * 50 }}" stroke-dashoffset="{{ 2 * 3.14159 * 50 * (1 - $approvalRate / 100) }}"/>
                        </svg>
                        <div class="ring-text">
                            <div class="ring-value">{{ $approvalRate }}%</div>
                            <div class="ring-label">Approval</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-around w-100 mt-2">
                        <div class="text-center">
                            <div style="font-size:1.2rem; font-weight:800; color:var(--vt-success);">{{ $approvedQc }}</div>
                            <div style="font-size:.65rem; font-weight:600; color:var(--vt-text-muted); text-transform:uppercase;">OK</div>
                        </div>
                        <div class="text-center">
                            <div style="font-size:1.2rem; font-weight:800; color:var(--vt-danger);">{{ $rejectedQc }}</div>
                            <div style="font-size:.65rem; font-weight:600; color:var(--vt-text-muted); text-transform:uppercase;">Reject</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PENDING QC LIST + QUICK ACTIONS --}}
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="vt-card">
                <div class="vt-card-header">
                    <h6><i class="fas fa-exclamation-triangle"></i> SPK Menunggu QC</h6>
                    <a href="{{ route('spk.index') }}" style="font-size:.75rem; font-weight:600; color:var(--qc-primary); text-decoration:none;">Lihat Semua →</a>
                </div>
                <div style="padding:0;">
                    <table class="vt-table">
                        <thead><tr><th>No SPK</th><th>Judul Proyek</th><th>Item Pending</th></tr></thead>
                        <tbody>
                            @forelse($spkPendingQc as $spk)
                            <tr>
                                <td><a href="{{ route('spk.show', $spk->id) }}" style="color:var(--qc-primary); font-weight:600; text-decoration:none;">{{ $spk->no_spk }}</a></td>
                                <td>{{ $spk->judul_proyek }}</td>
                                <td><span class="pending-count">{{ $spk->items->count() }} item</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center" style="color:var(--vt-text-muted); padding:2rem;">Semua item sudah direview! 🎉</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="vt-card">
                <div class="vt-card-header">
                    <h6><i class="fas fa-bolt"></i> Aksi Cepat</h6>
                </div>
                <div class="vt-card-body d-flex flex-column" style="gap:.6rem;">
                    <a href="{{ route('spk.index') }}" class="quick-action">
                        <div class="qa-icon icon-purple"><i class="fas fa-search"></i></div>
                        <div class="qa-text">Lihat Semua SPK</div>
                    </a>
                    <a href="{{ route('jobsheet.index') }}" class="quick-action">
                        <div class="qa-icon icon-success"><i class="fas fa-clipboard-list"></i></div>
                        <div class="qa-text">Lihat Jobsheet</div>
                    </a>
                </div>
                <div class="vt-card-body" style="padding-top:0; font-size:.82rem; color:var(--vt-text-muted); border-top:1px solid var(--vt-border);">
                    <p class="mb-1 mt-3"><span style="color:var(--qc-primary); font-weight:700;">Panduan:</span></p>
                    <p class="mb-0">Masuk ke detail SPK, lalu validasi setiap item barang dengan status OK atau Reject.</p>
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
    const ctx = document.getElementById('qcChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($monthLabels),
                datasets: [{
                    label: 'Item Direview',
                    data: @json($monthlyQc),
                    backgroundColor: 'rgba(26,86,219,.7)',
                    borderColor: '#1a56db',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    barPercentage: .5
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
});
</script>
@endpush
