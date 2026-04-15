<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .scanner-shell {
        max-width: 760px;
        margin: 0 auto;
        padding: 24px 0 40px;
    }

    .scanner-hero {
        background: linear-gradient(135deg, #163a28 0%, #2f5f45 45%, #6ea988 100%);
        color: white;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 20px 40px rgba(16, 42, 28, 0.18);
        position: relative;
        overflow: hidden;
    }

    .scanner-hero::after {
        content: '';
        position: absolute;
        inset: auto -80px -90px auto;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        filter: blur(4px);
    }

    .scanner-kicker {
        text-transform: uppercase;
        letter-spacing: 0.16em;
        font-size: 0.78rem;
        font-weight: 700;
        opacity: 0.9;
        margin-bottom: 12px;
    }

    .scanner-title {
        font-size: clamp(1.9rem, 4vw, 3rem);
        line-height: 1.05;
        margin: 0;
        font-weight: 800;
    }

    .scanner-copy {
        max-width: 56ch;
        margin: 12px 0 0;
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
    }

    .scanner-card {
        margin-top: 22px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 12px 30px rgba(19, 55, 34, 0.12);
    }

    .status-banner {
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-success {
        background: #e7f8ef;
        color: #14532d;
    }

    .status-error {
        background: #fdecec;
        color: #9f1239;
    }

    .scan-form {
        display: grid;
        gap: 14px;
    }

    .scan-label {
        display: block;
        margin-bottom: 8px;
        font-size: 0.88rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #5f6f66;
    }

    .scan-input {
        width: 100%;
        border: 2px solid #d8e2db;
        border-radius: 14px;
        font-size: 1.15rem;
        padding: 18px 18px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        background: #fbfdfb;
    }

    .scan-input:focus {
        border-color: #2f5f45;
        box-shadow: 0 0 0 4px rgba(47, 95, 69, 0.12);
    }

    .scan-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .scan-btn {
        appearance: none;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        padding: 14px 20px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(47, 95, 69, 0.24);
    }

    .scan-hint {
        color: #64748b;
        font-size: 0.94rem;
    }

    .meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }

    .meta-card {
        background: #f7faf8;
        border: 1px solid #e3ece6;
        border-radius: 14px;
        padding: 14px 16px;
    }

    .meta-label {
        font-size: 0.76rem;
        text-transform: uppercase;
        font-weight: 700;
        color: #6b7280;
        letter-spacing: 0.08em;
    }

    .meta-value {
        margin-top: 6px;
        font-size: 1.05rem;
        font-weight: 700;
        color: #183124;
    }

    @media (max-width: 640px) {
        .scanner-shell {
            padding-inline: 14px;
        }

        .scanner-hero,
        .scanner-card {
            border-radius: 16px;
            padding: 20px;
        }

        .meta-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="scanner-shell">
    <div class="scanner-hero">
        <div class="scanner-kicker">Attendance Scanner</div>
        <h1 class="scanner-title"><i class="fas fa-id-card"></i> RFID Attendance Scanner</h1>
        <p class="scanner-copy">Scan an RFID card to automatically record Time In or Time Out for the current day.</p>

        <div class="scanner-card">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="status-banner status-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="status-banner status-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('attendance/scan') ?>" method="post" class="scan-form" id="rfidScanForm" autocomplete="off">
                <?= csrf_field() ?>
                <div>
                    <label for="rfid_number" class="scan-label">RFID Number</label>
                    <input type="text" id="rfid_number" name="rfid_number" class="scan-input" placeholder="Scan card now" autofocus>
                </div>
                <div class="scan-actions">
                    <button type="submit" class="scan-btn"><i class="fas fa-bolt"></i> Record Attendance</button>
                    <div class="scan-hint">The card reader can type into the field automatically. Press Enter after a scan if needed.</div>
                </div>
            </form>

            <div class="meta-grid">
                <div class="meta-card">
                    <div class="meta-label">Today</div>
                    <div class="meta-value" id="scannerDate"></div>
                </div>
                <div class="meta-card">
                    <div class="meta-label">Current Time</div>
                    <div class="meta-value" id="scannerTime"></div>
                </div>
                <div class="meta-card">
                    <div class="meta-label">Status</div>
                    <div class="meta-value">Ready for scan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const scannerInput = document.getElementById('rfid_number');
    const scannerForm = document.getElementById('rfidScanForm');
    const scannerServerNow = new Date(<?= json_encode((new DateTimeImmutable('now', new DateTimeZone(app_timezone())))->format(DateTimeInterface::ATOM)) ?>);
    const scannerClockBase = Date.now();

    if (scannerInput) {
        scannerInput.focus();
        scannerInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                if (scannerInput.value.trim() !== '') {
                    scannerForm.submit();
                }
            }
        });
    }

    const dateTarget = document.getElementById('scannerDate');
    const timeTarget = document.getElementById('scannerTime');

    function getScannerNow() {
        return new Date(scannerServerNow.getTime() + (Date.now() - scannerClockBase));
    }

    function renderClock() {
        const now = getScannerNow();
        if (dateTarget) {
            dateTarget.textContent = now.toLocaleDateString(undefined, {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        if (timeTarget) {
            timeTarget.textContent = now.toLocaleTimeString(undefined, {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    }

    renderClock();
    setInterval(renderClock, 1000);
</script>

<?= $this->endSection() ?>