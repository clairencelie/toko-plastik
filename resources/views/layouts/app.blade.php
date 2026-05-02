<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Plastik - POS Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --bg-color: #f8fafc;
            --sidebar-width: 260px;
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-color);
            color: #1e293b;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #1e293b;
            color: white;
            padding: 1.5rem 1rem;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .sidebar .nav-link.active {
            background: var(--primary-color);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .table thead th {
            background: #f1f5f9;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
            color: #64748b;
            padding: 1rem;
        }

        .btn-primary { background-color: var(--primary-color); border: none; }
        .btn-primary:hover { background-color: #1d4ed8; }

        .search-bar {
            max-width: 400px;
        }

        /* Fix for large pagination arrows */
        .pagination svg {
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="mb-4 px-3">
            <h4 class="fw-bold mb-0 text-white"><i class="fas fa-shopping-bag me-2"></i>Toko Plastik</h4>
            <small class="text-muted">Management System</small>
        </div>
        
        <nav class="nav flex-column">
            <a href="{{ route('barang.index') }}" class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Master Barang
            </a>
            <a href="{{ route('pelanggan.index') }}" class="nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Pelanggan
            </a>
            <a href="{{ route('supplier.index') }}" class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> Supplier
            </a>
            <div class="mt-4 mb-2 px-3 text-uppercase small fw-bold text-muted">Transaksi</div>
            <a href="{{ route('penjualan.index') }}" class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> Penjualan (POS)
            </a>
            <a href="{{ route('penerimaan.index') }}" class="nav-link {{ request()->routeIs('penerimaan.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> Pembelian
            </a>
            <div class="mt-4 mb-2 px-3 text-uppercase small fw-bold text-muted">Laporan</div>
            <a href="{{ route('report.stock') }}" class="nav-link {{ request()->routeIs('report.stock') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Stok Opname
            </a>
            <a href="{{ route('report.financial') }}" class="nav-link {{ request()->routeIs('report.financial') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> Keuangan
            </a>
        </nav>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
