<?php
namespace App\Http\Controllers;

use App\Models\KategoriSampah;
use App\Models\Keuangan;
use App\Models\MasterKategoriSampah;
use App\Models\PenjualanSampah;
use App\Models\ProdukKarya;
use App\Models\SetoranSampah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. STATISTIK USER
        $totalUsers   = User::count();
        $totalAdmins  = User::where('role', 'admin')->count();
        $totalPetugas = User::where('role', 'petugas')->count();
        $totalNasabah = User::where('role', 'user')->count();

        // 2. STATISTIK SETORAN (TRANSAKSI MASUK)
        $totalSetoran = SetoranSampah::count();
        $todaySetoran = SetoranSampah::whereDate('created_at', today())->count();

        $setoranStatus = [
            'pending'  => SetoranSampah::where('status', 'pending')->count(),
            'dijemput' => SetoranSampah::where('status', 'dijemput')->count(),
            'selesai'  => SetoranSampah::where('status', 'selesai')->count(),
            'ditolak'  => SetoranSampah::where('status', 'ditolak')->count(),
        ];

        // 3. KEUANGAN (LOGIC UTAMA)

        // A. Total Pembelian (Uang Keluar ke User)
        // Hitung estimasi_total dari setoran yang SUDAH SELESAI saja agar akurat
        $totalPembelian = SetoranSampah::where('status', 'selesai')->sum('estimasi_total');
        $todayRevenue   = SetoranSampah::where('status', 'selesai')
            ->whereDate('created_at', today())
            ->sum('estimasi_total');

        // B. Total Penjualan (Uang Masuk)
        $jualPengepul   = PenjualanSampah::sum('total_pendapatan');
        $jualKarya      = ProdukKarya::sum('harga_jual'); // Asumsi harga_jual adalah total pendapatan
        $totalPenjualan = $jualPengepul + $jualKarya;

        // C. Saldo / Kas (Rumus Agregasi)
        $modalMasuk = Keuangan::where('jenis', 'masuk')->sum('nominal');
        $opsKeluar  = Keuangan::where('jenis', 'keluar')->sum('nominal');

        // Rumus: (Modal Awal + Semua Penjualan) - (Semua Pembelian + Biaya Operasional)
        $saldoAktif = ($modalMasuk + $totalPenjualan) - ($totalPembelian + $opsKeluar);

        // 4. DATA PENDUKUNG (List & Chart)
        $totalKategori    = MasterKategoriSampah::count();
        $totalJenisSampah = KategoriSampah::count();

        // Recent Transactions (5 Terakhir)
        $recentSetoran = SetoranSampah::with(['user'])
            ->latest()
            ->take(5)
            ->get();

        // Top Kategori (Tetap gunakan query lama yang sudah fix)
        $topKategori = KategoriSampah::select([
            'kategori_sampah.id',
            'kategori_sampah.nama_sampah',
            'kategori_sampah.master_kategori_id',
            DB::raw('COUNT(setoran_sampah_detail.id) as jumlah_transaksi'),
            DB::raw('COALESCE(SUM(setoran_sampah_detail.subtotal), 0) as total_pendapatan'),
        ])
            ->leftJoin('setoran_sampah_detail', 'kategori_sampah.id', '=', 'setoran_sampah_detail.kategori_sampah_id')
            ->groupBy('kategori_sampah.id', 'kategori_sampah.nama_sampah', 'kategori_sampah.master_kategori_id')
            ->orderBy('jumlah_transaksi', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalNasabah', 'totalPetugas',
            'totalSetoran', 'todaySetoran', 'setoranStatus',
            'totalPembelian', 'todayRevenue',
            'totalPenjualan', 'jualPengepul', 'jualKarya',
            'saldoAktif',
            'totalKategori', 'totalJenisSampah',
            'recentSetoran', 'topKategori'
        ));
    }

    // public function index()
    // {
    //     // Total Statistics
    //     $totalUsers = User::count();
    //     $totalAdmins = User::where('role', 'admin')->count();
    //     $totalPetugas = User::where('role', 'petugas')->count();
    //     $totalNasabah = User::where('role', 'user')->count();

    //     // Setoran Statistics
    //     $totalSetoran = SetoranSampah::count();
    //     $totalSetoranPending = SetoranSampah::where('status', 'pending')->count();
    //     $totalSetoranDijemput = SetoranSampah::where('status', 'dijemput')->count();
    //     $totalSetoranSelesai = SetoranSampah::where('status', 'selesai')->count();
    //     $totalSetoranDitolak = SetoranSampah::where('status', 'ditolak')->count();

    //     // Revenue Statistics
    //     $totalRevenue = SetoranSampahDetail::sum('subtotal');
    //     $monthlyRevenue = SetoranSampahDetail::whereMonth('created_at', now()->month)
    //         ->whereYear('created_at', now()->year)
    //         ->sum('subtotal');

    //     // Kategori Statistics
    //     $totalKategori = MasterKategoriSampah::count();
    //     $totalJenisSampah = KategoriSampah::count();

    //     // Recent Transactions (Last 5 setoran)
    //     $recentSetoran = SetoranSampah::with(['user', 'petugas'])
    //         ->orderBy('created_at', 'desc')
    //         ->take(5)
    //         ->get();

    //     // Top Kategori Sampah (by jumlah transaksi) - FIXED QUERY
    //     $topKategori = KategoriSampah::select([
    //             'kategori_sampah.id',
    //             'kategori_sampah.nama_sampah',
    //             'kategori_sampah.master_kategori_id',
    //             'kategori_sampah.deskripsi',
    //             'kategori_sampah.harga_satuan',
    //             'kategori_sampah.jenis_satuan',
    //             'kategori_sampah.gambar_sampah',
    //             DB::raw('COUNT(setoran_sampah_detail.id) as jumlah_transaksi'),
    //             DB::raw('COALESCE(SUM(setoran_sampah_detail.jumlah), 0) as total_berat'),
    //             DB::raw('COALESCE(SUM(setoran_sampah_detail.subtotal), 0) as total_pendapatan')
    //         ])
    //         ->leftJoin('setoran_sampah_detail', 'kategori_sampah.id', '=', 'setoran_sampah_detail.kategori_sampah_id')
    //         ->groupBy(
    //             'kategori_sampah.id',
    //             'kategori_sampah.nama_sampah',
    //             'kategori_sampah.master_kategori_id',
    //             'kategori_sampah.deskripsi',
    //             'kategori_sampah.harga_satuan',
    //             'kategori_sampah.jenis_satuan',
    //             'kategori_sampah.gambar_sampah'
    //         )
    //         ->orderBy('jumlah_transaksi', 'desc')
    //         ->take(5)
    //         ->get();

    //     // Monthly Statistics for Chart
    //     $monthlySetoran = SetoranSampah::select(
    //             DB::raw('MONTH(created_at) as month'),
    //             DB::raw('COUNT(*) as total')
    //         )
    //         ->whereYear('created_at', now()->year)
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->get();

    //     $monthlyRevenueData = SetoranSampahDetail::select(
    //             DB::raw('MONTH(created_at) as month'),
    //             DB::raw('COALESCE(SUM(subtotal), 0) as total')
    //         )
    //         ->whereYear('created_at', now()->year)
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->get();

    //     // User Growth (Last 6 months)
    //     $userGrowth = User::select(
    //             DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
    //             DB::raw('COUNT(*) as total')
    //         )
    //         ->where('created_at', '>=', now()->subMonths(6))
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->get();

    //     // Today's Statistics
    //     $todaySetoran = SetoranSampah::whereDate('created_at', today())->count();
    //     $todayRevenue = SetoranSampahDetail::whereDate('created_at', today())->sum('subtotal');
    //     $todayUsers = User::whereDate('created_at', today())->count();

    //     // Setoran Status Distribution
    //     $setoranStatus = [
    //         'pending' => SetoranSampah::where('status', 'pending')->count(),
    //         'dijemput' => SetoranSampah::where('status', 'dijemput')->count(),
    //         'selesai' => SetoranSampah::where('status', 'selesai')->count(),
    //         'ditolak' => SetoranSampah::where('status', 'ditolak')->count(),
    //     ];

    //     // User Role Distribution
    //     $userRoleDistribution = [
    //         'admin' => User::where('role', 'admin')->count(),
    //         'petugas' => User::where('role', 'petugas')->count(),
    //         'user' => User::where('role', 'user')->count(),
    //     ];

    //     return view('admin.dashboard', compact(
    //         'totalUsers',
    //         'totalAdmins',
    //         'totalPetugas',
    //         'totalNasabah',
    //         'totalSetoran',
    //         'totalSetoranPending',
    //         'totalSetoranDijemput',
    //         'totalSetoranSelesai',
    //         'totalSetoranDitolak',
    //         'totalRevenue',
    //         'monthlyRevenue',
    //         'totalKategori',
    //         'totalJenisSampah',
    //         'recentSetoran',
    //         'topKategori',
    //         'monthlySetoran',
    //         'monthlyRevenueData',
    //         'userGrowth',
    //         'todaySetoran',
    //         'todayRevenue',
    //         'todayUsers',
    //         'setoranStatus',
    //         'userRoleDistribution'
    //     ));
    // }

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

    public function storeKeuangan(Request $request)
    {
        $request->validate([
            'nominal'    => 'required|numeric|min:0',
            'keterangan' => 'required|string',
            'jenis'      => 'required|in:masuk,keluar',
        ]);

        Keuangan::create([
            'jenis'      => $request->jenis,
            'nominal'    => $request->nominal,
            'keterangan' => $request->keterangan,
            'tanggal'    => now(),
        ]);

        return back()->with('success', 'Data keuangan berhasil dicatat.');
    }
}
