@php
    $canManageIncoming =
        auth()->check()
        && auth()->user()?->role === 'Foreman';
@endphp

<div class="card border-0 shadow-sm incoming-list-card">

    <div class="card-header bg-transparent border-bottom">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="card-title mb-1">
                    Daftar Incoming Packaging Material
                </h4>

                <p class="text-muted mb-0">
                    Pantau incoming, draft, dan proses sampling dari satu daftar.
                </p>
            </div>

            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                {{ $incomings->total() }} Data
            </span>
        </div>
    </div>

    <div class="card-body">

        <form
            id="incomingFilterForm"
            method="GET"
            action="{{ route('rmpm.pm.incoming.create') }}"
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
                    placeholder="Cari SPB, MID, supplier, material..."
                >
            </div>

            <div class="col-xl-3 col-md-6">
                <label class="form-label">
                    Jenis Incoming
                </label>

                <select
                    name="jenis_incoming_filter"
                    class="form-select"
                >
                    <option value="">
                        Semua jenis
                    </option>

                    @foreach ($jenisIncomings as $item)
                        <option
                            value="{{ $item->id }}"
                            @selected(
                                request('jenis_incoming_filter') == $item->id
                            )
                        >
                            {{ $item->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-xl-2 col-md-6">
                <label class="form-label">
                    Status
                </label>

                <select
                    name="status_filter"
                    class="form-select"
                >
                    <option value="">
                        Semua status
                    </option>

                    <option
                        value="draft"
                        @selected(request('status_filter') === 'draft')
                    >
                        Draft
                    </option>

                    @foreach ($samplingStatuses as $status)
                        <option
                            value="{{ $status->id }}"
                            @selected(
                                (string) request('status_filter')
                                === (string) $status->id
                            )
                        >
                            {{ $status->nama }}
                        </option>
                    @endforeach
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
                    href="{{ route('rmpm.pm.incoming.create') }}"
                    class="btn btn-light incoming-filter-reset"
                    title="Reset filter"
                >
                    <i class="mdi mdi-refresh"></i>
                </a>
            </div>
        </form>

        <div class="process-legend mb-3">
            <span>
                <i class="process-dot process-dot-info"></i>
                Belum Sampling
            </span>

            <span>
                <i class="process-dot process-dot-warning"></i>
                Draft / Simpan Sementara
            </span>

            <span>
                <i class="process-dot process-dot-success"></i>
                Sudah Sampling
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle incoming-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 65px;">No.</th>
                        <th>No. SPB</th>
                        <th class="text-end">Quantity Incoming</th>
                        <th>Jenis Incoming</th>
                        <th>MID</th>
                        <th>Supplier</th>
                        <th>Jenis Material</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="min-width: 175px;">
                            Proses
                        </th>
                        @if ($canManageIncoming)
                            <th class="text-center" style="width: 105px;">
                                Kelola Data
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @forelse ($incomings as $incoming)
                        @php
                            $databaseStatusName =
                                $incoming->samplingStatus?->nama
                                ?? 'Belum Sampling';

                            $databaseStatusLower =
                                strtolower($databaseStatusName);

                            $processStatus = strtolower(
                                trim(
                                    (string) (
                                        $incoming->process_status
                                        ?? ''
                                    )
                                )
                            );

                            $isDraft =
                                $processStatus === 'draft';

                            $isFinished =
                                ! $isDraft
                                && (
                                    str_contains(
                                        $databaseStatusLower,
                                        'sudah'
                                    )
                                    || str_contains(
                                        $databaseStatusLower,
                                        'selesai'
                                    )
                                );

                            if ($isDraft) {
                                $statusName = 'Draft';
                                $statusClass =
                                    'bg-warning text-dark';
                            } elseif ($isFinished) {
                                $statusName =
                                    $databaseStatusName;
                                $statusClass =
                                    'bg-success';
                            } else {
                                $statusName =
                                    $databaseStatusName;
                                $statusClass =
                                    'bg-info';
                            }

                            $jenisName = strtolower(
                                trim(
                                    $incoming
                                        ->jenisIncoming
                                        ?->nama
                                    ?? ''
                                )
                            );

                            $samplingUrl = null;

                            if (
                                str_contains(
                                    $jenisName,
                                    'pouch'
                                )
                            ) {
                                $samplingUrl = $isFinished
                                    ? route(
                                        'rmpm.pm.pouch.resume',
                                        $incoming
                                    )
                                    : route(
                                        'rmpm.pm.pouch.sampling',
                                        $incoming
                                    );
                            } elseif (
                                str_contains(
                                    $jenisName,
                                    'karton'
                                )
                                || str_contains(
                                    $jenisName,
                                    'kardus'
                                )
                            ) {
                                $samplingUrl = route(
                                    'rmpm.pm.karton.sampling',
                                    $incoming
                                );
                            } elseif (
                                str_contains(
                                    $jenisName,
                                    'inner'
                                )
                                || str_contains(
                                    $jenisName,
                                    'outer'
                                )
                            ) {
                                $samplingUrl = route(
                                    'rmpm.pm.inner-outer.sampling',
                                    $incoming
                                );
                            }

                            if ($isDraft) {
                                $samplingLabel =
                                    'Lanjutkan';
                                $samplingIcon =
                                    'mdi-progress-clock';
                                $samplingClass =
                                    'btn-warning';
                            } elseif ($isFinished) {
                                $isPouch =
                                    str_contains(
                                        $jenisName,
                                        'pouch'
                                    );

                                $samplingLabel =
                                    $isPouch
                                        ? 'Lihat Resume'
                                        : 'Lihat Hasil';

                                $samplingIcon =
                                    $isPouch
                                        ? 'mdi-file-document-outline'
                                        : 'mdi-eye-outline';

                                $samplingClass =
                                    'btn-success';
                            } else {
                                $samplingLabel =
                                    'Mulai Sampling';
                                $samplingIcon =
                                    'mdi-play-circle-outline';
                                $samplingClass =
                                    'btn-primary';
                            }
                        @endphp

                        <tr
                            @class([
                                'incoming-row-draft' => $isDraft,
                            ])
                        >
                            <td>
                                {{ $incomings->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <strong>
                                    {{ $incoming->no_spb }}
                                </strong>

                                @if ($isDraft)
                                    <div class="small text-warning mt-1">
                                        <i class="mdi mdi-content-save-edit-outline"></i>
                                        Ada data sementara
                                    </div>
                                @endif
                            </td>

                            <td class="text-end">
                                <strong>
                                    {{
                                        $incoming->jumlah !== null
                                            ? rtrim(
                                                rtrim(
                                                    number_format(
                                                        (float) $incoming->jumlah,
                                                        2,
                                                        ',',
                                                        '.'
                                                    ),
                                                    '0'
                                                ),
                                                ','
                                            )
                                            : '-'
                                    }}
                                </strong>
                            </td>

                            <td>
                                {{ $incoming->jenisIncoming?->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $incoming->mid ?? '-' }}
                            </td>

                            <td>
                                {{
                                    $incoming->supplier?->nama
                                    ?? $incoming->supplier?->nama_supplier
                                    ?? '-'
                                }}
                            </td>

                            <td>
                                {{ $incoming->jenisMaterial?->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $incoming->tanggal_kedatangan?->format('d/m/Y') }}
                            </td>

                            <td class="text-center">
                                <span class="badge {{ $statusClass }}">
                                    @if ($isDraft)
                                        <i class="mdi mdi-content-save-edit-outline me-1"></i>
                                    @elseif ($isFinished)
                                        <i class="mdi mdi-check-circle-outline me-1"></i>
                                    @else
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                    @endif

                                    {{ $statusName }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if ($samplingUrl)
                                    <a
                                        href="{{ $samplingUrl }}"
                                        class="btn {{ $samplingClass }} btn-sm px-3"
                                        title="{{ $samplingLabel }}"
                                    >
                                        <i class="mdi {{ $samplingIcon }} me-1"></i>
                                        {{ $samplingLabel }}
                                    </a>
                                @else
                                    <button
                                        type="button"
                                        class="btn btn-secondary btn-sm"
                                        disabled
                                    >
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                                        Belum Tersedia
                                    </button>
                                @endif
                            </td>

                            @if ($canManageIncoming)
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button
                                            type="button"
                                            class="btn btn-outline-warning btn-sm btnEdit"
                                            data-url="{{ route(
                                                'rmpm.pm.incoming.edit',
                                                $incoming
                                            ) }}"
                                            title="Edit Incoming"
                                        >
                                            <i class="mdi mdi-pencil"></i>
                                        </button>

                                        @if (! $isDraft && ! $isFinished)
                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'rmpm.pm.incoming.destroy',
                                                    $incoming
                                                ) }}"
                                                class="deleteForm"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-outline-danger btn-sm"
                                                    title="Hapus Incoming"
                                                >
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="{{ $canManageIncoming ? 11 : 10 }}"
                                class="text-center py-5"
                            >
                                <div class="empty-state-icon">
                                    <i class="mdi mdi-package-variant"></i>
                                </div>

                                <h5 class="mt-3 mb-1">
                                    Belum ada data incoming
                                </h5>

                                <p class="text-muted mb-0">
                                    Isi form di atas untuk membuat incoming pertama.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 incoming-pagination">
            {{ $incomings->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
.incoming-list-card {
    border-radius: 18px;
    overflow: hidden;
}

.incoming-table thead th {
    white-space: nowrap;
    vertical-align: middle;
}

.incoming-table tbody tr {
    transition:
        background-color .15s ease,
        box-shadow .15s ease;
}

.incoming-table tbody tr:hover {
    background: #f8fafc;
}

.incoming-table td {
    white-space: nowrap;
}

.incoming-table td:nth-child(6),
.incoming-table td:nth-child(7) {
    min-width: 130px;
    white-space: normal;
}

.incoming-table .btn {
    min-height: 31px;
}

.incoming-row-draft {
    background: #fffdf5;
}

.incoming-row-draft:hover {
    background: #fffbeb !important;
}

.process-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    color: #64748b;
    font-size: 12px;
}

.process-legend span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.process-dot {
    width: 9px;
    height: 9px;
    display: inline-block;
    border-radius: 50%;
}

.process-dot-info {
    background: #0ea5e9;
}

.process-dot-warning {
    background: #f59e0b;
}

.process-dot-success {
    background: #22c55e;
}
</style>