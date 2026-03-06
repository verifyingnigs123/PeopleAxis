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

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
    }

    .btn-secondary {
        background: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-danger:hover {
        background: #c0392b;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .detail-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 25px;
    }

    .section-title {
        color: #1e3c72;
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
        margin-bottom: 5px;
        letter-spacing: 0.5px;
    }

    .detail-value {
        color: #2c3e50;
        font-size: 1rem;
        font-weight: 500;
    }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-active {
        background: #d4edda;
        color: #155724;
    }

    .badge-inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-suspended {
        background: #fff3cd;
        color: #856404;
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .action-buttons {
            width: 100%;
        }

        .action-buttons .btn {
            flex: 1;
            justify-content: center;
        }
    }
</style>

<!-- Breadcrumbs -->
<div style="margin-bottom: 20px;">
    <a href="<?= base_url('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a> /
    <a href="<?= base_url('employee') ?>">Employees</a> /
    <span><?= esc($employee->first_name . ' ' . $employee->last_name) ?></span>
</div>

<!-- Page Header with Actions -->
<div class="page-header">
    <h1><i class="fas fa-user"></i> <?= esc($employee->first_name . ' ' . $employee->last_name) ?></h1>
    <div class="action-buttons">
        <?php if (in_array(session()->get('role'), ['hr']) || in_array(session()->get('role_name'), ['HR Admin'])): ?>
            <?php if (($employee->account_status ?? '') === 'rejected'): ?>
            <button class="btn btn-reapply" onclick="openEditModal(true)">
                <i class="fas fa-redo"></i> Re-apply for Approval
            </button>
            <?php else: ?>
            <button class="btn btn-primary" onclick="openEditModal(false)">
                <i class="fas fa-edit"></i> Edit Employee
            </button>
            <?php endif; ?>
        <?php endif; ?>
        <a href="<?= base_url('employee') ?>" class="btn btn-secondary">
            <i class="fas fa-list"></i> Back to List
        </a>
    </div>
</div>

<!-- Employee Details -->
<div class="detail-grid">
    <!-- Personal Information -->
    <div class="detail-section">
        <h3 class="section-title"><i class="fas fa-address-card"></i> Personal Information</h3>
        
        <div class="detail-row">
            <span class="detail-label">Employee ID</span>
            <span class="detail-value"><?= esc($employee->employee_id) ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Full Name</span>
            <span class="detail-value"><?= esc($employee->first_name . ' ' . $employee->last_name) ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Email Address</span>
            <span class="detail-value">
                <a href="mailto:<?= esc($employee->email) ?>" style="color: #2a5298; text-decoration: none;">
                    <?= esc($employee->email) ?>
                </a>
            </span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Phone Number</span>
            <span class="detail-value"><?= esc($employee->phone ?? 'N/A') ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Date of Birth</span>
            <span class="detail-value">
                <?php if ($employee->date_of_birth): ?>
                    <?= date('F d, Y', strtotime($employee->date_of_birth)) ?>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </span>
        </div>
    </div>

    <!-- Employment Information -->
    <div class="detail-section">
        <h3 class="section-title"><i class="fas fa-briefcase"></i> Employment Information</h3>

        <div class="detail-row">
            <span class="detail-label">Department</span>
            <span class="detail-value"><?= $department ? esc($department->name) : 'N/A' ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Position</span>
            <span class="detail-value"><?= $position ? esc($position->name) : 'N/A' ?></span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Date of Joining</span>
            <span class="detail-value">
                <?php if ($employee->date_of_joining): ?>
                    <?= date('F d, Y', strtotime($employee->date_of_joining)) ?>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </span>
        </div>

        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <?php 
                    $statusClass = 'badge-' . strtolower($employee->status);
                    $statusText = ucfirst($employee->status ?? 'active');
                ?>
                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
            </span>
        </div>
    </div>
</div>

<?php if (in_array(session()->get('role'), ['hr']) || in_array(session()->get('role_name'), ['HR Admin'])): ?>
<!-- ═══ Edit Employee Modal ═══ -->
<div id="editEmployeeModal" class="edit-modal-overlay">
    <div class="edit-modal-box">
        <div class="edit-modal-header">
            <h2><i class="fas fa-user-edit"></i> Edit Employee</h2>
            <button class="edit-modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="edit-modal-body">
            <!-- Re-apply notice (only shown in re-apply mode) -->
            <div id="reapplyNotice" class="reapply-notice">
                <i class="fas fa-info-circle" style="font-size:1.1rem;margin-top:2px;"></i>
                <div>
                    <strong>Re-applying for approval.</strong> You may update any details below before submitting.
                    Once submitted, your application will be sent back to the Super Admin for review.
                </div>
            </div>
            <!-- Alerts -->
            <div id="editErrors" class="edit-alert edit-alert-danger" style="display:none;">
                <i class="fas fa-exclamation-circle"></i>
                <div id="editErrorList"></div>
            </div>
            <div id="editSuccess" class="edit-alert edit-alert-success" style="display:none;">
                <i class="fas fa-check-circle"></i>
                <div id="editSuccessMsg"></div>
            </div>

            <form id="editEmployeeForm">
                <?= csrf_field() ?>

                <div class="edit-form-row">
                    <div class="edit-form-group">
                        <label class="edit-label">First Name <span class="edit-required">*</span></label>
                        <input type="text" class="edit-input" id="edit_first_name" name="first_name"
                               value="<?= esc($employee->first_name) ?>" required autocomplete="off">
                        <span class="edit-field-error" id="err_first_name"></span>
                    </div>
                    <div class="edit-form-group">
                        <label class="edit-label">Last Name <span class="edit-required">*</span></label>
                        <input type="text" class="edit-input" id="edit_last_name" name="last_name"
                               value="<?= esc($employee->last_name) ?>" required autocomplete="off">
                        <span class="edit-field-error" id="err_last_name"></span>
                    </div>
                </div>

                <div class="edit-form-row">
                    <div class="edit-form-group">
                        <label class="edit-label">Email <span class="edit-required">*</span></label>
                        <input type="email" class="edit-input" id="edit_email" name="email"
                               value="<?= esc($employee->email) ?>" required autocomplete="off">
                        <span class="edit-field-error" id="err_email"></span>
                    </div>
                    <div class="edit-form-group">
                        <label class="edit-label">Phone Number</label>
                        <input type="tel" class="edit-input" id="edit_phone" name="phone"
                               value="<?= esc($employee->phone ?? '') ?>" autocomplete="off">
                        <span class="edit-field-error" id="err_phone"></span>
                    </div>
                </div>

                <div class="edit-form-row">
                    <div class="edit-form-group">
                        <label class="edit-label">Department</label>
                        <select class="edit-input" id="edit_department_id" name="department_id">
                            <option value="">Select Department</option>
                            <?php foreach ($departments ?? [] as $dept): ?>
                                <option value="<?= $dept->id ?>" <?= ($employee->department_id == $dept->id) ? 'selected' : '' ?>>
                                    <?= esc($dept->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="edit-form-group">
                        <label class="edit-label">Position</label>
                        <select class="edit-input" id="edit_position_id" name="position_id">
                            <option value="">Select Position</option>
                            <?php foreach ($positions ?? [] as $pos): ?>
                                <option value="<?= $pos->id ?>" <?= ($employee->position_id == $pos->id) ? 'selected' : '' ?>>
                                    <?= esc($pos->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="edit-form-row">
                    <div class="edit-form-group">
                        <label class="edit-label">Date of Birth</label>
                        <input type="date" class="edit-input" id="edit_date_of_birth" name="date_of_birth"
                               value="<?= esc($employee->date_of_birth ?? '') ?>">
                    </div>
                    <div class="edit-form-group">
                        <label class="edit-label">Date of Joining <span class="edit-required">*</span></label>
                        <input type="date" class="edit-input" id="edit_date_of_joining" name="date_of_joining"
                               value="<?= esc($employee->date_of_joining ?? '') ?>" required>
                    </div>
                </div>

                <div class="edit-form-row">
                    <div class="edit-form-group">
                        <label class="edit-label">Status</label>
                        <select class="edit-input" id="edit_status" name="status">
                            <option value="active"   <?= $employee->status === 'active'    ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $employee->status === 'inactive'  ? 'selected' : '' ?>>Inactive</option>
                            <option value="suspended" <?= $employee->status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="edit-modal-actions">
                    <button type="submit" class="btn btn-success" id="editSubmitBtn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-success {
        background: linear-gradient(135deg, #1a6b39 0%, #27ae60 100%);
        color: white;
    }
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26,107,57,0.35);
    }
    .btn-reapply {
        background: linear-gradient(135deg, #b7770d 0%, #f39c12 100%);
        color: white;
    }
    .btn-reapply:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(243,156,18,0.4);
    }
    .edit-modal-overlay {
        display: none;
        position: fixed;
        z-index: 1100;
        inset: 0;
        background: rgba(0,0,0,.52);
        align-items: center;
        justify-content: center;
        animation: emFadeIn .25s ease;
    }
    .edit-modal-overlay.show { display: flex; }
    @keyframes emFadeIn { from{opacity:0} to{opacity:1} }
    .edit-modal-box {
        background: #fff;
        border-radius: 10px;
        width: 92%;
        max-width: 740px;
        max-height: 92vh;
        overflow-y: auto;
        box-shadow: 0 8px 32px rgba(0,0,0,.22);
        animation: emSlideIn .28s ease;
    }
    @keyframes emSlideIn { from{transform:translateY(-40px);opacity:0} to{transform:translateY(0);opacity:1} }
    .edit-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 22px 28px;
        background: linear-gradient(135deg,#1a6b39 0%,#27ae60 100%);
        color: #fff;
        border-radius: 10px 10px 0 0;
    }
    .edit-modal-header h2 { margin:0; font-size:1.25rem; font-weight:700; }
    .edit-modal-close {
        background: none; border: none; color: #fff;
        font-size: 2rem; cursor: pointer; line-height:1;
        width:36px; height:36px; border-radius:50%;
        display:flex; align-items:center; justify-content:center;
        transition: background .2s;
    }
    .edit-modal-close:hover { background: rgba(255,255,255,.2); }
    .edit-modal-body  { padding: 28px; }
    .edit-form-row    { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px; }
    .edit-form-group  { display:flex; flex-direction:column; }
    .edit-label       { font-weight:600; color:#2c3e50; font-size:.92rem; margin-bottom:6px; }
    .edit-required    { color:#e74c3c; }
    .edit-input {
        padding: 9px 12px;
        border: 1px solid #d5dce0;
        border-radius: 6px;
        font-size: .93rem;
        font-family: inherit;
        width: 100%;
        box-sizing: border-box;
        transition: border-color .25s;
    }
    .edit-input:focus { outline:none; border-color:#27ae60; box-shadow:0 0 0 3px rgba(39,174,96,.12); }
    .edit-input.is-invalid { border-color:#e74c3c; box-shadow:0 0 0 3px rgba(231,76,60,.12); }
    .edit-input.is-valid   { border-color:#27ae60; box-shadow:0 0 0 3px rgba(39,174,96,.10); }
    .edit-field-error {
        font-size:.82rem;
        color:#e74c3c;
        margin-top:5px;
        display:none;
    }
    .edit-field-error.visible { display:block; }
    .edit-alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 7px;
        margin-bottom: 18px;
        font-size: .92rem;
    }
    .edit-alert-danger  { background:#fadbd8; color:#c0392b; border-left:4px solid #c0392b; }
    .edit-alert-success { background:#d5f5e3; color:#1e8449; border-left:4px solid #27ae60; }
    .edit-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 26px;
        padding-top: 18px;
        border-top: 1px solid #e8eef5;
    }
    /* Re-apply banner */
    .reapply-notice {
        display: none;
        background: #fff8ec;
        border-left: 4px solid #f39c12;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 18px;
        font-size: .92rem;
        color: #7d5a00;
        gap: 10px;
        align-items: flex-start;
    }
    .reapply-notice.visible { display: flex; }
    .edit-modal-overlay.reapply-mode .edit-modal-header {
        background: linear-gradient(135deg, #b7770d 0%, #f39c12 100%);
    }
    .edit-modal-overlay.reapply-mode #editSubmitBtn {
        background: linear-gradient(135deg, #b7770d 0%, #f39c12 100%);
    }
        .edit-modal-actions { flex-direction:column; }
        .edit-modal-actions .btn { width:100%; justify-content:center; }
    }
</style>

<script>
    const REAPPLY_URL = '<?= base_url('employee/re-apply/' . $employee->id) ?>';
    const EDIT_URL    = '<?= base_url('employee/update/' . $employee->id) ?>';

    // ── Validation helpers ──
    const RULES = {
        first_name:     { label: 'First Name',    required: true,  pattern: /^[A-Za-z\s\-\']+$/, msg: 'First name must contain only letters, spaces, hyphens or apostrophes — no special characters.' },
        last_name:      { label: 'Last Name',     required: true,  pattern: /^[A-Za-z\s\-\']+$/, msg: 'Last name must contain only letters, spaces, hyphens or apostrophes — no special characters.' },
        email:          { label: 'Email',         required: true,  pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, msg: 'Please enter a valid email address.' },
        phone:          { label: 'Phone',         required: false, pattern: /^[0-9\s\+\-\(\)]*$/, msg: 'Phone must contain only digits, spaces, +, -, or parentheses — no special characters.' },
    };

    function showFieldError(id, msg) {
        const input = document.getElementById('edit_' + id);
        const span  = document.getElementById('err_' + id);
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        if (span) { span.textContent = msg; span.classList.add('visible'); }
    }
    function clearFieldError(id) {
        const input = document.getElementById('edit_' + id);
        const span  = document.getElementById('err_' + id);
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        if (span) { span.textContent = ''; span.classList.remove('visible'); }
    }
    function clearAllFieldErrors() {
        Object.keys(RULES).forEach(id => {
            const input = document.getElementById('edit_' + id);
            const span  = document.getElementById('err_' + id);
            if (input) { input.classList.remove('is-invalid','is-valid'); }
            if (span)  { span.textContent = ''; span.classList.remove('visible'); }
        });
    }

    // Live validation on input
    Object.entries(RULES).forEach(([id, rule]) => {
        const el = document.getElementById('edit_' + id);
        if (!el) return;
        el.addEventListener('input', () => validateField(id, el.value.trim()));
        el.addEventListener('blur',  () => validateField(id, el.value.trim()));
    });

    function validateField(id, value) {
        const rule = RULES[id];
        if (rule.required && value === '') {
            showFieldError(id, rule.label + ' is required.'); return false;
        }
        if (value !== '' && rule.pattern && !rule.pattern.test(value)) {
            showFieldError(id, rule.msg); return false;
        }
        clearFieldError(id); return true;
    }

    function validateAll() {
        let valid = true;
        Object.entries(RULES).forEach(([id, rule]) => {
            const el = document.getElementById('edit_' + id);
            if (!el) return;
            if (!validateField(id, el.value.trim())) valid = false;
        });
        return valid;
    }

    // ── Modal open / close ──
    function openEditModal(isReapply) {
        isReapply = !!isReapply;
        clearAllFieldErrors();
        document.getElementById('editErrors').style.display  = 'none';
        document.getElementById('editSuccess').style.display = 'none';

        const overlay = document.getElementById('editEmployeeModal');
        const header  = overlay.querySelector('.edit-modal-header h2');
        const btn     = document.getElementById('editSubmitBtn');
        const notice  = document.getElementById('reapplyNotice');

        if (isReapply) {
            overlay.classList.add('reapply-mode');
            header.innerHTML  = '<i class="fas fa-redo"></i> Re-apply for Approval';
            btn.innerHTML     = '<i class="fas fa-paper-plane"></i> Apply for Approval';
            btn.dataset.mode  = 'reapply';
            notice.classList.add('visible');
        } else {
            overlay.classList.remove('reapply-mode');
            header.innerHTML  = '<i class="fas fa-user-edit"></i> Edit Employee';
            btn.innerHTML     = '<i class="fas fa-save"></i> Save Changes';
            btn.dataset.mode  = 'edit';
            notice.classList.remove('visible');
        }

        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeEditModal() {
        document.getElementById('editEmployeeModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        clearAllFieldErrors();
        document.getElementById('editErrors').style.display  = 'none';
        document.getElementById('editSuccess').style.display = 'none';
    }
    document.getElementById('editEmployeeModal').addEventListener('click', function(e){
        if (e.target === this) closeEditModal();
    });
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeEditModal();
    });

    // ── Form submit ──
    document.getElementById('editEmployeeForm').addEventListener('submit', function(e){
        e.preventDefault();

        // Client-side validation first
        if (!validateAll()) {
            document.getElementById('editErrors').style.display = 'none';
            return;
        }

        const errDiv  = document.getElementById('editErrors');
        const errList = document.getElementById('editErrorList');
        const sucDiv  = document.getElementById('editSuccess');
        const sucMsg  = document.getElementById('editSuccessMsg');
        const btn     = document.getElementById('editSubmitBtn');

        const isReapply = (btn.dataset.mode === 'reapply');
        const endpoint  = isReapply ? REAPPLY_URL : EDIT_URL;

        errDiv.style.display = 'none';
        sucDiv.style.display = 'none';
        btn.disabled = true;
        btn.innerHTML = isReapply
            ? '<i class="fas fa-spinner fa-spin"></i> Submitting&hellip;'
            : '<i class="fas fa-spinner fa-spin"></i> Saving&hellip;';

        const formData = new FormData(this);

        fetch(endpoint, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (data.csrf_hash) {
                const csrfInput = document.querySelector('#editEmployeeForm input[name="<?= csrf_token() ?>"]');
                if (csrfInput) csrfInput.value = data.csrf_hash;
            }
            if (data.success) {
                sucDiv.style.display = 'flex';
                sucMsg.innerHTML = isReapply
                    ? '<strong>Re-application submitted!</strong> The Super Admin has been notified and will review your application.'
                    : '<strong>Employee updated successfully!</strong>';
                setTimeout(() => window.location.reload(), 2000);
            } else {
                if (data.errors) {
                    let unmapped = [];
                    Object.entries(data.errors).forEach(([field, msg]) => {
                        const el = document.getElementById('edit_' + field);
                        if (el) { showFieldError(field, msg); }
                        else    { unmapped.push(msg); }
                    });
                    if (unmapped.length) {
                        errDiv.style.display = 'flex';
                        errList.innerHTML = '<strong>Errors:</strong><ul style="margin:8px 0 0 0;padding-left:20px;">' +
                            unmapped.map(m => `<li>${m}</li>`).join('') + '</ul>';
                    }
                } else {
                    errDiv.style.display = 'flex';
                    errList.innerHTML = `<strong>${data.message ?? 'An error occurred.'}</strong>`;
                }
            }
        })
        .catch(() => {
            errDiv.style.display = 'flex';
            errList.innerHTML = '<strong>Network error. Please try again.</strong>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = isReapply
                ? '<i class="fas fa-paper-plane"></i> Apply for Approval'
                : '<i class="fas fa-save"></i> Save Changes';
        });
    });
</script>
<?php endif; ?>

<?= $this->endSection() ?>
