<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .table-responsive {
            border-radius: 0.375rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('/') ?>">PeopleAxis</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= site_url('/delete-requests') ?>">
                    <i class="fas fa-trash-alt"></i> Delete Requests
                </a>
                <a class="nav-link" href="<?= site_url('/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-trash-alt text-danger"></i> Delete Requests</h2>
                    <a href="<?= site_url('/delete-requests/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Delete Request
                    </a>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($requests)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                No delete requests found
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($requests as $request): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($request['first_name'] . ' ' . $request['last_name']) ?></strong><br>
                                                    <small class="text-muted"><?= esc($request['email']) ?></small>
                                                </td>
                                                <td>
                                                    <span title="<?= esc($request['reason']) ?>">
                                                        <?= esc(substr($request['reason'], 0, 50)) ?><?= strlen($request['reason']) > 50 ? '...' : '' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($request['status'] === 'pending'): ?>
                                                        <span class="badge bg-warning status-badge">
                                                            <i class="fas fa-clock"></i> Pending
                                                        </span>
                                                    <?php elseif ($request['status'] === 'approved'): ?>
                                                        <span class="badge bg-success status-badge">
                                                            <i class="fas fa-check"></i> Approved
                                                        </span>
                                                    <?php elseif ($request['status'] === 'rejected'): ?>
                                                        <span class="badge bg-danger status-badge">
                                                            <i class="fas fa-times"></i> Rejected
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?= date('M j, Y H:i', strtotime($request['created_at'])) ?></small>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="<?= site_url('/delete-requests/' . $request['id']) ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($request['status'] === 'pending'): ?>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger cancel-request" 
                                                                data-id="<?= $request['id'] ?>"
                                                                title="Cancel Request">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($pager->hasMore()): ?>
                            <div class="d-flex justify-content-center mt-3">
                                <?= $pager->links() ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Request Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Delete Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this delete request? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Request</button>
                    <button type="button" class="btn btn-danger" id="confirmCancel">Yes, Cancel Request</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let cancelRequestId = null;

        // Cancel request functionality
        $('.cancel-request').click(function() {
            cancelRequestId = $(this).data('id');
            $('#cancelModal').modal('show');
        });

        $('#confirmCancel').click(function() {
            if (cancelRequestId) {
                $.ajax({
                    url: `<?= site_url('/delete-requests/cancel/') ?>${cancelRequestId}`,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while cancelling the request.');
                    }
                });
            }
        });

        // Real-time notification polling
        function checkNotifications() {
            $.get('<?= site_url('/delete-requests/get-pending-count') ?>', function(data) {
                if (data.count > 0) {
                    // Update badge if needed
                    console.log('Pending requests:', data.count);
                }
            });
        }

        // Check for notifications every 30 seconds
        setInterval(checkNotifications, 30000);
    </script>
</body>
</html>
