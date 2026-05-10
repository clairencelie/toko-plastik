@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Catat Penerimaan Barang</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('penerimaan.index') }}" class="text-decoration-none">Penerimaan</a></li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>
    </div>
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

<form action="{{ route('penerimaan.store') }}" method="POST" id="penerimaan-form">
    @csrf
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">No. Penerimaan</label>
                            <input type="text" name="nopenerimaan" class="form-control bg-light fw-bold text-primary" value="{{ $nopenerimaan }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tanggal</label>
                            <input type="date" name="tglpenerimaan" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Supplier</label>
                            <select name="supplier_id" class="form-select select2-supplier" required>
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier }}">{{ $supplier->keterangan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Jatuh Tempo</label>
                            <input type="date" name="tgljatuhtempo" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-uppercase small text-muted">Daftar Barang</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="add-row">
                        <i class="fas fa-plus me-1"></i> Tambah Baris
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="item-table">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40%">Barang</th>
                                    <th style="width: 15%" class="text-center">Jumlah</th>
                                    <th style="width: 20%" class="text-end">Harga Beli</th>
                                    <th style="width: 20%" class="text-end">Subtotal</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td class="p-3">
                                        <select name="items[0][kodebarang]" class="form-select item-select select2-barang" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach ($barangs as $barang)
                                                <option value="{{ $barang->kodebarang }}" 
                                                    data-price="{{ $barang->hargabeli ?? 0 }}"
                                                    data-stock="{{ $barang->stok->saldoakhir ?? 0 }}">
                                                    {{ $barang->namabarang }} (Stok: {{ $barang->stok->saldoakhir ?? 0 }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][jumlah]" class="form-control text-center qty" min="0.01" step="0.01" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][harga]" class="form-control text-end price" step="0.01" required>
                                    </td>
                                    <td class="text-end fw-bold p-3">
                                        <span class="subtotal-text">0</span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-link text-danger p-0 remove-row" style="display: none;">
                                            <i class="fas fa-times-circle fa-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light p-4">
                    <div class="row justify-content-end">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold text-muted text-uppercase small">Grand Total</span>
                                <h3 class="fw-bold text-primary mb-0">Rp <span id="grand-total">0</span></h3>
                                <input type="hidden" name="grandtotal" id="input-grandtotal" value="0">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Bayar Tunai</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">Rp</span>
                                    <input type="number" name="tunai" class="form-control border-start-0 fw-bold" value="0" id="input-tunai">
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="fas fa-save me-2"></i> Simpan Transaksi
                                </button>
                                <a href="{{ route('penerimaan.index') }}" class="btn btn-link text-muted">Batal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function() {
        function initSelect2() {
            $('.select2-supplier').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Supplier'
            });
            $('.select2-barang').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Barang'
            });
        }

        initSelect2();

        const itemTable = $('#item-table tbody');
        let rowCount = 1;

        $('#add-row').click(function() {
            const newRow = $('.item-row:first').clone();
            
            // Clean up Select2 artifacts from the clone
            newRow.find('.select2-container').remove();
            newRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden');
            newRow.find('option').removeAttr('data-select2-id');
            
            // Reset values
            newRow.find('input').val('');
            newRow.find('.qty').val('');
            newRow.find('.price').val('');
            newRow.find('.subtotal-text').text('0');
            newRow.find('.remove-row').show();

            newRow.find('input, select').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${rowCount}]`));
                }
            });

            itemTable.append(newRow);
            
            // Re-init only the new select
            newRow.find('.select2-barang').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Barang'
            });
            
            rowCount++;
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            updateGrandTotal();
        });

        $(document).on('change', '.select2-barang', function() {
            const row = $(this).closest('tr');
            const option = $(this).find(':selected');
            const price = option.data('price') || 0;
            
            row.find('.price').val(price);
            calculateRow(row);
        });

        $(document).on('input', '.qty, .price', function() {
            calculateRow($(this).closest('tr'));
        });

        function calculateRow(row) {
            const qty = parseFloat(row.find('.qty').val()) || 0;
            const price = parseFloat(row.find('.price').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal-text').text(subtotal.toLocaleString('id-ID'));
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            $('.subtotal-text').each(function() {
                total += parseFloat($(this).text().replace(/\./g, '').replace(',', '.')) || 0;
            });
            $('#grand-total').text(total.toLocaleString('id-ID'));
            $('#input-grandtotal').val(total);
        }
    });
</script>
@endpush
@endsection
