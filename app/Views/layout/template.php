<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?= csrf_meta() ?>
    <title><?= $title ?? 'HR Management System' ?> - PeopleAxis</title>
    
    <!-- Preconnect to CDN origins early so DNS/TCP/TLS are ready before assets download -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --pa-bg: #f0f7f2;
            --pa-surface: #ffffff;
            --pa-border: #c7ddd0;
            --pa-border-soft: #dbe9e0;
            --pa-text: #1f362a;
            --pa-muted: #5f7b69;
            --pa-primary: #6ea988;
            --pa-primary-dark: #5b9474;
            --pa-success: #72ae8d;
            --pa-warning: #80a08b;
            --pa-danger: #6f8f7b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--pa-bg);
            color: var(--pa-text);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            line-height: 1.5;
        }

        a {
            color: var(--pa-primary);
        }

        a:hover {
            color: var(--pa-primary-dark);
        }

        .page-header {
            background: var(--pa-surface);
            border: 1px solid var(--pa-border-soft);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        .page-header h1 {
            margin: 0;
            font-size: 1.65rem;
            color: var(--pa-text);
            font-weight: 700;
        }

        .page-header p {
            margin: 0.4rem 0 0;
            color: var(--pa-muted);
        }

        .breadcrumb-custom {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--pa-primary);
        }

        .card {
            border: 1px solid var(--pa-border-soft);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
            border-radius: 10px;
            margin-bottom: 1.25rem;
            background: var(--pa-surface);
        }

        .card-header {
            background: #f4faf6;
            color: #254132;
            border-bottom: 1px solid var(--pa-border-soft);
            padding: 0.9rem 1rem;
            font-weight: 600;
        }

        .card-header + .card-body {
            padding-top: 1rem;
        }

        .btn-primary {
            background-color: var(--pa-primary);
            border-color: var(--pa-primary);
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .btn-primary:hover {
            background-color: var(--pa-primary-dark);
            border-color: var(--pa-primary-dark);
        }

        .badge-status {
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .badge-active {
            background-color: var(--pa-success);
            color: white;
        }

        .badge-inactive {
            background-color: #95ad9f;
            color: white;
        }

        .badge-pending {
            background-color: var(--pa-warning);
            color: white;
        }

        .stat-card {
            background: var(--pa-surface);
            padding: 1rem;
            border: 1px solid var(--pa-border-soft);
            border-radius: 10px;
            text-align: center;
            margin-bottom: 1rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--pa-primary);
        }

        .stat-card .stat-label {
            color: var(--pa-muted);
            font-size: 0.9rem;
            margin-top: 0.4rem;
        }

        .stat-card i {
            font-size: 1.6rem;
            color: var(--pa-primary);
            margin-bottom: 0.4rem;
        }

        .table {
            color: #355444;
            border-color: var(--pa-border-soft);
        }

        .table thead th {
            background: #f4faf6;
            color: #355444;
            font-weight: 600;
            border-bottom: 1px solid var(--pa-border);
        }

        .table-striped tbody tr:hover {
            background-color: #eef7f1;
        }

        .form-control,
        .form-select {
            border: 1px solid var(--pa-border);
            border-radius: 8px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--pa-primary);
            box-shadow: 0 0 0 0.2rem rgba(110, 169, 136, 0.2);
        }

        .alert {
            border-radius: 8px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #ecf7f0;
            color: #355444;
            border-color: #c7ddd0;
        }

        .alert-danger {
            background-color: #e7f2ea;
            color: #254132;
            border-color: #c7ddd0;
        }

        .alert-warning {
            background-color: #f3faf5;
            color: #4b6f5a;
            border-color: #dbe9e0;
        }

        .alert-info {
            background-color: #ecf7f0;
            color: #355444;
            border-color: #c7ddd0;
        }

        .modal-content {
            border-radius: 10px;
            border: 1px solid var(--pa-border-soft);
        }

        .modal-header {
            background: #f4faf6;
            color: #254132;
            border-bottom: 1px solid var(--pa-border-soft);
        }

        .footer {
            margin-top: auto;
            padding: 1rem;
            background: #f4faf6;
            border-top: 1px solid var(--pa-border-soft);
            text-align: center;
            color: var(--pa-muted);
        }

        .footer a {
            color: var(--pa-primary);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }
        }

        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        .loading-spinner.active {
            display: flex;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(240, 247, 242, 0.95);
            z-index: 9998;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            text-align: center;
            color: var(--pa-text);
        }

        .loading-content i {
            font-size: 2rem;
            color: var(--pa-primary);
            margin-bottom: 1rem;
            animation: spin 1s linear infinite;
        }

        .loading-content p {
            margin: 0;
            font-weight: 500;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<?php
    $currentUrl = rtrim(current_url(), '/');
    $hideSharedNavigation = in_array($currentUrl, [
        rtrim(base_url(), '/'),
        rtrim(base_url('login'), '/'),
        rtrim(base_url('forgot-password'), '/'),
    ], true);
?>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <i class="fas fa-circle-notch"></i>
            <p>Loading...</p>
        </div>
    </div>
    
    <?php if (! $hideSharedNavigation): ?>
        <?= $this->include('layout/header') ?>
    <?php endif; ?>
    
    <!-- Content renders directly inside content-area opened by header -->
    <?= $this->renderSection('content') ?>
    
    <?php if (! $hideSharedNavigation): ?>
        <?= $this->include('layout/footer') ?>
    <?php endif; ?>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <script>
        // Global loading overlay management
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        function showLoading() {
            if (loadingOverlay) {
                loadingOverlay.classList.add('active');
            }
        }
        
        function hideLoading() {
            if (loadingOverlay) {
                loadingOverlay.classList.remove('active');
            }
        }
        
        // Show loading on page navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Handle all internal links
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a[href]');
                if (link && link.href && link.href.startsWith(window.location.origin) && !link.hasAttribute('download') && !link.getAttribute('target')) {
                    // Don't show loading for anchor links on same page
                    if (link.getAttribute('href').startsWith('#')) return;
                    
                    // Don't show loading for external links or downloads
                    e.preventDefault();
                    showLoading();
                    
                    // Small delay to ensure overlay is visible before navigation
                    setTimeout(() => {
                        window.location.href = link.href;
                    }, 50);
                }
            });
            
            // Handle form submissions
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (e.defaultPrevented) return;
                if (form && form.method !== 'get') {
                    showLoading();
                }
            });
            
            // Hide loading when page is fully loaded
            window.addEventListener('load', function() {
                hideLoading();
            });
            
            // Hide loading on page unload (navigation away)
            window.addEventListener('beforeunload', function() {
                showLoading();
            });
            
            // Hide loading when back/forward buttons are used
            window.addEventListener('popstate', function() {
                hideLoading();
            });
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
