@extends('layouts.component.main')

@section('title')
    Inner / Outer
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
                            Inner / Outer
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Daftar SPB yang sudah dibuat melalui Input Incoming.
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
                                Inner / Outer
                            </li>
                        </ol>
                    </div>

                </div>

            </div>
        </div>

        {{-- BACK --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-end">

                    <div class="back-action-card">

                        <div class="back-action-icon">
                            <i class="mdi mdi-package-variant-closed"></i>
                        </div>

                        <div class="back-action-content">
                            <strong>
                                Packaging Material
                            </strong>

                            <small>
                                Kembali ke menu proses PM
                            </small>
                        </div>

                        <a
                            href="{{ route('rmpm.pm') }}"
                            class="btn btn-primary px-4"
                        >
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Kembali
                        </a>

                    </div>

                </div>
            </div>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="mdi mdi-check-circle-outline me-1"></i>
                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif

        {{-- TABLE --}}
        <div class="row">
            <div class="col-12">

                <div class="card border-0 shadow-sm list-card">

                    <div class="card-header bg-transparent border-bottom">

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                            <div>
                                <h4 class="card-title mb-1">
                                    Daftar Sampling Inner / Outer
                                </h4>

                                <p class="text-muted mb-0">
                                    Data otomatis berasal dari Input Incoming.
                                </p>
                            </div>

                            <span
                                id="innerOuterDataCount"
                                class="badge bg-primary-subtle text-primary px-3 py-2"
                            >
                                {{ $incomings->total() }} Data
                            </span>

                        </div>

                    </div>

                    <div class="card-body">

                        {{-- FILTER --}}
                        <form
                            id="innerOuterFilterForm"
                            method="GET"
                            action="{{ route('rmpm.pm.inner-outer') }}"
                            class="row g-3 mb-4"
                        >
                            <div class="col-xl-5 col-md-6">

                                <label class="form-label">
                                    Pencarian
                                </label>

                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    value="{{ request('search') }}"
                                    placeholder="Cari No. SPB atau jenis material..."
                                >

                            </div>

                            <div class="col-xl-3 col-md-6">

                                <label class="form-label">
                                    Jenis Incoming
                                </label>

                                <select
                                    name="jenis_incoming"
                                    class="form-select"
                                >
                                    <option value="">
                                        Semua
                                    </option>

                                    <option
                                        value="Inner"
                                        @selected(
                                            request('jenis_incoming') === 'Inner'
                                        )
                                    >
                                        Inner
                                    </option>

                                    <option
                                        value="Outer"
                                        @selected(
                                            request('jenis_incoming') === 'Outer'
                                        )
                                    >
                                        Outer
                                    </option>
                                </select>

                            </div>

                            <div class="col-xl-2 col-md-6">

                                <label class="form-label">
                                    Status
                                </label>

                                <select
                                    name="status"
                                    class="form-select"
                                >
                                    <option value="">
                                        Semua status
                                    </option>

                                    <option
                                        value="Belum Sampling"
                                        @selected(
                                            request('status') === 'Belum Sampling'
                                        )
                                    >
                                        Belum Sampling
                                    </option>

                                    <option
                                        value="Sudah Sampling"
                                        @selected(
                                            request('status') === 'Sudah Sampling'
                                        )
                                    >
                                        Sudah Sampling
                                    </option>
                                </select>

                            </div>

                            <div class="col-xl-2 col-md-6 d-flex align-items-end gap-2">

                                <button
                                    type="submit"
                                    class="btn btn-primary flex-grow-1"
                                >
                                    <i class="mdi mdi-magnify me-1"></i>
                                    Filter
                                </button>

                                <a
                                    href="{{ route('rmpm.pm.inner-outer') }}"
                                    id="innerOuterResetFilter"
                                    class="btn btn-light"
                                    title="Reset filter"
                                >
                                    <i class="mdi mdi-refresh"></i>
                                </a>

                            </div>
                        </form>

                        <div id="innerOuterResultArea">
                            <div class="table-responsive">

                                <table class="table table-bordered table-hover align-middle">

                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">
                                            No.
                                        </th>

                                        <th>No. SPB</th>
                                        <th>Jenis Incoming</th>
                                        <th>Jenis Material</th>

                                        <th style="width: 180px;">
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
                                                str_contains($statusLower, 'sudah') ||
                                                str_contains($statusLower, 'selesai');
                                        @endphp

                                        <tr>
                                            <td>
                                                {{ $incomings->firstItem() + $loop->index }}
                                            </td>

                                            <td>
                                                <strong>
                                                    {{ $incoming->no_spb }}
                                                </strong>
                                            </td>

                                            <td>
                                                {{ $incoming->jenisIncoming?->nama ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $incoming->jenisMaterial?->nama ?? '-' }}
                                            </td>

                                            <td>
                                                @if ($sudahSampling)

                                                    <a
                                                        href="{{ route(
                                                            'rmpm.pm.inner-outer.sampling',
                                                            $incoming
                                                        ) }}"
                                                        class="btn btn-success btn-sm"
                                                    >
                                                        <i class="mdi mdi-eye-outline me-1"></i>
                                                        Lihat Data
                                                    </a>

                                                @elseif ($isDraft)

                                                    <a
                                                        href="{{ route(
                                                            'rmpm.pm.inner-outer.sampling',
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
                                                            'rmpm.pm.inner-outer.sampling',
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
                                                    Belum ada data Inner / Outer
                                                </h5>

                                                <p class="text-muted mb-0">
                                                    Buat SPB melalui menu Input Incoming terlebih dahulu.
                                                </p>
                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>
                            </table>

                        </div>

                            <div class="mt-3">
                                {{ $incomings->links() }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

@endsection

@section('styles')

<style>
    .back-action-card {
        width: 100%;
        max-width: 430px;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.07);
    }

    .back-action-icon {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #fff7ed;
        color: #f97316;
        font-size: 25px;
    }

    .back-action-content {
        flex-grow: 1;
    }

    .back-action-content strong,
    .back-action-content small {
        display: block;
    }

    .back-action-content small {
        margin-top: 2px;
        color: #64748b;
    }

    .list-card {
        border-radius: 16px;
        overflow: hidden;
    }

    .empty-state-icon {
        width: 70px;
        height: 70px;
        margin: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #f1f5f9;
        color: #94a3b8;
        font-size: 36px;
    }


    .inner-outer-loading {
        position: relative;
        min-height: 160px;
        pointer-events: none;
        opacity: 0.55;
    }

    .inner-outer-loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 38px;
        height: 38px;
        margin: -19px 0 0 -19px;
        border: 4px solid #e2e8f0;
        border-top-color: #4f46e5;
        border-radius: 50%;
        animation: innerOuterSpin 0.8s linear infinite;
        z-index: 20;
    }

    @keyframes innerOuterSpin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 575.98px) {
        .back-action-card {
            max-width: 100%;
            flex-wrap: wrap;
        }

        .back-action-card .btn {
            width: 100%;
        }
    }
