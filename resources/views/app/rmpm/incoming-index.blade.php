@extends('layouts.component.main')

@section('title')
    Input Incoming PM
@endsection

@section('content')

@php
    $masterBarangWpmMap = collect($masterBarangWpm ?? [])
        ->mapWithKeys(function ($barang) {
            $mid = (string) ($barang['mid'] ?? '');

            if ($mid === '') {
                return [];
            }

            return [
                $mid => [
                    'nama_barang' =>
                        $barang['nama_barang'] ?? '-',

                    'uom' =>
                        $barang['uom'] ?? '-',
                ],
            ];
        })
        ->all();
@endphp

<div class="page-content">
    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                    <div>
                        <h4 class="mb-sm-0">
                            Input Incoming Packaging Material
                        </h4>

                        <p class="text-muted mb-0 mt-1">
                            Buat incoming, lalu lanjutkan langsung ke proses sampling dari daftar yang sama.
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
                                Input Incoming
                            </li>

                        </ol>
                    </div>

                </div>

            </div>
        </div>

        {{-- TOMBOL KEMBALI --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-end">
                    <div class="back-action-card">
                        <div class="back-action-icon">
                            <i class="mdi mdi-package-variant-closed"></i>
                        </div>

                        <div class="back-action-content">
                            <span class="back-action-label">
                                Packaging Material
                            </span>

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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="mdi mdi-alert-circle-outline me-1"></i>

                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        @endif

        {{-- AJAX ALERT --}}
        <div id="ajaxAlertContainer"></div>

        <div
            id="editModeBanner"
            class="alert alert-warning border-0 shadow-sm d-none"
        >
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <strong>
                        <i class="mdi mdi-pencil-outline me-1"></i>
                        Mode Edit Incoming
                    </strong>

                    <div class="small mt-1">
                        Anda sedang mengubah data SPB
                        <span id="editModeSpb" class="fw-bold">-</span>.
                    </div>
                </div>

                <button
                    type="button"
                    id="btnCancelEdit"
                    class="btn btn-sm btn-outline-dark"
                >
                    <i class="mdi mdi-close me-1"></i>
                    Batal Edit
                </button>
            </div>
        </div>

        {{-- FORM --}}
        <div class="row">
            <div class="col-12">

                <div class="card border-0 shadow-sm incoming-form-card">

                    <div class="card-header bg-transparent border-bottom">

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                            <div class="d-flex align-items-center gap-3">

                                <div class="form-header-icon">
                                    <i class="mdi mdi-cloud-upload-outline"></i>
                                </div>

                                <div>
                                    <h4
                                        class="card-title mb-1"
                                        id="formTitle"
                                    >
                                        Buat Data Incoming PM
                                    </h4>

                                    <p class="text-muted mb-0">
                                        Lengkapi data incoming PM secara terstruktur.
                                    </p>
                                </div>

                            </div>

                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                Field bertanda * wajib diisi
                            </span>

                        </div>

                    </div>

                    <div class="card-body p-4">

                        <form
                            id="incomingForm"
                            method="POST"
                            action="{{ route('rmpm.pm.incoming.store') }}"
                        >
                            @csrf

                            <div id="methodContainer"></div>

                            <div class="row g-3">

                                <div class="col-12">
                                    <div class="form-section-heading">
                                        <div>
                                            <strong>Data Kedatangan</strong>
                                            <small>
                                                Informasi utama incoming material.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <label for="no_spb" class="form-label">
                                        Nomor SPB
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-file-document-outline"></i>
                                        </span>

                                        <input
                                            type="text"
                                            name="no_spb"
                                            id="no_spb"
                                            class="form-control @error('no_spb') is-invalid @enderror"
                                            value="{{ old('no_spb') }}"
                                            placeholder="Contoh: 9000724343"
                                            maxlength="100"
                                        >
                                    </div>

                                    @error('no_spb')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <label for="tanggal_kedatangan" class="form-label">
                                        Tanggal Kedatangan
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="date"
                                        name="tanggal_kedatangan"
                                        id="tanggal_kedatangan"
                                        class="form-control @error('tanggal_kedatangan') is-invalid @enderror"
                                        value="{{ old('tanggal_kedatangan', now()->format('Y-m-d')) }}"
                                    >

                                    @error('tanggal_kedatangan')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <label for="jam_kedatangan" class="form-label">
                                        Jam Kedatangan
                                    </label>

                                    <input
                                        type="time"
                                        name="jam_kedatangan"
                                        id="jam_kedatangan"
                                        class="form-control"
                                        value="{{ old('jam_kedatangan', now()->format('H:i')) }}"
                                    >
                                </div>

                                <div class="col-xl-3 col-md-6">
                                    <label for="no_mobil" class="form-label">
                                        Nomor Mobil
                                    </label>

                                    <input
                                        type="text"
                                        name="no_mobil"
                                        id="no_mobil"
                                        class="form-control"
                                        value="{{ old('no_mobil') }}"
                                        placeholder="Contoh: B 9697 FCI"
                                        maxlength="100"
                                    >
                                </div>

                                <div class="col-12 mt-2">
                                    <div class="form-section-heading">
                                        <div>
                                            <strong>Data Material & Proses</strong>
                                            <small>
                                                Jenis, supplier, quantity, dan status sampling.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <label for="jenis_incoming_id" class="form-label">
                                        Jenis Incoming
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="jenis_incoming_id"
                                        id="jenis_incoming_id"
                                        class="form-select @error('jenis_incoming_id') is-invalid @enderror"
                                    >
                                        <option value="">
                                            Pilih jenis incoming
                                        </option>

                                        @foreach ($jenisIncomings as $item)
                                            <option
                                                value="{{ $item->id }}"
                                                @selected(old('jenis_incoming_id') == $item->id)
                                            >
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('jenis_incoming_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <div
                                        id="jenisIncomingLainnyaWrapper"
                                        class="other-inline-field d-none"
                                    >
                                        <label
                                            for="jenis_incoming_lainnya"
                                            class="form-label mb-1"
                                        >
                                            Jenis Incoming Lainnya
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            name="jenis_incoming_lainnya"
                                            id="jenis_incoming_lainnya"
                                            class="form-control @error('jenis_incoming_lainnya') is-invalid @enderror"
                                            value="{{ old('jenis_incoming_lainnya') }}"
                                            placeholder="Contoh: Paper Bag, Label Sticker, dll."
                                            maxlength="255"
                                        >

                                        <small class="text-muted d-block mt-1">
                                            Isi manual karena memilih Others.
                                        </small>

                                        @error('jenis_incoming_lainnya')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <label for="jenis_material_id" class="form-label">
                                        Jenis Material
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="jenis_material_id"
                                        id="jenis_material_id"
                                        class="form-select @error('jenis_material_id') is-invalid @enderror"
                                    >
                                        <option value="">
                                            Pilih jenis material
                                        </option>

                                        @foreach ($jenisMaterials as $material)
                                            <option
                                                value="{{ $material->id }}"
                                                @selected(old('jenis_material_id') == $material->id)
                                            >
                                                {{ $material->nama }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('jenis_material_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <label for="supplier_id" class="form-label">
                                        Supplier
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        name="supplier_id"
                                        id="supplier_id"
                                        class="form-select @error('supplier_id') is-invalid @enderror"
                                    >
                                        <option value="">
                                            Pilih supplier
                                        </option>

                                        @foreach ($suppliers as $supplier)
                                            <option
                                                value="{{ $supplier->id }}"
                                                @selected(old('supplier_id') == $supplier->id)
                                            >
                                                {{ $supplier->nama ?? $supplier->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('supplier_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <div
                                        id="supplierLainnyaWrapper"
                                        class="other-inline-field d-none"
                                    >
                                        <label
                                            for="supplier_lainnya"
                                            class="form-label mb-1"
                                        >
                                            Supplier Lainnya
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            name="supplier_lainnya"
                                            id="supplier_lainnya"
                                            class="form-control @error('supplier_lainnya') is-invalid @enderror"
                                            value="{{ old('supplier_lainnya') }}"
                                            placeholder="Contoh: PT ABC Packaging"
                                            maxlength="255"
                                        >

                                        <small class="text-muted d-block mt-1">
                                            Isi manual karena memilih Others.
                                        </small>

                                        @error('supplier_lainnya')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <label for="mid" class="form-label">
                                        MID dari WPM
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div
                                        class="wpm-autosuggest"
                                        id="wpmAutosuggest"
                                    >
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-magnify"></i>
                                            </span>

                                            <input
                                                type="text"
                                                name="mid"
                                                id="mid"
                                                class="form-control @error('mid') is-invalid @enderror"
                                                value="{{ old('mid') }}"
                                                placeholder="Ketik MID atau nama barang..."
                                                maxlength="100"
                                                autocomplete="off"
                                                spellcheck="false"
                                                required
                                                role="combobox"
                                                aria-autocomplete="list"
                                                aria-expanded="false"
                                                aria-controls="wpmSuggestionList"
                                            >

                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary d-none"
                                                id="btnClearMid"
                                                title="Hapus pilihan MID"
                                            >
                                                <i class="mdi mdi-close"></i>
                                            </button>
                                        </div>

                                        <div
                                            id="wpmSuggestionList"
                                            class="wpm-suggestion-panel d-none"
                                            role="listbox"
                                        ></div>
                                    </div>

                                    <small class="text-muted d-block mt-1">
                                        Ketik MID atau nama barang, lalu pilih hasil autosuggest dari WPM.
                                    </small>

                                    @if (! empty($wpmError))
                                        <small class="text-danger d-block mt-1">
                                            <i class="mdi mdi-alert-circle-outline me-1"></i>
                                            {{ $wpmError }}
                                        </small>
                                    @endif

                                    @error('mid')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <label for="nama_barang_wpm" class="form-label">
                                        Nama Barang WPM
                                    </label>

                                    <input
                                        type="text"
                                        id="nama_barang_wpm"
                                        class="form-control bg-light"
                                        value=""
                                        placeholder="Terisi otomatis setelah MID dipilih"
                                        readonly
                                    >
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <label for="uom_wpm" class="form-label">
                                        UOM WPM
                                    </label>

                                    <input
                                        type="text"
                                        id="uom_wpm"
                                        class="form-control bg-light"
                                        value=""
                                        placeholder="Terisi otomatis"
                                        readonly
                                    >
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <label for="quantity_incoming" class="form-label">
                                        Quantity Incoming
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-package-variant"></i>
                                        </span>

                                        <input
                                            type="number"
                                            name="quantity_incoming"
                                            id="quantity_incoming"
                                            class="form-control @error('quantity_incoming') is-invalid @enderror"
                                            value="{{ old('quantity_incoming') }}"
                                            placeholder="Masukkan quantity"
                                            min="0.01"
                                            step="0.01"
                                            required
                                        >
                                    </div>

                                    @error('quantity_incoming')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-xl-4 col-md-6">
                                    <label for="jumlah_sampel" class="form-label">
                                        Jumlah Sampel
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="mdi mdi-flask-outline"></i>
                                        </span>

                                        <input
                                            type="number"
                                            name="jumlah_sampel"
                                            id="jumlah_sampel"
                                            class="form-control @error('jumlah_sampel') is-invalid @enderror"
                                            value="{{ old('jumlah_sampel') }}"
                                            placeholder="Masukkan jumlah sampel"
                                            min="1"
                                            max="50"
                                            step="1"
                                            required
                                        >
                                    </div>

                                    @error('jumlah_sampel')
                                        <div class="text-danger small mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">
                                        Status Proses
                                    </label>

                                    <div
                                        id="statusDefaultBox"
                                        class="status-default-box status-wide"
                                    >
                                        <i
                                            id="statusDefaultIcon"
                                            class="mdi mdi-clock-outline"
                                        ></i>

                                        <div>
                                            <strong id="statusDefaultText">
                                                Belum Sampling
                                            </strong>

                                            <small id="statusDefaultHelp">
                                                Status ditentukan otomatis oleh sistem.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="keterangan" class="form-label">
                                        Keterangan
                                    </label>

                                    <textarea
                                        name="keterangan"
                                        id="keterangan"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Tambahkan catatan apabila diperlukan"
                                    >{{ old('keterangan') }}</textarea>
                                </div>

                            </div>

                            <div class="form-action-bar mt-4">
                                <button
                                    type="button"
                                    class="btn btn-light border"
                                    id="btnReset"
                                >
                                    <i class="mdi mdi-refresh me-1"></i>
                                    Reset Form
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-primary px-4"
                                    id="btnSubmit"
                                >
                                    <i class="mdi mdi-content-save-outline me-1"></i>

                                    <span id="submitText">
                                        Simpan Incoming
                                    </span>
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

        {{-- LIST --}}
        <div class="row mt-4">
            <div class="col-12">
                <div id="incomingListContainer" class="position-relative">
                    @include('app.rmpm.partials.incoming-list')
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
        background: #eef2ff;
        color: #4f46e5;
        font-size: 25px;
    }

    .back-action-content {
        min-width: 0;
        flex-grow: 1;
    }

    .back-action-label,
    .back-action-content small {
        display: block;
    }

    .back-action-label {
        color: #1e293b;
        font-weight: 700;
    }

    .back-action-content small {
        color: #64748b;
        margin-top: 2px;
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

    .incoming-form-card {
        border-radius: 18px;
        overflow: hidden;
    }

    .incoming-form-card .card-body {
        width: 100%;
    }



    .form-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0 8px;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-section-heading strong,
    .form-section-heading small {
        display: block;
    }

    .form-section-heading strong {
        color: #1e293b;
        font-size: 15px;
    }

    .form-section-heading small {
        margin-top: 2px;
        color: #64748b;
        font-size: 12px;
    }

    .status-wide {
        width: 100%;
        min-height: 54px;
    }

    .form-action-bar {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 18px;
        border-top: 1px solid #e2e8f0;
    }

    @media (max-width: 575.98px) {
        .form-action-bar {
            flex-direction: column;
        }

        .form-action-bar .btn {
            width: 100%;
        }
    }

    .form-header-icon {
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        background: #ecfdf5;
        color: #14b8a6;
        font-size: 31px;
    }

    .status-default-box {
        min-height: 39px;
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 8px 13px;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        background: #eff6ff;
        color: #2563eb;
    }

    .status-default-box > i {
        font-size: 23px;
    }

    .status-default-box.status-is-success {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #16a34a;
    }

    .status-default-box.status-is-warning {
        border-color: #fde68a;
        background: #fffbeb;
        color: #d97706;
    }

    .status-default-box.status-is-info {
        border-color: #bfdbfe;
        background: #eff6ff;
        color: #2563eb;
    }

    .status-default-box strong,
    .status-default-box small {
        display: block;
    }

    .status-default-box small {
        color: #64748b;
        font-size: 11px;
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

    #incomingListContainer.is-loading::after {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 20;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.72);
    }
    #incomingListContainer.is-loading::before {
        content: '';
        position: absolute;
        top: 28px;
        left: 50%;
        z-index: 21;
        width: 34px;
        height: 34px;
        margin-left: -17px;
        border: 4px solid #e2e8f0;
        border-top-color: #4f46e5;
        border-radius: 50%;
        animation: incomingSpin .7s linear infinite;
    }
    @keyframes incomingSpin { to { transform: rotate(360deg); } }

    .other-inline-field {
        margin-top: 10px;
        padding: 10px 12px;
        border: 1px solid #dbe4f0;
        border-radius: 10px;
        background: #f8fafc;
    }

    .other-inline-field .form-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }

    .other-inline-field .form-control {
        min-height: 38px;
    }

    /* WPM AUTOSUGGEST */
    .wpm-autosuggest {
        position: relative;
        z-index: 50;
    }

    .wpm-suggestion-panel {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 1055;
        max-height: 330px;
        overflow-y: auto;
        border: 1px solid #dbe4f0;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.15);
    }

    .wpm-suggestion-item {
        width: 100%;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 0;
        border-bottom: 1px solid #eef2f7;
        background: transparent;
        text-align: left;
        color: #1e293b;
        cursor: pointer;
    }

    .wpm-suggestion-item:last-child {
        border-bottom: 0;
    }

    .wpm-suggestion-item:hover,
    .wpm-suggestion-item.is-active {
        background: #f5f7ff;
    }

    .wpm-suggestion-main {
        min-width: 0;
        flex: 1;
    }

    .wpm-suggestion-mid {
        display: block;
        margin-bottom: 3px;
        color: #172554;
        font-size: 14px;
        font-weight: 700;
    }

    .wpm-suggestion-name {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
        word-break: break-word;
    }

    .wpm-suggestion-uom {
        flex-shrink: 0;
        padding: 4px 8px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        font-size: 11px;
        font-weight: 700;
    }

    .wpm-suggestion-empty {
        padding: 16px;
        color: #64748b;
        text-align: center;
        font-size: 12px;
    }

    .wpm-suggestion-hint {
        padding: 10px 14px;
        border-top: 1px solid #eef2f7;
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
    }

    @media (max-width: 575.98px) {
        .wpm-suggestion-panel {
            max-height: 280px;
        }
    }

</style>

@endsection

@section('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('incomingForm');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnReset = document.getElementById('btnReset');
    const methodContainer = document.getElementById('methodContainer');
    const formTitle = document.getElementById('formTitle');
    const ajaxAlertContainer = document.getElementById('ajaxAlertContainer');
    const editModeBanner = document.getElementById('editModeBanner');
    const editModeSpb = document.getElementById('editModeSpb');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const statusDefaultBox = document.getElementById('statusDefaultBox');
    const statusDefaultIcon = document.getElementById('statusDefaultIcon');
    const statusDefaultText = document.getElementById('statusDefaultText');
    const statusDefaultHelp = document.getElementById('statusDefaultHelp');

    const defaultAction = @json(route('rmpm.pm.incoming.store'));
    const updateBaseUrl = @json(url('/rmpm/pm/incoming'));
    const defaultDate = @json(now()->format('Y-m-d'));
    const defaultTime = @json(now()->format('H:i'));
    const listContainer = document.getElementById('incomingListContainer');
    const midInput = document.getElementById('mid');
    const namaBarangWpmInput = document.getElementById('nama_barang_wpm');
    const uomWpmInput = document.getElementById('uom_wpm');
    const wpmAutosuggest = document.getElementById('wpmAutosuggest');
    const wpmSuggestionList = document.getElementById('wpmSuggestionList');
    const btnClearMid = document.getElementById('btnClearMid');

    const jenisIncomingSelect =
        document.getElementById('jenis_incoming_id');

    const jenisIncomingLainnyaWrapper =
        document.getElementById('jenisIncomingLainnyaWrapper');

    const jenisIncomingLainnyaInput =
        document.getElementById('jenis_incoming_lainnya');

    const supplierSelect =
        document.getElementById('supplier_id');

    const supplierLainnyaWrapper =
        document.getElementById('supplierLainnyaWrapper');

    const supplierLainnyaInput =
        document.getElementById('supplier_lainnya');

    const masterBarangWpm = @json($masterBarangWpmMap);

    const masterBarangWpmList = Object.entries(masterBarangWpm)
        .map(function ([mid, barang]) {
            return {
                mid: String(mid ?? ''),
                nama_barang: String(barang?.nama_barang ?? '-'),
                uom: String(barang?.uom ?? '-')
            };
        });

    let wpmActiveIndex = -1;
    let wpmCurrentResults = [];

    function setListLoading(loading) {
        if (!listContainer) return;
        listContainer.classList.toggle('is-loading', loading);
    }

    async function loadIncomingList(
        url,
        updateBrowserUrl = true
    ) {
        if (!listContainer) {
            return;
        }

        setListLoading(true);

        try {
            const requestUrl =
                new URL(url, window.location.origin);

            requestUrl.searchParams.set(
                'partial',
                '1'
            );

            const response = await fetch(
                requestUrl.toString(),
                {
                    method: 'GET',
                    headers: {
                        Accept: 'text/html',
                        'X-Requested-With':
                            'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error(
                    'Daftar incoming gagal dimuat.'
                );
            }

            listContainer.innerHTML =
                await response.text();

            if (updateBrowserUrl) {
                requestUrl.searchParams.delete(
                    'partial'
                );

                window.history.pushState(
                    {},
                    '',
                    requestUrl.toString()
                );
            }
        } catch (error) {
            showAlert(
                'danger',
                error.message
                    ?? 'Daftar incoming gagal dimuat.'
            );
        } finally {
            setListLoading(false);
        }
    }

    function getFilterUrl(filterForm) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            const cleanedValue = String(value).trim();
            if (cleanedValue !== '') params.append(key, cleanedValue);
        }
        const queryString = params.toString();
        return queryString ? `${filterForm.action}?${queryString}` : filterForm.action;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function clearValidationErrors() {
        form.querySelectorAll('.is-invalid').forEach(function (element) {
            element.classList.remove('is-invalid');
        });

        form.querySelectorAll('.ajax-invalid-feedback').forEach(function (element) {
            element.remove();
        });
    }

    function showAlert(type, message) {
        const icon = type === 'success'
            ? 'mdi-check-circle-outline'
            : 'mdi-alert-circle-outline';

        ajaxAlertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show">
                <i class="mdi ${icon} me-1"></i>
                ${escapeHtml(message)}
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>
        `;

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function showValidationErrors(errors) {
        clearValidationErrors();

        let firstField = null;

        Object.entries(errors ?? {}).forEach(function ([field, messages]) {
            const input = form.querySelector(`[name="${CSS.escape(field)}"]`);

            if (!input) {
                return;
            }

            input.classList.add('is-invalid');

            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback ajax-invalid-feedback';
            feedback.textContent = Array.isArray(messages)
                ? messages[0]
                : messages;

            const inputGroup = input.closest('.input-group');

            if (inputGroup) {
                inputGroup.insertAdjacentElement('afterend', feedback);
            } else {
                input.insertAdjacentElement('afterend', feedback);
            }

            if (!firstField) {
                firstField = input;
            }
        });

        if (firstField) {
            firstField.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            firstField.focus();
        }
    }

    function isEditMode() {
        return methodContainer.querySelector(
            'input[name="_method"][value="PUT"]'
        ) !== null;
    }

    function setLoading(loading) {
        btnSubmit.disabled = loading;
        btnReset.disabled = loading;

        if (loading) {
            btnSubmit.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1"></span>
                Memproses...
            `;
            return;
        }

        btnSubmit.innerHTML = `
            <i class="mdi mdi-content-save-outline me-1"></i>
            <span id="submitText">
                ${isEditMode() ? 'Perbarui Incoming' : 'Simpan Incoming'}
            </span>
        `;
    }

    function updateStatusDisplay(statusName = 'Belum Sampling') {
        const normalizedStatus =
            String(statusName ?? 'Belum Sampling').trim();

        const statusLower =
            normalizedStatus.toLowerCase();

        statusDefaultText.textContent =
            normalizedStatus || 'Belum Sampling';

        statusDefaultBox.classList.remove(
            'status-is-success',
            'status-is-warning',
            'status-is-info'
        );

        if (
            statusLower.includes('sudah')
            || statusLower.includes('selesai')
        ) {
            statusDefaultBox.classList.add(
                'status-is-success'
            );

            statusDefaultIcon.className =
                'mdi mdi-check-circle-outline';

            statusDefaultHelp.textContent =
                'Data incoming ini sudah melalui proses sampling.';

            return;
        }

        if (
            statusLower.includes('proses')
            || statusLower.includes('draft')
        ) {
            statusDefaultBox.classList.add(
                'status-is-warning'
            );

            statusDefaultIcon.className =
                'mdi mdi-progress-clock';

            statusDefaultHelp.textContent =
                'Proses sampling masih berjalan atau tersimpan sementara.';

            return;
        }

        statusDefaultBox.classList.add(
            'status-is-info'
        );

        statusDefaultIcon.className =
            'mdi mdi-clock-outline';

        statusDefaultHelp.textContent =
            'Status ditentukan otomatis oleh sistem.';
    }

    function isJenisIncomingOthers() {
        const selectedOption =
            jenisIncomingSelect?.options[
                jenisIncomingSelect.selectedIndex
            ];

        const selectedText =
            String(selectedOption?.textContent ?? '')
                .trim()
                .toLowerCase();

        return selectedText === 'others';
    }

    function updateJenisIncomingLainnya(
        clearWhenHidden = true
    ) {
        const isOthers =
            isJenisIncomingOthers();

        jenisIncomingLainnyaWrapper?.classList.toggle(
            'd-none',
            !isOthers
        );

        if (!jenisIncomingLainnyaInput) {
            return;
        }

        jenisIncomingLainnyaInput.required =
            isOthers;

        jenisIncomingLainnyaInput.disabled =
            !isOthers;

        if (
            !isOthers
            && clearWhenHidden
        ) {
            jenisIncomingLainnyaInput.value = '';
        }
    }

    function isSupplierOthers() {
        const selectedOption =
            supplierSelect?.options[
                supplierSelect.selectedIndex
            ];

        const selectedText =
            String(selectedOption?.textContent ?? '')
                .trim()
                .toLowerCase();

        return selectedText === 'others';
    }

    function updateSupplierLainnya(
        clearWhenHidden = true
    ) {
        const isOthers =
            isSupplierOthers();

        supplierLainnyaWrapper?.classList.toggle(
            'd-none',
            !isOthers
        );

        if (!supplierLainnyaInput) {
            return;
        }

        supplierLainnyaInput.required =
            isOthers;

        supplierLainnyaInput.disabled =
            !isOthers;

        if (
            !isOthers
            && clearWhenHidden
        ) {
            supplierLainnyaInput.value = '';
        }
    }

    function resetForm(scrollToTop = true) {
        form.reset();
        form.action = defaultAction;

        methodContainer.innerHTML = '';
        formTitle.textContent = 'Buat Data Incoming PM';

        editModeBanner.classList.add('d-none');
        editModeSpb.textContent = '-';

        document.getElementById('tanggal_kedatangan').value = defaultDate;
        document.getElementById('jam_kedatangan').value = defaultTime;

        updateStatusDisplay('Belum Sampling');

        if (jenisIncomingLainnyaInput) {
            jenisIncomingLainnyaInput.value = '';
        }

        updateJenisIncomingLainnya();

        if (supplierLainnyaInput) {
            supplierLainnyaInput.value = '';
        }

        updateSupplierLainnya();

        namaBarangWpmInput.value = '';
        uomWpmInput.value = '';
        btnClearMid.classList.add('d-none');
        closeWpmSuggestions();

        clearValidationErrors();
        setLoading(false);

        if (scrollToTop) {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    }

    async function parseJsonResponse(response) {
        const contentType = response.headers.get('content-type') ?? '';

        if (!contentType.includes('application/json')) {
            const body = await response.text();

            throw new Error(
                'Server tidak mengembalikan JSON. Periksa method controller edit/store/update. ' +
                body.slice(0, 120)
            );
        }

        return response.json();
    }

    btnReset.addEventListener('click', function () {
        resetForm();
    });

    btnCancelEdit.addEventListener('click', function () {
        resetForm();
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        clearValidationErrors();
        ajaxAlertContainer.innerHTML = '';
        setLoading(true);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            });

            const result = await parseJsonResponse(response);

            if (!response.ok) {
                if (response.status === 422) {
                    showValidationErrors(result.errors ?? {});

                    const firstMessage =
                        Object.values(result.errors ?? {}).flat()[0]
                        ?? 'Periksa kembali data yang diisi.';

                    showAlert('danger', firstMessage);
                    return;
                }

                throw new Error(
                    result.message ?? 'Data incoming gagal diproses.'
                );
            }

            const message =
                result.message ?? 'Data incoming berhasil disimpan.';

            showAlert('success', message);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }

            resetForm(false);
            await loadIncomingList(window.location.href, false);
        } catch (error) {
            showAlert(
                'danger',
                error.message ?? 'Terjadi kesalahan saat memproses data.'
            );

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message ?? 'Terjadi kesalahan.'
                });
            }
        } finally {
            setLoading(false);
        }
    });

    document.addEventListener('submit', function (event) {
        const filterForm = event.target.closest('#incomingFilterForm');
        if (!filterForm) return;
        event.preventDefault();
        loadIncomingList(getFilterUrl(filterForm));
    });

    document.addEventListener('click', function (event) {
        const paginationLink = event.target.closest('#incomingListContainer .pagination a');
        if (paginationLink) {
            event.preventDefault();
            loadIncomingList(paginationLink.href);
            return;
        }
        const resetLink = event.target.closest('.incoming-filter-reset');
        if (resetLink) {
            event.preventDefault();
            loadIncomingList(resetLink.href);
        }
    });

    window.addEventListener('popstate', function () {
        loadIncomingList(window.location.href, false);
    });

    /*
    |--------------------------------------------------------------------------
    | EDIT - EVENT DELEGATION
    |--------------------------------------------------------------------------
    | Tetap bekerja walaupun isi tabel berubah.
    */
    document.addEventListener('click', async function (event) {
        const editButton = event.target.closest('.btnEdit');

        if (!editButton) {
            return;
        }

        event.preventDefault();

        const url = editButton.dataset.url;

        if (!url) {
            showAlert('danger', 'URL edit tidak ditemukan pada tombol.');
            return;
        }

        clearValidationErrors();
        ajaxAlertContainer.innerHTML = '';
        editButton.disabled = true;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Memuat data...',
                allowOutsideClick: false,
                didOpen: function () {
                    Swal.showLoading();
                }
            });
        }

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await parseJsonResponse(response);

            if (!response.ok) {
                throw new Error(
                    result.message ?? 'Data incoming tidak berhasil dimuat.'
                );
            }

            const data = result.data ?? result;

            form.action = `${updateBaseUrl}/${data.id}`;

            methodContainer.innerHTML = `
                <input type="hidden" name="_method" value="PUT">
            `;

            formTitle.textContent = 'Edit Data Incoming';

            editModeBanner.classList.remove('d-none');
            editModeSpb.textContent = data.no_spb ?? '-';

            document.getElementById('no_spb').value =
                data.no_spb ?? '';

            document.getElementById('jenis_incoming_id').value =
                data.jenis_incoming_id ?? '';

            if (jenisIncomingLainnyaInput) {
                jenisIncomingLainnyaInput.value =
                    data.jenis_incoming_lainnya ?? '';
            }

            updateJenisIncomingLainnya(false);

            document.getElementById('supplier_id').value =
                data.supplier_id ?? '';

            if (supplierLainnyaInput) {
                supplierLainnyaInput.value =
                    data.supplier_lainnya ?? '';
            }

            updateSupplierLainnya(false);

            document.getElementById('jenis_material_id').value =
                data.jenis_material_id ?? '';

            document.getElementById('mid').value =
                data.mid ?? '';

            updateWpmDetail();

            document.getElementById('no_mobil').value =
                data.no_mobil ?? '';

            document.getElementById('tanggal_kedatangan').value =
                data.tanggal_kedatangan ?? '';

            document.getElementById('jam_kedatangan').value =
                data.jam_kedatangan
                    ? String(data.jam_kedatangan).slice(0, 5)
                    : '';

            document.getElementById('quantity_incoming').value =
                data.quantity_incoming ?? '';

            document.getElementById('jumlah_sampel').value =
                data.jumlah_sampel ?? '';

            updateStatusDisplay(
                data.status_name ?? 'Belum Sampling'
            );

            document.getElementById('keterangan').value =
                data.keterangan ?? '';

            setLoading(false);

            if (typeof Swal !== 'undefined') {
                Swal.close();
            }

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        } catch (error) {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }

            showAlert(
                'danger',
                error.message ?? 'Data incoming tidak berhasil dimuat.'
            );
        } finally {
            editButton.disabled = false;
        }
    });

    function normalizeWpmText(value) {
        return String(value ?? '')
            .trim()
            .toLowerCase();
    }

    function closeWpmSuggestions() {
        wpmSuggestionList.classList.add('d-none');
        wpmSuggestionList.innerHTML = '';
        midInput.setAttribute('aria-expanded', 'false');
        wpmActiveIndex = -1;
        wpmCurrentResults = [];
    }

    function setWpmDetail(mid) {
        const selectedMid = String(mid ?? '').trim();
        const selectedBarang = masterBarangWpm[selectedMid] ?? null;

        namaBarangWpmInput.value =
            selectedBarang?.nama_barang ?? '';

        uomWpmInput.value =
            selectedBarang?.uom ?? '';

        btnClearMid.classList.toggle(
            'd-none',
            selectedMid === ''
        );

        return selectedBarang;
    }

    function selectWpmItem(item) {
        if (!item) {
            return;
        }

        midInput.value = item.mid;
        setWpmDetail(item.mid);
        closeWpmSuggestions();

        midInput.classList.remove('is-invalid');

        const existingFeedback =
            midInput
                .closest('.wpm-autosuggest')
                ?.parentElement
                ?.querySelector('.ajax-invalid-feedback');

        existingFeedback?.remove();
    }

    function renderWpmSuggestions(query) {
        const keyword = normalizeWpmText(query);

        if (keyword === '') {
            closeWpmSuggestions();
            return;
        }

        const startsWithResults = [];
        const containsResults = [];

        masterBarangWpmList.forEach(function (item) {
            const mid = normalizeWpmText(item.mid);
            const name = normalizeWpmText(item.nama_barang);
            const uom = normalizeWpmText(item.uom);

            const isStartsWith =
                mid.startsWith(keyword)
                || name.startsWith(keyword);

            const isContains =
                mid.includes(keyword)
                || name.includes(keyword)
                || uom.includes(keyword);

            if (isStartsWith) {
                startsWithResults.push(item);
            } else if (isContains) {
                containsResults.push(item);
            }
        });

        wpmCurrentResults = [
            ...startsWithResults,
            ...containsResults
        ].slice(0, 12);

        wpmActiveIndex = -1;

        if (wpmCurrentResults.length === 0) {
            wpmSuggestionList.innerHTML = `
                <div class="wpm-suggestion-empty">
                    <i class="mdi mdi-magnify-close me-1"></i>
                    MID atau nama barang tidak ditemukan.
                </div>
            `;

            wpmSuggestionList.classList.remove('d-none');
            midInput.setAttribute('aria-expanded', 'true');
            return;
        }

        const itemsHtml = wpmCurrentResults
            .map(function (item, index) {
                return `
                    <button
                        type="button"
                        class="wpm-suggestion-item"
                        data-index="${index}"
                        role="option"
                    >
                        <span class="wpm-suggestion-main">
                            <span class="wpm-suggestion-mid">
                                ${escapeHtml(item.mid)}
                            </span>

                            <span class="wpm-suggestion-name">
                                ${escapeHtml(item.nama_barang)}
                            </span>
                        </span>

                        <span class="wpm-suggestion-uom">
                            ${escapeHtml(item.uom)}
                        </span>
                    </button>
                `;
            })
            .join('');

        wpmSuggestionList.innerHTML = `
            ${itemsHtml}

            <div class="wpm-suggestion-hint">
                Menampilkan maksimal 12 hasil. Ketik lebih spesifik untuk mempersempit pencarian.
            </div>
        `;

        wpmSuggestionList.classList.remove('d-none');
        midInput.setAttribute('aria-expanded', 'true');
    }

    function updateWpmActiveItem() {
        const items =
            wpmSuggestionList.querySelectorAll(
                '.wpm-suggestion-item'
            );

        items.forEach(function (item, index) {
            item.classList.toggle(
                'is-active',
                index === wpmActiveIndex
            );
        });

        if (
            wpmActiveIndex >= 0
            && items[wpmActiveIndex]
        ) {
            items[wpmActiveIndex].scrollIntoView({
                block: 'nearest'
            });
        }
    }

    function updateWpmDetail() {
        const selectedMid =
            String(midInput.value ?? '').trim();

        setWpmDetail(selectedMid);
    }

    jenisIncomingSelect?.addEventListener(
        'change',
        function () {
            updateJenisIncomingLainnya();
        }
    );

    supplierSelect?.addEventListener(
        'change',
        function () {
            updateSupplierLainnya();
        }
    );

    midInput.addEventListener('input', function () {
        const typedValue =
            String(midInput.value ?? '');

        const exactMid =
            String(typedValue).trim();

        setWpmDetail(exactMid);
        renderWpmSuggestions(typedValue);
    });

    midInput.addEventListener('focus', function () {
        if (String(midInput.value ?? '').trim() !== '') {
            renderWpmSuggestions(midInput.value);
        }
    });

    midInput.addEventListener('keydown', function (event) {
        if (
            wpmSuggestionList.classList.contains('d-none')
            || wpmCurrentResults.length === 0
        ) {
            if (
                event.key === 'ArrowDown'
                && String(midInput.value ?? '').trim() !== ''
            ) {
                renderWpmSuggestions(midInput.value);
            }

            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();

            wpmActiveIndex =
                Math.min(
                    wpmActiveIndex + 1,
                    wpmCurrentResults.length - 1
                );

            updateWpmActiveItem();
            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();

            wpmActiveIndex =
                Math.max(
                    wpmActiveIndex - 1,
                    0
                );

            updateWpmActiveItem();
            return;
        }

        if (event.key === 'Enter') {
            if (wpmActiveIndex >= 0) {
                event.preventDefault();
                selectWpmItem(
                    wpmCurrentResults[wpmActiveIndex]
                );
            }

            return;
        }

        if (event.key === 'Escape') {
            closeWpmSuggestions();
        }
    });

    wpmSuggestionList.addEventListener(
        'mousedown',
        function (event) {
            const itemButton =
                event.target.closest(
                    '.wpm-suggestion-item'
                );

            if (!itemButton) {
                return;
            }

            event.preventDefault();

            const item =
                wpmCurrentResults[
                    Number(itemButton.dataset.index)
                ];

            selectWpmItem(item);
        }
    );

    btnClearMid.addEventListener('click', function () {
        midInput.value = '';
        namaBarangWpmInput.value = '';
        uomWpmInput.value = '';
        btnClearMid.classList.add('d-none');
        closeWpmSuggestions();
        midInput.focus();
    });

    document.addEventListener('click', function (event) {
        if (!wpmAutosuggest.contains(event.target)) {
            closeWpmSuggestions();
        }
    });

    updateStatusDisplay('Belum Sampling');
    updateWpmDetail();
    updateJenisIncomingLainnya(false);
    updateSupplierLainnya(false);

    document.addEventListener('submit', function (event) {
        const deleteForm = event.target.closest('.deleteForm');

        if (!deleteForm) {
            return;
        }

        event.preventDefault();

        if (typeof Swal === 'undefined') {
            if (confirm('Hapus data incoming?')) {
                deleteForm.submit();
            }
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Hapus data incoming?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (result.isConfirmed) {
                deleteForm.submit();
            }
        });
    });
});
</script>

@endsection