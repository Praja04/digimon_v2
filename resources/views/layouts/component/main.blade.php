<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>@yield('title') | {{ env('APP_NAME') }}</title>
    @vite(['resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/images/icon-utility/kecap.png">

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Layout config Js -->
    <script src="{{ asset('assets') }}/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets') }}/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets') }}/css/app.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets') }}/css/custom.min.css" rel="stylesheet" type="text/css" />

    <!-- Sweet Alerts -->
    <link href="{{ asset('assets') }}/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets') }}/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="{{ asset('assets') }}/js/pages/sweetalerts.init.js"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    @yield('styles')
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.component.topbar')

        @include('layouts.component.sidebar')

        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->

        <div class="main-content">

            @yield('content')

            @include('layouts.component.footer')
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets') }}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets') }}/libs/simplebar/simplebar.min.js"></script>
    <script src="{{ asset('assets') }}/libs/node-waves/waves.min.js"></script>
    <script src="{{ asset('assets') }}/libs/feather-icons/feather.min.js"></script>
    <script src="{{ asset('assets') }}/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="{{ asset('assets') }}/js/plugins.js"></script>

    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <!--select2 cdn-->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('assets') }}/js/pages/select2.init.js"></script>

    <script src="{{ asset('assets') }}/js/pages/datatables.init.js"></script>

    <!-- App js -->
    <script src="{{ asset('assets') }}/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            @if (auth()->user()->role === 'Foreman' || auth()->user()->role === 'Supervisor')

                const notifBtn = document.getElementById('page-header-notifications-dropdown');
                const notifBadge = document.getElementById('notif-badge');
                const notifContainer = document.getElementById('notification-list');
                const markAllBtn = document.getElementById('mark-all-read');

                async function loadNotifications() {
                    try {
                        const res = await fetch('/notifications/unread');
                        const data = await res.json();

                        // Update badge
                        if (data.length > 0) {
                            notifBadge.textContent = data.length;
                            notifBadge.classList.remove('d-none');
                        } else {
                            notifBadge.classList.add('d-none');
                        }

                        // Kosongkan container
                        notifContainer.innerHTML = '';

                        if (!data || data.length === 0) {
                            notifContainer.innerHTML = `
                    <div class="text-center p-4 text-muted">
                        <i class="bx bx-bell-off fs-22"></i>
                        <p class="mt-2 mb-0">Tidak ada notifikasi baru</p>
                    </div>
                `;
                            return;
                        }

                        data.forEach(n => {
                            const item = document.createElement('div');
                            item.className =
                                'text-reset notification-item d-block dropdown-item position-relative';

                            item.innerHTML = `
                    <div class="d-flex">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title bg-soft-info text-info rounded-circle fs-16">
                                <i class="bx bx-badge-check"></i>
                            </span>
                        </div>

                        <div class="flex-1">
                            <a href="/notifications" class="stretched-link">
                                <h6 class="mt-0 mb-2 lh-base">
                                    ${n.title}
                                </h6>
                            </a>

                            <div class="fs-13 text-muted">
                                <p class="mb-1">
                                    Status: <span class="text-danger">${n.status_disposition}</span>
                                </p>
                            </div>

                            <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                <span>
                                    <i class="mdi mdi-clock-outline"></i>
                                    ${new Date(n.created_at).toLocaleString('id-ID')}
                                </span>
                            </p>
                        </div>
                    </div>
                `;

                            notifContainer.appendChild(item);
                        });

                    } catch (error) {
                        console.error('Error load notifications:', error);

                        notifContainer.innerHTML = `
                <div class="text-center text-danger p-4">
                    <i class="bx bx-error-circle fs-22"></i>
                    <p class="mt-2">Gagal memuat notifikasi</p>
                </div>
            `;
                    }
                }

                async function markAllRead() {
                    try {
                        const response = await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Content-Type': 'application/json'
                            }
                        });

                        if (response.ok) {
                            await loadNotifications();

                            Swal.fire({
                                icon: 'success',
                                title: 'Semua notifikasi ditandai sudah dibaca',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    } catch (error) {
                        console.error('Error mark all read:', error);
                    }
                }

                if (notifBtn) {
                    notifBtn.addEventListener('shown.bs.dropdown', function() {
                        loadNotifications();
                    });
                }

                if (markAllBtn) {
                    markAllBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        markAllRead();
                    });
                }

                // Load awal
                loadNotifications();

                // ===== REALTIME REVERB =====
                if (window.Echo) {
                    window.Echo.channel('disposition-channel')
                        .listen('.ProcessOutsideDisposition', (e) => {

                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 4000
                            });

                            Toast.fire({
                                icon: "info",
                                title: e.title || "Disposisi Baru"
                            });

                            if ('Notification' in window && Notification.permission === 'granted') {
                                new Notification(e.title || 'Disposisi Baru', {
                                    body: e.message || 'Terdapat disposisi baru.',
                                    icon: '{{ asset('assets/images/icon-utility/kecap.png') }}'
                                });
                            }

                            loadNotifications();
                        });
                }
            @endif
        });
    </script>


    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('body').on('click', '.logout-link', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Keluar',
                    text: "Apakah kamu yakin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, keluar!',
                    cancelButtonText: 'Batal',
                }).then((willLogout) => {
                    if (willLogout.value) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        logoutUser();
                    }
                });
            })

            function logoutUser() {
                $.ajax({
                    url: "{{ route('logout') }}",
                    type: 'POST',
                    data: $('#logout-form').serialize(),
                    success: function(response) {
                        window.location.href = "{{ route('login') }}";
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" +
                            thrownError);
                    }
                });
            }
        })
    </script>

    @yield('scripts')
</body>

</html>
