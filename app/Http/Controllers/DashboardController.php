<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use App\Models\User;
use App\Models\JobSheet;
use App\Models\SpkItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return $this->adminDashboard($user);
        } elseif ($user->isQualityControl()) {
            return $this->qcDashboard($user);
        } else {
            return $this->operatorDashboard($user);
        }
    }

    /**
     * Dashboard untuk Super Admin — akses penuh semua data
     */
    private function adminDashboard($user)
    {
        // 1. RINGKASAN DATA (STATISTIK ATAS)
        $totalSpk       = Spk::count();
        $spkDiproses    = Spk::where('status', 'Diproses')->count();
        $spkSelesai     = Spk::where('status', 'Selesai')->count();
        $pendingQc      = SpkItem::where('status_qc', 'Pending')->count(); 

        // 2. DATA UNTUK TABEL/LIST
        $recentSpk      = Spk::latest()->take(5)->get();
        $recentActivity = JobSheet::with(['operator', 'spk'])->latest()->take(5)->get();

        // 3. DATA UNTUK CHART — SPK per bulan (6 bulan terakhir)
        $monthlySpk = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabels[] = $date->translatedFormat('M Y');
            $monthlySpk[] = Spk::whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->count();
        }

        // 4. DISTRIBUSI STATUS SPK
        $statusDistribution = [
            'Diproses' => $spkDiproses,
            'Selesai'  => $spkSelesai,
        ];

        // 5. COMPLETION RATE
        $completionRate = $totalSpk > 0 ? round(($spkSelesai / $totalSpk) * 100) : 0;

        // 6. TOP 3 OPERATORS
        $topOperators = JobSheet::select('operator_id', DB::raw('COUNT(*) as total_jobs'))
            ->with('operator')
            ->groupBy('operator_id')
            ->orderByDesc('total_jobs')
            ->take(3)
            ->get();

        // 7. TOTAL JAM KERJA (bulan ini)
        $totalJamBulanIni = JobSheet::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_jam');

        return view('dashboard', compact(
            'totalSpk', 'spkDiproses', 'spkSelesai', 'pendingQc',
            'recentSpk', 'recentActivity',
            'monthlySpk', 'monthLabels', 'statusDistribution',
            'completionRate', 'topOperators', 'totalJamBulanIni'
        ));
    }

    /**
     * Dashboard untuk Operator — hanya data pekerjaan sendiri
     */
    private function operatorDashboard($user)
    {
        // Statistik pekerjaan operator ini
        $totalJobSaya     = JobSheet::where('operator_id', $user->id)->count();
        $jobBulanIni      = JobSheet::where('operator_id', $user->id)
                                ->whereYear('created_at', now()->year)
                                ->whereMonth('created_at', now()->month)
                                ->count();
        $totalJamSaya     = JobSheet::where('operator_id', $user->id)->sum('total_jam');
        $totalJamBulanIni = JobSheet::where('operator_id', $user->id)
                                ->whereYear('created_at', now()->year)
                                ->whereMonth('created_at', now()->month)
                                ->sum('total_jam');

        // SPK yang sedang diproses (untuk dikerjakan)
        $spkDiproses = Spk::where('status', 'Diproses')->latest()->take(5)->get();

        // Aktivitas terakhir saya
        $recentActivity = JobSheet::with(['spk'])
            ->where('operator_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Chart: Jam kerja per bulan (6 bulan terakhir)
        $monthlyJam = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabels[] = $date->translatedFormat('M Y');
            $monthlyJam[] = (float) JobSheet::where('operator_id', $user->id)
                                ->whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->sum('total_jam');
        }

        return view('dashboard-operator', compact(
            'totalJobSaya', 'jobBulanIni', 'totalJamSaya', 'totalJamBulanIni',
            'spkDiproses', 'recentActivity', 'monthlyJam', 'monthLabels'
        ));
    }

    /**
     * Dashboard untuk QC — fokus pada item yang perlu divalidasi
     */
    private function qcDashboard($user)
    {
        // Statistik QC
        $pendingQc   = SpkItem::where('status_qc', 'Pending')->count();
        $approvedQc  = SpkItem::where('status_qc', 'OK')->count();
        $rejectedQc  = SpkItem::where('status_qc', 'Reject')->count();
        $totalItems  = SpkItem::count();

        // SPK yang memiliki item pending QC
        $spkPendingQc = Spk::whereHas('items', function($q) {
            $q->where('status_qc', 'Pending');
        })->with(['items' => function($q) {
            $q->where('status_qc', 'Pending');
        }])->latest()->take(5)->get();

        // Approval rate
        $totalReviewed = $approvedQc + $rejectedQc;
        $approvalRate  = $totalReviewed > 0 ? round(($approvedQc / $totalReviewed) * 100) : 0;

        // Chart: QC per bulan (6 bulan terakhir)
        $monthlyQc = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabels[] = $date->translatedFormat('M Y');
            $monthlyQc[] = SpkItem::whereIn('status_qc', ['OK', 'Reject'])
                                ->whereYear('updated_at', $date->year)
                                ->whereMonth('updated_at', $date->month)
                                ->count();
        }

        return view('dashboard-qc', compact(
            'pendingQc', 'approvedQc', 'rejectedQc', 'totalItems',
            'spkPendingQc', 'approvalRate', 'monthlyQc', 'monthLabels'
        ));
    }
}