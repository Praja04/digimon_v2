@extends('layouts.component.main')

@section('title')
    Packaging Sampling Online - {{ $jenisIncoming->nama }}
@endsection

@section('content')

<style>
    .packaging-page {
        min-height: calc(100vh - 80px);
        padding: 32px 28px 40px;
        background-color: #f4f6f9;
    }

    .packaging-container {
        width: 100%;
        max-width: 1280px;
        margin: 0 auto;
    }

    .packaging-card {
        overflow: hidden;
        border: 1px solid #e8ebef;
        border-radius: 12px;
        background-color: #ffffff;
        box-shadow: 0 5px 20px rgba(31, 45, 61, 0.06);
    }

    .packaging-card-header {
        position: relative;
        padding: 38px 32px 34px;
        border-bottom: 4px solid #dc3545;
        background-color: #ffffff;
        text-align: center;
    }

    .packaging-department {
        position: absolute;
        top: 18px;
        right: 28px;
        color: #6c757d;
        font-size: 12px;
        font-weight: 600;
    }

    .packaging-title {
        margin: 0;
        color: #1f2937;
        font-size: 26px;
        font-weight: 700;
        line-height: 1.3;
        text-transform: uppercase;
    }

    .packaging-subtitle {
        margin-top: 14px;
        color: #374151;
        font-size: 18px;
        font-weight: 600;
    }

    .packaging-card-body {
        padding: 30px 28px 32px;
    }

    .packaging-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
        padding: 18px 20px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background-color: #f8fafc;
    }

    .packaging-info-content {
        min-width: 0;
    }

    .packaging-info-title {
        margin: 0;
        color: #1f2937;
        font-size: 18px;
        font-weight: 700;
    }

    .packaging-info-text {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
    }

    .packaging-type-badge {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-width: 90px;
        padding: 9px 16px;
        border-radius: 999px;
        background-color: #0d6efd;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
    }

    .packaging-table-wrapper {
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background-color: #ffffff;
    }

    .packaging-table-scroll {
        overflow-x: auto;
    }

    .packaging-table {
        width: 100%;
        min-width: 900px;
        margin: 0;
        border-collapse: collapse;
        vertical-align: middle;
    }

    .packaging-table thead th {
        padding: 16px 18px;
        border: 0;
        background-color: #f6a062;
        color: #1f2937;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
    }

    .packaging-table tbody td {
        padding: 16px 18px;
        border-top: 1px solid #edf0f3;
        color: #374151;
        font-size: 13px;
        line-height: 1.5;
        vertical-align: middle;
    }

    .packaging-table tbody tr:first-child td {
        border-top: 0;
    }

    .packaging-table tbody tr {
        transition: background-color 0.15s ease-in-out;
    }

    .packaging-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .packaging-table .column-number {
        width: 70px;
        text-align: center;
    }

    .packaging-table .column-spb {
        width: 190px;
    }

    .packaging-table .column-type {
        width: 190px;
        text-align: center;
    }

    .packaging-table .column-material {
        min-width: 320px;
    }

    .packaging-table .column-action {
        width: 190px;
        text-align: center;
    }

    .spb-number {
        color: #111827;
        font-weight: 600;
        white-space: nowrap;
    }

    .incoming-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        padding: 7px 13px;
        border-radius: 7px;
        background-color: #e7f1ff;
        color: #0d6efd;
        font-size: 12px;
        font-weight: 600;
    }

    .material-text {
        display: block;
        color: #374151;
        word-break: break-word;
    }

    .material-empty {
        color: #9ca3af;
        font-style: italic;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-width: 135px;
        padding: 8px 13px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-belum {
        background-color: #fff3cd;
        color: #8a6500;
    }

    .status-sudah {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .packaging-empty {
        padding: 54px 24px !important;
        text-align: center;
    }

    .packaging-empty-icon {
        display: block;
        margin-bottom: 12px;
        color: #adb5bd;
        font-size: 42px;
    }

    .packaging-empty-title {
        margin-bottom: 5px;
        color: #495057;
        font-size: 16px;
        font-weight: 600;
    }

    .packaging-empty-text {
        margin: 0;
        color: #868e96;
        font-size: 13px;
    }

    .packaging-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 22px 28px;
        border-top: 1px solid #e5e7eb;
        background-color: #ffffff;
    }

    .packaging-footer-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-width: 100px;
        padding: 10px 18px;
        border: 0;
        border-radius: 7px;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition:
            transform 0.15s ease-in-out,
            opacity 0.15s ease-in-out;
    }

    .packaging-footer-button:hover {
        color: #ffffff;
        transform: translateY(-1px);
        opacity: 0.9;
    }

    .packaging-back-button {
        background-color: #fd7e14;
    }

    .packaging-home-button {
        background-color: #0d6efd;
    }

    @media (max-width: 1200px) {
        .packaging-page {
            padding: 26px 22px 34px;
        }

        .packaging-card-body {
            padding: 26px 22px 28px;
        }
    }

    @media (max-width: 768px) {
        .packaging-page {
            padding: 18px 12px 26px;
        }

        .packaging-card {
            border-radius: 9px;
        }

        .packaging-card-header {
            padding: 48px 16px 26px;
        }

        .packaging-department {
            top: 15px;
            right: 16px;
        }

        .packaging-title {
            font-size: 20px;
        }

        .packaging-subtitle {
            margin-top: 10px;
            font-size: 16px;
        }

        .packaging-card-body {
            padding: 20px 14px 24px;
        }

        .packaging-info {
            align-items: flex-start;
            flex-direction: column;
            margin-bottom: 20px;
            padding: 15px;
        }

        .packaging-type-badge {
            min-width: 84px;
        }

        .packaging-footer {
            justify-content: stretch;
            padding: 16px 14px;
        }

        .packaging-footer-button {
            flex: 1;
        }
    }
