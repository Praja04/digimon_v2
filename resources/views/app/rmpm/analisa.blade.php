@extends('layouts.component.main')
@section('title', 'Form Analisa')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Form Analisa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('rmpm.index') }}">RMPM</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('rmpm.show', $identitas->id) }}">Detail</a>
                                </li>
                                <li class="breadcrumb-item active">Analisa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm">

                        {{-- Card Header --}}
                        <div class="card-header bg-light border-bottom">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h5 class="mb-0 fw-semibold text-dark">Input Analisa Bahan Baku</h5>
                                <p class="mb-0 small text-muted d-flex flex-wrap gap-3">
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ri-price-tag-3-line me-1"></i>
                                        <strong>Jenis:</strong>&nbsp;{{ $identitas->jenis }}
                                    </span>
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ri-file-list-3-line me-1"></i>
                                        <strong>SPB:</strong>&nbsp;{{ $identitas->no_spb }}
                                    </span>
                                    <span class="d-inline-flex align-items-center">
                                        <i class="ri-building-line me-1"></i>
                                        <strong>Supplier:</strong>&nbsp;{{ $identitas->supplier }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- STEP 1: Setup --}}
                            <div id="setupSection">
                                <div class="alert alert-info border-info" role="alert">
                                    <strong>Info:</strong> Tentukan jenis analisa dan jumlah sampel terlebih dahulu,
                                    kemudian isi data analisa per field.
                                </div>

                                <div class="row g-3 align-items-end">
                                    @if (in_array($identitas->jenis, ['Gula Tebu', 'Gula Kelapa']))
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Jenis Analisa</label>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="analisa_type" id="shortTerm"
                                                    value="short-term">
                                                <label class="btn btn-outline-primary" for="shortTerm">Short Term</label>
                                                <input type="radio" class="btn-check" name="analisa_type" id="longTerm"
                                                    value="long-term">
                                                <label class="btn btn-outline-primary" for="longTerm">Long Term</label>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-6" id="jumlahWrapper">
                                        <input type="number" class="form-control" id="jumlahData" min="1"
                                            max="20" placeholder="Jumlah Sampel">
                                    </div>

                                    <div class="col-md-3">
                                        <button class="btn btn-primary w-100" id="btnMulai">
                                            <i class="ri-play-line me-1"></i> Mulai Input
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="{{ route('rmpm.show', $identitas->id) }}"
                                            class="btn btn-light w-100">Kembali</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <hr id="dividerForm" style="display:none;" class="my-4">

                            {{-- STEP 2: Form --}}
                            <div id="analisaSection" style="display:none;">

                                {{-- Sub-header --}}
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-primary fs-6" id="labelAnalisaType"></span>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-secondary fs-6" id="labelJumlahSampel"></span>
                                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" id="btnEditJumlah"
                                                title="Edit jumlah sampel" style="font-size:12px;line-height:1.8;">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger" id="btnReset">
                                        <i class="ri-refresh-line me-1"></i> Reset
                                    </button>
                                </div>

                                {{-- Edit jumlah inline --}}
                                <div id="editJumlahWrapper" class="mb-3 p-3 bg-light rounded border" style="display:none;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <label class="form-label mb-0 fw-semibold">Ubah Jumlah Sampel:</label>
                                        <input type="number" class="form-control form-control-sm" id="editJumlahInput"
                                            min="1" max="20" style="max-width:100px;">
                                        <button class="btn btn-sm btn-primary" id="btnApplyJumlah">Terapkan</button>
                                        <button class="btn btn-sm btn-light" id="btnCancelJumlah">Batal</button>
                                    </div>
                                    <div class="form-text mt-1">
                                        <strong>
                                            <i class="ri-alert-line me-1"></i> Data yang sudah diisi akan tetap tersimpan
                                            pada
                                            slot yang masih ada.
                                        </strong>
                                    </div>
                                </div>

                                <form id="formAnalisa" enctype="multipart/form-data" novalidate>
                                    @csrf
                                    <input type="hidden" name="id_identitas" value="{{ $identitas->id }}">
                                    <input type="hidden" name="jenis" value="{{ $identitas->jenis }}">
                                    <input type="hidden" name="analisa_type" id="hiddenAnalisaType">

                                    <div class="accordion accordion-flush" id="analisaAccordion"></div>

                                    <div class="mt-4 d-flex gap-2 justify-content-end">
                                        <a href="{{ route('rmpm.show', $identitas->id) }}" class="btn btn-light">Batal</a>
                                        <button type="submit" class="btn btn-primary" id="btnSimpan">
                                            <i class="ri-save-line me-1"></i> Simpan Analisa
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const JENIS = '{{ $identitas->jenis }}';
        const IDENTITAS_ID = '{{ $identitas->id }}';
        const DRAFT_KEY = `rmpm_analisa_${IDENTITAS_ID}`;

        let currentType = null;
        let currentJumlah = 0;

        const FIELD_DEFS = {
            'short-term': [{
                    key: 'brix[]',
                    label: 'Brix',
                    type: 'decimal',
                    unit: ''
                },
                {
                    key: 'ph[]',
                    label: 'pH',
                    type: 'decimal',
                    unit: ''
                },
                {
                    key: 'kotoran[]',
                    label: 'Kotoran',
                    type: 'decimal',
                    unit: ''
                },
                {
                    key: 'ka[]',
                    label: 'KA',
                    type: 'decimal',
                    unit: '%'
                },
                {
                    key: 'organo[]',
                    label: 'Organo',
                    type: 'text',
                    unit: ''
                },
                {
                    key: 'warna[]',
                    label: 'Warna',
                    type: 'text',
                    unit: ''
                },
                {
                    key: 'aroma[]',
                    label: 'Aroma',
                    type: 'text',
                    unit: ''
                },
                {
                    key: 'disposisi',
                    label: 'Disposisi',
                    type: 'select',
                    unit: ''
                },
            ],
            'long-term': [{
                    key: 'uji_kristal',
                    label: 'Uji Kristal',
                    type: 'crystal',
                    unit: ''
                },
                {
                    key: 'disposisi',
                    label: 'Disposisi',
                    type: 'select-long',
                    unit: ''
                },
            ],
            'garam-gula': [{
                    key: 'fisik[]',
                    label: 'Fisik',
                    type: 'text',
                    unit: ''
                },
                {
                    key: '%ka[]',
                    label: '%KA',
                    type: 'decimal',
                    unit: '%'
                },
                {
                    key: 'kotoran[]',
                    label: 'Kotoran',
                    type: 'decimal',
                    unit: ''
                },
                {
                    key: 'organo[]',
                    label: 'Organo',
                    type: 'text',
                    unit: ''
                },
                {
                    key: 'warna[]',
                    label: 'Warna',
                    type: 'text',
                    unit: ''
                },
                {
                    key: 'aroma[]',
                    label: 'Aroma',
                    type: 'text',
                    unit: ''
                },
                {
                    key: '%nacl[]',
                    label: '%NaCl',
                    type: 'decimal',
                    unit: '%'
                },
                {
                    key: 'gross_weight[]',
                    label: 'Gross Weight',
                    type: 'decimal',
                    unit: 'kg'
                },
                {
                    key: 'disposisi',
                    label: 'Disposisi',
                    type: 'select',
                    unit: ''
                },
            ],
        };

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            restoreFromDraft();

            $('input[name="analisa_type"]').on('change', function() {
                const isLong = $(this).val() === 'long-term';
                $('#jumlahWrapper').toggle(!isLong);
                if (isLong) $('#jumlahData').val('');
            });

            $('#btnMulai').on('click', handleMulai);
            $('#btnReset').on('click', handleReset);

            $('#btnEditJumlah').on('click', function() {
                $('#editJumlahInput').val(currentJumlah);
                $('#editJumlahWrapper').slideDown(150);
                $(this).hide();
            });
            $('#btnCancelJumlah').on('click', function() {
                $('#editJumlahWrapper').slideUp(150);
                $('#btnEditJumlah').show();
            });
            $('#btnApplyJumlah').on('click', handleApplyJumlah);

            $(document).on('change', '#selectUjiKristal', handleCrystalChange);
            $(document).on('input change',
                '#formAnalisa input:not([type=file]), #formAnalisa select, #formAnalisa textarea',
                saveDraft);
            $(document).on('input', '.decimal-input', function() {
                $(this).val($(this).val().replace(/[^0-9,.]/g, '').replace(/(,.*),/, '$1'));
            });
            $(document).on('input', '.upper-input', function() {
                $(this).val($(this).val().toUpperCase());
            });

            $('#formAnalisa').on('submit', handleSubmit);
        });

        function handleMulai() {
            const isGulaKristal = ['Gula Tebu', 'Gula Kelapa'].includes(JENIS);
            let type = isGulaKristal ? $('input[name="analisa_type"]:checked').val() : 'garam-gula';

            if (isGulaKristal && !type) {
                return Swal.fire({
                    icon: 'warning',
                    text: 'Pilih jenis analisa terlebih dahulu.'
                });
            }
            const jumlah = type === 'long-term' ? 1 : parseInt($('#jumlahData').val());
            if (!jumlah || jumlah <= 0) {
                return Swal.fire({
                    icon: 'warning',
                    text: 'Masukkan jumlah sampel yang valid.'
                });
            }
            startForm(type, jumlah);
        }

        function handleApplyJumlah() {
            const newJumlah = parseInt($('#editJumlahInput').val());
            if (!newJumlah || newJumlah <= 0) {
                return Swal.fire({
                    icon: 'warning',
                    text: 'Masukkan jumlah sampel yang valid.'
                });
            }
            const savedValues = collectArrayValues();

            currentJumlah = newJumlah;
            renderAccordion(currentType, currentJumlah);
            updateLabels();

            restoreArrayValues(savedValues, newJumlah);

            $('#editJumlahWrapper').slideUp(150);
            $('#btnEditJumlah').show();
            saveDraft();
        }

        function collectArrayValues() {
            const saved = {};
            $('#formAnalisa').find('input[type=text]').each(function() {
                const name = $(this).attr('name');
                if (!name || !name.endsWith('[]')) return;
                if (!saved[name]) saved[name] = [];
                saved[name].push($(this).val());
            });
            return saved;
        }

        function restoreArrayValues(saved, maxCount) {
            for (const [name, values] of Object.entries(saved)) {
                $(`[name="${name}"]`).each(function(i) {
                    if (i < maxCount && values[i] !== undefined) $(this).val(values[i]);
                });
            }
        }

        function handleReset() {
            Swal.fire({
                icon: 'question',
                title: 'Reset Form?',
                text: 'Semua data draft akan dihapus.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
            }).then(r => {
                if (r.isConfirmed) {
                    clearDraft();
                    location.reload();
                }
            });
        }

        function startForm(type, jumlah) {
            currentType = type;
            currentJumlah = jumlah;
            $('#hiddenAnalisaType').val(type);
            renderAccordion(type, jumlah);
            $('#setupSection').hide();
            $('#dividerForm, #analisaSection').show();
            updateLabels();
            saveDraft();
        }

        function updateLabels() {
            const labels = {
                'short-term': 'Short Term',
                'long-term': 'Long Term',
                'garam-gula': 'Analisa'
            };
            $('#labelAnalisaType').text(labels[currentType] || '');
            $('#labelJumlahSampel').text(currentJumlah + ' Sampel');
        }

        function renderAccordion(type, jumlah) {
            const fields = FIELD_DEFS[type] || [];
            let html = '';

            fields.forEach((field, idx) => {
                const accId = 'acc-' + field.key.replace(/[\[\]%.]/g, '-').replace(/-+/g, '-');
                const isOpen = idx === 0;
                const isSimple = ['select', 'select-long', 'crystal'].includes(field.type);

                html += `
                <div class="accordion-item border rounded mb-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button ${isOpen ? '' : 'collapsed'} bg-light fw-semibold"
                            type="button" data-bs-toggle="collapse" data-bs-target="#${accId}">
                            <span class="badge bg-primary rounded-pill d-flex align-items-center justify-content-center me-2"
                                style="width:24px;height:24px;font-size:11px;flex-shrink:0;">${idx + 1}</span>
                            ${field.label}
                            ${!isSimple ? `<span class="ms-2 badge bg-light text-secondary border" style="font-size:11px;">${jumlah} sampel</span>` : ''}
                        </button>
                    </h2>
                    <div id="${accId}" class="accordion-collapse collapse ${isOpen ? 'show' : ''}">
                        <div class="accordion-body">
                            ${renderFieldBody(field, jumlah)}
                        </div>
                    </div>
                </div>`;
            });

            $('#analisaAccordion').html(html);
        }

        function renderFieldBody(field, jumlah) {
            switch (field.type) {

                case 'select':
                    return `
                    <div class="p-3 border rounded bg-white">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Pilih Disposisi</label>
                                <select class="form-select form-select-sm" name="disposisi">
                                    <option value="">-- Pilih --</option>
                                    <option value="Release">Release</option>
                                    <option value="Reject">Reject</option>
                                </select>
                            </div>
                        </div>
                    </div>`;

                case 'select-long':
                    return `
                    <div class="p-3 border rounded bg-white">
                        <div id="disposisiPlaceholder" class="text-muted small fst-italic">
                            Disposisi akan terisi otomatis setelah memilih hasil uji kristal.
                        </div>
                        <div id="disposisiAutoWrapper" style="display:none;">
                            <label class="form-label small fw-semibold">Disposisi</label>
                            <input class="form-control form-control-sm bg-light" value="Release (otomatis)" readonly style="max-width:200px;">
                            <input type="hidden" name="disposisi" value="Release">
                        </div>
                        <div id="disposisiManualWrapper" style="display:none;">
                            <label class="form-label small fw-semibold">Pilih Disposisi</label>
                            <select class="form-select form-select-sm" name="disposisi" style="max-width:200px;">
                                <option value="">-- Pilih --</option>
                                <option value="Release">Release</option>
                                <option value="Reject">Reject</option>
                            </select>
                            <div class="form-text text-muted">Wajib diisi karena uji kristal positif.</div>
                        </div>
                    </div>`;

                case 'crystal':
                    return `
                    <div class="p-3 border rounded bg-white">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Hasil Uji Kristal</label>
                                <select class="form-select form-select-sm" name="uji_kristal" id="selectUjiKristal" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="negatif">Negatif</option>
                                    <option value="positif">Positif</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="attachmentWrapper" style="display:none;">
                                <label class="form-label small fw-semibold">Lampiran Gambar Kristal</label>
                                <input type="file" class="form-control form-control-sm" name="attachment" accept="image/*">
                                <div class="form-text">Format: JPG/PNG, maks 5MB</div>
                            </div>
                        </div>
                    </div>`;

                case 'decimal':
                case 'text': {
                    const cls = field.type === 'decimal' ? 'decimal-input' : 'upper-input';
                    const unitBadge = field.unit ?
                        `<span class="badge bg-light text-secondary border ms-1" style="font-size:10px;">${field.unit}</span>` :
                        '';
                    let html = '';
                    for (let i = 1; i <= jumlah; i++) {
                        html += `
                        <div class="p-3 border rounded bg-white mb-2">
                            <div class="d-flex align-items-center mb-2 pb-1 border-bottom">
                                <span class="badge bg-secondary rounded-pill d-flex align-items-center justify-content-center me-2"
                                    style="width:22px;height:22px;font-size:10px;flex-shrink:0;">${i}</span>
                                <strong class="text-dark small">Sampel ${i}</strong>
                                ${unitBadge}
                            </div>
                            <input type="text" class="form-control form-control-sm ${cls}"
                                name="${field.key}" placeholder="Masukkan nilai sampel ${i}">
                        </div>`;
                    }
                    return html;
                }

                default:
                    return '';
            }
        }

        function handleCrystalChange() {
            const val = $(this).val();
            $('#attachmentWrapper').toggle(val === 'positif');
            $('#disposisiPlaceholder').toggle(!val);
            $('#disposisiAutoWrapper').toggle(val === 'negatif');
            $('#disposisiManualWrapper').toggle(val === 'positif');
            if (val !== 'positif') $('input[name="attachment"]').val('');
            saveDraft();
        }

        function handleSubmit(e) {
            e.preventDefault();

            const emptyFields = [];
            const firstEmptyItem = {
                el: null,
                collapseId: null
            };

            $('#formAnalisa')
                .find('input:not([type=file]):not([type=hidden]), select')
                .filter(function() {
                    return $(this).closest('[style*="display:none"], [style*="display: none"]').length === 0;
                })
                .each(function() {
                    const val = $(this).val();
                    if (val && val.trim()) return;

                    const $accItem = $(this).closest('.accordion-item');
                    if (!$accItem.length) return;

                    const $btn = $accItem.find('.accordion-button').first();
                    const label = $btn.clone().find('span').remove().end().text().trim();

                    if (label && !emptyFields.includes(label)) emptyFields.push(label);

                    if (!firstEmptyItem.el) {
                        firstEmptyItem.el = $accItem[0];
                        firstEmptyItem.collapseId = $accItem.find('.accordion-collapse').attr('id');
                    }
                });

            if (emptyFields.length) {
                if (firstEmptyItem.collapseId) {
                    const bsEl = document.getElementById(firstEmptyItem.collapseId);
                    if (bsEl) new bootstrap.Collapse(bsEl, {
                        show: true
                    });
                }

                return Swal.fire({
                    icon: 'warning',
                    title: 'Data belum lengkap',
                    html: 'Field berikut masih kosong:<br><br>' +
                        emptyFields.map(f => `<span class="badge bg-danger me-1 mb-1">${f}</span>`).join(''),
                    confirmButtonText: 'Oke',
                });
            }

            const $disposisiVisible = $('select[name="disposisi"]').filter(':visible');
            if ($disposisiVisible.length && !$disposisiVisible.val()) {
                const bsEl = $disposisiVisible.closest('.accordion-collapse')[0];
                if (bsEl) new bootstrap.Collapse(bsEl, {
                    show: true
                });
                return Swal.fire({
                    icon: 'warning',
                    text: 'Pilih disposisi terlebih dahulu.'
                });
            }

            if (currentType === 'long-term' && $('#selectUjiKristal').val() === 'positif') {
                const file = $('input[name="attachment"]')[0];
                if (!file?.files?.length) {
                    return Swal.fire({
                        icon: 'warning',
                        text: 'Lampirkan gambar kristal.'
                    });
                }
            }

            $('.decimal-input').each(function() {
                $(this).val($(this).val().replace(',', '.'));
            });

            const urlMap = {
                'short-term': '/analisa/rmpm/short-term',
                'long-term': '/analisa/rmpm/long-term',
                'garam-gula': '/analisa/rmpm/garam-gula',
            };

            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: window.location.origin + urlMap[currentType],
                type: 'POST',
                data: new FormData(document.getElementById('formAnalisa')),
                processData: false,
                contentType: false,
                success: function() {
                    clearDraft();
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data analisa berhasil disimpan!'
                        })
                        .then(() => window.location.href = '{{ route('rmpm.show', $identitas->id) }}');
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Gagal menyimpan data.'
                    });
                },
            });
        }

        // ─────────────────────────────────────────────
        // DRAFT (localStorage)
        // ─────────────────────────────────────────────
        function saveDraft() {
            if (!currentType) return;
            const fields = {};

            $('#formAnalisa').find('input:not([type=file]), select, textarea').each(function() {
                const name = $(this).attr('name');
                if (!name || name === '_token') return;
                if (name.endsWith('[]')) {
                    if (!fields[name]) fields[name] = [];
                    fields[name].push($(this).val());
                } else {
                    fields[name] = $(this).val();
                }
            });

            localStorage.setItem(DRAFT_KEY, JSON.stringify({
                setup: {
                    type: currentType,
                    jumlah: currentJumlah
                },
                fields,
            }));
        }

        function loadFieldsFromDraft(fields) {
            // Beri waktu DOM render selesai
            setTimeout(() => {
                for (const [name, value] of Object.entries(fields)) {
                    if (['_token', 'id_identitas', 'jenis', 'analisa_type'].includes(name)) continue;
                    const $els = $(`[name="${name}"]`);
                    if (!$els.length) continue;
                    if (Array.isArray(value)) {
                        $els.each(function(i) {
                            if (value[i] !== undefined) $(this).val(value[i]);
                        });
                    } else {
                        $els.val(value);
                    }
                }
                if ($('#selectUjiKristal').length && $('#selectUjiKristal').val()) {
                    $('#selectUjiKristal').trigger('change');
                }
            }, 150);
        }

        function restoreFromDraft() {
            let draft;
            try {
                draft = JSON.parse(localStorage.getItem(DRAFT_KEY));
            } catch {
                return;
            }
            if (!draft?.setup?.type) return;

            currentType = draft.setup.type;
            currentJumlah = draft.setup.jumlah;

            if (['Gula Tebu', 'Gula Kelapa'].includes(JENIS)) {
                $(`input[name="analisa_type"][value="${currentType}"]`).prop('checked', true).trigger('change');
            }
            $('#jumlahData').val(currentJumlah);

            renderAccordion(currentType, currentJumlah);
            $('#setupSection').hide();
            $('#dividerForm, #analisaSection').show();
            updateLabels();

            if (draft.fields) loadFieldsFromDraft(draft.fields);
        }

        function clearDraft() {
            localStorage.removeItem(DRAFT_KEY);
        }
    </script>
@endsection
