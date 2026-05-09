@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Kasir (Point of Sale)</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('penjualan.index') }}" class="text-decoration-none">Penjualan</a></li>
                <li class="breadcrumb-item active">Transaksi Baru</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('penjualan.store') }}" method="POST" id="penjualan-form">
    @csrf
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">No. Transaksi</label>
                            <input type="text" name="nopenjualan" class="form-control bg-light fw-bold text-primary" value="{{ $nopenjualan }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Tanggal</label>
                            <input type="date" name="tglpenjualan" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pelanggan</label>
                            <select name="kodepelanggan" class="form-select select2" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach ($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->kodepelanggan }}">{{ $pelanggan->namapelanggan }}</option>
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

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-uppercase small text-muted">Item Penjualan</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="add-row">
                        <i class="fas fa-plus me-1"></i> Tambah Barang
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="item-table">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40%">Barang</th>
                                    <th style="width: 15%" class="text-center">Jumlah</th>
                                    <th style="width: 20%" class="text-end">Harga Jual</th>
                                    <th style="width: 20%" class="text-end">Subtotal</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td class="p-3">
                                        <select name="items[0][kodebarang]" class="form-select item-select" required>
                                            <option value="">Cari Barang...</option>
                                            @foreach ($barangs as $barang)
                                                <option value="{{ $barang->kodebarang }}" data-price="{{ $barang->hargajual ?? 0 }}">{{ $barang->namabarang }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="items[0][satuan]" value="1">
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][jumlah]" class="form-control text-center qty" min="1" step="0.01" required>
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
                <div class="card-footer bg-dark p-4 text-white rounded-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-uppercase opacity-75">Bayar Tunai</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-white text-white opacity-75">Rp</span>
                                        <input type="number" name="tunai" class="form-control bg-transparent border-white text-white fw-bold" value="0" id="input-tunai">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-uppercase opacity-75">Kembali</label>
                                    <div class="h4 fw-bold mb-0 mt-1">Rp <span id="kembali">0</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="fw-bold text-uppercase small opacity-75 d-block mb-1">Total Pembayaran</span>
                            <h1 class="fw-bold mb-3">Rp <span id="grand-total">0</span></h1>
                            <input type="hidden" name="grandtotal" id="input-grandtotal" value="0">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('penjualan.index') }}" class="btn btn-outline-light px-4">Batal</a>
                                <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                    <i class="fas fa-check-circle me-2"></i> SELESAIKAN TRANSAKSI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemTable = document.getElementById('item-table').getElementsByTagName('tbody')[0];
        const addRowBtn = document.getElementById('add-row');
        const grandTotalEl = document.getElementById('grand-total');
        const inputGrandTotal = document.getElementById('input-grandtotal');
        const inputTunai = document.getElementById('input-tunai');
        const kembaliEl = document.getElementById('kembali');
        let rowCount = 1;

        addRowBtn.addEventListener('click', function() {
            const newRow = itemTable.rows[0].cloneNode(true);
            
            const inputs = newRow.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.name = input.name.replace('[0]', `[${rowCount}]`);
                input.value = '';
            });

            newRow.querySelector('.subtotal-text').innerText = '0';
            newRow.querySelector('.remove-row').style.display = 'block';
            
            itemTable.appendChild(newRow);
            rowCount++;
        });

        itemTable.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('tr').remove();
                updateGrandTotal();
            }
        });

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty') || e.target.classList.contains('price')) {
                calculateRow(e.target.closest('tr'));
            }
            if (e.target.id === 'input-tunai') {
                updateKembali();
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('item-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const price = selectedOption.dataset.price || 0;
                const row = e.target.closest('tr');
                row.querySelector('.price').value = price;
                calculateRow(row);
            }
        });

        function calculateRow(row) {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const subtotal = qty * price;
            row.querySelector('.subtotal-text').innerText = subtotal.toLocaleString('id-ID');
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-text').forEach(el => {
                total += parseFloat(el.innerText.replace(/\./g, '').replace(',', '.')) || 0;
            });
            grandTotalEl.innerText = total.toLocaleString('id-ID');
            inputGrandTotal.value = total;
            updateKembali();
        }

        function updateKembali() {
            const grandTotal = parseFloat(inputGrandTotal.value) || 0;
            const tunai = parseFloat(inputTunai.value) || 0;
            const kembali = Math.max(0, tunai - grandTotal);
            kembaliEl.innerText = kembali.toLocaleString('id-ID');
        }
    });
</script>
@endsection
