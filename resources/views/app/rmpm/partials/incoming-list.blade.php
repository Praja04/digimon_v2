<div class="card border-0 shadow-sm incoming-list-card">

                    <div class="card-header bg-transparent border-bottom">

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                            <div>
                                <h4 class="card-title mb-1">
                                    Daftar Incoming Packaging Material
                                </h4>

                                <p class="text-muted mb-0">
                                    Pantau incoming dan status proses sampling.
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

                                    @foreach ($samplingStatuses as $status)
                                        <option
                                            value="{{ $status->id }}"
                                            @selected(
                                                request('status_filter') == $status->id
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

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle">

                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 65px;">
                                            No.
                                        </th>

                                        <th>No. SPB</th>
                                        <th>Quantity Incoming</th>
                                        <th>Jenis Incoming</th>
                                        <th>MID</th>
                                        <th>Supplier</th>
                                        <th>Jenis Material</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>

                                        <th style="min-width: 155px;">
                                            Proses
                                        </th>

                                        <th style="width: 105px;">
                                            Kelola Data
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($incomings as $incoming)

                                        @php
                                            $statusName =
                                                $incoming->samplingStatus?->nama
                                                ?? 'Belum Sampling';

                                            $statusLower = strtolower($statusName);

                                            $statusClass =
                                                str_contains($statusLower, 'sudah') ||
                                                str_contains($statusLower, 'selesai')
                                                    ? 'bg-success'
                                                    : (
                                                        str_contains($statusLower, 'proses')
                                                            ? 'bg-warning text-dark'
                                                            : 'bg-info'
                                                    );

                                            $jenisName = strtolower(
                                                trim(
                                                    $incoming->jenisIncoming?->nama
                                                    ?? ''
                                                )
                                            );

                                            $samplingUrl = null;
                                            $samplingLabel = 'Mulai Sampling';
                                            $samplingIcon = 'mdi-play-circle-outline';
                                            $samplingClass = 'btn-primary';

                                            if (str_contains($jenisName, 'pouch')) {
                                                $samplingUrl = route(
                                                    'rmpm.pm.pouch.sampling',
                                                    $incoming
                                                );
                                            } elseif (
                                                str_contains($jenisName, 'karton')
                                                || str_contains($jenisName, 'kardus')
                                            ) {
                                                $samplingUrl = route(
                                                    'rmpm.pm.karton.sampling',
                                                    $incoming
                                                );
                                            } elseif (
                                                str_contains($jenisName, 'inner')
                                                || str_contains($jenisName, 'outer')
                                            ) {
                                                $samplingUrl = route(
                                                    'rmpm.pm.inner-outer.sampling',
                                                    $incoming
                                                );
                                            }

                                            if (
                                                str_contains($statusLower, 'sudah')
                                                || str_contains($statusLower, 'selesai')
                                            ) {
                                                $samplingLabel = 'Lihat Hasil';
                                                $samplingIcon = 'mdi-eye-outline';
                                                $samplingClass = 'btn-success';
                                            } elseif (
                                                str_contains($statusLower, 'proses')
                                                || str_contains($statusLower, 'draft')
                                            ) {
                                                $samplingLabel = 'Lanjutkan';
                                                $samplingIcon = 'mdi-progress-clock';
                                                $samplingClass = 'btn-warning';
                                            }
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

                                            <td>
                                                <span class="badge {{ $statusClass }}">
                                                    {{ $statusName }}
                                                </span>
                                            </td>

                                            <td>
                                                @if ($samplingUrl)
                                                    <a
                                                        href="{{ $samplingUrl }}"
                                                        class="btn {{ $samplingClass }} btn-sm"
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
                                                        title="Form sampling belum tersedia"
                                                    >
                                                        <i class="mdi mdi-alert-circle-outline me-1"></i>
                                                        Belum Tersedia
                                                    </button>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="d-flex gap-1">
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

                                                    @if (
                                                        ! str_contains($statusLower, 'sudah')
                                                        && ! str_contains($statusLower, 'selesai')
                                                    )
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
                                        </tr>

                                    @empty

                                        <tr>
                                            <td
                                                colspan="11"
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