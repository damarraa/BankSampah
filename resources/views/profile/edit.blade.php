{{-- LOGIC: Tentukan Layout Berdasarkan Role --}}
@php
    $layout = 'layouts.user'; // Default
    if (Auth::user()->role === 'admin') {
        $layout = 'layouts.admin';
    } elseif (Auth::user()->role === 'petugas') {
        $layout = 'layouts.petugas';
    }
@endphp

@extends($layout)

@section('title', 'Edit Profil')

@push('styles')
    <style>
        /* Gunakan Style Root yang sama agar konsisten */
        :root {
            --brand: #10b981;
            --brand-dark: #059669;
            --bg: #f8fafc;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --radius: 16px;
            --danger: #ef4444;
        }

        /* Container Adjustment */
        .profile-page {
            padding-bottom: 60px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .container-fluid {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ===== HERO HEADER ===== */
        .profile-header {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            padding: 40px 0 80px;
            color: #fff;
            border-radius: 0 0 40px 40px;
            margin-bottom: -50px;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.5);
            text-align: center;
        }

        .header-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0;
        }

        .header-sub {
            opacity: 0.9;
            margin-top: 5px;
            font-size: 0.95rem;
        }

        /* ===== SETTINGS CARDS ===== */
        .settings-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .settings-card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--line);
            overflow: hidden;
        }

        .card-head {
            padding: 16px 24px;
            border-bottom: 1px solid var(--line);
            background: #fcfcfc;
            font-weight: 700;
            color: var(--ink);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-head i {
            color: var(--brand);
            font-size: 1.1rem;
        }

        .card-head.danger i {
            color: var(--danger);
        }

        .card-body {
            padding: 24px;
        }

        /* ===== OVERRIDE FORM STYLES (Agar seragam dengan desain baru) ===== */
        /* Kita menimpa style input bawaan Breeze/Tailwind di sini */

        .settings-card label {
            display: block;
            font-weight: 700;
            color: var(--ink);
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        .settings-card input[type="text"],
        .settings-card input[type="email"],
        .settings-card input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--line);
            font-size: 0.95rem;
            color: var(--ink);
            background: #f8fafc;
            transition: 0.2s;
        }

        .settings-card input:focus {
            border-color: var(--brand);
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .settings-card .text-sm {
            font-size: 0.8rem;
            color: var(--muted);
        }

        .settings-card .mt-1 {
            margin-top: 0.25rem;
        }

        .settings-card .mt-2 {
            margin-top: 0.5rem;
        }

        .settings-card .mt-6 {
            margin-top: 1.5rem;
        }

        .settings-card .block {
            display: block;
        }

        /* Buttons Override */
        .settings-card button,
        .settings-card .inline-flex {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: 0.2s;
            text-transform: none;
            /* Reset uppercase jika ada */
        }

        .settings-card button:hover {
            background: var(--brand-dark);
            transform: translateY(-1px);
        }

        /* Danger Zone Specific */
        .danger-zone button {
            background: #fef2f2;
            color: var(--danger);
            border: 1px solid #fecaca;
        }

        .danger-zone button:hover {
            background: var(--danger);
            color: #fff;
            border-color: var(--danger);
        }
    </style>
@endpush

@section('content')
    <div class="profile-page">

        {{-- Header --}}
        <div class="profile-header">
            <div class="container-fluid">
                <h1 class="header-title">Pengaturan Akun</h1>
                <p class="header-sub">Kelola informasi profil, email, dan keamanan akun Anda.</p>
            </div>
        </div>

        <div class="container-fluid">
            <div class="settings-wrapper">

                {{-- 1. Update Profile Info --}}
                <div class="settings-card">
                    <div class="card-head">
                        <i class="fa-solid fa-user-circle"></i> Informasi Profil
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- 2. Update Password --}}
                <div class="settings-card">
                    <div class="card-head">
                        <i class="fa-solid fa-lock"></i> Ganti Password
                    </div>
                    <div class="card-body">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- 3. Delete Account --}}
                <div class="settings-card danger-zone" style="border-color: #fecaca;">
                    <div class="card-head danger"
                        style="background: #fef2f2; border-bottom-color: #fecaca; color: #991b1b;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Hapus Akun
                    </div>
                    <div class="card-body">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
