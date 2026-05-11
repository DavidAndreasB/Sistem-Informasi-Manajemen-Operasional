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
    --vt-border: #b0bec5;
    --vt-shadow: 0 2px 6px rgba(0,0,0,.08), 0 4px 14px rgba(0,0,0,.06);
    --vt-shadow-lg: 0 6px 20px rgba(26,86,219,.15), 0 8px 32px rgba(0,0,0,.08);
    --vt-radius: 16px;
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
.icon-green { background: #d1fae5; color: #059669; }
.icon-blue { background: var(--vt-primary-light); color: var(--vt-primary); }
.icon-warning { background: #fef3c7; color: var(--vt-warning); }
.icon-purple { background: #ede9fe; color: #7c3aed; }
.bar-green { background: linear-gradient(90deg, #059669, #34d399); }
.bar-blue { background: linear-gradient(90deg, var(--vt-primary), var(--vt-accent)); }
.bar-warning { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.bar-purple { background: linear-gradient(90deg, #7c3aed, #a78bfa); }

.vt-card { background: var(--vt-card); border-radius: var(--vt-radius); border: 1.5px solid var(--vt-border); box-shadow: var(--vt-shadow); overflow: hidden; transition: box-shadow .3s ease; }
.vt-card:hover { box-shadow: var(--vt-shadow-lg); }
.vt-card-header { padding: 1.2rem 1.5rem; border-bottom: 1px solid var(--vt-border); display: flex; align-items: center; justify-content: space-between; }
.vt-card-header h6 { font-size: .85rem; font-weight: 700; color: var(--vt-text); margin: 0; display: flex; align-items: center; gap: .5rem; }
.vt-card-header h6 i { color: var(--vt-primary); }
.vt-card-body { padding: 1.5rem; }

.vt-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.vt-table thead th { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: var(--vt-text-muted); padding: .8rem 1rem; border-bottom: 2px solid var(--vt-border); background: #f8fafc; }
.vt-table tbody td { padding: .85rem 1rem; font-size: .85rem; color: var(--vt-text); border-bottom: 1px solid var(--vt-border); vertical-align: middle; }
.vt-table tbody tr { transition: background .2s; }
.vt-table tbody tr:hover { background: #f8fafc; }
.vt-table tbody tr:last-child td { border-bottom: none; }

.badge-pill-vt { padding: .35rem .85rem; border-radius: 50px; font-size: .7rem; font-weight: 600; }
.badge-vt-warning { background: #fef3c7; color: #92400e; }

.timeline-item { display: flex; gap: 1rem; padding-bottom: 1.2rem; position: relative; }
.timeline-item:not(:last-child)::after { content: ''; position: absolute; left: 17px; top: 40px; bottom: 0; width: 2px; background: var(--vt-border); }
.timeline-dot { width: 36px; height: 36px; border-radius: 50%; background: var(--vt-primary-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--vt-primary); font-size: .75rem; z-index: 1; }
.timeline-content { flex: 1; }
.timeline-content .tl-user { font-weight: 600; font-size: .85rem; color: var(--vt-text); }
.timeline-content .tl-desc { font-size: .8rem; color: var(--vt-text-muted); margin-top: .15rem; }
.timeline-content .tl-time { font-size: .7rem; color: #94a3b8; margin-top: .2rem; display: flex; align-items: center; gap: .3rem; }

.quick-action { display: flex; align-items: center; gap: .8rem; padding: .9rem 1.1rem; border-radius: 12px; border: 1.5px solid var(--vt-border); background: var(--vt-card); transition: all .25s ease; text-decoration: none; color: var(--vt-text); }
.quick-action:hover { border-color: var(--vt-primary); background: var(--vt-primary-light); transform: translateX(4px); text-decoration: none; color: var(--vt-primary); }
.quick-action .qa-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .9rem; }
.quick-action .qa-text { font-size: .82rem; font-weight: 600; }

.chart-container { position: relative; width: 100%; }
</style>
@endpush

@section('content')
<div class="dashboard-wrap">
<div class="container-fluid px-4 py-3">

    {{-- HEADER --}}
    <div class="dash-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-hard-hat mr-2"></i>Dashboard Operator</h1>
                <p>Halo, <strong>{{ Auth::user()->username }}</strong> — berikut ringkasan pekerjaan Anda.</p>
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
                        <div class="stat-label">Total Pekerjaan</div>
                        <div class="stat-value">{{ $totalJobSaya }}</div>
                    </div>
                    <div class="stat-icon icon-green"><i class="fas fa-briefcase"></i></div>
                </div>
                <div class="stat-bar bar-green"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Job Bulan Ini</div>
                        <div class="stat-value">{{ $jobBulanIni }}</div>
                    </div>
                    <div class="stat-icon icon-blue"><i class="fas fa-calendar-check"></i></div>
                </div>
                <div class="stat-bar bar-blue"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Total Jam Kerja</div>
                        <div class="stat-value">{{ number_format($totalJamSaya, 1) }}</div>
                    </div>
                    <div class="stat-icon icon-purple"><i class="fas fa-clock"></i></div>
                </div>
                <div class="stat-bar bar-purple"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Jam Bulan Ini</div>
                        <div class="stat-value">{{ number_format($totalJamBulanIni, 1) }}</div>
                    </div>
                    <div class="stat-icon icon-warning"><i class="fas fa-hourglass-half"></i></div>
                </div>
                <div class="stat-bar bar-warning"></div>
            </div>
        </div>
    </div>

    {{-- CHART + QUICK ACTIONS --}}
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="vt-card h-100">
                <div class="vt-card-header">
                    <h6><i class="fas fa-chart-bar"></i> Jam Kerja Saya (6 Bulan Terakhir)</h6>
                </div>
                <div class="vt-card-body">
                    <div class="chart-container" style="height:280px;">
                        <canvas id="jamKerjaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="vt-card h-100">
                <div class="vt-card-header">
                    <h6><i class="fas fa-bolt"></i> Aksi Cepat</h6>
                </div>
                <div class="vt-card-body d-flex flex-column" style="gap:.6rem;">
                    <a href="{{ route('jobsheet.index') }}" class="quick-action">
                        <div class="qa-icon icon-green"><i class="fas fa-clipboard-list"></i></div>
                        <div class="qa-text">Lihat Jobsheet</div>
                    </a>
                    <a href="{{ route('spk.index') }}" class="quick-action">
                        <div class="qa-icon icon-blue"><i class="fas fa-folder-open"></i></div>
                        <div class="qa-text">Lihat Daftar SPK</div>
                    </a>
                </div>
                <div class="vt-card-body" style="padding-top:0; font-size:.82rem; color:var(--vt-text-muted); border-top:1px solid var(--vt-border); margin-top:auto;">
                    <p class="mb-1 mt-3"><span style="color:var(--vt-primary); font-weight:700;">Panduan:</span></p>
                    <p class="mb-0">Pilih menu "Jobsheet", pilih proyek yang ingin dikerjakan, lalu catat jam kerja Anda.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- PROYEK TERSEDIA + AKTIVITAS --}}
    <div class="row mb-4">
        <div class="col-lg-6 mb-3">
            <div class="vt-card">
                <div class="vt-card-header">
                    <h6><i class="fas fa-tasks"></i> SPK Sedang Diproses</h6>
                </div>
                <div style="padding:0;">
                    <table class="vt-table">
                        <thead><tr><th>No SPK</th><th>Judul Proyek</th></tr></thead>
                        <tbody>
                            @forelse($spkDiproses as $spk)
                            <tr>
                                <td><a href="{{ route('jobsheet.show', $spk->id) }}" style="color:var(--vt-primary); font-weight:600; text-decoration:none;">{{ $spk->no_spk }}</a></td>
                                <td>{{ $spk->judul_proyek }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center" style="color:var(--vt-text-muted); padding:2rem;">Tidak ada SPK yang sedang diproses.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="vt-card">
                <div class="vt-card-header">
                    <h6><i class="fas fa-history"></i> Aktivitas Terakhir Saya</h6>
                </div>
                <div class="vt-card-body">
                    @forelse($recentActivity as $job)
                    <div class="timeline-item">
                        <div class="timeline-dot"><i class="fas fa-wrench"></i></div>
                        <div class="timeline-content">
                            <div class="tl-desc">
                                Mengerjakan <strong>{{ $job->jenis_pekerjaan }}</strong>
                                pada <a href="{{ route('jobsheet.show', $job->spk_id) }}" style="color:var(--vt-primary); font-weight:600;">{{ $job->spk->no_spk ?? '-' }}</a>
                            </div>
                            <div class="tl-time"><i class="far fa-clock"></i> {{ $job->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center mb-0" style="color:var(--vt-text-muted);">Belum ada aktivitas.</p>
                    @endforelse
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
    const ctx = document.getElementById('jamKerjaChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($monthLabels),
                datasets: [{
                    label: 'Jam Kerja',
                    data: @json($monthlyJam),
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
                    y: { beginAtZero: true, ticks: { font: { family: 'Inter', size: 11 } }, grid: { color: '#f0f0f0' } },
                    x: { ticks: { font: { family: 'Inter', size: 11 } }, grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endpush
