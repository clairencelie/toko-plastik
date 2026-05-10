@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Catat Pelunasan Hutang</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('kaskeluar.index') }}">Pelunasan Hutang</a></li>
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

<form action="{{ route('kaskeluar.store') }}" method="POST" id="kaskeluarForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Informasi Kas</h5>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">NOMOR KAS KELUAR</label>
                        <input type="text" class="form-control bg-light fw-bold text-primary" value="{{ $nokaskeluar }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">TANGGAL BAYAR</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">SUPPLIER</label>
                        <select name="supplier_id" id="supplier_id" class="form-select select2-supplier" required>
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->supplier }}">{{ $s->keterangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">KETERANGAN (OPSIONAL)</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Pembayaran faktur bulan Januari..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4 bg-danger text-white">
                <div class="card-body p-4 text-center">
                    <div class="small fw-bold opacity-75 mb-1">TOTAL PEMBAYARAN</div>
                    <h2 class="fw-bold mb-0" id="grandTotalText">Rp 0</h2>
                </div>
            </div>
            
            <div class="mt-4 d-grid">
                <button type="submit" class="btn btn-primary py-3 fw-bold shadow-sm" id="submitBtn">
                    SIMPAN PELUNASAN
                </button>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Daftar Hutang Outstanding</h5>
                    <div id="apPlaceholder" class="text-center py-5 text-muted">
                        <i class="fas fa-truck-loading fa-3x opacity-25 mb-3"></i>
                        <p>Pilih supplier untuk memuat daftar hutang</p>
                    </div>
                    
                    <div class="table-responsive d-none" id="apTableContainer">
                        <table class="table table-hover align-middle" id="apTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>No. Faktur</th>
                                    <th>Tgl. Terima</th>
                                    <th class="text-end">Sisa Hutang</th>
                                    <th style="width: 30%" class="text-end">Jumlah Bayar</th>
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
        $('.select2-supplier').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Supplier'
        });

        $('#supplier_id').change(function() {
            const supplierId = $(this).val();
            
            if (!supplierId) {
                $('#apPlaceholder').removeClass('d-none');
                $('#apTableContainer').addClass('d-none');
                return;
            }

            $.get(`/kaskeluar/ap/${supplierId}`, function(data) {
                $('#apPlaceholder').addClass('d-none');
                $('#apTableContainer').removeClass('d-none');
                
                let html = '';
                data.forEach((item, index) => {
                    html += `
                    <tr>
                        <td>
                            <span class="fw-bold">${item.noap}</span><br>
                            <small class="text-muted">${item.nopenerimaan}</small>
                            <input type="hidden" name="items[${index}][noap]" value="${item.noap}">
                        </td>
                        <td>${item.tglap}</td>
                        <td class="text-end fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(item.sisa)}</td>
                        <td>
                            <input type="number" name="items[${index}][bayar]" class="form-control text-end input-bayar" 
                                   min="0" max="${item.sisa}" step="0.01" value="0" data-sisa="${item.sisa}">
                        </td>
                    </tr>
                    `;
                });

                if (data.length === 0) {
                    html = '<tr><td colspan="4" class="text-center py-4">Tidak ada hutang outstanding untuk supplier ini.</td></tr>';
                }
                
                $('#apTable tbody').html(html);
                updateGrandTotal();
            });
        });

        $(document).on('input', '.input-bayar', function() {
            const val = parseFloat($(this).val()) || 0;
            const sisa = parseFloat($(this).data('sisa')) || 0;
            
            if (val > sisa) {
                $(this).val(sisa);
            }
            updateGrandTotal();
        });

        function updateGrandTotal() {
            let total = 0;
            $('.input-bayar').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#grandTotalText').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
            
            if (total > 0) {
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        }
    });
</script>
@endpush
