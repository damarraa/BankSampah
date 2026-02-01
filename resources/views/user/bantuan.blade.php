@extends('layouts.user')
@section('title', 'Pusat Bantuan')

@push('styles')
    <style>
        /* Styling Dasar sama dengan Statistik */
        :root {
            --brand: #10b981;
            --bg: #f8fafc;
            --card: #fff;
            --ink: #0f172a;
            --line: #e2e8f0;
            --radius: 16px;
        }

        .page-header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            /* Beda warna biar fresh (Biru) */
            padding: 50px 0 90px;
            color: #fff;
            border-radius: 0 0 50px 50px;
            margin-bottom: -60px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .search-box {
            max-width: 500px;
            margin: 20px auto 0;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border-radius: 50px;
            border: none;
            font-size: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            outline: none;
            color: var(--ink);
            font-weight: 600;
        }

        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.2rem;
        }

        .container-fluid {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* CONTENT CARD */
        .help-card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--line);
            position: relative;
            z-index: 10;
            margin-bottom: 40px;
        }

        /* FAQ ACCORDION */
        .faq-item {
            border-bottom: 1px solid var(--line);
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-btn {
            width: 100%;
            text-align: left;
            padding: 18px 0;
            background: none;
            border: none;
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: .2s;
        }

        .faq-btn:hover {
            color: var(--brand);
        }

        .faq-btn .icon {
            font-size: 0.9rem;
            color: #94a3b8;
            transition: transform 0.3s;
        }

        .faq-btn.active .icon {
            transform: rotate(180deg);
            color: var(--brand);
        }

        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            padding-right: 20px;
        }

        .faq-content p {
            margin: 0 0 16px;
        }

        /* CONTACT GRID */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 600px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }
        }

        .contact-box {
            padding: 20px;
            border-radius: 16px;
            border: 1px solid var(--line);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: .2s;
            text-decoration: none;
        }

        .contact-box:hover {
            transform: translateY(-3px);
            border-color: var(--brand);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .c-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="page-header">
            <div class="container-fluid">
                <h1 style="font-weight: 800; font-size: 2rem; margin:0;">Pusat Bantuan</h1>
                <p style="opacity:0.9; margin-top:8px;">Temukan jawaban atau hubungi tim support kami.</p>

                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" class="search-input" placeholder="Cari topik bantuan (misal: pembayaran)..."
                        id="faqSearch">
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="help-card">
                <h3 style="margin:0 0 20px; font-weight:800; color:var(--ink);">Pertanyaan Umum (FAQ)</h3>

                <div id="faqList">
                    {{-- FAQ 1 --}}
                    <div class="faq-item">
                        <button class="faq-btn">
                            Bagaimana cara menyetor sampah?
                            <i class="fa-solid fa-chevron-down icon"></i>
                        </button>
                        <div class="faq-content">
                            <p>Anda bisa memilih menu "Buat Setoran" di dashboard. Pilih metode "Jemput" jika ingin petugas
                                datang, atau "Antar Sendiri" jika ingin membawa sampah ke gudang kami.</p>
                        </div>
                    </div>

                    {{-- FAQ 2 --}}
                    <div class="faq-item">
                        <button class="faq-btn">
                            Kapan uang hasil setoran cair?
                            <i class="fa-solid fa-chevron-down icon"></i>
                        </button>
                        <div class="faq-content">
                            <p>Setelah petugas menimbang dan memverifikasi sampah Anda, saldo akan otomatis masuk ke akun
                                Anda saat status setoran berubah menjadi "Selesai".</p>
                        </div>
                    </div>

                    {{-- FAQ 3 --}}
                    <div class="faq-item">
                        <button class="faq-btn">
                            Apa saja jenis sampah yang diterima?
                            <i class="fa-solid fa-chevron-down icon"></i>
                        </button>
                        <div class="faq-content">
                            <p>Kami menerima Plastik, Kertas/Karton, Logam, dan Elektronik Bekas. Detail harga per kg bisa
                                Anda lihat di halaman Dashboard bagian Katalog.</p>
                        </div>
                    </div>
                    {{-- FAQ 4 --}}
                    <div class="faq-item">
                        <button class="faq-btn">
                            Apakah ada biaya penjemputan?
                            <i class="fa-solid fa-chevron-down icon"></i>
                        </button>
                        <div class="faq-content">
                            <p>Untuk saat ini layanan penjemputan GRATIS dengan minimum berat sampah 5kg. Jika kurang dari
                                itu, disarankan menggunakan metode Antar Sendiri.</p>
                        </div>
                    </div>
                </div>
            </div>

            <h3 style="margin:0 0 20px; font-weight:800; color:var(--ink); text-align:center;">Masih butuh bantuan?</h3>

            <div class="contact-grid">
                <a href="https://wa.me/6281234567890" target="_blank" class="contact-box" style="background:#f0fdf4;">
                    <div class="c-icon" style="background:#dcfce7; color:#16a34a;"><i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; color:#166534; font-size:1.1rem;">WhatsApp</div>
                        <div style="font-size:0.85rem; color:#15803d;">Chat Admin (09:00 - 17:00)</div>
                    </div>
                </a>

                <a href="mailto:support@sampahku.id" class="contact-box" style="background:#eff6ff;">
                    <div class="c-icon" style="background:#dbeafe; color:#2563eb;"><i class="fa-regular fa-envelope"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; color:#1e40af; font-size:1.1rem;">Email</div>
                        <div style="font-size:0.85rem; color:#1d4ed8;">support@sampahku.id</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Accordion Logic
        const acc = document.getElementsByClassName("faq-btn");

        for (let i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                const panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }

        // Search Logic (Simple Filter)
        const searchInput = document.getElementById('faqSearch');
        const faqItems = document.querySelectorAll('.faq-item');

        searchInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();

            faqItems.forEach(item => {
                const text = item.querySelector('.faq-btn').innerText.toLowerCase();
                if (text.includes(term)) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        });
    </script>
@endpush
