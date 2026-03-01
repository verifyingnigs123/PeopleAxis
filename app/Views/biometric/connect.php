<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-header h1 {
        color: #1e3c72;
        font-weight: 700;
        margin: 0;
    }

    .grid-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    @media (max-width: 768px) {
        .grid-container {
            grid-template-columns: 1fr;
        }
    }

    .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 25px;
        border-left: 4px solid #2a5298;
    }

    .card h3 {
        color: #1e3c72;
        margin: 0 0 20px 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
    }

    .card-icon {
        font-size: 1.5rem;
        color: #2a5298;
    }

    .device-status {
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .status-badge {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-online {
        background: #27ae60;
    }

    .status-offline {
        background: #e74c3c;
    }

    .status-text {
        flex: 1;
    }

    .status-text strong {
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .status-text p {
        margin: 3px 0 0 0;
        color: #7f8c8d;
        font-size: 0.85rem;
    }

    .device-info {
        background: #f8f9ff;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e1e8ed;
        font-size: 0.9rem;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #7f8c8d;
        font-weight: 600;
    }

    .info-value {
        color: #2c3e50;
        font-weight: 500;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
    }

    .btn-group {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex: 1;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 60, 114, 0.4);
    }

    .btn-secondary {
        background: #e1e8ed;
        color: #2c3e50;
    }

    .btn-secondary:hover {
        background: #d5dce3;
    }

    .btn-success {
        background: #27ae60;
        color: white;
    }

    .btn-success:hover {
        background: #229954;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
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
        background: #d1ecf1;
        color: #0c5460;
    }

    .last-sync {
        background: #f8f9ff;
        padding: 12px 15px;
        border-radius: 6px;
        font-size: 0.85rem;
        color: #7f8c8d;
        margin-top: 10px;
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <span>Biometric</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1><i class="fas fa-fingerprint"></i> Biometric Management</h1>
        <p>Connect and manage biometric devices</p>
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

<!-- Device Connection Grid -->
<div class="grid-container">
    <!-- Device Status Card -->
    <div class="card">
        <h3>
            <i class="fas fa-wifi card-icon"></i> Device Status
        </h3>

        <div class="device-status">
            <span class="status-badge status-online"></span>
            <div class="status-text">
                <strong>Connection Status</strong>
                <p><?= isset($deviceStatus) && $deviceStatus ? 'Connected' : 'Disconnected' ?></p>
            </div>
        </div>

        <div class="device-info">
            <div class="info-row">
                <span class="info-label">Device ID:</span>
                <span class="info-value"><?= esc($deviceId ?? 'Not Assigned') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Device Model:</span>
                <span class="info-value"><?= esc($deviceModel ?? 'Unknown') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Firmware Version:</span>
                <span class="info-value"><?= esc($firmwareVersion ?? 'Unknown') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Records:</span>
                <span class="info-value"><?= $totalRecords ?? 0 ?></span>
            </div>
        </div>

        <form action="<?= base_url('biometric/manualSync') ?>" method="POST" style="margin-top: 15px;">
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

        <div class="alert alert-info" style="margin-top: 15px; margin-bottom: 0;">
            <strong>Note:</strong> Default biometric device port is 4370.
        </div>
    </div>
</div>

<!-- Additional Device Controls -->
<div class="card">
    <h3>
        <i class="fas fa-cogs card-icon"></i> Device Controls
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
        <form action="<?= base_url('biometric/clearLogs') ?>" method="POST" style="display: inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary" style="width: 100%;" onclick="return confirm('Clear all device logs? This action cannot be undone.')">
                <i class="fas fa-trash"></i> Clear Logs
            </button>
        </form>

        <form action="<?= base_url('biometric/rebootDevice') ?>" method="POST" style="display: inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-secondary" style="width: 100%;" onclick="return confirm('Reboot device? This will temporarily disconnect it.')">
                <i class="fas fa-power-off"></i> Reboot Device
            </button>
        </form>

        <form action="<?= base_url('biometric/getDeviceInfo') ?>" method="POST" style="display: inline;">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-info-circle"></i> Refresh Info
            </button>
        </form>
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
