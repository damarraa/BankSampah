<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SetoranSampah;
use App\Models\KategoriSampah;
use App\Models\MasterKategoriSampah;
use App\Models\SetoranSampahDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Total Statistics
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalPetugas = User::where('role', 'petugas')->count();
        $totalNasabah = User::where('role', 'user')->count();

        // Setoran Statistics
        $totalSetoran = SetoranSampah::count();
        $totalSetoranPending = SetoranSampah::where('status', 'pending')->count();
        $totalSetoranDijemput = SetoranSampah::where('status', 'dijemput')->count();
        $totalSetoranSelesai = SetoranSampah::where('status', 'selesai')->count();
        $totalSetoranDitolak = SetoranSampah::where('status', 'ditolak')->count();

        // Revenue Statistics
        $totalRevenue = SetoranSampahDetail::sum('subtotal');
        $monthlyRevenue = SetoranSampahDetail::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('subtotal');

        // Kategori Statistics
        $totalKategori = MasterKategoriSampah::count();
        $totalJenisSampah = KategoriSampah::count();

        // Recent Transactions (Last 5 setoran)
        $recentSetoran = SetoranSampah::with(['user', 'petugas'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Top Kategori Sampah (by jumlah transaksi) - FIXED QUERY
        $topKategori = KategoriSampah::select([
                'kategori_sampah.id',
                'kategori_sampah.nama_sampah',
                'kategori_sampah.master_kategori_id',
                'kategori_sampah.deskripsi',
                'kategori_sampah.harga_satuan',
                'kategori_sampah.jenis_satuan',
                'kategori_sampah.gambar_sampah',
                DB::raw('COUNT(setoran_sampah_detail.id) as jumlah_transaksi'),
                DB::raw('COALESCE(SUM(setoran_sampah_detail.jumlah), 0) as total_berat'),
                DB::raw('COALESCE(SUM(setoran_sampah_detail.subtotal), 0) as total_pendapatan')
            ])
            ->leftJoin('setoran_sampah_detail', 'kategori_sampah.id', '=', 'setoran_sampah_detail.kategori_sampah_id')
            ->groupBy(
                'kategori_sampah.id',
                'kategori_sampah.nama_sampah',
                'kategori_sampah.master_kategori_id',
                'kategori_sampah.deskripsi',
                'kategori_sampah.harga_satuan',
                'kategori_sampah.jenis_satuan',
                'kategori_sampah.gambar_sampah'
            )
            ->orderBy('jumlah_transaksi', 'desc')
            ->take(5)
            ->get();

        // Monthly Statistics for Chart
        $monthlySetoran = SetoranSampah::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyRevenueData = SetoranSampahDetail::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COALESCE(SUM(subtotal), 0) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // User Growth (Last 6 months)
        $userGrowth = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Today's Statistics
        $todaySetoran = SetoranSampah::whereDate('created_at', today())->count();
        $todayRevenue = SetoranSampahDetail::whereDate('created_at', today())->sum('subtotal');
        $todayUsers = User::whereDate('created_at', today())->count();

        // Setoran Status Distribution
        $setoranStatus = [
            'pending' => SetoranSampah::where('status', 'pending')->count(),
            'dijemput' => SetoranSampah::where('status', 'dijemput')->count(),
            'selesai' => SetoranSampah::where('status', 'selesai')->count(),
            'ditolak' => SetoranSampah::where('status', 'ditolak')->count(),
        ];

        // User Role Distribution
        $userRoleDistribution = [
            'admin' => User::where('role', 'admin')->count(),
            'petugas' => User::where('role', 'petugas')->count(),
            'user' => User::where('role', 'user')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'totalPetugas',
            'totalNasabah',
            'totalSetoran',
            'totalSetoranPending',
            'totalSetoranDijemput',
            'totalSetoranSelesai',
            'totalSetoranDitolak',
            'totalRevenue',
            'monthlyRevenue',
            'totalKategori',
            'totalJenisSampah',
            'recentSetoran',
            'topKategori',
            'monthlySetoran',
            'monthlyRevenueData',
            'userGrowth',
            'todaySetoran',
            'todayRevenue',
            'todayUsers',
            'setoranStatus',
            'userRoleDistribution'
        ));
    }

    public function getChartData(Request $request)
    {
        $year = $request->get('year', now()->year);

        $monthlyData = SetoranSampah::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_setoran'),
                DB::raw('(SELECT COALESCE(SUM(subtotal), 0) FROM setoran_sampah_detail
                    WHERE MONTH(created_at) = MONTH(setoran_sampah.created_at)
                    AND YEAR(created_at) = YEAR(setoran_sampah.created_at)) as total_pendapatan')
            )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($monthlyData);
    }
}
