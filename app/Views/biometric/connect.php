<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .biometric-shell {
        max-width: 1240px;
        margin: 0 auto;
        padding: 8px 0 18px;
    }

    .breadcrumbs {
        margin-bottom: 14px;
        font-size: 0.9rem;
    }

    .breadcrumbs a {
        color: #6ea988;
        text-decoration: none;
    }

    .breadcrumbs a:hover {
        text-decoration: underline;
    }

    .breadcrumbs span {
        color: #6f8192;
        margin: 0 6px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    }

    .page-header h1 {
        margin: 0;
        color: #2f5f45;
        font-weight: 700;
        font-size: 1.8rem;
        line-height: 1;
    }

    .page-header p {
        margin: 6px 0 0;
        color: #6f8192;
        font-size: 0.92rem;
    }

    .alert {
        border-radius: 8px;
        border: none;
        margin-bottom: 14px;
        padding: 12px 14px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .alert-info {
        background: #eaf3fb;
        color: #2c587f;
        border: 1px solid #d4e3f2;
    }

    .grid-container {
        display: grid;
        grid-template-columns: 1.05fr 1fr;
        gap: 14px;
        margin-bottom: 14px;
    }

    .card {
        background: #ffffff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        padding: 16px;
    }

    .card h3 {
        color: #1f3550;
        margin: 0 0 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.12rem;
    }

    .card-icon {
        font-size: 1rem;
        color: #6ea988;
    }

    .device-status {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fbff;
        border: 1px solid #e2ebf4;
    }

    .status-badge {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-online {
        background: #24a45a;
    }

    .status-offline {
        background: #d9534f;
    }

    .status-text {
        flex: 1;
    }

    .status-text strong {
        color: #2f4358;
        font-size: 0.9rem;
    }

    .status-value {
        margin: 4px 0 0;
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .status-value-online {
        background: #e9f7ec;
        color: #1d7a3f;
        border-color: #cfead8;
    }

    .status-value-offline {
        background: #fdecec;
        color: #b43a3a;
        border-color: #f4d3d3;
    }

    .device-info {
        background: #fbfdff;
        border: 1px solid #e8eef5;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 9px 0;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.9rem;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #6e8192;
        font-weight: 600;
    }

    .info-value {
        color: #23384f;
        font-weight: 600;
        text-align: right;
    }

    .sync-form {
        margin-top: 2px;
    }

    .last-sync {
        background: #f8fbff;
        border: 1px solid #e2ebf4;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 0.84rem;
        color: #6f8192;
        margin-top: 10px;
    }

    .form-group {
        margin-bottom: 13px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        color: #2f4358;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 9px 11px;
        border: 1px solid #d9e2ec;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .btn-group {
        display: flex;
        gap: 8px;
    }

    .btn {
        padding: 9px 12px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease;
        font-size: 0.86rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        flex: 1;
        line-height: 1;
    }

    .btn-primary {
        background: #6ea988;
        color: #ffffff;
        border-color: #6ea988;
    }

    .btn-primary:hover {
        background: #21437c;
        border-color: #21437c;
    }

    .btn-secondary {
        background: #f1f5fb;
        color: #6ea988;
        border-color: #c9d8ef;
    }

    .btn-secondary:hover {
        background: #e7effa;
        border-color: #bad0ee;
    }

    .btn-success {
        background: #27ae60;
        color: #ffffff;
        border-color: #27ae60;
    }

    .btn-success:hover {
        background: #239953;
        border-color: #239953;
    }

    .controls-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 8px;
    }

    .controls-grid form {
        margin: 0;
    }

    @media (max-width: 992px) {
        .grid-container {
            grid-template-columns: 1fr;
        }

        .page-header h1 {
            font-size: 1.55rem;
        }
    }

    @media (max-width: 640px) {
        .page-header {
            padding: 14px;
        }

        .btn-group {
            flex-direction: column;
        }
    }
</style>

<div class="biometric-shell">

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
        <span>/</span>
        <span>Biometric</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-fingerprint"></i> Biometric Management</h1>
            <p>Connect, monitor, and sync biometric devices</p>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php
        $isConnected = (bool)($deviceStatus['connected'] ?? false);
        $deviceRecords = $totalRecords ?? ($deviceStatus['records'] ?? 0);
    ?>

    <!-- Device Connection Grid -->
    <div class="grid-container">
        <!-- Device Status Card -->
        <div class="card">
            <h3>
                <i class="fas fa-wifi card-icon"></i> Device Status
            </h3>

            <div class="device-status">
                <span class="status-badge <?= $isConnected ? 'status-online' : 'status-offline' ?>"></span>
                <div class="status-text">
                    <strong>Connection Status</strong>
                    <div class="status-value <?= $isConnected ? 'status-value-online' : 'status-value-offline' ?>">
                        <?= $isConnected ? 'Connected' : 'Disconnected' ?>
                    </div>
                </div>
            </div>

            <div class="device-info">
                <div class="info-row">
                    <span class="info-label">Device ID</span>
                    <span class="info-value"><?= esc($deviceId ?? 'Not Assigned') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Device Model</span>
                    <span class="info-value"><?= esc($deviceModel ?? 'Unknown') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Firmware Version</span>
                    <span class="info-value"><?= esc($firmwareVersion ?? 'Unknown') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Records</span>
                    <span class="info-value"><?= esc((string)$deviceRecords) ?></span>
                </div>
            </div>

            <form action="<?= base_url('biometric/manualSync') ?>" method="POST" class="sync-form">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-sync"></i> Sync Now
                </button>
            </form>

            <div class="last-sync">
                <strong>Last Sync:</strong> <?= $lastSync ? date('M d, Y H:i', strtotime($lastSync)) : 'Never' ?>
            </div>
        </div>

        <!-- Device Connection Card -->
        <div class="card">
            <h3>
                <i class="fas fa-plug card-icon"></i> Connect Device
            </h3>

            <form action="<?= base_url('biometric/connect') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="device_ip" class="form-label">Device IP Address</label>
                    <input
                        type="text"
                        id="device_ip"
                        name="device_ip"
                        class="form-control"
                        placeholder="e.g., 192.168.1.100"
                        value="<?= old('device_ip') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="device_port" class="form-label">Device Port</label>
                    <input
                        type="number"
                        id="device_port"
                        name="device_port"
                        class="form-control"
                        placeholder="e.g., 4370"
                        value="<?= old('device_port') ?>"
                        min="1"
                        max="65535"
                    >
                </div>

                <div class="form-group">
                    <label for="device_password" class="form-label">Device Password</label>
                    <input
                        type="password"
                        id="device_password"
                        name="device_password"
                        class="form-control"
                        placeholder="Enter device password if required"
                        value="<?= old('device_password') ?>"
                    >
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" onclick="testConnection()">
                        <i class="fas fa-check"></i> Test Connection
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Connect Device
                    </button>
                </div>
            </form>

            <div class="alert alert-info" style="margin-top: 12px; margin-bottom: 0;">
                <strong>Note:</strong> Default biometric device port is 4370.
            </div>
        </div>
    </div>

    <!-- Additional Device Controls -->
    <div class="card">
        <h3>
            <i class="fas fa-cogs card-icon"></i> Device Controls
        </h3>

        <div class="controls-grid">
            <form action="<?= base_url('biometric/clearLogs') ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary" style="width: 100%;" onclick="return confirm('Clear all device logs? This action cannot be undone.')">
                    <i class="fas fa-trash"></i> Clear Logs
                </button>
            </form>

            <form action="<?= base_url('biometric/rebootDevice') ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary" style="width: 100%;" onclick="return confirm('Reboot device? This will temporarily disconnect it.')">
                    <i class="fas fa-power-off"></i> Reboot Device
                </button>
            </form>

            <form action="<?= base_url('biometric/getDeviceInfo') ?>" method="POST">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-info-circle"></i> Refresh Info
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function testConnection() {
    const ip = document.getElementById('device_ip').value;
    const port = document.getElementById('device_port').value;

    if (!ip || !port) {
        alert('Please enter device IP address and port');
        return;
    }

    // Send AJAX request to test connection
    const formData = new FormData();
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    formData.append('device_ip', ip);
    formData.append('device_port', port);

    fetch('<?= base_url('biometric/testConnection') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Device connection successful!');
        } else {
            alert('Connection failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Connection error: ' + error.message);
    });
}
</script>

<?= $this->endSection() ?>
