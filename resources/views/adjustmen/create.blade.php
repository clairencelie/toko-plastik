@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Buat Adjustment Stok</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('adjustmen.index') }}">Adjustment</a></li>
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

<form action="{{ route('adjustmen.store') }}" method="POST" id="adjustmentForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Informasi Utama</h5>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">NOMOR ADJUSTMENT</label>
                        <input type="text" class="form-control bg-light fw-bold text-primary" value="{{ $noadjustmen }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">TANGGAL</label>
                        <input type="date" name="tanggaladjustmen" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">KETERANGAN</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Alasan adjustment..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Item Barang</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addItem">
                            <i class="fas fa-plus me-1"></i> Tambah Baris
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless align-middle" id="itemsTable">
                            <thead>
                                <tr class="border-bottom">
                                    <th style="width: 40%">Barang</th>
                                    <th style="width: 20%">Jumlah (+/-)</th>
                                    <th style="width: 20%">Harga (HPP)</th>
                                    <th style="width: 15%">Subtotal</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row border-bottom">
                                    <td class="py-3">
                                        <select name="items[0][kodebarang]" class="form-select select2-barang" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach($barangs as $barang)
                                                <option value="{{ $barang->kodebarang }}" 
                                                    data-price="{{ $barang->hargabeli }}"
                                                    data-stock="{{ $barang->stok->saldoakhir ?? 0 }}">
                                                    {{ $barang->namabarang }} (Stok: {{ $barang->stok->saldoakhir ?? 0 }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="items[0][jumlah]" class="form-control input-jumlah" value="1" step="0.01" required>
                                            <span class="input-group-text small bg-light stock-label">0</span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][harga]" class="form-control input-harga" value="0" step="0.01" required>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <span class="subtotal-text">0</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm text-danger remove-row"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-muted">TOTAL ADJUSTMENT</span>
                        <h4 class="fw-bold mb-0 text-primary" id="grandTotalText">Rp 0</h4>
                    </div>

                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-primary py-3 fw-bold">
                            SIMPAN ADJUSTMENT
                        </button>
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
        let rowCount = 1;

        function initSelect2() {
            $('.select2-barang').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Barang'
            });
        }

        initSelect2();

        function calculateRow(row) {
            const qty = parseFloat(row.find('.input-jumlah').val()) || 0;
            const price = parseFloat(row.find('.input-harga').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal-text').text(new Intl.NumberFormat('id-ID').format(subtotal));
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            $('.item-row').each(function() {
                const qty = parseFloat($(this).find('.input-jumlah').val()) || 0;
                const price = parseFloat($(this).find('.input-harga').val()) || 0;
                total += (qty * price);
            });
            $('#grandTotalText').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
        }

        $('#addItem').click(function() {
            const newRow = $('.item-row:first').clone();
            
            // Clean up Select2 artifacts from the clone
            newRow.find('.select2-container').remove();
            newRow.find('select').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').removeAttr('aria-hidden');
            newRow.find('option').removeAttr('data-select2-id');
            
            // Reset values
            newRow.find('input').val(0);
            newRow.find('.input-jumlah').val(1);
            newRow.find('select').val('');
            newRow.find('.subtotal-text').text('0');
            newRow.find('.stock-label').text('0');

            newRow.find('input, select').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${rowCount}]`));
                }
            });

            $('#itemsTable tbody').append(newRow);
            
            // Re-init only the new select
            newRow.find('.select2-barang').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Barang'
            });
            
            rowCount++;
        });

        $(document).on('change', '.select2-barang', function() {
            const row = $(this).closest('.item-row');
            const option = $(this).find(':selected');
            const price = option.data('price') || 0;
            const stock = option.data('stock') || 0;
            
            row.find('.input-harga').val(price);
            row.find('.stock-label').text(stock);
            calculateRow(row);
        });

        $(document).on('input', '.input-jumlah, .input-harga', function() {
            calculateRow($(this).closest('.item-row'));
        });

        $(document).on('click', '.remove-row', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
                updateGrandTotal();
            }
        });
    });
</script>
@endpush
