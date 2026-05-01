<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    * {
        box-sizing: border-box;
    }

    .attendance-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header Section */
    .attendance-header {
        margin-bottom: 30px;
    }

    .breadcrumb-nav {
        margin-bottom: 20px;
        font-size: 0.95rem;
        color: #64748b;
    }

    .breadcrumb-nav a {
        color: #2f5f45;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-nav a:hover {
        text-decoration: underline;
    }

    /* RFID Scanner Section */
    .rfid-scanner-section {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        border-radius: 15px;
        padding: 35px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 8px 24px rgba(47, 95, 69, 0.2);
    }

    .rfid-scanner-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rfid-scanner-subtitle {
        font-size: 0.95rem;
        opacity: 0.95;
        margin-bottom: 25px;
    }

    .rfid-scanner-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        align-items: start;
    }

    .rfid-input-wrapper {
        position: relative;
    }

    .rfid-input-wrapper input {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        font-size: 1.1rem;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-family: 'Courier New', monospace;
        transition: all 0.3s ease;
    }

    .rfid-input-wrapper input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .rfid-input-wrapper input:focus {
        outline: none;
        border-color: white;
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
    }

    .rfid-scanner-icon {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.5rem;
        opacity: 0.8;
    }

    .scanner-info {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        font-size: 0.95rem;
    }

    .info-item i {
        font-size: 1.3rem;
        min-width: 30px;
        text-align: center;
    }

    .info-item strong {
        font-weight: 600;
    }

    .realtime-display {
        font-family: 'Courier New', monospace;
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: 0.05em;
    }

    /* Main Grid Layout */
    .dashboard-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    /* Employee & Status Card */
    .employee-status-card,
    .todays-attendance-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .employee-status-card:hover,
    .todays-attendance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        padding: 20px 25px;
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-body {
        padding: 25px;
    }

    .employee-info {
        text-align: center;
        margin-bottom: 25px;
    }

    .employee-avatar {
        width: 80px;
        height: 80px;
        margin: 0 auto 15px;
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }

    .employee-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2f5f45;
        margin-bottom: 8px;
    }

    .employee-id {
        font-size: 0.95rem;
        color: #64748b;
        font-family: 'Courier New', monospace;
        font-weight: 600;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        padding-top: 20px;
        border-top: 2px solid #eef2f7;
    }

    .info-block {
        text-align: center;
    }

    .info-label {
        font-size: 0.85rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2f5f45;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }

    /* Today's Attendance Card */
    .time-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }

    .time-block {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
    }

    .time-block.checked-in {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    }

    .time-block.checked-out {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    }

    .time-label {
        font-size: 0.85rem;
        color: #495057;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .time-value {
        font-size: 2rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        color: #2f5f45;
        margin-bottom: 5px;
    }

    .time-status {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 600;
    }

    .duration-info {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        border: 2px solid #dee2e6;
    }

    .duration-label {
        font-size: 0.85rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .duration-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2f5f45;
        font-family: 'Courier New', monospace;
    }

    .elapsed-time {
        font-size: 1.2rem;
        color: #27ae60;
        font-weight: 700;
        margin-top: 8px;
        font-family: 'Courier New', monospace;
    }

    /* Statistics Section */
    .stats-section {
        margin-bottom: 30px;
    }

    .stats-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2f5f45;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease;
        border-top: 4px solid #6ea988;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-card.late {
        border-top-color: #f39c12;
    }

    .stat-card.absent {
        border-top-color: #e74c3c;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #6ea988;
    }

    .stat-card.late .stat-value {
        color: #f39c12;
    }

    .stat-card.absent .stat-value {
        color: #e74c3c;
    }

    .stat-card.present .stat-value {
        color: #27ae60;
    }

    /* Attendance History */
    .attendance-history {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .history-header {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        padding: 22px 25px;
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table thead th {
        background: #f8f9fa;
        color: #2f5f45;
        font-weight: 700;
        padding: 16px 20px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .history-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #eef2f7;
        font-size: 0.95rem;
        color: #495057;
    }

    .history-table tbody tr:hover {
        background: #f8f9ff;
    }

    .history-table tbody tr:last-child td {
        border-bottom: none;
    }

    .date-cell {
        font-weight: 600;
        color: #2f5f45;
    }

    .day-cell {
        font-size: 0.85rem;
        color: #7f8c8d;
    }

    .time-cell {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        background: #f5f7fa;
        padding: 6px 10px;
        border-radius: 6px;
        display: inline-block;
    }

    .duration-cell {
        font-weight: 700;
        color: #2f5f45;
    }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-present {
        background: #d4edda;
        color: #155724;
    }

    .badge-absent {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-late {
        background: #fff3cd;
        color: #856404;
    }

    .badge-halfday {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-active {
        background: #cfe2ff;
        color: #084298;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    .empty-state {
        text-align: center;
        padding: 60px 30px;
        color: #95a5a6;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .pagination-wrap {
        padding: 20px 25px;
        border-top: 1px solid #eef2f7;
        background: #f8fafc;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 1024px) {
        .dashboard-content {
            grid-template-columns: 1fr;
        }

        .rfid-scanner-content {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .rfid-scanner-section {
            padding: 25px;
        }

        .card-body {
            padding: 20px;
        }

        .time-section {
            grid-template-columns: 1fr;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="attendance-container">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> / 
        <span style="color: #2f5f45; font-weight: 600;">Attendance</span>
    </div>



<!-- Attendance History Section -->
    <?php if (!empty($records)): ?>
    <div class="attendance-history">
        <div class="history-header">
            <i class="fas fa-history"></i> Attendance History (<?= count($records ?? []) ?> records)
        </div>

        <div class="table-wrapper">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Break Out</th>
                        <th>Break In</th>
                        <th>Check Out</th>
                        <th>Duration</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $i => $record): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div class="date-cell"><?= date('M d, Y', strtotime($record->date ?? date('Y-m-d'))) ?></div>
                            <div class="day-cell"><?= date('l', strtotime($record->date ?? date('Y-m-d'))) ?></div>
                        </td>
                        <td>
                            <?php if ($record->time_in): ?>
                                <span class="time-cell">
                                    <i class="fas fa-sign-in-alt" style="margin-right: 4px;"></i>
                                    <?= date('H:i', strtotime($record->time_in)) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #95a5a6;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($record->break_out): ?>
                                <span class="time-cell">
                                    <i class="fas fa-pause" style="margin-right: 4px; color: #f39c12;"></i>
                                    <?= date('H:i', strtotime($record->break_out)) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #95a5a6;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($record->break_in): ?>
                                <span class="time-cell">
                                    <i class="fas fa-play" style="margin-right: 4px; color: #3498db;"></i>
                                    <?= date('H:i', strtotime($record->break_in)) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #95a5a6;">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($record->time_out): ?>
                                <span class="time-cell">
                                    <i class="fas fa-sign-out-alt" style="margin-right: 4px;"></i>
                                    <?= date('H:i', strtotime($record->time_out)) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #95a5a6;">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="duration-cell">
                            <?php
                                if ($record->time_in && $record->time_out) {
                                    $start = strtotime($record->time_in);
                                    $end = strtotime($record->time_out);
                                    $duration = round(($end - $start) / 3600, 1);
                                    echo $duration . ' hrs';
                                } else {
                                    echo '<span style="color: #95a5a6;">-</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                                $status = strtolower((string) ($record->status ?? 'absent'));
                                $badgeClass = match($status) {
                                    'present' => 'badge-present',
                                    'late' => 'badge-late',
                                    'half-day', 'half day' => 'badge-halfday',
                                    default => 'badge-absent'
                                };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= ucwords(str_replace('-', ' ', $status)) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager) && $pager): ?>
        <div class="pagination-wrap">
            <?= $pager->links() ?>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>No Attendance Records</h3>
        <p>No attendance records found for the selected period.</p>
    </div>
    <?php endif; ?>
</div>

<script>
// Update real-time clock and date
function updateRealtimeClock() {
    const now = new Date();
    
    // Update date
    const dateOptions = { weekday: 'short', year: 'numeric', month: 'short', day: '2-digit' };
    document.getElementById('todayDate').textContent = now.toLocaleDateString('en-US', dateOptions);
    
    // Update time
    const timeString = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    document.getElementById('currentTime').textContent = timeString;
}

// Update elapsed time for active sessions
function updateElapsedTimes() {
    const elapsedElements = document.querySelectorAll('.elapsed-time[data-checkin]');
    elapsedElements.forEach(el => {
        const checkInTime = el.getAttribute('data-checkin');
        if (!checkInTime) return;

        const [hours, minutes, seconds] = checkInTime.split(':').map(Number);
        const checkInDate = new Date();
        checkInDate.setHours(hours, minutes, seconds, 0);

        const now = new Date();
        const diffMs = now - checkInDate;
        const diffSeconds = Math.floor(diffMs / 1000);
        const hrs = Math.floor(diffSeconds / 3600);
        const mins = Math.floor((diffSeconds % 3600) / 60);
        const secs = diffSeconds % 60;

        el.textContent = String(hrs).padStart(2, '0') + ':' + 
                        String(mins).padStart(2, '0') + ':' + 
                        String(secs).padStart(2, '0');
    });
}

// Initialize clocks
updateRealtimeClock();
updateElapsedTimes();
setInterval(updateRealtimeClock, 1000);
setInterval(updateElapsedTimes, 1000);

// RFID Scanner functionality
document.getElementById('rfidInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const rfidValue = this.value.trim();
        if (rfidValue) {
            // Submit RFID to backend for attendance recording
            fetch('<?= base_url('attendance/recordRfid') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ rfid_number: rfidValue })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const action = data.action === 'checkout' ? 'Check Out' : 'Check In';
                    alert('✓ ' + action + ' successful!');
                    this.value = '';
                    // Reload page after a short delay
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('✗ Error: ' + (data.message || 'Failed to record attendance'));
                    this.value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('✗ Error recording attendance. Please try again.');
                this.value = '';
            });
        }
    }
});
</script>

<?= $this->endSection() ?>
