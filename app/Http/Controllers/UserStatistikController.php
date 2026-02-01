<?php
namespace App\Http\Controllers;

use App\Models\SetoranSampah;
use App\Models\SetoranSampahDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserStatistikController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalTransaksi = SetoranSampah::where('user_id', $userId)
            ->where('status', 'selesai')
            ->count();

        $stats = SetoranSampahDetail::whereHas('setoran', function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->where('status', 'selesai');
        })
            ->selectRaw('COALESCE(SUM(subtotal), 0) as total_uang, COALESCE(SUM(jumlah), 0) as total_berat')
            ->first();

        $totalPendapatan = $stats->total_uang;
        $totalBerat      = $stats->total_berat;

        $chartLabels     = [];
        $chartIncomeData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date      = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('M');
            $month     = $date->month;
            $year      = $date->year;

            $income = SetoranSampah::where('user_id', $userId)
                ->where('status', 'selesai')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('estimasi_total');

            $chartLabels[]     = $monthName;
            $chartIncomeData[] = $income;
        }

        $categories = SetoranSampahDetail::whereHas('setoran', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('status', 'selesai');
        })
            ->join('kategori_sampah', 'setoran_sampah_detail.kategori_sampah_id', '=', 'kategori_sampah.id')
            ->select('kategori_sampah.nama_sampah', DB::raw('SUM(setoran_sampah_detail.jumlah) as total_qty'))
            ->groupBy('kategori_sampah.nama_sampah')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $chartCategoryLabels = $categories->pluck('nama_sampah')->toArray();
        $chartCategoryData   = $categories->pluck('total_qty')->toArray();

        if (empty($chartCategoryLabels)) {
            $chartCategoryLabels = ['Belum ada data'];
            $chartCategoryData   = [1];
        }

        return view('user.statistik', compact(
            'totalTransaksi',
            'totalPendapatan',
            'totalBerat',
            'chartLabels',
            'chartIncomeData',
            'chartCategoryLabels',
            'chartCategoryData'
        ));
    }
}
