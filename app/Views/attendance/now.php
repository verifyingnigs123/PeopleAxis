<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .attendance-now-container {
        max-width: 600px;
        margin: 40px auto;
        padding: 20px;
    }

    .attendance-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .attendance-header h1 {
        margin: 0 0 10px;
        font-size: 2rem;
        color: #2a4839;
        font-weight: 700;
    }

    .attendance-header p {
        margin: 0;
        color: #5f7b69;
        font-size: 1rem;
    }

    .attendance-time {
        background: linear-gradient(135deg, #6ea988, #5b9474);
        color: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(35, 71, 52, 0.15);
    }

    .attendance-time-label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .attendance-time-value {
        font-size: 2.5rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
    }

    .attendance-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        border-bottom: 2px solid #c7ddd0;
    }

    .tab-btn {
        padding: 12px 20px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        color: #5f7b69;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .tab-btn:hover {
        color: #2a4839;
    }

    .tab-btn.active {
        color: #6ea988;
        border-bottom-color: #6ea988;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .rfid-input-box {
        background: #f7fbf8;
        border: 2px solid #c7ddd0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        text-align: center;
    }

    .rfid-input-box input {
        width: 100%;
        padding: 12px;
        border: 1px solid #c7ddd0;
        border-radius: 8px;
        font-size: 1.1rem;
        text-align: center;
        margin-bottom: 10px;
    }

    .rfid-input-box input:focus {
        outline: none;
        border-color: #6ea988;
        box-shadow: 0 0 0 3px rgba(110, 169, 136, 0.1);
    }

    .rfid-status {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: none;
        text-align: center;
        font-weight: 600;
    }

    .rfid-status.active {
        display: block;
    }

    .rfid-status.success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .rfid-status.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .rfid-status.waiting {
        background: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .rfid-hint {
        font-size: 0.9rem;
        color: #5f7b69;
        padding: 10px;
        background: #ecf7f0;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .attendance-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 30px;
    }

    .attendance-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 20px;
        border: 2px solid transparent;
        border-radius: 12px;
        background: white;
        text-decoration: none;
        color: white;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        min-height: 120px;
    }

    .attendance-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .attendance-btn i {
        font-size: 2rem;
    }

    .btn-check-in {
        background: linear-gradient(135deg, #10b981, #059669);
        border-color: #10b981;
    }

    .btn-check-in:hover {
        background: #059669;
        border-color: #047857;
    }

    .btn-break-out {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-color: #f59e0b;
    }

    .btn-break-out:hover {
        background: #d97706;
        border-color: #b45309;
    }

    .btn-break-in {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-color: #3b82f6;
    }

    .btn-break-in:hover {
        background: #2563eb;
        border-color: #1d4ed8;
    }

    .btn-check-out {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: #ef4444;
    }

    .btn-check-out:hover {
        background: #dc2626;
        border-color: #b91c1c;
    }

    .attendance-records {
        background: #f7fbf8;
        border: 1px solid #c7ddd0;
        border-radius: 12px;
        padding: 20px;
        margin-top: 30px;
    }

    .attendance-records h3 {
        margin: 0 0 15px;
        font-size: 1.1rem;
        color: #2a4839;
        font-weight: 700;
    }

    .record-item {
        background: white;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid #6ea988;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .record-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .record-time {
        font-weight: 600;
        color: #2a4839;
    }

    .record-label {
        font-size: 0.85rem;
        color: #5f7b69;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-present {
        background: #dcfce7;
        color: #166534;
    }

    .badge-late {
        background: #fef3c7;
        color: #92400e;
    }

    .alert {
        border-radius: 10px;
        border: 1px solid transparent;
        margin-bottom: 20px;
        padding: 12px 15px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .no-records {
        text-align: center;
        padding: 20px;
        color: #5f7b69;
    }

    .attendance-back-btn {
        display: inline-block;
        margin-bottom: 20px;
        padding: 10px 16px;
        background: #ecf7f0;
        color: #6ea988;
        border: 1px solid #c7ddd0;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .attendance-back-btn:hover {
        background: #dfeee5;
        border-color: #b6d3c1;
        color: #5b9474;
    }

    @media (max-width: 576px) {
        .attendance-now-container {
            margin: 20px auto;
            padding: 15px;
        }

        .attendance-header h1 {
            font-size: 1.5rem;
        }

        .attendance-time-value {
            font-size: 2rem;
        }

        .attendance-actions {
            grid-template-columns: 1fr;
        }

        .attendance-btn {
            min-height: 100px;
        }
    }

    /* Employee Details Modal */
    .rfid-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .rfid-modal-overlay.active {
        display: flex;
    }

    .rfid-modal-content {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        width: 90%;
        padding: 30px;
        text-align: center;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .rfid-modal-success {
        border-top: 5px solid #10b981;
    }

    .rfid-modal-error {
        border-top: 5px solid #ef4444;
    }

    .rfid-employee-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin: 0 auto 20px;
        border: 3px solid #6ea988;
        object-fit: cover;
        background: linear-gradient(135deg, #2f5f45, #6ea988);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: 700;
    }

    .rfid-employee-avatar.loading {
        animation: pulse 1.5s ease-in-out infinite;
    }

    .avatar-container {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 20px;
        border-radius: 50%;
        border: 3px solid #6ea988;
        overflow: hidden;
        background: linear-gradient(135deg, #2f5f45, #6ea988);
    }

    .avatar-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-fallback {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #2f5f45, #6ea988);
    }

    .avatar-fallback.show {
        display: flex;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    .rfid-employee-name {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2a4839;
        margin: 10px 0 5px;
    }

    .rfid-employee-id {
        font-size: 0.9rem;
        color: #5f7b69;
        margin-bottom: 15px;
    }

    .rfid-employee-info {
        background: #f7fbf8;
        border-radius: 10px;
        padding: 15px;
        margin: 15px 0;
        text-align: left;
    }

    .rfid-info-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #c7ddd0;
        font-size: 0.9rem;
    }

    .rfid-info-item:last-child {
        border-bottom: none;
    }

    .rfid-info-label {
        font-weight: 600;
        color: #5f7b69;
    }

    .rfid-info-value {
        color: #2a4839;
    }

    .rfid-action-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        margin: 15px 0;
        font-size: 0.95rem;
    }

    .rfid-action-check-in {
        background: #dcfce7;
        color: #166534;
    }

    .rfid-action-break-out {
        background: #fef3c7;
        color: #92400e;
    }

    .rfid-action-break-in {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .rfid-action-check-out {
        background: #fee2e2;
        color: #991b1b;
    }

    .rfid-success-icon {
        font-size: 3rem;
        color: #10b981;
        margin-bottom: 15px;
    }

    .rfid-error-icon {
        font-size: 3rem;
        color: #ef4444;
        margin-bottom: 15px;
    }

    .rfid-modal-message {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .rfid-modal-close {
        margin-top: 20px;
        padding: 10px 20px;
        background: #6ea988;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    .rfid-modal-close:hover {
        background: #5b9474;
    }
</style>

<div class="attendance-now-container">
    <a href="<?= base_url('/') ?>" class="attendance-back-btn">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>
    
    <div class="attendance-header">
        <h1><i class="fas fa-clock"></i> Attendance Check-in / Check-out</h1>
        <?php if ($isLoggedIn && $employee): ?>
            <p>Welcome, <?= esc($employee->first_name . ' ' . $employee->last_name) ?></p>
        <?php else: ?>
            <p>Tap your RFID card to record attendance</p>
        <?php endif; ?>
    </div>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= session()->get('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= session()->get('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle"></i> <?= session()->get('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="attendance-time">
        <div class="attendance-time-label">Current Time</div>
        <div class="attendance-time-value" id="currentTime"><?= $currentTime ?></div>
    </div>

    <!-- Tab Navigation -->
    <div class="attendance-tabs">
        <?php if ($isLoggedIn): ?>
            <button class="tab-btn active" onclick="switchTab('manual')">
                <i class="fas fa-hand-pointer"></i> Manual
            </button>
        <?php endif; ?>
        <button class="tab-btn <?= !$isLoggedIn ? 'active' : '' ?>" onclick="switchTab('rfid')">
            <i class="fas fa-id-card"></i> RFID Tap
        </button>
    </div>

    <!-- Manual Buttons Tab (Only for logged in users) -->
    <?php if ($isLoggedIn): ?>
        <div id="manual" class="tab-content active">
            <div class="attendance-actions">
                <a href="<?= base_url('attendance/check-in') ?>" class="attendance-btn btn-check-in">
                    <i class="fas fa-sign-in-alt"></i>
                    Check In
                </a>
                <a href="<?= base_url('attendance/break-out') ?>" class="attendance-btn btn-break-out">
                    <i class="fas fa-door-open"></i>
                    Break Out
                </a>
                <a href="<?= base_url('attendance/break-in') ?>" class="attendance-btn btn-break-in">
                    <i class="fas fa-door-closed"></i>
                    Break In
                </a>
                <a href="<?= base_url('attendance/check-out') ?>" class="attendance-btn btn-check-out">
                    <i class="fas fa-sign-out-alt"></i>
                    Check Out
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- RFID Tap Tab -->
    <div id="rfid" class="tab-content <?= !$isLoggedIn ? 'active' : '' ?>">
        <div class="rfid-input-box">
            <div class="rfid-hint">
                <i class="fas fa-info-circle"></i> Tap your RFID card on the reader
            </div>
            <input 
                type="text" 
                id="rfidInput" 
                class="rfid-input" 
                placeholder="RFID will appear here automatically"
                autocomplete="off"
                autofocus
            >
            <div id="rfidStatus" class="rfid-status"></div>
            <button type="button" class="btn btn-outline-secondary" onclick="clearRfidInput()">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
    </div>

    <?php if ($isLoggedIn): ?>
        <?php if (!empty($todayRecords)): ?>
            <div class="attendance-records">
                <h3><i class="fas fa-history"></i> Today's Records</h3>
                <?php foreach ($todayRecords as $record): ?>
                    <div class="record-item">
                        <div class="record-info">
                            <div class="record-time">
                                <i class="fas fa-clock"></i> 
                                Check In: <?= !empty($record->time_in) ? date('h:i A', strtotime($record->time_in)) : '-' ?>
                            </div>
                            <div class="record-label">
                                Check Out: <?= !empty($record->time_out) ? date('h:i A', strtotime($record->time_out)) : 'Not checked out' ?>
                            </div>
                            <?php if (!empty($record->break_out)): ?>
                                <div class="record-label">
                                    <i class="fas fa-pause"></i> Break: <?= date('h:i A', strtotime($record->break_out)) ?> 
                                    <?php if (!empty($record->break_in)): ?>
                                        to <?= date('h:i A', strtotime($record->break_in)) ?>
                                    <?php else: ?>
                                        (ongoing)
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="badge <?= $record->status === 'Late' ? 'badge-late' : 'badge-present' ?>">
                                <?= esc($record->status ?? 'Present') ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="attendance-records">
                <div class="no-records">
                    <p><i class="fas fa-inbox"></i> No records yet. Check in to get started.</p>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    // Update time every second
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
    }

    setInterval(updateTime, 1000);

    // Tab switching
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected tab
        document.getElementById(tabName).classList.add('active');
        event.target.closest('.tab-btn').classList.add('active');

        // Focus on RFID input if switching to RFID tab
        if (tabName === 'rfid') {
            setTimeout(() => {
                document.getElementById('rfidInput').focus();
            }, 100);
        }
    }

    // RFID Input Handler
    let rfidBuffer = '';
    let rfidTimeout;

    document.getElementById('rfidInput').addEventListener('keydown', function(e) {
        // If Enter is pressed, process the RFID
        if (e.key === 'Enter') {
            e.preventDefault();
            processRfid(this.value);
            return;
        }

        // Clear timeout on key press
        clearTimeout(rfidTimeout);

        // Accumulate RFID data
        rfidBuffer += e.key;

        // If we have a complete RFID (typically ends with Enter or after 500ms of no input)
        rfidTimeout = setTimeout(() => {
            if (rfidBuffer.length > 5) {
                processRfid(rfidBuffer);
            }
            rfidBuffer = '';
        }, 500);
    });

    function clearRfidInput() {
        document.getElementById('rfidInput').value = '';
        document.getElementById('rfidStatus').classList.remove('active', 'success', 'error', 'waiting');
        rfidBuffer = '';
        document.getElementById('rfidInput').focus();
    }

    function showRfidStatus(message, type) {
        const statusEl = document.getElementById('rfidStatus');
        statusEl.innerHTML = message;
        statusEl.classList.remove('success', 'error', 'waiting');
        statusEl.classList.add('active', type);
    }

    function processRfid(rfidNumber) {
        if (!rfidNumber || rfidNumber.trim().length < 3) {
            showRfidStatus('Invalid RFID', 'error');
            setTimeout(clearRfidInput, 2000);
            return;
        }

        showRfidStatus('<i class="fas fa-spinner fa-spin"></i> Processing...', 'waiting');

        // Send RFID to server
        fetch('<?= base_url('api/attendance/rfid-process') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                rfid_number: rfidNumber.trim()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showRfidStatus(
                    '<i class="fas fa-check-circle"></i> ' + data.message + ' - ' + data.action,
                    'success'
                );
                
                // Show employee details modal
                displayEmployeeModal(data);
            } else {
                showRfidStatus(
                    '<i class="fas fa-exclamation-circle"></i> ' + data.message,
                    'error'
                );
                document.getElementById('rfidInput').value = '';
                setTimeout(() => {
                    document.getElementById('rfidInput').focus();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showRfidStatus(
                '<i class="fas fa-times-circle"></i> Connection error',
                'error'
            );
            document.getElementById('rfidInput').value = '';
        });

        document.getElementById('rfidInput').value = '';
        rfidBuffer = '';
    }

    function displayEmployeeModal(data) {
        const modal = document.getElementById('rfidModal');
        const content = document.getElementById('rfidModalContent');
        
        const actionColors = {
            'check-in': 'rfid-action-check-in',
            'break-out': 'rfid-action-break-out',
            'break-in': 'rfid-action-break-in',
            'check-out': 'rfid-action-check-out',
        };
        
        const actionColor = actionColors[data.actionType] || 'rfid-action-check-in';
        
        // Get employee initials for fallback
        const employeeName = data.employee.name || 'User';
        const initials = employeeName.split(' ').map(n => n.charAt(0)).join('').toUpperCase();
        
        content.innerHTML = `
            <div class="rfid-modal-success">
                <div class="rfid-success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div 
                    class="rfid-employee-avatar loading" 
                    id="avatarContainer"
                    style="position: relative; overflow: hidden;"
                >
                    <img 
                        id="employeeAvatar"
                        src="${data.employee.profile_photo}" 
                        alt="${employeeName}" 
                        class="rfid-employee-avatar"
                        style="width: 100%; height: 100%; position: absolute; top: 0; left: 0; margin: 0; border: none;"
                        onerror="showAvatarFallback('${initials}')"
                    >
                    <span id="avatarFallback" style="display: none; position: absolute; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center;">${initials}</span>
                </div>
                <div class="rfid-employee-name">${employeeName}</div>
                <div class="rfid-employee-id">ID: ${data.employee.employee_id}</div>
                
                <div class="rfid-action-badge ${actionColor}">
                    ${data.action}
                </div>
                
                <div class="rfid-employee-info">
                    <div class="rfid-info-item">
                        <span class="rfid-info-label"><i class="fas fa-briefcase"></i> Designation:</span>
                        <span class="rfid-info-value">${data.employee.designation || 'N/A'}</span>
                    </div>
                    <div class="rfid-info-item">
                        <span class="rfid-info-label"><i class="fas fa-building"></i> Department:</span>
                        <span class="rfid-info-value">${data.employee.department || 'N/A'}</span>
                    </div>
                    <div class="rfid-info-item">
                        <span class="rfid-info-label"><i class="fas fa-clock"></i> Time:</span>
                        <span class="rfid-info-value">${data.timestamp}</span>
                    </div>
                    <div class="rfid-info-item">
                        <span class="rfid-info-label"><i class="fas fa-calendar"></i> Date:</span>
                        <span class="rfid-info-value">${data.date}</span>
                    </div>
                </div>
                
                <div class="rfid-modal-message">
                    ✓ Attendance Recorded Successfully
                </div>
                
                <button class="rfid-modal-close" onclick="closeRfidModal()">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>
        `;
        
        modal.classList.add('active');
        
        // Remove loading animation when image loads
        const img = document.getElementById('employeeAvatar');
        img.addEventListener('load', function() {
            document.getElementById('avatarContainer').classList.remove('loading');
        });
        
        // Auto-close after 5 seconds
        setTimeout(() => {
            if (modal.classList.contains('active')) {
                closeRfidModal();
            }
        }, 5000);
    }

    function showAvatarFallback(initials) {
        const img = document.getElementById('employeeAvatar');
        if (img) {
            img.style.display = 'none';
        }
        const fallback = document.getElementById('avatarFallback');
        if (fallback) {
            fallback.style.display = 'flex';
        }
        const container = document.getElementById('avatarContainer');
        if (container) {
            container.classList.remove('loading');
        }
    }

    function closeRfidModal() {
        const modal = document.getElementById('rfidModal');
        modal.classList.remove('active');
        document.getElementById('rfidInput').focus();
        clearRfidInput();
    }

    // Close modal when clicking outside
    document.getElementById('rfidModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRfidModal();
        }
    });


    // Auto-focus RFID input when page loads if on RFID tab
    window.addEventListener('load', function() {
        if (document.getElementById('rfid').classList.contains('active')) {
            document.getElementById('rfidInput').focus();
        }
    });
</script>

<!-- RFID Success Modal -->
<div id="rfidModal" class="rfid-modal-overlay">
    <div class="rfid-modal-content" id="rfidModalContent">
        <!-- Modal content will be inserted here by JavaScript -->
    </div>
</div>

<?= $this->endSection() ?>
