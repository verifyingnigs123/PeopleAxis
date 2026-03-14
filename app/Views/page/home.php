<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --minimal-bg: #eef6f1;
        --minimal-surface: #ffffff;
        --minimal-border: #c7ddd0;
        --minimal-border-strong: #b6d3c1;
        --minimal-ink: #1f362a;
        --minimal-muted: #5f7b69;
        --minimal-accent: #6ea988;
        --minimal-accent-dark: #5b9474;
        --minimal-accent-soft: #e8f4ed;
    }

    .minimal-home {
        min-height: 100vh;
        padding: 28px 16px 34px;
        background: linear-gradient(180deg, #f7fcf9 0%, var(--minimal-bg) 100%);
    }

    .minimal-wrap {
        max-width: 980px;
        margin: 0 auto;
        color: var(--minimal-ink);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .minimal-hero {
        border: 1px solid var(--minimal-border);
        border-radius: 14px;
        background: var(--minimal-surface);
        box-shadow: 0 6px 16px rgba(35, 71, 52, 0.08);
        padding: 22px;
        display: grid;
        grid-template-columns: minmax(0, 1.3fr) minmax(0, 0.9fr);
        gap: 16px;
    }

    .minimal-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 10px;
        border-radius: 999px;
        background: var(--minimal-accent-soft);
        color: var(--minimal-accent);
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 12px;
    }

    .minimal-title {
        margin: 0;
        font-size: 2.1rem;
        line-height: 1.15;
        letter-spacing: -0.01em;
        color: var(--minimal-ink);
        font-weight: 700;
    }

    .minimal-subtitle {
        margin: 12px 0 0;
        color: var(--minimal-muted);
        line-height: 1.6;
        font-size: 0.98rem;
        max-width: 58ch;
    }

    .minimal-actions {
        margin-top: 16px;
        display: grid;
        gap: 10px;
        justify-items: start;
    }

    .minimal-primary-action,
    .minimal-secondary-action {
        display: flex;
        align-items: center;
    }

    .minimal-secondary-action {
        gap: 10px;
    }

    .minimal-btn,
    .minimal-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 9px;
        padding: 10px 15px;
        font-size: 0.92rem;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease;
    }

    .minimal-btn {
        background: var(--minimal-accent);
        color: #ffffff;
        border: 1px solid var(--minimal-accent);
        min-width: 210px;
        justify-content: center;
    }

    .minimal-btn:hover {
        background: var(--minimal-accent-dark);
        border-color: var(--minimal-accent-dark);
    }

    .minimal-link {
        background: transparent;
        color: #4b6a58;
        border: none;
        padding: 0;
        font-size: 0.88rem;
    }

    .minimal-link:hover {
        transform: translateY(-1px);
    }

    .minimal-link:hover {
        background: transparent;
        color: #264336;
    }

    .minimal-side {
        border: 1px solid var(--minimal-border-strong);
        border-radius: 12px;
        background: #f7fbf8;
        padding: 15px 16px;
    }

    .minimal-side h2 {
        margin: 0 0 8px;
        font-size: 1.02rem;
        color: #2a4839;
        font-weight: 700;
    }

    .minimal-side ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 9px;
    }

    .minimal-side li {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        color: #3f5f4e;
    }

    .minimal-side i {
        color: var(--minimal-accent);
    }

    .minimal-features {
        margin-top: 14px;
    }

    .minimal-features-head {
        margin: 2px 2px 10px;
    }

    .minimal-features-head h2 {
        margin: 0;
        font-size: 1.08rem;
        color: #2a4839;
        font-weight: 700;
    }

    .minimal-features-head p {
        margin: 6px 0 0;
        color: var(--minimal-muted);
        font-size: 0.9rem;
    }

    .minimal-features-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .minimal-card {
        border: 1px solid var(--minimal-border);
        border-radius: 12px;
        background: var(--minimal-surface);
        padding: 14px;
        box-shadow: 0 2px 8px rgba(35, 71, 52, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .minimal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(35, 71, 52, 0.09);
    }

    .minimal-card i {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--minimal-accent-soft);
        color: var(--minimal-accent);
        margin-bottom: 7px;
    }

    .minimal-card h3 {
        margin: 0 0 6px;
        font-size: 1rem;
        color: #2a4839;
    }

    .minimal-card p {
        margin: 0;
        color: var(--minimal-muted);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    @media (max-width: 900px) {
        .minimal-hero {
            grid-template-columns: 1fr;
        }

        .minimal-title {
            font-size: 1.9rem;
        }

        .minimal-features-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 576px) {
        .minimal-home {
            padding-top: 12px;
        }

        .minimal-hero {
            padding: 18px;
        }

        .minimal-title {
            font-size: 1.7rem;
        }

        .minimal-actions {
            justify-items: stretch;
        }

        .minimal-btn,
        .minimal-link {
            justify-content: center;
        }

        .minimal-primary-action,
        .minimal-secondary-action {
            width: 100%;
        }

        .minimal-link {
            padding: 6px 0 0;
        }

        .minimal-features-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="minimal-home">
    <div class="minimal-wrap">
        <section class="minimal-hero">
            <div>
                <div class="minimal-tag">
                    <i class="fas fa-briefcase"></i>
                    PeopleAxis HR Suite
                </div>

                <h1 class="minimal-title">Simple HR Operations</h1>
                <p class="minimal-subtitle">
                    Manage attendance, leave requests, employee records, and reporting from one clean workspace.
                </p>

                <div class="minimal-actions">
                    <div class="minimal-primary-action">
                        <a href="<?= base_url('login') ?>" class="minimal-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            Log In to Dashboard
                        </a>
                    </div>
                    <div class="minimal-secondary-action">
                        <a href="#minimal-features" class="minimal-link">
                            <i class="fas fa-list"></i>
                            View Features
                        </a>
                    </div>
                </div>
            </div>

            <aside class="minimal-side">
                <h2>Core Modules</h2>
                <ul>
                    <li><i class="fas fa-users"></i> Employee Management</li>
                    <li><i class="fas fa-calendar-check"></i> Leave Workflow</li>
                    <li><i class="fas fa-clock"></i> Attendance Monitoring</li>
                    <li><i class="fas fa-chart-line"></i> Performance Reports</li>
                </ul>
            </aside>
        </section>

        <section id="minimal-features" class="minimal-features">
            <div class="minimal-features-head">
                <h2>Key Features</h2>
                <p>Everything you need to run daily HR tasks in one organized workspace.</p>
            </div>

            <div class="minimal-features-grid">
                <article class="minimal-card">
                    <i class="fas fa-id-badge"></i>
                    <h3>Employee Records</h3>
                    <p>Maintain profiles, roles, and department assignments with structured data.</p>
                </article>

                <article class="minimal-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Leave Management</h3>
                    <p>Handle leave requests and approval flows with clear status tracking.</p>
                </article>

                <article class="minimal-card">
                    <i class="fas fa-chart-column"></i>
                    <h3>Performance Insights</h3>
                    <p>Review attendance trends and key metrics to support daily decisions.</p>
                </article>
            </div>
        </section>
    </div>
</div>

<?= $this->endSection() ?>

