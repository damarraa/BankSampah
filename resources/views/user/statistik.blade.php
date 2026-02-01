@extends('layouts.user')
@section('title', 'Statistik Saya')

@push('styles')
    <style>
        /* Gunakan variabel root yang sama */
        :root {
            --brand: #10b981;
            --brand-dark: #059669;
            --bg: #f8fafc;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --radius: 20px;
        }

        /* HEADER */
        .page-header {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            padding: 40px 0 80px;
            color: #fff;
            border-radius: 0 0 50px 50px;
            margin-bottom: -50px;
            position: relative;
            z-index: 1;
            text-align: center;
            box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.5);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
        }

        .page-subtitle {
            margin-top: 8px;
            opacity: 0.9;
            font-size: 1rem;
        }

        /* LAYOUT */
        .container-fluid {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* IMPACT CARDS */
        .impact-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            position: relative;
            z-index: 10;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .impact-grid {
                grid-template-columns: 1fr;
            }
        }

        .impact-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: transform 0.2s;
        }

        .impact-card:hover {
            transform: translateY(-5px);
        }

        .impact-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 16px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 1.8rem;
            background: var(--bg-icon);
            color: var(--text-icon);
        }

        .impact-val {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--ink);
            line-height: 1.2;
        }

        .impact-lbl {
            font-size: 0.85rem;
            color: var(--muted);
            font-weight: 600;
            margin-top: 4px;
        }

        /* CHARTS SECTION */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }

        @media (max-width: 900px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid var(--line);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .chart-header {
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--ink);
        }

        /* ECO BADGE */
        .eco-badge {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 2px dashed #10b981;
            border-radius: 20px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .eco-text h4 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
            color: #065f46;
        }

        .eco-text p {
            margin: 4px 0 0;
            font-size: 0.9rem;
            color: #047857;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="page-header">
            <div class="container-fluid">
                <h1 class="page-title">Statistik Saya</h1>
                <p class="page-subtitle">Lihat dampak positif yang kamu berikan untuk lingkungan & dompetmu!</p>
            </div>
        </div>

        <div class="container-fluid">
            {{-- IMPACT CARDS --}}
            <div class="impact-grid">
                <div class="impact-card">
                    <div class="impact-icon" style="--bg-icon:#ecfdf5; --text-icon:#10b981">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div class="impact-val">Rp {{ number_format($totalPendapatan ?? 0) }}</div>
                    <div class="impact-lbl">Total Cuan</div>
                </div>

                <div class="impact-card">
                    <div class="impact-icon" style="--bg-icon:#eff6ff; --text-icon:#3b82f6">
                        <i class="fa-solid fa-weight-hanging"></i>
                    </div>
                    <div class="impact-val">{{ $totalBerat ?? 0 }} kg</div>
                    <div class="impact-lbl">Sampah Didaur Ulang</div>
                </div>

                <div class="impact-card">
                    <div class="impact-icon" style="--bg-icon:#fff7ed; --text-icon:#f59e0b">
                        <i class="fa-solid fa-recycle"></i>
                    </div>
                    <div class="impact-val">{{ $totalTransaksi ?? 0 }}</div>
                    <div class="impact-lbl">Kali Setoran</div>
                </div>
            </div>

            {{-- ECO BADGE --}}
            <div class="eco-badge">
                <div style="font-size: 3rem;">🌱</div>
                <div class="eco-text">
                    <h4>Pahlawan Lingkungan!</h4>
                    <p>Dengan mendaur ulang <b>{{ $totalBerat ?? 0 }} kg</b> sampah, kamu setara dengan menyelamatkan
                        <b>{{ ceil(($totalBerat ?? 0) * 0.5) }}</b> pohon kecil! Terus semangat!</p>
                </div>
            </div>

            <div style="margin-top: 30px;"></div>

            {{-- CHARTS --}}
            <div class="charts-grid">
                {{-- Line Chart: History Pendapatan --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Riwayat Pendapatan (6 Bulan)</h3>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>

                {{-- Doughnut Chart: Komposisi Sampah --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Jenis Sampah Favorit</h3>
                    </div>
                    <div style="height: 300px; display:flex; justify-content:center;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const ctxIncome = document.getElementById('incomeChart').getContext('2d');
        new Chart(ctxIncome, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($chartIncomeData),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5]
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const ctxCat = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: ['Plastik', 'Kertas', 'Logam', 'Elektronik'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#64748b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    </script>
@endpush
