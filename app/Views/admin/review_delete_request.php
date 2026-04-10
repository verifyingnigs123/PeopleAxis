<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .employee-card {
            border-left: 4px solid #007bff;
        }
        .warning-card {
            border-left: 4px solid #ffc107;
        }
        .danger-card {
            border-left: 4px solid #dc3545;
        }
        .success-card {
            border-left: 4px solid #198754;
        }
        .action-card {
            transition: transform 0.2s;
        }
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .data-item {
            padding: 0.75rem;
            border-radius: 0.375rem;
            background: #f8f9fa;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('/') ?>">
                <i class="fas fa-crown"></i> PeopleAxis Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?= site_url('/admin/delete-requests') ?>">
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
                    <h2><i class="fas fa-gavel text-primary"></i> Review Delete Request</h2>
                    <a href="<?= site_url('/admin/delete-requests') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Requests
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Request Information -->
                        <div class="card employee-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Request Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Request ID:</strong> #<?= $request['id'] ?></p>
                                        <p><strong>Status:</strong> 
                                            <span class="badge bg-warning">Pending</span>
                                        </p>
                                        <p><strong>Created:</strong> <?= date('M j, Y H:i', strtotime($request['created_at'])) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Requested By:</strong> <?= esc($request['requester_fullname']) ?></p>
                                        <p><strong>Requester Username:</strong> <?= esc($request['requester_name']) ?></p>
                                        <p><strong>Request Date:</strong> <?= date('Y-m-d', strtotime($request['created_at'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Information -->
                        <div class="card employee-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user"></i> Employee Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> <?= esc($request['first_name'] . ' ' . $request['last_name']) ?></p>
                                        <p><strong>Email:</strong> <?= esc($request['email']) ?></p>
                                        <p><strong>Phone:</strong> <?= esc($request['phone'] ?? 'N/A') ?></p>
                                        <p><strong>Position:</strong> <?= esc($request['position'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Department:</strong> <?= esc($request['department'] ?? 'N/A') ?></p>
                                        <p><strong>Hire Date:</strong> <?= $request['hire_date'] ? date('M j, Y', strtotime($request['hire_date'])) : 'N/A' ?></p>
                                        <p><strong>Employee ID:</strong> <?= $request['employee_id'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Data Impact -->
                        <div class="card warning-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-database"></i> Related Data Impact</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="data-item text-center">
                                            <h4 class="text-primary"><?= $related_data['attendance_count'] ?></h4>
                                            <p class="mb-0">Attendance Records</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="data-item text-center">
                                            <h4 class="text-info"><?= $related_data['leave_requests_count'] ?></h4>
                                            <p class="mb-0">Leave Requests</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="data-item text-center">
                                            <h4 class="text-success"><?= $related_data['salary_records_count'] ?></h4>
                                            <p class="mb-0">Salary Records</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> 
                                    Approving this request will soft delete the employee and all associated data will be preserved but marked as deleted.
                                </div>
                            </div>
                        </div>

                        <!-- Reason for Deletion -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-comment-alt"></i> Reason for Deletion</h5>
                            </div>
                            <div class="card-body">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-0"><?= nl2br(esc($request['reason'])) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Review Actions -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card action-card success-card">
                                    <div class="card-body text-center">
                                        <h5 class="text-success"><i class="fas fa-check-circle"></i> Approve Request</h5>
                                        <p class="text-muted">Approve this delete request. The employee will be soft deleted from the system.</p>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card action-card danger-card">
                                    <div class="card-body text-center">
                                        <h5 class="text-danger"><i class="fas fa-times-circle"></i> Reject Request</h5>
                                        <p class="text-muted">Reject this delete request. The employee will remain active in the system.</p>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Quick Info -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-bolt"></i> Quick Info</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Request Age:</strong></p>
                                <p class="text-muted mb-3">
                                    <?php
                                    $created = new DateTime($request['created_at']);
                                    $now = new DateTime();
                                    $interval = $created->diff($now);
                                    echo $interval->days > 0 ? $interval->days . ' days ago' : 'Today';
                                    ?>
                                </p>
                                <p class="mb-2"><strong>Priority:</strong></p>
                                <p class="text-muted mb-0">Normal</p>
                            </div>
                        </div>

                        <!-- Review Checklist -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-clipboard-check"></i> Review Checklist</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check1">
                                    <label class="form-check-label" for="check1">
                                        Employee information verified
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check2">
                                    <label class="form-check-label" for="check2">
                                        Deletion reason is valid
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check3">
                                    <label class="form-check-label" for="check3">
                                        Related data impact considered
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="check4">
                                    <label class="form-check-label" for="check4">
                                        Company policy compliance checked
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-history"></i> Recent Activity</h6>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="d-flex mb-3">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-plus-circle text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Request Created</h6>
                                            <p class="text-muted mb-0 small">
                                                <?= date('M j, Y H:i', strtotime($request['created_at'])) ?><br>
                                                by <?= esc($request['requester_fullname']) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check"></i> Approve Delete Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="approveForm">
                    <div class="modal-body">
                        <p>Are you sure you want to approve the delete request for <strong><?= esc($request['first_name'] . ' ' . $request['last_name']) ?></strong>?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> 
                            This action will soft delete the employee from the system. All data will be preserved but marked as deleted.
                        </div>
                        <div class="mb-3">
                            <label for="approveNotes" class="form-label">Review Notes (Optional)</label>
                            <textarea class="form-control" id="approveNotes" name="review_notes" rows="3" 
                                      placeholder="Add any notes for this approval..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Approve Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times"></i> Reject Delete Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm">
                    <div class="modal-body">
                        <p>Are you sure you want to reject the delete request for <strong><?= esc($request['first_name'] . ' ' . $request['last_name']) ?></strong>?</p>
                        <div class="mb-3">
                            <label for="rejectNotes" class="form-label required">Rejection Reason</label>
                            <textarea class="form-control" id="rejectNotes" name="review_notes" rows="3" 
                                      placeholder="Please provide a reason for rejection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Reject Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Approve form submission
        $('#approveForm').submit(function(e) {
            e.preventDefault();
            
            const formData = {
                review_notes: $('#approveNotes').val()
            };

            // Disable submit button
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: `<?= site_url('/admin/approve-delete-request/') ?><?= $request['id'] ?>`,
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect || '<?= site_url('/admin/delete-requests') ?>';
                    } else {
                        alert('Error: ' + response.message);
                        $('#approveForm').find('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-check"></i> Approve Request');
                    }
                },
                error: function() {
                    alert('An error occurred while approving the request.');
                    $('#approveForm').find('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-check"></i> Approve Request');
                }
            });
        });

        // Reject form submission
        $('#rejectForm').submit(function(e) {
            e.preventDefault();
            
            const formData = {
                review_notes: $('#rejectNotes').val()
            };

            if (!formData.review_notes.trim()) {
                alert('Rejection reason is required.');
                return;
            }

            // Disable submit button
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: `<?= site_url('/admin/reject-delete-request/') ?><?= $request['id'] ?>`,
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect || '<?= site_url('/admin/delete-requests') ?>';
                    } else {
                        alert('Error: ' + response.message);
                        $('#rejectForm').find('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-times"></i> Reject Request');
                    }
                },
                error: function() {
                    alert('An error occurred while rejecting the request.');
                    $('#rejectForm').find('button[type="submit"]').prop('disabled', false).html('<i class="fas fa-times"></i> Reject Request');
                }
            });
        });

        // Checklist validation
        function checkAllCheckboxes() {
            const allChecked = $('#check1, #check2, #check3, #check4').filter(':checked').length === 4;
            if (allChecked) {
                $('.action-card').addClass('border-success border-2');
            } else {
                $('.action-card').removeClass('border-success border-2');
            }
        }

        $('#check1, #check2, #check3, #check4').change(checkAllCheckboxes);
    </script>
</body>
</html>
