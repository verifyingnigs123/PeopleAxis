<?php /**
 * Shared print helpers for reports.
 * Include this partial in report views to provide a consistent Print button
 * and a `printReport()` function that prints only a target report container.
 */ ?>

<?php $printTarget = $printTarget ?? '[data-print-root]'; ?>

<div class="export-print" style="display:inline-block;">
    <button class="btn-export" onclick="printReport('<?= esc($printTarget, 'js') ?>')">
        <i class="fas fa-print"></i> Print Report
    </button>
</div>

<style>
@media print {
    .export-print,
    .export-buttons,
    .action-buttons,
    .back-link { display: none !important; }

    @page {
        size: landscape;
        margin: 12mm;
    }

    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100%;
    }

    [data-print-root] {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
    }

    .report-table-container {
        page-break-inside: avoid;
        break-inside: avoid;
    }
}
</style>

<script>
if (typeof printReport !== 'function') {
    function printReport(targetSelector) {
        try {
            const selector = targetSelector || '[data-print-root]';
            const printRoot = document.querySelector(selector);

            if (!printRoot) {
                window.print();
                return;
            }

            const printWindow = window.open('', '_blank', 'width=1024,height=768');
            if (!printWindow) {
                window.print();
                return;
            }

            const clone = printRoot.cloneNode(true);
            clone.querySelectorAll('.export-print, .export-buttons, .action-buttons, .back-link').forEach((el) => el.remove());

            let styles = '';
            document.querySelectorAll('style, link[rel="stylesheet"]').forEach((node) => {
                styles += node.outerHTML;
            });

            const printStyles = `
                <style>
                    @page { size: landscape; margin: 12mm; }
                    html, body { margin: 0; padding: 0; background: #fff; width: 100%; }
                    body { font-family: Arial, Helvetica, sans-serif; color: #1f2937; }
                    .export-print, .export-buttons, .action-buttons, .back-link { display: none !important; }
                    [data-print-root] { width: 100% !important; margin: 0 !important; padding: 0 !important; box-shadow: none !important; border: none !important; border-radius: 0 !important; }
                    .report-table-container { width: 100% !important; box-shadow: none !important; border: 0 !important; }
                    .table-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                    .report-table { width: 100% !important; }
                    .report-table thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                </style>
            `;

            printWindow.document.open();
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Report Print</title>
                        ${styles}
                        ${printStyles}
                    </head>
                    <body>${clone.outerHTML}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();

            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        } catch (e) {
            console.warn('Print not available', e);
        }
    }
}
</script>
