<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Plastik - Admin</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .navbar { background: #333; color: #fff; padding: 1rem; }
        .navbar a { color: #fff; text-decoration: none; margin-right: 1rem; }
        .container { padding: 2rem; }
        .card { background: #fff; padding: 1.5rem; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        table th, table td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #ddd; }
        table th { background: #f8f8f8; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; }
        .form-group input, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 3px; }
        .alert { padding: 1rem; margin-bottom: 1rem; border-radius: 3px; }
        .alert-success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="{{ route('barang.index') }}">Barang</a>
        <a href="{{ route('pelanggan.index') }}">Pelanggan</a>
        <a href="{{ route('supplier.index') }}">Supplier</a>
        <a href="{{ route('penjualan.index') }}">Penjualan</a>
        <a href="{{ route('penerimaan.index') }}">Pembelian</a>
        <a href="{{ route('report.stock') }}">Laporan Stok</a>
        <a href="{{ route('report.financial') }}">Laporan Keuangan</a>
    </div>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>
