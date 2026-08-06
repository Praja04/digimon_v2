@extends('layouts.component.main')

@section('title')
    Pengecekan Pouch
@endsection

@section('content')

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                    <div>
                        <h4 class="mb-sm-0">
                            Pengecekan Pouch
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Daftar incoming Pouch yang berasal dari Input Incoming.
                        </p>
                    </div>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.index') }}">
                                    RMPM
                                </a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="{{ route('rmpm.pm') }}">
                                    Packaging Material
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Pouch
                            </li>

                        </ol>
                    </div>

                </div>

            </div>
        </div>

        {{-- HEADER --}}
        <div class="pouch-header mb-4">

            <div class="pouch-header-icon">
                <i class="mdi mdi-package-variant"></i>
            </div>

            <div class="flex-grow-1">

                <span class="pouch-label">
                    PACKAGING ONLINE
                </span>

                <h2 class="mb-1 text-white">
                    Pouch
                </h2>

                <p class="mb-0 pouch-description">
                    Kelola antrian sampling dan pemeriksaan material Pouch.
                </p>

            </div>

            <a
                href="{{ route('rmpm.pm') }}"
                class="btn btn-light px-4"
            >
                <i class="mdi mdi-arrow-left me-1"></i>
                Kembali
            </a>

        </div>

        {{-- TABLE CARD --}}
        <div class="card border-0 shadow-sm pouch-card">

            <div class="card-header bg-transparent border-bottom">

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                    <div>
                        <h4 class="card-title mb-1">
                            Daftar Sampling Pouch
                        </h4>

                        <p class="text-muted mb-0">
                            Data otomatis berasal dari Input Incoming.
                        </p>
                    </div>

                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                        {{ $incomings->total() }} Data
                    </span>

                </div>

            </div>

            <div class="card-body">

                {{-- FILTER --}}
                <form
                    method="GET"
                    action="{{ route('rmpm.pm.pouch') }}"
                    class="pouch-filter-wrap mb-4"
                >
                    <div class="pouch-filter-search">
                        <label
                            for="search"
                            class="form-label"
                        >
                            Pencarian
                        </label>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Cari No. SPB atau jenis material..."
                        >
                    </div>

                    <div class="pouch-filter-status">
                        <label
                            for="status"
                            class="form-label"
                        >
                            Status
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select"
                        >
                            <option value="">
                                Semua status
                            </option>

                            <option
                                value="Belum Sampling"
                                @selected(
                                    request('status')
                                    === 'Belum Sampling'
                                )
                            >
                                Belum Sampling
                            </option>

                            <option
                                value="Sudah Sampling"
                                @selected(
                                    request('status')
                                    === 'Sudah Sampling'
                                )
                            >
                                Sudah Sampling
                            </option>
                        </select>
                    </div>

                    <div class="pouch-filter-buttons">
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="mdi mdi-magnify me-1"></i>
                            Filter
                        </button>

                        <a
                            href="{{ route('rmpm.pm.pouch') }}"
                            class="btn btn-light pouch-reset-button"
                            title="Reset filter"
                        >
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    </div>
                </form>

                {{-- TABLE --}}
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle pouch-table">

                        <thead>
                            <tr>
                                <th style="width: 70px;">
                                    No.
                                </th>

                                <th>No. SPB</th>
                                <th>Jenis Incoming</th>
                                <th>Jenis Material</th>

                                <th style="width: 210px;">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($incomings as $incoming)

                                @php
                                    $sampling =
                                        $samplingByIncoming->get(
                                            $incoming->id
                                        );

                                    $isDraft =
                                        $sampling
                                        && $sampling->status_proses
                                            === 'draft';

                                    $statusName =
                                        $incoming->samplingStatus?->nama
                                        ?? 'Belum Sampling';

                                    $statusLower =
                                        strtolower($statusName);

                                    $sudahSampling =
                                        str_contains(
                                            $statusLower,
                                            'sudah'
                                        )
                                        ||
                                        str_contains(
                                            $statusLower,
                                            'selesai'
                                        );
                                @endphp

                                <tr>

                                    <td class="text-center">
                                        {{
                                            $incomings->firstItem()
                                            + $loop->index
                                        }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $incoming->no_spb ?? '-' }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="pouch-table-text">
                                            {{
                                                $incoming
                                                    ->jenisIncoming
                                                    ?->nama
                                                ?? '-'
                                            }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="pouch-table-text">
                                            {{
                                                $incoming
                                                    ->jenisMaterial
                                                    ?->nama
                                                ?? '-'
                                            }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($sudahSampling)

                                            <div class="d-flex flex-wrap gap-1">
                                                <a
                                                    href="{{ route(
                                                        'rmpm.pm.pouch.sampling',
                                                        $incoming
                                                    ) }}"
                                                    class="btn btn-success btn-sm"
                                                >
                                                    <i class="mdi mdi-eye-outline me-1"></i>
                                                    Lihat Data
                                                </a>

                                                <button
                                                    type="button"
                                                    class="btn btn-primary btn-sm btn-pouch-qrcode"
                                                    data-id="{{ $incoming->id }}"
                                                    data-url="{{ route(
                                                        'rmpm.pm.pouch.qrcode',
                                                        $incoming,
                                                        false
                                                    ) }}"
                                                >
                                                    <i class="mdi mdi-qrcode me-1"></i>
                                                    QR Code
                                                </button>
                                            </div>

                                        @elseif ($isDraft)

                                            <a
                                                href="{{ route(
                                                    'rmpm.pm.pouch.sampling',
                                                    $incoming
                                                ) }}"
                                                class="btn btn-primary btn-sm"
                                            >
                                                <i class="mdi mdi-play-circle-outline me-1"></i>
                                                Continue
                                            </a>

                                            <span class="badge bg-warning text-dark ms-1">
                                                Draft
                                            </span>

                                        @else

                                            <a
                                                href="{{ route(
                                                    'rmpm.pm.pouch.sampling',
                                                    $incoming
                                                ) }}"
                                                class="btn btn-warning btn-sm"
                                            >
                                                <i class="mdi mdi-clock-outline me-1"></i>
                                                Mulai Sampling
                                            </a>

                                        @endif
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="5"
                                        class="text-center py-5"
                                    >

                                        <div class="empty-state-icon">
                                            <i class="mdi mdi-package-variant"></i>
                                        </div>

                                        <h5 class="mt-3 mb-1">
                                            Belum ada data Pouch
                                        </h5>

                                        <p class="text-muted mb-0">
                                            Buat data Pouch melalui Input Incoming terlebih dahulu.
                                        </p>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- PAGINATION --}}
                @if ($incomings->hasPages())
                    <div class="mt-3">
                        {{ $incomings->links() }}
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>

