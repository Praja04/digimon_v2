@extends('layouts.component.main')
@section('title', 'Notifikasi')
@section('styles')
    <style>
        .notif-item {
            border-left: 3px solid transparent;
            transition: all 0.2s ease-in-out;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid #f1f1f1;
        }

        .notif-item:last-child {
            border-bottom: none;
        }

        .notif-item.unread-notif {
            border-left: 5px solid #007bff;
            background-color: #f8faff;
            font-weight: 500;
            color: #212529 !important;
        }

        .notif-item.read-notif {
            background-color: #ffffff;
            border-left: 3px solid #dee2e6;
            opacity: 0.85;
            color: #6c757d !important;
        }

        .notif-item:hover {
            background-color: #f1f5f9;
        }

        .unread-notif:hover {
            background-color: #e5f0ff;
        }

        .notif-title-text {
            font-weight: 600;
            margin-right: 0.5rem;
            line-height: 1.2;
        }

        .notif-po-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.1rem 0.4rem;
            border-radius: 0.25rem;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            white-space: nowrap;
            line-height: 1;
        }

        .read-notif .notif-title-text {
            color: #6c757d !important;
        }

        .unread-notif .notif-title-text {
            color: #212529 !important;
        }
    </style>
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">@yield('title')</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Menu</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card bg-primary-gradient p-4 mb-4 shadow rounded-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-1 fw-bold">🔔 Kotak Notifikasi</h4>
                                <p class="mb-0 opacity-75">Kelola semua pesan dan peringatan sistem Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card shadow-sm border rounded-3">
                        <div class="card-header d-flex justify-content-between align-items-center py-3 bg-white">
                            <h5 class="mb-0 fw-semibold text-dark">Daftar Pesan</h5>
                            <form id="markAllReadForm" method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link text-primary p-0">
                                    <i class="ri-check-double-line me-1"></i> Tandai Semua Dibaca
                                </button>
                            </form>
                        </div>

                        <div class="list-group list-group-flush p-2">
                            @if ($notifications->isEmpty())
                                <div class="text-center text-muted py-5">
                                    <i class="ri-check-double-line fs-1 mb-3 d-block text-success"></i>
                                    <h6 class="fw-bold">Tidak ada notifikasi.</h6>
                                    <p class="mb-0 text-sm">Kotak masuk Anda bersih.</p>
                                </div>
                            @else
                                @foreach ($notifications as $notif)
                                    @php
                                        $isUnread = $notif->status === 'unread';
                                        $disposisiBadge = match (strtoupper($notif->status_disposition)) {
                                            'NOT OK' => 'danger',
                                            'OK' => 'success',
                                            default => 'secondary',
                                        };
                                        $messageText = $notif->message ?? null;
                                    @endphp
                                    {{-- PENERAPAN CLASS READ/UNREAD --}}
                                    <a href="javascript:void(0);"
                                        class="list-group-item d-flex justify-content-between align-items-center notif-item {{ $isUnread ? 'unread-notif' : 'read-notif' }}"
                                        onclick="markReadAndRedirect('{{ $notif->id }}')">

                                        <div class="d-flex align-items-center flex-grow-1">
                                            <div class="me-3">
                                                @if ($isUnread)
                                                    <i class="ri-mail-unread-fill fs-5 text-primary"></i>
                                                @else
                                                    <i class="ri-mail-open-line fs-5 opacity-75 text-secondary"></i>
                                                @endif
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center">
                                                    <span class="notif-title-text">
                                                        {{ $notif->title }}
                                                    </span>
                                                    <span class="notif-po-badge">
                                                        @if ($notif->productionBatch && $notif->productionBatch->po_number)
                                                            No. PO: {{ $notif->productionBatch->po_number }}
                                                        @endif
                                                    </span>
                                                </div>

                                                <small class="d-block mt-1">
                                                    <span
                                                        class="badge bg-{{ $disposisiBadge }} bg-opacity-75 rounded-pill me-2">
                                                        {{ $notif->status_disposition }}
                                                    </span>
                                                    <span class="text-muted">
                                                        <i class="ri-time-line me-1"></i>
                                                        {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                                    </span>
                                                </small>

                                                @if (!empty($messageText))
                                                    <div
                                                        class="mt-1 p-2 border-start border-3 border-warning bg-light rounded-sm">
                                                        <small class="fw-semibold text-warning">Catatan:</small>
                                                        <small class="d-block text-dark">{{ $messageText }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Penanda BARU diletakkan di kanan --}}
                                        @if ($isUnread)
                                            <span class="text-danger fw-bolder ms-3 text-uppercase"
                                                style="font-size: 0.7rem;">NEW</span>
                                        @endif
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $notifications->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function markReadAndRedirect(id) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ?
                document.querySelector('meta[name="csrf-token"]').content : '';

            try {
                const response = await fetch(`{{ url('notifications/mark-read') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                // Redirect ke halaman batch yang sesuai
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.reload();
                }
            } catch (err) {
                console.error('Gagal update status:', err);
                // Tetap reload jika ada error
                window.location.reload();
            }
        }

        $(document).ready(function() {
            $('#markAllReadForm').on('submit', function(e) {
                e.preventDefault();

                const url = "{{ route('notifications.markAllAsRead') }}";
                const csrfToken = $('input[name="_token"]').val();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Semua notifikasi berhasil ditandai dibaca.',
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            alert('Semua notifikasi berhasil ditandai dibaca.');
                            window.location.reload();
                        }

                    },
                    error: function(xhr) {
                        alert('Gagal menandai semua notifikasi dibaca.');
                        console.error('AJAX Error:', xhr);
                    }
                });
            });
        });
    </script>
@endsection
