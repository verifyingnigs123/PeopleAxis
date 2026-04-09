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
        color: #2f5f45;
        font-weight: 700;
        margin: 0;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    @media (max-width: 768px) {
        .detail-grid { grid-template-columns: 1fr; }
    }

    .detail-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 25px;
    }

    .section-title {
        color: #2f5f45;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e8eef5;
    }

    .detail-row {
        margin-bottom: 18px;
    }

    .detail-label {
        display: block;
        color: #7f8c8d;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .detail-value {
        color: #2c3e50;
        font-size: 1rem;
        font-weight: 500;
    }

    .badge {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-pending  { background: #fff3cd; color: #856404; }
    .badge-approved { background: #d4edda; color: #155724; }
    .badge-rejected { background: #f8d7da; color: #721c24; }

    /* Approval action card */
    .approval-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 30px;
        margin-bottom: 30px;
    }

    .approval-card h3 {
        color: #2f5f45;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .approval-card p {
        color: #555;
        margin-bottom: 25px;
    }

    .approval-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 28px;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-accept {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: white;
    }

    .btn-accept:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
    }

    .btn-reject {
        background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
        color: white;
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(192, 57, 43, 0.4);
    }

    .btn-back {
        background: #95a5a6;
        color: white;
    }

    .btn-back:hover { background: #7f8c8d; }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    /* Status banners */
    .status-banner {
        padding: 18px 24px;
        border-radius: 8px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 600;
        font-size: 1rem;
    }

    .status-banner.approved {
        background: #d4edda;
        color: #155724;
        border-left: 5px solid #28a745;
    }

    .status-banner.rejected {
        background: #f8d7da;
        color: #721c24;
        border-left: 5px solid #dc3545;
    }

    .status-banner.pending {
        background: #fff3cd;
        color: #856404;
        border-left: 5px solid #ffc107;
    }

    /* Rejection notes textarea */
    .rejection-notes {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d5dce0;
        border-radius: 6px;
        font-size: 0.95rem;
        font-family: inherit;
        resize: vertical;
        min-height: 100px;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }

    .rejection-notes:focus {
        outline: none;
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .rejection-form-area {
        background: #fff5f5;
        border: 1px solid #f5c6cb;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        display: none;
    }

    .rejection-form-area label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #721c24;
    }

    /* Alert */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
    .alert-danger  { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <a href="<?= base_url('employee') ?>">Employees</a> /
    <span>Review Employee</span>
</div>

<!-- Page Header -->
<div class="page-header">
    <h1><i class="fas fa-user-check"></i> Review New Employee</h1>
    <a href="<?= base_url('employee') ?>" class="btn btn-back">
        <i class="fas fa-arrow-left"></i> Back to Employees
    </a>
</div>

<!-- Flash Messages -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle fa-lg"></i>
        <span><?= session()->getFlashdata('success') ?></span>
    </div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle fa-lg"></i>
        <span><?= session()->getFlashdata('error') ?></span>
    </div>
<?php endif; ?>

<!-- API Response alert (JS-controlled) -->
<div id="apiAlert" class="alert" style="display:none;"></div>

<!-- Current status banner -->
<?php if ($employee->account_status === 'approved'): ?>
    <div class="status-banner approved">
        <i class="fas fa-check-circle fa-lg"></i>
        This employee has already been <strong>approved</strong>. An account has been created and credentials sent to <strong><?= esc($employee->email) ?></strong>.
    </div>
<?php elseif ($employee->account_status === 'rejected'): ?>
    <div class="status-banner rejected">
        <i class="fas fa-times-circle fa-lg"></i>
        This employee application has been <strong>rejected</strong>.
        <?php if (!empty($employee->approval_notes)): ?>
            Reason: <em><?= esc($employee->approval_notes) ?></em>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="status-banner pending">
        <i class="fas fa-hourglass-half fa-lg"></i>
        This employee is <strong>pending review</strong>. Please review the details below and take action.
    </div>
<?php endif; ?>

<!-- Employee Details -->
<div class="detail-grid">

    <!-- Personal Info -->
    <div class="detail-section">
        <div class="section-title"><i class="fas fa-id-card"></i> Personal Information</div>

        <div class="detail-row">
            <span class="detail-label">Employee ID</span>
            <span class="detail-value"><strong><?= esc($employee->employee_id) ?></strong></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Full Name</span>
            <span class="detail-value"><?= esc(trim($employee->first_name . ' ' . $employee->last_name)) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email Address</span>
            <span class="detail-value"><?= esc($employee->email) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">RFID Number</span>
            <span class="detail-value"><?= esc($employee->rfid_number ?? 'N/A') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Phone Number</span>
            <span class="detail-value"><?= esc($employee->phone ?? 'N/A') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Date of Birth</span>
            <span class="detail-value"><?= $employee->date_of_birth ? date('F j, Y', strtotime($employee->date_of_birth)) : 'N/A' ?></span>
        </div>
    </div>

    <!-- Employment Info -->
    <div class="detail-section">
        <div class="section-title"><i class="fas fa-briefcase"></i> Employment Information</div>

        <div class="detail-row">
            <span class="detail-label">Department</span>
            <span class="detail-value"><?= esc($department ? $department->name : 'N/A') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Position</span>
            <span class="detail-value"><?= esc($position ? $position->name : 'N/A') ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Date of Joining</span>
            <span class="detail-value"><?= $employee->date_of_joining ? date('F j, Y', strtotime($employee->date_of_joining)) : 'N/A' ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Employment Status</span>
            <span class="detail-value"><?= esc(ucfirst($employee->status ?? 'active')) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Account Status</span>
            <span class="badge badge-<?= esc($employee->account_status ?? 'pending') ?>">
                <?= esc(ucfirst($employee->account_status ?? 'pending')) ?>
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Submitted On</span>
            <span class="detail-value"><?= $employee->created_at ? date('F j, Y g:i A', strtotime($employee->created_at)) : 'N/A' ?></span>
        </div>
    </div>

</div>

<!-- Approval Action Card — only shown when status is pending -->
<?php if ($employee->account_status === 'pending'): ?>
<div class="approval-card">
    <h3><i class="fas fa-gavel"></i> Take Action</h3>
    <p>
        By accepting, you will automatically create a system account for this employee and send their login credentials to <strong><?= esc($employee->email) ?></strong>.<br>
        By rejecting, a rejection notification will be sent to <strong><?= esc($employee->email) ?></strong> and their record will be marked as rejected.
    </p>

    <div class="approval-actions">
        <button id="btnAccept" class="btn btn-accept" onclick="handleAccept()">
            <i class="fas fa-check-circle"></i> Accept &amp; Create Account
        </button>
        <button id="btnReject" class="btn btn-reject" onclick="toggleRejectForm()">
            <i class="fas fa-times-circle"></i> Reject Application
        </button>
    </div>

    <!-- Rejection notes form (shown when Reject is clicked) -->
    <div id="rejectionFormArea" class="rejection-form-area">
        <label for="rejectionNotes">Reason for Rejection <span style="color:#e74c3c;">*</span></label>
        <textarea id="rejectionNotes" class="rejection-notes" placeholder="Enter the reason for rejecting this employee application..."></textarea>
        <div style="margin-top:15px; display:flex; gap:10px; flex-wrap:wrap;">
            <button id="btnConfirmReject" class="btn btn-reject" onclick="handleReject()">
                <i class="fas fa-paper-plane"></i> Confirm Rejection &amp; Notify Employee
            </button>
            <button class="btn btn-back" onclick="toggleRejectForm()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    const employeeId   = <?= (int) $employee->id ?>;
    const approveUrl   = '<?= base_url('employee/approve-account/' . $employee->id) ?>';
    const rejectUrl    = '<?= base_url('employee/reject-account/' . $employee->id) ?>';
    const csrfName     = '<?= csrf_token() ?>';
    let   csrfHash     = '<?= csrf_hash() ?>';

    function showApiAlert(message, type) {
        const el = document.getElementById('apiAlert');
        el.className = 'alert alert-' + type;
        el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} fa-lg"></i><span>${message}</span>`;
        el.style.display = 'flex';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function setLoading(btnId, loading) {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        btn.disabled = loading;
        if (loading) {
            btn.dataset.original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        } else {
            btn.innerHTML = btn.dataset.original || btn.innerHTML;
        }
    }

    function toggleRejectForm() {
        const area = document.getElementById('rejectionFormArea');
        area.style.display = area.style.display === 'block' ? 'none' : 'block';
    }

    function handleAccept() {
        if (!confirm('Are you sure you want to ACCEPT this employee and create their account? Login credentials will be emailed to ' + '<?= esc($employee->email, 'js') ?>' + '.')) return;

        setLoading('btnAccept', true);
        document.getElementById('btnReject').disabled = true;

        const formData = new FormData();
        formData.append(csrfName, csrfHash);

        fetch(approveUrl, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.csrf_hash) csrfHash = data.csrf_hash;
            if (data.success) {
                showApiAlert(data.message, 'success');
                // Hide the action card and show approved banner
                document.querySelector('.approval-card').style.display = 'none';
                document.querySelector('.status-banner').className = 'status-banner approved';
                document.querySelector('.status-banner').innerHTML = '<i class="fas fa-check-circle fa-lg"></i> This employee has been <strong>approved</strong>. Login credentials sent to <strong><?= esc($employee->email, 'js') ?></strong>.';
                // Refresh after 3s
                setTimeout(() => location.reload(), 3000);
            } else {
                showApiAlert(data.message || 'Failed to approve employee.', 'danger');
                setLoading('btnAccept', false);
                document.getElementById('btnReject').disabled = false;
            }
        })
        .catch(err => {
            showApiAlert('An unexpected error occurred. Please try again.', 'danger');
            setLoading('btnAccept', false);
            document.getElementById('btnReject').disabled = false;
        });
    }

    function handleReject() {
        const notes = document.getElementById('rejectionNotes').value.trim();
        if (!notes) {
            document.getElementById('rejectionNotes').focus();
            document.getElementById('rejectionNotes').style.borderColor = '#e74c3c';
            alert('Please provide a reason for rejection.');
            return;
        }
        document.getElementById('rejectionNotes').style.borderColor = '';

        if (!confirm('Are you sure you want to REJECT this application? A rejection email will be sent to <?= esc($employee->email, 'js') ?>.')) return;

        setLoading('btnConfirmReject', true);

        const formData = new FormData();
        formData.append(csrfName, csrfHash);
        formData.append('rejection_notes', notes);

        fetch(rejectUrl, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.csrf_hash) csrfHash = data.csrf_hash;
            if (data.success) {
                showApiAlert(data.message, 'success');
                document.querySelector('.approval-card').style.display = 'none';
                document.querySelector('.status-banner').className = 'status-banner rejected';
                document.querySelector('.status-banner').innerHTML = '<i class="fas fa-times-circle fa-lg"></i> This employee application has been <strong>rejected</strong>. A rejection email has been sent.';
                // Remove automatic page refresh for real-time update
            } else {
                showApiAlert(data.message || 'Failed to reject application.', 'danger');
                setLoading('btnConfirmReject', false);
            }
        })
        .catch(() => {
            showApiAlert('An unexpected error occurred. Please try again.', 'danger');
            setLoading('btnConfirmReject', false);
        });
    }
</script>

<?= $this->endSection() ?>
