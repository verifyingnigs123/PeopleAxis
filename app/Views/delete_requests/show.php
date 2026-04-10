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
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
        .detail-card {
            border-left: 4px solid #007bff;
        }
        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 1.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 12px;
            width: 2px;
            height: calc(100% - 12px);
            background: #dee2e6;
        }
        .timeline-item:last-child::after {
            display: none;
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
                    <h2><i class="fas fa-eye text-primary"></i> Delete Request Details</h2>
                    <a href="<?= site_url('/delete-requests') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Requests
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Request Information -->
                        <div class="card detail-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Request Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Request ID:</strong> #<?= $request['id'] ?></p>
                                        <p><strong>Status:</strong> 
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
                                        </p>
                                        <p><strong>Created:</strong> <?= date('M j, Y H:i', strtotime($request['created_at'])) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Requested By:</strong> <?= esc($user['name']) ?></p>
                                        <p><strong>Request Date:</strong> <?= date('Y-m-d', strtotime($request['created_at'])) ?></p>
                                        <?php if ($request['reviewed_at']): ?>
                                            <p><strong>Reviewed:</strong> <?= date('M j, Y H:i', strtotime($request['reviewed_at'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Information -->
                        <div class="card detail-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user"></i> Employee Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> <?= esc($request['first_name'] . ' ' . $request['last_name']) ?></p>
                                        <p><strong>Email:</strong> <?= esc($request['email']) ?></p>
                                        <p><strong>Phone:</strong> <?= esc($request['phone'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Position:</strong> <?= esc($request['position'] ?? 'N/A') ?></p>
                                        <p><strong>Department:</strong> <?= esc($request['department'] ?? 'N/A') ?></p>
                                        <p><strong>Hire Date:</strong> <?= $request['hire_date'] ? date('M j, Y', strtotime($request['hire_date'])) : 'N/A' ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reason for Deletion -->
                        <div class="card detail-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-comment-alt"></i> Reason for Deletion</h5>
                            </div>
                            <div class="card-body">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0"><?= nl2br(esc($request['reason'])) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Review Information (if reviewed) -->
                        <?php if ($request['reviewed_by'] && $request['reviewed_at']): ?>
                            <div class="card detail-card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-gavel"></i> Review Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reviewed By:</strong> Super Admin</p>
                                            <p><strong>Review Date:</strong> <?= date('M j, Y H:i', strtotime($request['reviewed_at'])) ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Decision:</strong> 
                                                <?php if ($request['status'] === 'approved'): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php elseif ($request['status'] === 'rejected'): ?>
                                                    <span class="badge bg-danger">Rejected</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if ($request['review_notes']): ?>
                                        <div class="mt-3">
                                            <strong>Review Notes:</strong>
                                            <div class="bg-light p-3 rounded mt-2">
                                                <p class="mb-0"><?= nl2br(esc($request['review_notes'])) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <?php if ($request['status'] === 'pending'): ?>
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-danger cancel-request" data-id="<?= $request['id'] ?>">
                                            <i class="fas fa-times"></i> Cancel Request
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <!-- Timeline -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-history"></i> Timeline</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <h6>Request Created</h6>
                                        <p class="text-muted mb-0">
                                            <?= date('M j, Y H:i', strtotime($request['created_at'])) ?><br>
                                            by <?= esc($user['name']) ?>
                                        </p>
                                    </div>
                                    <?php if ($request['reviewed_at']): ?>
                                        <div class="timeline-item">
                                            <h6>Request <?= ucfirst($request['status']) ?></h6>
                                            <p class="text-muted mb-0">
                                                <?= date('M j, Y H:i', strtotime($request['reviewed_at'])) ?><br>
                                                by Super Admin
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="<?= site_url('/delete-requests') ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-list"></i> View All Requests
                                    </a>
                                    <a href="<?= site_url('/delete-requests/create') ?>" class="btn btn-outline-success">
                                        <i class="fas fa-plus"></i> New Request
                                    </a>
                                    <?php if ($request['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-outline-danger cancel-request" data-id="<?= $request['id'] ?>">
                                            <i class="fas fa-times"></i> Cancel This Request
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
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
    </script>
</body>
</html>