<div class="modal fade" id="pouchQrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-sm">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title" id="pouchQrModalLabel">
                    QR Code Pouch
                </h6>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>
            </div>

            <div
                class="modal-body text-center p-3"
                id="pouchQrPrintArea"
            >
                <div id="pouchQrImageArea"></div>

                <div
                    class="mt-2 small text-muted"
                    id="pouchQrLabelText"
                ></div>
            </div>

            <div class="modal-footer bg-light py-2">
                <button
                    type="button"
                    class="btn btn-sm btn-light"
                    data-bs-dismiss="modal"
                >
                    Tutup
                </button>

                <button
                    type="button"
                    id="btnPrintPouchQr"
                    class="btn btn-sm btn-primary"
                >
                    <i class="mdi mdi-printer"></i>
                    Cetak
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')

<style>
    .pouch-header {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 24px 26px;
        border-radius: 18px;
        background: linear-gradient(
            135deg,
            #0f766e,
            #14b8a6
        );
        box-shadow: 0 12px 28px rgba(20, 184, 166, 0.18);
    }

    .pouch-header-icon {
        width: 76px;
        height: 76px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        font-size: 42px;
    }

    .pouch-label {
        display: block;
        margin-bottom: 4px;
        color: rgba(255, 255, 255, 0.8);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .pouch-description {
        color: rgba(255, 255, 255, 0.84);
    }

    .pouch-card {
        overflow: hidden;
        border-radius: 16px;
    }

    .pouch-table thead th {
        background: #f1f5f9;
        vertical-align: middle;
        white-space: nowrap;
    }

    .pouch-table tbody td {
        color: #212529;
        font-size: 14px;
        font-weight: 400;
        vertical-align: middle;
    }

    .pouch-table-text {
        display: inline-block;
        color: #212529 !important;
        font-size: 14px !important;
        font-weight: 400 !important;
        line-height: 1.5;
        text-decoration: none !important;
    }

    .empty-state-icon {
        width: 72px;
        height: 72px;
        margin: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #ecfeff;
        color: #0f766e;
        font-size: 38px;
    }


    

    

    

    

    @media (max-width: 767.98px) {
        .pouch-header {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .pouch-header .btn {
            width: 100%;
        }
    }

    .pouch-filter-wrap {
        display: flex !important;
        align-items: flex-end !important;
        gap: 16px !important;
        width: 100% !important;
        flex-wrap: nowrap !important;
    }

    .pouch-filter-search {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }

    .pouch-filter-status {
        flex: 0 0 180px !important;
        width: 180px !important;
        min-width: 180px !important;
    }

    .pouch-filter-search,
    .pouch-filter-status {
        display: block !important;
        position: static !important;
    }

    .pouch-filter-search .form-label,
    .pouch-filter-status .form-label {
        display: block !important;
        width: 100% !important;
        margin: 0 0 8px !important;
        position: static !important;
    }

    .pouch-filter-search .form-control,
    .pouch-filter-status .form-select {
        display: block !important;
        position: static !important;
        float: none !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        height: 42px !important;
        min-height: 42px !important;
        margin: 0 !important;
        box-sizing: border-box !important;
    }

    .pouch-filter-status .form-select {
        padding: 6px 36px 6px 12px !important;
        font-size: 14px !important;
    }

    .pouch-filter-buttons {
        flex: 0 0 auto !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        position: static !important;
    }

    .pouch-filter-buttons .btn {
        position: static !important;
        float: none !important;
        height: 42px !important;
        min-height: 42px !important;
        margin: 0 !important;
        white-space: nowrap !important;
    }

    .pouch-filter-buttons .btn-primary {
        min-width: 100px !important;
        padding: 6px 16px !important;
    }

    .pouch-reset-button {
        width: 42px !important;
        min-width: 42px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    @media (max-width: 991.98px) {
        .pouch-filter-wrap {
            flex-wrap: wrap !important;
        }

        .pouch-filter-search {
            flex: 1 1 100% !important;
        }

        .pouch-filter-status {
            flex: 0 0 180px !important;
        }
    }

    @media (max-width: 575.98px) {
        .pouch-filter-wrap {
            display: block !important;
        }

        .pouch-filter-status {
            width: 100% !important;
            min-width: 0 !important;
            margin-top: 16px !important;
        }

        .pouch-filter-buttons {
            margin-top: 16px !important;
        }
    }

</style>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement =
        document.getElementById('pouchQrModal');

    const modal =
        new bootstrap.Modal(modalElement);

    const imageArea =
        document.getElementById('pouchQrImageArea');

    const labelArea =
        document.getElementById('pouchQrLabelText');

    const modalLabel =
        document.getElementById('pouchQrModalLabel');

    document.addEventListener(
        'click',
        async function (event) {
            const button =
                event.target.closest(
                    '.btn-pouch-qrcode'
                );

            if (!button) {
                return;
            }

            modalLabel.textContent =
                `QR Code - Pouch #${button.dataset.id}`;

            imageArea.innerHTML = `
                <div class="spinner-border" role="status"></div>
            `;

            labelArea.textContent =
                'Memuat QR Code...';

            modal.show();

            try {
                const response =
                    await fetch(
                        button.dataset.url,
                        {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With':
                                    'XMLHttpRequest'
                            }
                        }
                    );

                const result =
                    await response.json();

                if (
                    !response.ok
                    || result.status !== 'success'
                ) {
                    throw new Error(
                        result.message
                        ?? 'QR Code gagal dibuat.'
                    );
                }

                imageArea.innerHTML = `
                    <img
                        src="data:image/png;base64,${result.qrCode}"
                        alt="QR Code Pouch"
                        style="max-width:300px;width:100%;"
                    >
                `;

                labelArea.innerHTML = `
                    <strong>${escapeHtml(result.label)}</strong>
                `;
            } catch (error) {
                imageArea.innerHTML = `
                    <p class="text-danger mb-0">
                        QR Code gagal dimuat.
                    </p>
                `;

                labelArea.textContent =
                    error.message
                    ?? 'Terjadi kesalahan.';
            }
        }
    );

    document
        .getElementById('btnPrintPouchQr')
        .addEventListener(
            'click',
            function () {
                printPouchQr();
            }
        );

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function printPouchQr() {
        const printArea =
            document.getElementById(
                'pouchQrPrintArea'
            );

        const qrImage =
            printArea.querySelector('img');

        if (!qrImage) {
            alert('QR Code belum tersedia.');
            return;
        }

        const label =
            labelArea.textContent.trim();

        const printWindow =
            window.open(
                '',
                '_blank',
                'width=340,height=430'
            );

        if (!printWindow) {
            alert(
                'Izinkan pop-up browser untuk mencetak QR Code.'
            );
            return;
        }

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Print QR Pouch</title>
                <style>
                    @page {
                        size: 75mm 100mm;
                        margin: 0;
                    }

                    * {
                        box-sizing: border-box;
                    }

                    body {
                        width: 75mm;
                        height: 100mm;
                        margin: 0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-family: Arial, sans-serif;
                    }

                    .print-wrap {
                        width: 100%;
                        padding: 4mm;
                        text-align: center;
                    }

                    img {
                        width: 58mm;
                        height: 58mm;
                    }

                    .label {
                        margin-top: 3mm;
                        font-size: 8pt;
                        font-weight: 700;
                        word-break: break-word;
                    }
                </style>
            </head>
            <body>
                <div class="print-wrap">
                    <img
                        src="${qrImage.src}"
                        alt="QR Pouch"
                    >

                    <div class="label">
                        ${escapeHtml(label)}
                    </div>
                </div>
            </body>
            </html>
        `);

        printWindow.document.close();

        printWindow.onload = function () {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    }
});
</script>
@endsection