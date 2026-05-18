<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>
<style>
.al-tabs-nav { display:flex; gap:3px; flex-wrap:wrap; background:#fff; border-radius:14px 14px 0 0; border:1px solid #dbe9e0; border-bottom:none; padding:12px 16px 0; }
.al-tab-btn { padding:9px 14px; border:none; background:transparent; color:#6b7280; font-weight:600; font-size:.83rem; border-radius:8px 8px 0 0; cursor:pointer; border-bottom:3px solid transparent; transition:all .2s; display:flex; align-items:center; gap:5px; }
.al-tab-btn:hover { background:#f4faf6; color:#2f5f45; }
.al-tab-btn.active { color:#2f5f45; border-bottom-color:#4e8c68; background:#f4faf6; }
.al-tab-count { background:#e8f5ee; color:#2f5f45; border-radius:20px; padding:1px 7px; font-size:.72rem; font-weight:700; }
.al-tab-btn.active .al-tab-count { background:#4e8c68; color:#fff; }
.al-tab-panel { display:none; }
.al-tab-panel.active { display:block; }
.al-panel { background:#fff; border:1px solid #dbe9e0; border-top:none; border-radius:0 0 14px 14px; overflow:hidden; }
.al-ph { padding:16px 22px; display:flex; align-items:center; gap:12px; background:linear-gradient(135deg,#2f5f45,#4e8c68); }
.al-ph-icon { width:38px; height:38px; background:rgba(255,255,255,.15); border-radius:9px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1rem; }
.al-ph h2 { margin:0; font-size:1rem; font-weight:700; color:#fff; }
.al-ph p  { margin:2px 0 0; font-size:.78rem; color:rgba(255,255,255,.72); }
.al-search { padding:11px 18px; border-bottom:1px solid #e7f0eb; background:#fafcfb; display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.al-si { flex:1; min-width:180px; padding:7px 13px; border:1.5px solid #c7ddd0; border-radius:8px; font-size:.87rem; outline:none; }
.al-si:focus { border-color:#4e8c68; }
.al-sel { padding:7px 11px; border:1.5px solid #c7ddd0; border-radius:8px; font-size:.87rem; background:#fff; outline:none; cursor:pointer; }
.al-table { width:100%; border-collapse:collapse; font-size:.87rem; }
.al-table thead th { background:#f4faf6; color:#2f5f45; font-weight:700; padding:10px 16px; text-align:left; border-bottom:2px solid #dbe9e0; font-size:.74rem; text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }
.al-table tbody td { padding:11px 16px; border-bottom:1px solid #f0f6f2; color:#374151; vertical-align:middle; }
.al-table tbody tr:last-child td { border-bottom:none; }
.al-table tbody tr:hover { background:#f8fbf9; }
.al-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:.73rem; font-weight:700; white-space:nowrap; }
.b-login{background:#dbeafe;color:#1e40af} .b-logout{background:#f3f4f6;color:#374151} .b-failed{background:#fef3c7;color:#92400e} .b-locked{background:#fee2e2;color:#991b1b}
.b-create{background:#dcfce7;color:#166534} .b-update{background:#d1ecf1;color:#0c5460} .b-delete{background:#fee2e2;color:#991b1b} .b-restore{background:#fff3cd;color:#856404}
.b-leave{background:#ede9fe;color:#5b21b6} .b-approve{background:#dcfce7;color:#166534} .b-reject{background:#fee2e2;color:#991b1b} .b-salary{background:#fef9ec;color:#92400e} .b-other{background:#e2e3e5;color:#383d41}
.b-success{background:#dcfce7;color:#166534} .b-medium{background:#fef3c7;color:#92400e} .b-high{background:#fee2e2;color:#991b1b}
.u-chip { display:flex; flex-direction:column; }
.u-name { font-weight:600; color:#1e3a2f; font-size:.87rem; }
.u-role { font-size:.74rem; color:#6b7280; }
.al-ts { font-size:.78rem; color:#6b7280; white-space:nowrap; }
.al-desc { font-size:.82rem; color:#4b5563; max-width:400px; }
.al-empty { padding:46px 20px; text-align:center; color:#9ca3af; }
.al-empty i { font-size:2rem; margin-bottom:10px; display:block; color:#d1fae5; }
.al-hero { background:linear-gradient(135deg,#1e3a2f,#2f5f45 55%,#4e8c68); border-radius:14px; padding:22px 26px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px; }
.al-hero h1 { margin:0; font-size:1.45rem; font-weight:800; color:#fff; }
.al-hero p  { margin:5px 0 0; color:rgba(255,255,255,.72); font-size:.86rem; }
.al-hero-total { background:rgba(255,255,255,.15); border-radius:8px; padding:8px 16px; color:#fff; font-size:.85rem; font-weight:700; display:flex; align-items:center; gap:7px; }
.al-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:14px; margin-bottom:20px; }
.al-stat { background:#fff; border-radius:12px; padding:16px 18px; box-shadow:0 2px 10px rgba(47,95,69,.08); display:flex; align-items:center; gap:13px; border-left:5px solid transparent; }
.al-stat.c-g{border-color:#4caf7d} .al-stat.c-b{border-color:#3a7bd5} .al-stat.c-r{border-color:#e53935} .al-stat.c-a{border-color:#f59e0b}
.al-stat-icon { width:42px; height:42px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.c-g .al-stat-icon{background:#e8f5ee;color:#2e7d52} .c-b .al-stat-icon{background:#e3edf9;color:#2557a7} .c-r .al-stat-icon{background:#fdecea;color:#c62828} .c-a .al-stat-icon{background:#fef9ec;color:#b45309}
.al-stat-val { font-size:1.8rem; font-weight:800; color:#1e3a2f; line-height:1; }
.al-stat-lbl { font-size:.76rem; font-weight:600; color:#7a9a87; text-transform:uppercase; letter-spacing:.05em; margin-top:3px; }
.al-ip { font-family:monospace; font-size:.82rem; background:#f1f5f9; color:#334155; padding:2px 8px; border-radius:6px; }
.sl-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.73rem; font-weight:700; }
.sl-active{background:#dcfce7;color:#166534} .sl-expired{background:#f3f4f6;color:#6b7280}
</style>

<!-- Breadcrumb -->
<div style="margin-bottom:14px;font-size:.86rem;color:#7a9a87;">
    <a href="<?= base_url('dashboard') ?>" style="color:#4e8c68;text-decoration:none;"><i class="fas fa-home"></i> Dashboard</a>
    <span style="margin:0 6px;color:#c7ddd0;">/</span>
    <span style="color:#2f5f45;font-weight:600;">Activity Logs</span>
</div>

<!-- Hero -->
<div class="al-hero">
    <div>
        <h1><i class="fas fa-history" style="margin-right:9px;opacity:.85;"></i>Activity Logs</h1>
        <p>Complete audit trail — system activity, sessions, login attempts &amp; security alerts.</p>
    </div>
    <div class="al-hero-total"><i class="fas fa-database"></i> <?= number_format($total) ?> Total Records</div>
</div>

<!-- Stats -->
<div class="al-stats">
    <div class="al-stat c-g"><div class="al-stat-icon"><i class="fas fa-sign-in-alt"></i></div><div><div class="al-stat-val"><?= !empty($hasLoginAttempts) ? number_format($stats['total_logins']) : 'N/A' ?></div><div class="al-stat-lbl">Total Logins</div></div></div>
    <div class="al-stat c-b"><div class="al-stat-icon"><i class="fas fa-calendar-day"></i></div><div><div class="al-stat-val"><?= !empty($hasLoginAttempts) ? number_format($stats['today_logins']) : 'N/A' ?></div><div class="al-stat-lbl">Logins Today</div></div></div>
    <div class="al-stat c-r"><div class="al-stat-icon"><i class="fas fa-times-circle"></i></div><div><div class="al-stat-val"><?= !empty($hasLoginAttempts) ? number_format($stats['total_failed']) : 'N/A' ?></div><div class="al-stat-lbl">Failed Attempts</div></div></div>
    <div class="al-stat c-a"><div class="al-stat-icon"><i class="fas fa-user-clock"></i></div><div><div class="al-stat-val"><?= number_format($stats['active_sessions']) ?></div><div class="al-stat-lbl">Active Sessions</div></div></div>
</div>

<?php if (empty($hasLoginAttempts)): ?>
<div style="margin-bottom:16px;padding:10px 14px;border:1px solid #fde68a;background:#fffbeb;color:#92400e;border-radius:10px;font-size:.84rem;" role="status" aria-live="polite">
    Login attempt metrics are unavailable because the <strong>login_attempts</strong> table is missing.
</div>
<?php endif; ?>

<!-- Tabs -->
<div class="al-tabs-nav" role="tablist">
    <button class="al-tab-btn active" onclick="alTab('all')" id="altab-all"><i class="fas fa-list"></i> All <span class="al-tab-count"><?= count($all) ?></span></button>
    <button class="al-tab-btn" onclick="alTab('auth')" id="altab-auth"><i class="fas fa-sign-in-alt"></i> Authentication <span class="al-tab-count"><?= count($auth) ?></span></button>
    <button class="al-tab-btn" onclick="alTab('users')" id="altab-users"><i class="fas fa-users"></i> Users <span class="al-tab-count"><?= count($users) ?></span></button>
    <button class="al-tab-btn" onclick="alTab('employees')" id="altab-employees"><i class="fas fa-id-badge"></i> Employees <span class="al-tab-count"><?= count($employees) ?></span></button>
    <button class="al-tab-btn" onclick="alTab('leaves')" id="altab-leaves"><i class="fas fa-calendar-check"></i> Leaves <span class="al-tab-count"><?= count($leaves) ?></span></button>
    <button class="al-tab-btn" onclick="alTab('salary')" id="altab-salary"><i class="fas fa-money-bill-wave"></i> Salary <span class="al-tab-count"><?= count($salary) ?></span></button>
    <button class="al-tab-btn" onclick="alTab('sessions')" id="altab-sessions"><i class="fas fa-desktop"></i> Sessions <span class="al-tab-count"><?= count($sessionsTable) ?></span></button>
    <button class="al-tab-btn" onclick="alTab('attempts')" id="altab-attempts"><i class="fas fa-key"></i> Login Attempts <span class="al-tab-count"><?= !empty($hasLoginAttempts) ? count($loginAttempts) : 'N/A' ?></span></button>
    <button class="al-tab-btn" onclick="alTab('alerts')" id="altab-alerts"><i class="fas fa-exclamation-triangle"></i> Alerts <span class="al-tab-count"><?= !empty($hasLoginAttempts) ? count($intrusions) : 'N/A' ?></span></button>
</div>

<?php
function alBadge(string $action): string {
    $a = strtolower($action);
    $map = ['login'=>['Login','b-login'],'logout'=>['Logout','b-logout'],'failed login'=>['Failed Login','b-failed'],'account locked'=>['Account Locked','b-locked'],'create'=>['Create','b-create'],'update'=>['Update','b-update'],'delete'=>['Delete','b-delete'],'restore'=>['Restore','b-restore'],'leave submitted'=>['Leave Submitted','b-leave'],'leave approved'=>['Leave Approved','b-approve'],'leave rejected'=>['Leave Rejected','b-reject'],'salary'=>['Salary','b-salary']];
    foreach ($map as $key => [$label, $cls]) { if (str_contains($a,$key)) return "<span class='al-badge {$cls}'>{$label}</span>"; }
    return "<span class='al-badge b-other'>".esc(strtoupper($action))."</span>";
}
function alTable(string $id, array $rows): void { ?>
<div style="overflow-x:auto;">
<?php if (!empty($rows)): ?>
<table class="al-table" id="<?= $id ?>"><thead><tr><th>#</th><th>Timestamp</th><th>User</th><th>Action</th><th>Details</th></tr></thead><tbody>
<?php foreach ($rows as $i => $log): ?>
<tr>
    <td style="color:#9ca3af;font-size:.8rem;"><?= $i+1 ?></td>
    <td><span class="al-ts"><?= $log->timestamp ? date('M d, Y', strtotime($log->timestamp)) : '—' ?><br><span style="font-size:.72rem;"><?= $log->timestamp ? date('H:i:s', strtotime($log->timestamp)) : '' ?></span></span></td>
    <td><div class="u-chip"><span class="u-name"><?= esc($log->admin_name ?? 'System') ?></span><span class="u-role"><?= esc($log->role_name ?? '') ?> (ID: <?= (int)($log->user_id??0) ?>)</span></div></td>
    <td><?= alBadge($log->action ?? '') ?></td>
    <td><span class="al-desc"><?= esc($log->description ?? '—') ?></span></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php else: ?><div class="al-empty"><i class="fas fa-inbox"></i><p>No records in this category yet.</p></div><?php endif; ?>
</div>
<?php } ?>

<!-- ── All ── -->
<div id="alpanel-all" class="al-tab-panel active"><div class="al-panel">
    <div class="al-ph"><div class="al-ph-icon"><i class="fas fa-list"></i></div><div><h2>All System Activity</h2><p>Every recorded action across the platform</p></div></div>
    <div class="al-search"><input class="al-si" placeholder="🔍 Search…" oninput="alFilter('t-all',this.value)"><select class="al-sel" onchange="alFilterCol('t-all',this.value,3)"><option value="">All Actions</option><option value="login">Login</option><option value="logout">Logout</option><option value="failed">Failed</option><option value="create">Create</option><option value="update">Update</option><option value="delete">Delete</option><option value="restore">Restore</option><option value="leave">Leave</option><option value="salary">Salary</option></select></div>
    <?php alTable('t-all', $all); ?>
</div></div>

<!-- ── Auth ── -->
<div id="alpanel-auth" class="al-tab-panel"><div class="al-panel">
    <div class="al-ph"><div class="al-ph-icon"><i class="fas fa-sign-in-alt"></i></div><div><h2>Authentication Logs</h2><p>Login, logout, failed attempts &amp; lockouts</p></div></div>
    <div class="al-search"><input class="al-si" placeholder="🔍 Search…" oninput="alFilter('t-auth',this.value)"><select class="al-sel" onchange="alFilterCol('t-auth',this.value,3)"><option value="">All</option><option value="login">Login</option><option value="logout">Logout</option><option value="failed">Failed</option><option value="locked">Locked</option></select></div>
    <?php alTable('t-auth', $auth); ?>
</div></div>

<!-- ── Users ── -->
<div id="alpanel-users" class="al-tab-panel"><div class="al-panel">
    <div class="al-ph"><div class="al-ph-icon"><i class="fas fa-users-cog"></i></div><div><h2>User Management</h2><p>Account creations, updates, deletions &amp; restorations</p></div></div>
    <div class="al-search"><input class="al-si" placeholder="🔍 Search…" oninput="alFilter('t-users',this.value)"><select class="al-sel" onchange="alFilterCol('t-users',this.value,3)"><option value="">All</option><option value="create">Create</option><option value="update">Update</option><option value="delete">Delete</option><option value="restore">Restore</option></select></div>
    <?php alTable('t-users', $users); ?>
</div></div>

<!-- ── Employees ── -->
<div id="alpanel-employees" class="al-tab-panel"><div class="al-panel">
    <div class="al-ph"><div class="al-ph-icon"><i class="fas fa-id-badge"></i></div><div><h2>Employee Management</h2><p>Employee record changes, approvals &amp; rejections</p></div></div>
    <div class="al-search"><input class="al-si" placeholder="🔍 Search…" oninput="alFilter('t-employees',this.value)"></div>
    <?php alTable('t-employees', $employees); ?>
</div></div>

<!-- ── Leaves ── -->
<div id="alpanel-leaves" class="al-tab-panel"><div class="al-panel">
    <div class="al-ph"><div class="al-ph-icon"><i class="fas fa-calendar-check"></i></div><div><h2>Leave Request Logs</h2><p>Submissions, approvals &amp; rejections</p></div></div>
    <div class="al-search"><input class="al-si" placeholder="🔍 Search…" oninput="alFilter('t-leaves',this.value)"></div>
    <?php alTable('t-leaves', $leaves); ?>
</div></div>

<!-- ── Salary ── -->
<div id="alpanel-salary" class="al-tab-panel"><div class="al-panel">
    <div class="al-ph"><div class="al-ph-icon"><i class="fas fa-money-bill-wave"></i></div><div><h2>Salary &amp; Payroll</h2><p>Salary rate updates &amp; payroll changes</p></div></div>
    <div class="al-search"><input class="al-si" placeholder="🔍 Search…" oninput="alFilter('t-salary',this.value)"></div>
    <?php alTable('t-salary', $salary); ?>
</div></div>

<!-- ── Sessions ── -->
<div id="alpanel-sessions" class="al-tab-panel"><div class="al-panel">
    <div class="al-ph"><div class="al-ph-icon"><i class="fas fa-desktop"></i></div><div><h2>Recent Sessions</h2><p>Last known login session per user</p></div></div>
    <div class="al-search"><input class="al-si" placeholder="🔍 Search user…" oninput="alFilter('t-sessions',this.value)"><select class="al-sel" onchange="alFilterCol('t-sessions',this.value,6)"><option value="">All</option><option value="active">Active</option><option value="expired">Expired</option></select></div>
    <div style="overflow-x:auto;">
    <?php if (!empty($sessionsTable)): ?>
    <table class="al-table" id="t-sessions"><thead><tr><th>User</th><th>User Type</th><th>ID</th><th>Issued</th><th>Last Seen</th><th>Expires</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($sessionsTable as $s): ?>
    <tr>
        <td><span class="u-name"><?= esc($s->user_name) ?></span></td>
        <td style="font-size:.83rem;font-weight:600;"><?= esc($s->user_type) ?></td>
        <td style="font-family:monospace;color:#6b7280;"><?= (int)$s->user_id ?></td>
        <td><span class="al-ts"><?= $s->issued_at  ? date('Y-m-d H:i:s',strtotime($s->issued_at))  : '—' ?></span></td>
        <td><span class="al-ts"><?= $s->last_seen  ? date('Y-m-d H:i:s',strtotime($s->last_seen))  : '—' ?></span></td>
        <td><span class="al-ts"><?= $s->expires_at ? date('Y-m-d H:i:s',strtotime($s->expires_at)) : '—' ?></span></td>
        <td><?php if($s->status==='active'): ?><span class="sl-badge sl-active"><i class="fas fa-circle" style="font-size:.5rem;"></i> Active</span><?php else: ?><span class="sl-badge sl-expired"><i class="fas fa-circle" style="font-size:.5rem;"></i> Expired</span><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table>
    <?php else: ?><div class="al-empty"><i class="fas fa-desktop"></i><p>No session data yet.</p></div><?php endif; ?>
    </div>
</div></div>

<!-- ── Login Attempts ── -->
<div id="alpanel-attempts" class="al-tab-panel"><div class="al-panel">
    <div class="al-ph"><div class="al-ph-icon"><i class="fas fa-key"></i></div><div><h2>Login Attempts</h2><p>All authentication attempts — successful and failed</p></div></div>
    <div class="al-search"><input class="al-si" placeholder="🔍 Search email or IP…" oninput="alFilter('t-attempts',this.value)"><select class="al-sel" onchange="alFilterCol('t-attempts',this.value,2)"><option value="">All</option><option value="success">Success</option><option value="failed">Failed</option></select></div>
    <div style="overflow-x:auto;">
    <?php if (!empty($loginAttempts)): ?>
    <table class="al-table" id="t-attempts"><thead><tr><th>User Type</th><th>User ID</th><th>Result</th><th>Reason</th><th>IP Address</th><th>Time</th></tr></thead><tbody>
    <?php foreach ($loginAttempts as $a): ?>
    <tr>
        <td style="font-weight:600;font-size:.83rem;"><?= esc($a->user_type ?? ($a->role_name ?? '—')) ?></td>
        <td style="font-family:monospace;color:#6b7280;"><?= $a->user_id ? (int)$a->user_id : '—' ?></td>
        <td><?php if($a->result==='success'): ?><span class="al-badge b-success"><i class="fas fa-check"></i> Success</span><?php else: ?><span class="al-badge b-locked"><i class="fas fa-times"></i> Failed</span><?php endif; ?></td>
        <td style="font-size:.8rem;color:#6b7280;"><?= $a->reason ? esc(str_replace('_',' ',$a->reason)) : '—' ?></td>
        <td><span class="al-ip"><?= esc($a->ip_address ?? '—') ?></span></td>
        <td><span class="al-ts"><?= $a->created_at ? date('Y-m-d H:i:s',strtotime($a->created_at)) : '—' ?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table>
    <?php else: ?><div class="al-empty"><i class="fas fa-key"></i><p>No login attempts recorded yet.</p></div><?php endif; ?>
    </div>
</div></div>

<!-- ── Alerts ── -->
<div id="alpanel-alerts" class="al-tab-panel"><div class="al-panel">
    <div class="al-ph"><div class="al-ph-icon"><i class="fas fa-exclamation-triangle"></i></div><div><h2>Intrusion Alerts</h2><p>Repeated failed login attempts grouped by email &amp; IP</p></div></div>
    <div style="overflow-x:auto;">
    <?php if (!empty($intrusions)): ?>
    <table class="al-table" id="t-alerts"><thead><tr><th>Type</th><th>Email / Target</th><th>IP Address</th><th>Severity</th><th>Status</th><th>Count</th><th>Triggered At</th></tr></thead><tbody>
    <?php foreach ($intrusions as $al): $sev = $al->count >= 5 ? 'high' : 'medium'; ?>
    <tr>
        <td style="font-size:.82rem;font-weight:600;">brute_force_attempt</td>
        <td style="font-size:.82rem;"><?= esc($al->email ?? '—') ?></td>
        <td><span class="al-ip"><?= esc($al->ip_address ?? '—') ?></span></td>
        <td><span class="al-badge b-<?= $sev ?>"><?= ucfirst($sev) ?></span></td>
        <td><span class="al-badge b-locked">open</span></td>
        <td style="font-weight:700;color:#991b1b;"><?= (int)$al->count ?></td>
        <td><span class="al-ts"><?= $al->triggered_at ? date('Y-m-d H:i:s',strtotime($al->triggered_at)) : '—' ?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table>
    <?php else: ?><div class="al-empty"><i class="fas fa-shield-alt" style="color:#d1fae5;"></i><p style="color:#166534;font-weight:600;">No intrusion alerts. System is secure.</p></div><?php endif; ?>
    </div>
</div></div>

<script>
function alTab(name) {
    document.querySelectorAll('.al-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.al-tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('altab-'+name).classList.add('active');
    document.getElementById('alpanel-'+name).classList.add('active');
}
function alFilter(id, q) {
    const rows = document.querySelectorAll('#'+id+' tbody tr');
    const lq = q.toLowerCase();
    rows.forEach(r => r.style.display = (!lq || r.textContent.toLowerCase().includes(lq)) ? '' : 'none');
}
function alFilterCol(id, val, col) {
    const rows = document.querySelectorAll('#'+id+' tbody tr');
    const lv = val.toLowerCase();
    rows.forEach(r => { const c = r.cells[col]; r.style.display = (!lv || (c && c.textContent.toLowerCase().includes(lv))) ? '' : 'none'; });
}
</script>
<?= $this->endSection() ?>