</style>

<div class="packaging-page">

    <div class="packaging-container">

        <div class="packaging-card">

            {{-- HEADER --}}
            <div class="packaging-card-header">

                <span class="packaging-department">
                    QC Field
                </span>

                <h1 class="packaging-title">
                    Packaging Sampling Online
                </h1>

                <div class="packaging-subtitle">
                    {{ $jenisIncoming->nama }}
                </div>

            </div>

            {{-- CONTENT --}}
            <div class="packaging-card-body">

                <div class="packaging-info">

                    <div class="packaging-info-content">

                        <h2 class="packaging-info-title">
                            Daftar Packaging Sampling
                        </h2>

                        <p class="packaging-info-text">
                            Menampilkan data sampling berdasarkan jenis incoming
                            yang dipilih.
                        </p>

                    </div>

                    <span class="packaging-type-badge">
                        <i class="ri-inbox-archive-line"></i>
                        {{ $jenisIncoming->nama }}
                    </span>

                </div>

                <div class="packaging-table-wrapper">

                    <div class="packaging-table-scroll">

                        <table class="packaging-table">

                            <thead>
                                <tr>

                                    <th class="column-number">
                                        No.
                                    </th>

                                    <th class="column-spb">
                                        No SPB
                                    </th>

                                    <th class="column-type">
                                        Jenis Incoming
                                    </th>

                                    <th class="column-material">
                                        Jenis Material
                                    </th>

                                    <th class="column-action">
                                        Action
                                    </th>

                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($data as $item)

                                    @php
                                        $status = strtolower(
                                            trim($item->status ?? '')
                                        );

                                        $isBelumSampling =
                                            $status === 'belum sampling';

                                        $statusClass = $isBelumSampling
                                            ? 'status-belum'
                                            : 'status-sudah';
                                    @endphp

                                    <tr>

                                        <td class="column-number">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td class="column-spb">
                                            <span class="spb-number">
                                                {{ $item->no_spb ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="column-type">
                                            <span class="incoming-badge">
                                                {{
                                                    $item->jenis_incoming
                                                    ?? $jenisIncoming->nama
                                                }}
                                            </span>
                                        </td>

                                        <td class="column-material">

                                            @if (! empty($item->jenis_material))

                                                <span class="material-text">
                                                    {{ $item->jenis_material }}
                                                </span>

                                            @else

                                                <span class="material-empty">
                                                    Material belum tersedia
                                                </span>

                                            @endif

                                        </td>

                                        <td class="column-action">

                                            <span
                                                class="status-badge {{
                                                    $statusClass
                                                }}"
                                            >

                                                @if ($isBelumSampling)

                                                    <i class="ri-time-line"></i>

                                                @else

                                                    <i class="ri-checkbox-circle-line"></i>

                                                @endif

                                                {{
                                                    $item->status
                                                    ?? 'Belum Sampling'
                                                }}

                                            </span>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td
                                            colspan="5"
                                            class="packaging-empty"
                                        >
                                            <i
                                                class="
                                                    ri-inbox-line
                                                    packaging-empty-icon
                                                "
                                            ></i>

                                            <div class="packaging-empty-title">
                                                Data belum tersedia
                                            </div>

                                            <p class="packaging-empty-text">
                                                Belum ada data packaging sampling
                                                untuk jenis
                                                {{ $jenisIncoming->nama }}.
                                            </p>
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="packaging-footer">

                <a
                    href="{{ url()->previous() }}"
                    class="
                        packaging-footer-button
                        packaging-back-button
                    "
                >
                    <i class="ri-arrow-left-line"></i>
                    Back
                </a>

                <a
                    href="{{ route('homepage.index') }}"
                    class="
                        packaging-footer-button
                        packaging-home-button
                    "
                >
                    <i class="ri-home-4-line"></i>
                    Home
                </a>

            </div>

        </div>

    </div>

</div>

@endsection