</style>

@endsection


@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById(
            'innerOuterFilterForm'
        );

        const resetButton = document.getElementById(
            'innerOuterResetFilter'
        );

        const baseUrl = @json(route('rmpm.pm.inner-outer'));

        let activeRequest = null;
        let searchTimer = null;

        function getResultArea() {
            return document.getElementById(
                'innerOuterResultArea'
            );
        }

        function setLoading(isLoading) {
            const resultArea = getResultArea();

            if (!resultArea) {
                return;
            }

            resultArea.classList.toggle(
                'inner-outer-loading',
                isLoading
            );

            filterForm
                .querySelectorAll('input, select, button')
                .forEach(function (element) {
                    element.disabled = isLoading;
                });

            resetButton.classList.toggle(
                'disabled',
                isLoading
            );
        }

        async function loadTable(url, pushState = true) {
            if (activeRequest) {
                activeRequest.abort();
            }

            activeRequest = new AbortController();
            setLoading(true);

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        Accept: 'text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: activeRequest.signal
                });

                if (!response.ok) {
                    throw new Error(
                        'Data Inner / Outer gagal dimuat.'
                    );
                }

                const html = await response.text();
                const parser = new DOMParser();
                const documentResult = parser.parseFromString(
                    html,
                    'text/html'
                );

                const nextResultArea =
                    documentResult.getElementById(
                        'innerOuterResultArea'
                    );

                const nextDataCount =
                    documentResult.getElementById(
                        'innerOuterDataCount'
                    );

                if (!nextResultArea) {
                    throw new Error(
                        'Bagian tabel tidak ditemukan pada response.'
                    );
                }

                getResultArea().innerHTML =
                    nextResultArea.innerHTML;

                if (nextDataCount) {
                    document.getElementById(
                        'innerOuterDataCount'
                    ).textContent =
                        nextDataCount.textContent;
                }

                if (pushState) {
                    window.history.pushState(
                        {},
                        '',
                        url
                    );
                }
            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text:
                            error.message
                            ?? 'Data tidak berhasil dimuat.'
                    });
                } else {
                    alert(
                        error.message
                        ?? 'Data tidak berhasil dimuat.'
                    );
                }
            } finally {
                activeRequest = null;
                setLoading(false);
            }
        }

        function buildFilterUrl() {
            const formData = new FormData(filterForm);
            const query = new URLSearchParams();

            formData.forEach(function (value, key) {
                const cleanValue =
                    String(value).trim();

                if (cleanValue !== '') {
                    query.set(
                        key,
                        cleanValue
                    );
                }
            });

            const queryString = query.toString();

            return queryString
                ? `${baseUrl}?${queryString}`
                : baseUrl;
        }

        filterForm.addEventListener(
            'submit',
            function (event) {
                event.preventDefault();
                loadTable(buildFilterUrl());
            }
        );

        filterForm
            .querySelectorAll('select')
            .forEach(function (select) {
                select.addEventListener(
                    'change',
                    function () {
                        loadTable(buildFilterUrl());
                    }
                );
            });

        const searchInput = filterForm.querySelector(
            'input[name="search"]'
        );

        searchInput.addEventListener(
            'input',
            function () {
                clearTimeout(searchTimer);

                searchTimer = setTimeout(
                    function () {
                        loadTable(
                            buildFilterUrl()
                        );
                    },
                    450
                );
            }
        );

        resetButton.addEventListener(
            'click',
            function (event) {
                event.preventDefault();

                filterForm.reset();
                loadTable(baseUrl);
            }
        );

        document.addEventListener(
            'click',
            function (event) {
                const paginationLink =
                    event.target.closest(
                        '#innerOuterResultArea .pagination a'
                    );

                if (!paginationLink) {
                    return;
                }

                event.preventDefault();
                loadTable(
                    paginationLink.href
                );
            }
        );

        window.addEventListener(
            'popstate',
            function () {
                loadTable(
                    window.location.href,
                    false
                );
            }
        );
    });
</script>

@endsection