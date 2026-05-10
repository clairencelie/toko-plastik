@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Buat Tagihan Baru</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('tagihan.index') }}">Tagihan</a></li>
            <li class="breadcrumb-item active">Baru</li>
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

<form action="{{ route('tagihan.store') }}" method="POST" id="tagihanForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Informasi Tagihan</h5>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">NOMOR TAGIHAN</label>
                        <input type="text" class="form-control bg-light fw-bold text-primary" value="{{ $notagihan }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">TANGGAL</label>
                        <input type="date" name="tgltagihan" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">PELANGGAN</label>
                        <select name="kodepelanggan" id="kodepelanggan" class="form-select select2-pelanggan" required>
                            <option value="">Pilih Pelanggan</option>
                            @foreach($pelanggans as $p)
                                <option value="{{ $p->kodepelanggan }}">{{ $p->namapelanggan }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="namapelanggan" id="namapelanggan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">KETERANGAN</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
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
                    SIMPAN & TERBITKAN TAGIHAN
                </button>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Daftar Piutang (Kredit)</h5>
                    <div id="arPlaceholder" class="text-center py-5 text-muted">
                        <i class="fas fa-user-clock fa-3x opacity-25 mb-3"></i>
                        <p>Pilih pelanggan untuk melihat daftar piutang</p>
                    </div>
                    
                    <div class="table-responsive d-none" id="arTableContainer">
                        <table class="table table-hover align-middle" id="arTable">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="width: 5%">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>No. Faktur</th>
                                    <th>Tgl. Jual</th>
                                    <th>Jatuh Tempo</th>
                                    <th class="text-end">Sisa Piutang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populated via JS -->
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
    $(document).ready(function() {
        $('.select2-pelanggan').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Pelanggan'
        });

        $('#kodepelanggan').change(function() {
            const customerId = $(this).val();
            const customerName = $(this).find(':selected').text();
            $('#namapelanggan').val(customerName);
            
            if (!customerId) {
                $('#arPlaceholder').removeClass('d-none');
                $('#arTableContainer').addClass('d-none');
                return;
            }

            // Fetch AR via AJAX
            $.get(`/tagihan/ar/${customerId}`, function(data) {
                $('#arPlaceholder').addClass('d-none');
                $('#arTableContainer').removeClass('d-none');
                
                let html = '';
                data.forEach((item, index) => {
                    html += `
                    <tr class="ar-row">
                        <td>
                            <input type="checkbox" name="items[${index}][selected]" value="1" class="form-check-input row-checkbox">
                            <input type="hidden" name="items[${index}][nopenjualan]" value="${item.nopenjualan}">
                            <input type="hidden" name="items[${index}][total]" value="${item.total}">
                            <input type="hidden" name="items[${index}][tunai]" value="${item.tunai}">
                            <input type="hidden" name="items[${index}][kredit]" value="${item.kredit}">
                            <input type="hidden" name="items[${index}][bayar]" value="${item.bayar}">
                            <input type="hidden" name="items[${index}][sisabayar]" value="${item.sisa}" class="item-sisa">
                        </td>
                        <td><span class="fw-bold">${item.nopenjualan}</span></td>
                        <td>${item.tglar}</td>
                        <td><span class="badge bg-light text-dark">${item.tgljatuhtempo}</span></td>
                        <td class="text-end fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(item.sisa)}</td>
                    </tr>
                    `;
                });

                if (data.length === 0) {
                    html = '<tr><td colspan="5" class="text-center py-4">Tidak ada piutang untuk pelanggan ini.</td></tr>';
                }
                
                $('#arTable tbody').html(html);
                updateGrandTotal();
            });
        });

        $(document).on('change', '.row-checkbox, #selectAll', function() {
            if ($(this).attr('id') === 'selectAll') {
                $('.row-checkbox').prop('checked', $(this).prop('checked'));
            }
            updateGrandTotal();
        });

        function updateGrandTotal() {
            let total = 0;
            $('.row-checkbox:checked').each(function() {
                const sisa = parseFloat($(this).closest('td').find('.item-sisa').val()) || 0;
                total += sisa;
            });
            $('#grandTotalText').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
        }
    });
</script>
@endpush
