@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Catat Penerimaan Barang Baru</h2>
    <form action="{{ route('penerimaan.store') }}" method="POST">
        @csrf
        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label>No Penerimaan</label>
                <input type="text" name="nopenerimaan" value="PB-{{ date('YmdHis') }}" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Tanggal</label>
                <input type="date" name="tglpenerimaan" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex: 1;">
                <label>Supplier</label>
                <select name="supplier_id" required>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->autoid }}">{{ $supplier->namasupplier }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Tanggal Jatuh Tempo (Jika Kredit)</label>
                <input type="date" name="tgljatuhtempo">
            </div>
        </div>

        <hr>
        <h3>Daftar Item</h3>
        <table id="item-table">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Beli</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr class="item-row">
                    <td>
                        <select name="items[0][kodebarang]" required>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->kodebarang }}">{{ $barang->namabarang }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="items[0][satuan]" value="1">
                    </td>
                    <td><input type="number" name="items[0][jumlah]" class="qty" required></td>
                    <td><input type="number" name="items[0][harga]" class="price" required></td>
                    <td class="subtotal">0</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 1rem; text-align: right;">
            <strong>Grand Total: </strong><span id="grand-total">0</span>
            <input type="hidden" name="grandtotal" id="input-grandtotal" value="0">
        </div>

        <div class="form-group" style="width: 200px; margin-left: auto;">
            <label>Bayar Tunai</label>
            <input type="number" name="tunai" value="0">
        </div>

        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            <a href="{{ route('penerimaan.index') }}" class="btn" style="background:#ccc; color:#333;">Batal</a>
        </div>
    </form>
</div>

<script>
    // Simple script for basic calculations
    document.addEventListener('input', function(e) {
        if(e.target.classList.contains('qty') || e.target.classList.contains('price')) {
            let row = e.target.closest('tr');
            let qty = row.querySelector('.qty').value || 0;
            let price = row.querySelector('.price').value || 0;
            let subtotal = qty * price;
            row.querySelector('.subtotal').innerText = subtotal.toLocaleString('id-ID');
            
            updateGrandTotal();
        }
    });

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(el => {
            total += parseInt(el.innerText.replace(/\./g, '')) || 0;
        });
        document.getElementById('grand-total').innerText = total.toLocaleString('id-ID');
        document.getElementById('input-grandtotal').value = total;
    }
</script>
@endsection
