<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Plastik - Premium POS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #64748b;
            --bg-color: #f1f5f9;
            --sidebar-width: 280px;
            --accent-color: #f59e0b;
        }
        
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: var(--bg-color);
            color: #334155;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 2rem 1.25rem;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.875rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: #f8fafc;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2.5rem;
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .table thead th {
            background: #f8fafc;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 1.25rem 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .table tbody td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary { 
            background: var(--primary-color); 
            border: none;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        
        .btn-primary:hover { 
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            border-color: var(--primary-color);
        }

        .badge {
            padding: 0.5em 0.8em;
            border-radius: 8px;
            font-weight: 600;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="mb-5 px-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary p-2 rounded-3 shadow-sm">
                    <i class="fas fa-shopping-bag text-white fa-lg"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-white">Toko Plastik</h5>
                    <span class="badge bg-warning text-dark small" style="font-size: 0.6rem">PREMIUM ADMIN</span>
                </div>
            </div>
        </div>
        
        <nav class="nav flex-column">
            <a href="{{ route('barang.index') }}" class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}">
                <i class="fas fa-cube"></i> Master Barang
            </a>
            <a href="{{ route('pelanggan.index') }}" class="nav-link {{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
                <i class="fas fa-user-group"></i> Pelanggan
            </a>
            <a href="{{ route('supplier.index') }}" class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                <i class="fas fa-truck-fast"></i> Supplier
            </a>
            
            <div class="mt-4 mb-3 px-3 text-uppercase small fw-bold text-slate-500" style="color: #64748b; font-size: 0.7rem">Transaksi</div>
            <a href="{{ route('penjualan.index') }}" class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i> Penjualan (POS)
            </a>
            <a href="{{ route('penerimaan.index') }}" class="nav-link {{ request()->routeIs('penerimaan.*') ? 'active' : '' }}">
                <i class="fas fa-cart-flatbed"></i> Pembelian
            </a>
            
            <div class="mt-4 mb-3 px-3 text-uppercase small fw-bold text-slate-500" style="color: #64748b; font-size: 0.7rem">Analitik</div>
            <a href="{{ route('report.stock') }}" class="nav-link {{ request()->routeIs('report.stock') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Stok Opname
            </a>
            <a href="{{ route('report.financial') }}" class="nav-link {{ request()->routeIs('report.financial') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> Keuangan
            </a>
        </nav>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-lg mb-4" role="alert" style="border-left: 5px solid #10b981 !important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-circle-check me-3 fa-lg"></i>
                    <div>
                        <h6 class="mb-0 fw-bold">Berhasil!</h6>
                        <p class="mb-0 small text-muted">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
