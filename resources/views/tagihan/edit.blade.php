@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Edit Tagihan</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan</a></li>
            <li class="breadcrumb-item active">{{ $tagihan->notagihan }}</li>
        </ol>
    </nav>
</div>

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('tagihan.update', $tagihan->notagihan) }}" method="POST" id="tagihanForm">
    @csrf
    @method('PUT')
    <div class="row g-4">
        {{-- ============================================================ --}}
        {{-- PANEL KIRI                                                    --}}
        {{-- ============================================================ --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Informasi Tagihan</h5>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">NOMOR TAGIHAN</label>
                        <input type="text" class="form-control bg-light fw-bold text-primary"
                               value="{{ $tagihan->notagihan }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">TANGGAL</label>
                        <input type="date" name="tgltagihan" class="form-control"
                               value="{{ \Carbon\Carbon::parse($tagihan->tgltagihan)->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">SALESMAN</label>
                        <select name="salesman" class="form-select">
                            <option value="">-- Tidak ditentukan --</option>
                            @foreach($salesmen as $s)
                                <option value="{{ $s->salesman }}" {{ $tagihan->salesman == $s->salesman ? 'selected' : '' }}>
                                    {{ $s->keterangan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dropdown pilih pelanggan (single, digunakan berulang) --}}
                    <div class="mb-2">
                        <label class="form-label small fw-bold">TAMBAH PELANGGAN LAIN</label>
                        <select id="kodepelanggan" class="form-select select2-pelanggan">
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach($pelanggans as $p)
                                <option value="{{ $p->kodepelanggan }}">{{ $p->namapelanggan }}</option>
                            @endforeach
                        </select>
                        <div id="loadingAr" class="d-none mt-1 small text-primary">
                            <i class="fas fa-spinner fa-spin me-1"></i> Memuat piutang...
                        </div>
                    </div>

                    <div class="mb-4">
                        <small class="text-muted">Hilangkan centang untuk mengeluarkan transaksi dari tagihan ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">KETERANGAN</label>
                        <textarea name="keterangan" class="form-control" rows="3"
                                  placeholder="Catatan tambahan...">{{ $tagihan->keterangan }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4 bg-primary text-white">
                <div class="card-body p-4 text-center">
                    <div class="small fw-bold opacity-75 mb-1">TOTAL TAGIHAN</div>
                    <h2 class="fw-bold mb-0" id="grandTotalText">Rp 0</h2>
                </div>
            </div>

            <div class="mt-4 d-grid">
                <button type="submit" class="btn btn-primary py-3 fw-bold shadow-sm">
                    SIMPAN PERUBAHAN
                </button>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- PANEL KANAN – TABEL PIUTANG TERAKUMULASI                     --}}
        {{-- ============================================================ --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Daftar Piutang (Kredit)</h5>

                    <div id="arPlaceholder" class="text-center py-5 text-muted d-none">
                        <i class="fas fa-user-clock fa-3x opacity-25 mb-3 d-block"></i>
                        <p class="mb-0">Pilih pelanggan untuk melihat daftar piutang</p>
                    </div>

                    <div class="table-responsive" id="arTableContainer">
                        <table class="table table-hover align-middle mb-0" id="arTable">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="width:40px">
                                        <input type="checkbox" id="selectAll"
                                               class="form-check-input" title="Pilih semua">
                                    </th>
                                    <th>Pelanggan</th>
                                    <th>No. Faktur</th>
                                    <th>Tgl. Jual</th>
                                    <th>Jatuh Tempo</th>
                                    <th class="text-end">Sisa Piutang</th>
                                </tr>
                            </thead>
                            <tbody id="arTableBody">
                                @foreach($existingGroups as $group)
                                <tr class="table-secondary customer-group-header" data-customer-id="{{ $group['customerId'] }}">
                                    <td colspan="6" class="py-2 ps-3 fw-bold small">
                                        <i class="fas fa-user me-1 text-primary"></i>{{ $group['customerName'] }}
                                    </td>
                                </tr>
                                @foreach($group['rows'] as $row)
                                <tr class="ar-row" data-customer-id="{{ $group['customerId'] }}">
                                    <td>
                                        <input type="checkbox"
                                               name="items[{{ $group['customerId'] }}-{{ $loop->index }}][selected]"
                                               value="1" checked
                                               class="form-check-input row-checkbox">
                                        <input type="hidden" name="items[{{ $group['customerId'] }}-{{ $loop->index }}][nopenjualan]"  value="{{ $row['nopenjualan'] }}">
                                        <input type="hidden" name="items[{{ $group['customerId'] }}-{{ $loop->index }}][namapelanggan]" value="{{ $group['customerName'] }}">
                                        <input type="hidden" name="items[{{ $group['customerId'] }}-{{ $loop->index }}][total]"         value="{{ $row['total'] }}">
                                        <input type="hidden" name="items[{{ $group['customerId'] }}-{{ $loop->index }}][tunai]"         value="{{ $row['tunai'] }}">
                                        <input type="hidden" name="items[{{ $group['customerId'] }}-{{ $loop->index }}][kredit]"        value="{{ $row['kredit'] }}">
                                        <input type="hidden" name="items[{{ $group['customerId'] }}-{{ $loop->index }}][bayar]"         value="{{ $row['bayar'] }}">
                                        <input type="hidden" name="items[{{ $group['customerId'] }}-{{ $loop->index }}][sisabayar]"     value="{{ $row['sisabayar'] }}"
                                               class="item-sisa">
                                    </td>
                                    <td><span class="badge bg-white text-dark border">{{ $group['customerName'] }}</span></td>
                                    <td><span class="fw-bold">{{ $row['nopenjualan'] }}</span></td>
                                    <td class="small text-muted">{{ $row['tglar'] ?? '-' }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $row['tgljatuhtempo'] ?? '-' }}</span></td>
                                    <td class="text-end fw-bold">Rp {{ number_format($row['sisabayar'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // ─── state ────────────────────────────────────────────────────────────
    let itemIndex      = 0;           // global counter untuk name="items[n][...]" tambahan baru
    let loadedCustomers = {};         // { customerId: true }  — sudah dimuat

    // Tandai pelanggan yang barisnya sudah pra-terisi dari tagihan yang sedang diedit
    @foreach($existingGroups as $group)
        loadedCustomers["{{ $group['customerId'] }}"] = true;
    @endforeach

    // ─── Select2 ──────────────────────────────────────────────────────────
    $('.select2-pelanggan').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih Pelanggan'
    });

    // ─── Ketika pelanggan dipilih → muat piutangnya ───────────────────────
    $('#kodepelanggan').on('change', function () {
        const customerId   = $(this).val();
        const customerName = $(this).find(':selected').text().trim();

        if (!customerId) return;

        // Cegah load ganda untuk pelanggan yang sama
        if (loadedCustomers[customerId]) {
            alert(`Piutang "${customerName}" sudah ada di tabel.`);
            $(this).val(null).trigger('change.select2');
            return;
        }

        $('#loadingAr').removeClass('d-none');
        $('#kodepelanggan').prop('disabled', true);

        $.get('/tagihan/ar/' + customerId, function (data) {
            loadedCustomers[customerId] = true;

            const groupHeaderHtml = `
                <tr class="table-secondary customer-group-header" data-customer-id="${customerId}">
                    <td colspan="6" class="py-2 ps-3 fw-bold small">
                        <i class="fas fa-user me-1 text-primary"></i>${customerName}
                    </td>
                </tr>`;

            let rowsHtml = groupHeaderHtml;

            if (data.length === 0) {
                rowsHtml += `
                <tr class="ar-row" data-customer-id="${customerId}">
                    <td colspan="6" class="text-center py-3 text-muted small">
                        Tidak ada piutang untuk ${customerName}
                    </td>
                </tr>`;
            } else {
                data.forEach(function (item) {
                    const idx = 'new-' + (itemIndex++);
                    rowsHtml += `
                    <tr class="ar-row" data-customer-id="${customerId}">
                        <td>
                            <input type="checkbox"
                                   name="items[${idx}][selected]"
                                   value="1"
                                   class="form-check-input row-checkbox">
                            <input type="hidden" name="items[${idx}][nopenjualan]"   value="${item.nopenjualan}">
                            <input type="hidden" name="items[${idx}][namapelanggan]"  value="${customerName}">
                            <input type="hidden" name="items[${idx}][total]"          value="${item.total}">
                            <input type="hidden" name="items[${idx}][tunai]"          value="${item.tunai}">
                            <input type="hidden" name="items[${idx}][kredit]"         value="${item.kredit}">
                            <input type="hidden" name="items[${idx}][bayar]"          value="${item.bayar}">
                            <input type="hidden" name="items[${idx}][sisabayar]"      value="${item.sisa}"
                                   class="item-sisa">
                        </td>
                        <td><span class="badge bg-white text-dark border">${customerName}</span></td>
                        <td><span class="fw-bold">${item.nopenjualan}</span></td>
                        <td class="small text-muted">${item.tglar ?? '-'}</td>
                        <td>
                            <span class="badge bg-light text-dark">${item.tgljatuhtempo ?? '-'}</span>
                        </td>
                        <td class="text-end fw-bold">
                            Rp ${new Intl.NumberFormat('id-ID').format(item.sisa)}
                        </td>
                    </tr>`;
                });
            }

            $('#arPlaceholder').addClass('d-none');
            $('#arTableContainer').removeClass('d-none');

            $('#arTableBody').append(rowsHtml);
            updateGrandTotal();

        }).fail(function () {
            delete loadedCustomers[customerId];
            alert('Gagal memuat data piutang. Silakan coba lagi.');
        }).always(function () {
            $('#loadingAr').addClass('d-none');
            $('#kodepelanggan').prop('disabled', false);
        });
    });

    // ─── Checkbox selectAll ───────────────────────────────────────────────
    $('#selectAll').on('change', function () {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        updateGrandTotal();
    });

    $(document).on('change', '.row-checkbox', function () {
        updateGrandTotal();
    });

    // ─── Hitung grand total dari checkbox yang dicentang ─────────────────
    function updateGrandTotal() {
        let total = 0;
        $('.row-checkbox:checked').each(function () {
            total += parseFloat($(this).closest('td').find('.item-sisa').val()) || 0;
        });
        $('#grandTotalText').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));

        if ($('#arTableBody').children().length === 0) {
            $('#arTableContainer').addClass('d-none');
            $('#arPlaceholder').removeClass('d-none');
        }
    }

    // Hitung total awal dari baris yang sudah pra-dicentang
    updateGrandTotal();
});
</script>
@endpush
