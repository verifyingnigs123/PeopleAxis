<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>
<style>
/* ── Tabs ── */
.sl-tabs-nav {
    display: flex; gap: 4px;
    background: #fff; border-radius: 14px 14px 0 0;
    border: 1px solid #dbe9e0; border-bottom: none;
    padding: 12px 16px 0;
}
.sl-tab-btn {
    padding: 10px 20px; border: none; background: transparent;
    color: #6b7280; font-weight: 600; font-size: .88rem;
    border-radius: 8px 8px 0 0; cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all .2s; display: flex; align-items: center; gap: 7px;
}
.sl-tab-btn:hover { background: #f4faf6; color: #2f5f45; }
.sl-tab-btn.active { color: #2f5f45; border-bottom-color: #4e8c68; background: #f4faf6; }
.sl-tab-btn .sl-tab-count {
    background: #e8f5ee; color: #2f5f45;
    border-radius: 20px; padding: 1px 8px; font-size: .74rem; font-weight: 700;
}
.sl-tab-btn.active .sl-tab-count { background: #4e8c68; color: #fff; }

/* ── Tab panels ── */
.sl-tab-panels { border-radius: 0 0 14px 14px; }
.sl-tab-panel { display: none; }
.sl-tab-panel.active { display: block; }

/* ── Panel card ── */
.sl-panel {
    background: #fff;
    border: 1px solid #dbe9e0; border-top: none;
    border-radius: 0 0 14px 14px;
    overflow: hidden;
}
.sl-panel-header {
    padding: 18px 24px; display: flex; align-items: center; gap: 12px;
}
.sl-panel-header.green { background: linear-gradient(135deg,#2f5f45,#4e8c68); }
.sl-panel-header.blue  { background: linear-gradient(135deg,#2f5f45,#4e8c68); }
.sl-panel-header.slate { background: linear-gradient(135deg,#2f5f45,#4e8c68); }
.sl-panel-header.red   { background: linear-gradient(135deg,#2f5f45,#4e8c68); }
.sl-panel-header .ph-icon {
    width: 40px; height: 40px; background: rgba(255,255,255,.15);
    border-radius: 10px; display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.1rem; flex-shrink: 0;
}
.sl-panel-header h2 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #fff; }
.sl-panel-header p  { margin: 2px 0 0; font-size: .8rem; color: rgba(255,255,255,.72); }

/* ── Search bar ── */
.sl-search-bar {
    padding: 12px 18px; border-bottom: 1px solid #e7f0eb;
    display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
    background: #fafcfb;
}
.sl-search-input {
    flex: 1; min-width: 180px; padding: 8px 14px;
    border: 1.5px solid #c7ddd0; border-radius: 8px;
    font-size: .88rem; outline: none;
}
.sl-search-input:focus { border-color: #4e8c68; }
.sl-filter-sel {
    padding: 8px 12px; border: 1.5px solid #c7ddd0; border-radius: 8px;
    font-size: .88rem; background: #fff; outline: none; cursor: pointer;
}

/* ── Table ── */
.sl-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
.sl-table thead th {
    background: #f4faf6; color: #2f5f45; font-weight: 700;
    padding: 11px 16px; text-align: left; border-bottom: 2px solid #dbe9e0;
    font-size: .76rem; text-transform: uppercase; letter-spacing: .06em; white-space: nowrap;
}
.sl-table tbody td { padding: 11px 16px; border-bottom: 1px solid #f0f6f2; color: #374151; vertical-align: middle; }
.sl-table tbody tr:last-child td { border-bottom: none; }
.sl-table tbody tr:hover { background: #f8fbf9; }

/* ── Badges ── */
.badge-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px; font-size: .74rem; font-weight: 700;
}
.badge-active   { background: #dcfce7; color: #166534; }
.badge-expired  { background: #f3f4f6; color: #6b7280; }
.badge-success  { background: #dcfce7; color: #166534; }
.badge-failed   { background: #fee2e2; color: #991b1b; }
.badge-login    { background: #dbeafe; color: #1e40af; }
.badge-logout   { background: #f3f4f6; color: #374151; }
.badge-fl       { background: #fef3c7; color: #92400e; }
.badge-locked   { background: #fee2e2; color: #991b1b; }
.badge-medium   { background: #fef3c7; color: #92400e; }
.badge-high     { background: #fee2e2; color: #991b1b; }
.badge-low      { background: #f0fdf4; color: #166534; }
.badge-open     { background: #fee2e2; color: #991b1b; }
.badge-resolved { background: #dcfce7; color: #166534; }

/* ── User chip ── */
.u-chip { display: flex; align-items: center; gap: 9px; }
.u-av {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg,#4e8c68,#2f5f45);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 700; flex-shrink: 0;
}
.u-name { font-weight: 600; color: #1e3a2f; font-size: .88rem; }

/* ── Misc ── */
.sl-ts   { font-size: .79rem; color: #6b7280; white-space: nowrap; }
.sl-ip   { font-family: monospace; font-size: .82rem; background: #f1f5f9; color: #334155; padding: 2px 8px; border-radius: 6px; }
.sl-mono { font-family: monospace; color: #6b7280; }
.sl-empty { padding: 48px 20px; text-align: center; color: #9ca3af; }
.sl-empty i { font-size: 2.2rem; margin-bottom: 10px; display: block; color: #d1fae5; }

/* ── Stat bar ── */
.sl-stats { display: grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap: 16px; margin-bottom: 22px; }
.sl-stat {
    background: #fff; border-radius: 12px; padding: 18px 20px;
    box-shadow: 0 2px 10px rgba(47,95,69,.08);
    display: flex; align-items: center; gap: 14px;
    border-left: 5px solid transparent; transition: transform .2s;
}
.sl-stat:hover { transform: translateY(-2px); }
.sl-stat.c-green { border-color: #4caf7d; }
.sl-stat.c-blue  { border-color: #3a7bd5; }
.sl-stat.c-red   { border-color: #e53935; }
.sl-stat.c-amber { border-color: #f59e0b; }
.sl-stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.c-green .sl-stat-icon { background: #e8f5ee; color: #2e7d52; }
.c-blue  .sl-stat-icon { background: #e3edf9; color: #2557a7; }
.c-red   .sl-stat-icon { background: #fdecea; color: #c62828; }
.c-amber .sl-stat-icon { background: #fef9ec; color: #b45309; }
.sl-stat-val   { font-size: 1.9rem; font-weight: 800; color: #1e3a2f; line-height: 1; }
.sl-stat-label { font-size: .78rem; font-weight: 600; color: #7a9a87; text-transform: uppercase; letter-spacing: .05em; margin-top: 3px; }

/* ── Hero ── */
.sl-hero {
    background: linear-gradient(135deg,#1e3a2f,#2f5f45 60%,#4e8c68);
    border-radius: 14px; padding: 24px 28px;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    color: #fff; margin-bottom: 22px; flex-wrap: wrap;
}
.sl-hero h1 { margin: 0; font-size: 1.55rem; font-weight: 800; }
.sl-hero p  { margin: 5px 0 0; color: rgba(255,255,255,.72); font-size: .88rem; }
.sl-live-badge {
    background: rgba(255,255,255,.15); border-radius: 8px;
    padding: 8px 14px; font-size: .83rem; font-weight: 600;
    display: flex; align-items: center; gap: 7px;
}
.live-dot { width: 8px; height: 8px; border-radius: 50%; background: #4ade80; animation: pd 2s infinite; }
@keyframes pd { 0%,100%{opacity:1} 50%{opacity:.5} }
</style>

<!-- Breadcrumb -->
<div style="margin-bottom:14px;font-size:.86rem;color:#7a9a87;">
    <a href="<?= base_url('dashboard') ?>" style="color:#4e8c68;text-decoration:none;"><i class="fas fa-home"></i> Dashboard</a>
    <span style="margin:0 6px;color:#c7ddd0;">/</span>
    <span style="color:#2f5f45;font-weight:600;">Session &amp; Login Activity</span>
</div>

<!-- Hero -->
<div class="sl-hero">
    <div>
        <h1><i class="fas fa-shield-alt" style="margin-right:9px;opacity:.85;"></i>Session &amp; Login Activity</h1>
        <p>Monitor all login attempts, active sessions, and security events in real time.</p>
    </div>
    <div class="sl-live-badge">
        <span class="live-dot"></span>
        <?= $stats['active_sessions'] ?> Active Session<?= $stats['active_sessions'] !== 1 ? 's' : '' ?>
    </div>
</div>

<!-- Stat cards -->
<div class="sl-stats">
    <div class="sl-stat c-green">
        <div class="sl-stat-icon"><i class="fas fa-sign-in-alt"></i></div>
        <div><div class="sl-stat-val"><?= number_format($stats['total_logins']) ?></div><div class="sl-stat-label">Total Logins</div></div>
    </div>
    <div class="sl-stat c-blue">
        <div class="sl-stat-icon"><i class="fas fa-calendar-day"></i></div>
        <div><div class="sl-stat-val"><?= number_format($stats['today_logins']) ?></div><div class="sl-stat-label">Logins Today</div></div>
    </div>
    <div class="sl-stat c-red">
        <div class="sl-stat-icon"><i class="fas fa-times-circle"></i></div>
        <div><div class="sl-stat-val"><?= number_format($stats['total_failed']) ?></div><div class="sl-stat-label">Failed Attempts</div></div>
    </div>
    <div class="sl-stat c-amber">
        <div class="sl-stat-icon"><i class="fas fa-user-clock"></i></div>
        <div><div class="sl-stat-val"><?= number_format($stats['active_sessions']) ?></div><div class="sl-stat-label">Active Sessions</div></div>
    </div>
</div>

<!-- Tab nav -->
<div class="sl-tabs-nav" role="tablist">
    <button class="sl-tab-btn active" onclick="switchTab('sessions')" id="tab-sessions">
        <i class="fas fa-desktop"></i> Recent Sessions
        <span class="sl-tab-count"><?= count($sessionsTable) ?></span>
    </button>
    <button class="sl-tab-btn" onclick="switchTab('attempts')" id="tab-attempts">
        <i class="fas fa-key"></i> Login Attempts
        <span class="sl-tab-count"><?= count($loginAttempts) ?></span>
    </button>
    <button class="sl-tab-btn" onclick="switchTab('events')" id="tab-events">
        <i class="fas fa-exchange-alt"></i> Account Activity
        <span class="sl-tab-count"><?= count($activityEvents) ?></span>
    </button>
    <button class="sl-tab-btn" onclick="switchTab('alerts')" id="tab-alerts">
        <i class="fas fa-exclamation-triangle"></i> Intrusion Alerts
        <span class="sl-tab-count"><?= $stats['total_failed'] ?></span>
    </button>
</div>

<div class="sl-tab-panels">

    <!-- ═══════════════════════════════════════
         TAB 1 — Recent Sessions
    ═══════════════════════════════════════════ -->
    <div id="panel-sessions" class="sl-tab-panel active">
        <div class="sl-panel">
            <div class="sl-panel-header green">
                <div class="ph-icon"><i class="fas fa-desktop"></i></div>
                <div>
                    <h2>Recent Sessions</h2>
                    <p>Last known login session per user — auto-refreshes every 60 s</p>
                </div>
            </div>
            <div class="sl-search-bar">
                <input type="text" class="sl-search-input" placeholder="🔍 Search user or IP…" oninput="filterTable('t-sessions',this.value)">
                <select class="sl-filter-sel" onchange="filterByCol('t-sessions',this.value,6)">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
            <div style="overflow-x:auto;">
                <?php if (!empty($sessionsTable)): ?>
                <table class="sl-table" id="t-sessions">
                    <thead><tr>
                        <th>User</th><th>User Type</th><th>User ID</th>
                        <th>Issued</th><th>Last Seen</th><th>Expires</th><th>Status</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($sessionsTable as $s): ?>
                    <tr>
                        <td>
                            <div class="u-chip">
                                <div class="u-av"><?= strtoupper(substr((string)($s->user_name??'?'),0,2)) ?></div>
                                <span class="u-name"><?= esc($s->user_name??'Unknown') ?></span>
                            </div>
                        </td>
                        <td style="font-size:.83rem;color:#374151;font-weight:600;"><?= esc($s->user_type??'—') ?></td>
                        <td class="sl-mono"><?= (int)$s->user_id ?></td>
                        <td><span class="sl-ts"><?= $s->issued_at  ? date('Y-m-d H:i:s',strtotime($s->issued_at))  : '—' ?></span></td>
                        <td><span class="sl-ts"><?= $s->last_seen  ? date('Y-m-d H:i:s',strtotime($s->last_seen))  : '—' ?></span></td>
                        <td><span class="sl-ts"><?= $s->expires_at ? date('Y-m-d H:i:s',strtotime($s->expires_at)) : '—' ?></span></td>
                        <td>
                            <?php if($s->status==='active'): ?>
                                <span class="badge-status badge-active"><i class="fas fa-circle" style="font-size:.5rem;"></i> Active</span>
                            <?php else: ?>
                                <span class="badge-status badge-expired"><i class="fas fa-circle" style="font-size:.5rem;"></i> Expired</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="sl-empty"><i class="fas fa-desktop"></i><p>No session data yet. Sessions appear after users log in.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB 2 — Login Attempts
    ═══════════════════════════════════════════ -->
    <div id="panel-attempts" class="sl-tab-panel">
        <div class="sl-panel">
            <div class="sl-panel-header blue">
                <div class="ph-icon"><i class="fas fa-key"></i></div>
                <div>
                    <h2>Recent Login Attempts</h2>
                    <p>All authentication attempts — successful and failed</p>
                </div>
            </div>
            <div class="sl-search-bar">
                <input type="text" class="sl-search-input" placeholder="🔍 Search email or IP…" oninput="filterTable('t-attempts',this.value)">
                <select class="sl-filter-sel" onchange="filterByCol('t-attempts',this.value,2)">
                    <option value="">All Results</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div style="overflow-x:auto;">
                <?php if (!empty($loginAttempts)): ?>
                <table class="sl-table" id="t-attempts">
                    <thead><tr>
                        <th>User Type</th><th>User ID</th><th>Result</th>
                        <th>Reason</th><th>IP Address</th><th>Time</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($loginAttempts as $a): ?>
                    <tr>
                        <td style="font-weight:600;font-size:.83rem;"><?= esc($a->user_type??($a->role_name??'—')) ?></td>
                        <td class="sl-mono"><?= $a->user_id ? (int)$a->user_id : '—' ?></td>
                        <td>
                            <?php if($a->result==='success'): ?>
                                <span class="badge-status badge-success"><i class="fas fa-check"></i> Success</span>
                            <?php else: ?>
                                <span class="badge-status badge-failed"><i class="fas fa-times"></i> Failed</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.8rem;color:#6b7280;"><?= $a->reason ? esc(str_replace('_',' ',$a->reason)) : '—' ?></td>
                        <td><span class="sl-ip"><?= esc($a->ip_address??'—') ?></span></td>
                        <td><span class="sl-ts"><?= $a->created_at ? date('Y-m-d H:i:s',strtotime($a->created_at)) : '—' ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="sl-empty"><i class="fas fa-key"></i><p>No login attempts recorded yet.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB 3 — Account Activity Events
    ═══════════════════════════════════════════ -->
    <div id="panel-events" class="sl-tab-panel">
        <div class="sl-panel">
            <div class="sl-panel-header slate">
                <div class="ph-icon"><i class="fas fa-exchange-alt"></i></div>
                <div>
                    <h2>Account Activity Events</h2>
                    <p>Login, logout &amp; security events from the audit log</p>
                </div>
            </div>
            <div class="sl-search-bar">
                <input type="text" class="sl-search-input" placeholder="🔍 Search user or event…" oninput="filterTable('t-events',this.value)">
                <select class="sl-filter-sel" onchange="filterByCol('t-events',this.value,2)">
                    <option value="">All Events</option>
                    <option value="session_login">Login</option>
                    <option value="session_logout">Logout</option>
                    <option value="failed">Failed</option>
                    <option value="locked">Locked</option>
                </select>
            </div>
            <div style="overflow-x:auto;">
                <?php if (!empty($activityEvents)): ?>
                <table class="sl-table" id="t-events">
                    <thead><tr>
                        <th>User Type</th><th>User ID</th><th>Activity</th>
                        <th>Target</th><th>Time</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($activityEvents as $e):
                        $act = strtolower((string)($e->action??''));
                        $cls = match(true){
                            str_contains($act,'logout') => 'logout',
                            str_contains($act,'failed') => 'fl',
                            str_contains($act,'locked') => 'locked',
                            default                      => 'login',
                        };
                        $label = match(true){
                            str_contains($act,'logout') => 'session_logout',
                            str_contains($act,'failed') => 'failed_login',
                            str_contains($act,'locked') => 'account_locked',
                            default                      => 'session_login',
                        };
                    ?>
                    <tr>
                        <td style="font-weight:600;font-size:.83rem;"><?= esc($e->user_type??'—') ?></td>
                        <td class="sl-mono"><?= $e->user_id ? (int)$e->user_id : '—' ?></td>
                        <td><span class="badge-status badge-<?= $cls ?>"><?= esc($label) ?></span></td>
                        <td style="font-size:.8rem;color:#6b7280;">
                            <?= $e->user_name ? 'users #'.(int)$e->user_id : '—' ?>
                        </td>
                        <td><span class="sl-ts"><?= $e->timestamp ? date('Y-m-d H:i:s',strtotime($e->timestamp)) : '—' ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="sl-empty"><i class="fas fa-exchange-alt"></i><p>No activity events recorded yet.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════
         TAB 4 — Intrusion Alerts
    ═══════════════════════════════════════════ -->
    <div id="panel-alerts" class="sl-tab-panel">
        <div class="sl-panel">
            <div class="sl-panel-header red">
                <div class="ph-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <h2>Recent Intrusion Alerts</h2>
                    <p>Accounts locked out due to repeated failed login attempts</p>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <?php
                // Build intrusion alerts: emails with 3+ failed attempts grouped
                $db = \Config\Database::connect();
                $intrusions = $db->table('login_attempts')
                    ->select('email, ip_address, COUNT(*) as count, MAX(created_at) as triggered_at')
                    ->where('result','failed')
                    ->groupBy('email, ip_address')
                    ->having('count >=', 2)
                    ->orderBy('triggered_at','DESC')
                    ->limit(50)
                    ->get()->getResultObject();
                ?>
                <?php if (!empty($intrusions)): ?>
                <table class="sl-table" id="t-alerts">
                    <thead><tr>
                        <th>Type</th><th>Email / Target</th><th>IP Address</th>
                        <th>Severity</th><th>Status</th><th>Count</th><th>Triggered At</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($intrusions as $al):
                        $sev = $al->count >= 5 ? 'high' : 'medium';
                        $sevLabel = $al->count >= 5 ? 'High' : 'Medium';
                    ?>
                    <tr>
                        <td style="font-size:.83rem;font-weight:600;">brute_force_attempt</td>
                        <td style="font-size:.83rem;"><?= esc($al->email??'—') ?></td>
                        <td><span class="sl-ip"><?= esc($al->ip_address??'—') ?></span></td>
                        <td><span class="badge-status badge-<?= $sev ?>"><?= $sevLabel ?></span></td>
                        <td><span class="badge-status badge-open">open</span></td>
                        <td style="font-weight:700;color:#991b1b;"><?= (int)$al->count ?></td>
                        <td><span class="sl-ts"><?= $al->triggered_at ? date('Y-m-d H:i:s',strtotime($al->triggered_at)) : '—' ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="sl-empty">
                        <i class="fas fa-shield-check" style="color:#dcfce7;"></i>
                        <p style="color:#166534;font-weight:600;">No intrusion alerts detected. System is secure.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div><!-- /.sl-tab-panels -->

<script>
function switchTab(name) {
    document.querySelectorAll('.sl-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.sl-tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-'   + name).classList.add('active');
    document.getElementById('panel-' + name).classList.add('active');
}
function filterTable(id, q) {
    const rows = document.querySelectorAll('#' + id + ' tbody tr');
    const lq = q.toLowerCase();
    rows.forEach(r => r.style.display = (!lq || r.textContent.toLowerCase().includes(lq)) ? '' : 'none');
}
function filterByCol(id, val, col) {
    const rows = document.querySelectorAll('#' + id + ' tbody tr');
    const lv = val.toLowerCase();
    rows.forEach(r => {
        const cell = r.cells[col];
        r.style.display = (!lv || (cell && cell.textContent.toLowerCase().includes(lv))) ? '' : 'none';
    });
}
// Auto-refresh every 60 s
setTimeout(() => location.reload(), 60000);
</script>
<?= $this->endSection() ?>
