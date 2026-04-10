<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }
        .priority-high {
            border-left: 4px solid #dc3545;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .stats-card {
            border-radius: 0.5rem;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-2px);
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
                <div class="nav-item dropdown position-relative">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i> Notifications
                        <span class="notification-badge" id="notificationCount" style="display: none;">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" id="notificationList" style="width: 300px; max-height: 400px; overflow-y: auto;">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li id="noNotifications" class="dropdown-item text-muted">No new notifications</li>
                    </ul>
                </div>
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
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 id="pendingCount">-</h4>
                                <p class="mb-0">Pending Requests</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 id="approvedToday">-</h4>
                                <p class="mb-0">Approved Today</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 id="rejectedToday">-</h4>
                                <p class="mb-0">Rejected Today</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-times fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 id="totalCount">-</h4>
                                <p class="mb-0">Total Requests</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-bar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-trash-alt text-danger"></i> Pending Delete Requests</h2>
                    <div>
                        <a href="<?= site_url('/admin/all-delete-requests') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> View All Requests
                        </a>
                    </div>
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
                                        <th>Requester</th>
                                        <th>Reason</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($requests)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                                No pending delete requests
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($requests as $request): ?>
                                            <tr class="priority-high">
                                                <td>
                                                    <strong><?= esc($request['first_name'] . ' ' . $request['last_name']) ?></strong><br>
                                                    <small class="text-muted"><?= esc($request['email']) ?></small>
                                                </td>
                                                <td>
                                                    <strong><?= esc($request['requester_fullname']) ?></strong><br>
                                                    <small class="text-muted"><?= esc($request['requester_name']) ?></small>
                                                </td>
                                                <td>
                                                    <span title="<?= esc($request['reason']) ?>">
                                                        <?= esc(substr($request['reason'], 0, 80)) ?><?= strlen($request['reason']) > 80 ? '...' : '' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?= date('M j, Y H:i', strtotime($request['created_at'])) ?></small>
                                                </td>
                                                <td class="action-buttons">
                                                    <a href="<?= site_url('/admin/review-delete-request/' . $request['id']) ?>" 
                                                       class="btn btn-sm btn-primary" title="Review Request">
                                                        <i class="fas fa-eye"></i> Review
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success quick-approve" 
                                                            data-id="<?= $request['id'] ?>"
                                                            data-employee="<?= esc($request['first_name'] . ' ' . $request['last_name']) ?>"
                                                            title="Quick Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger quick-reject" 
                                                            data-id="<?= $request['id'] ?>"
                                                            data-employee="<?= esc($request['first_name'] . ' ' . $request['last_name']) ?>"
                                                            title="Quick Reject">
                                                        <i class="fas fa-times"></i>
                                                    </button>
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

    <!-- Quick Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check"></i> Approve Delete Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="approveForm">
                    <div class="modal-body">
                        <p>Are you sure you want to approve the delete request for <strong id="approveEmployeeName"></strong>?</p>
                        <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> This action will soft delete the employee from the system.</p>
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

    <!-- Quick Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times"></i> Reject Delete Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm">
                    <div class="modal-body">
                        <p>Are you sure you want to reject the delete request for <strong id="rejectEmployeeName"></strong>?</p>
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
        let currentRequestId = null;

        // Quick approve functionality
        $('.quick-approve').click(function() {
            currentRequestId = $(this).data('id');
            $('#approveEmployeeName').text($(this).data('employee'));
            $('#approveModal').modal('show');
        });

        // Quick reject functionality
        $('.quick-reject').click(function() {
            currentRequestId = $(this).data('id');
            $('#rejectEmployeeName').text($(this).data('employee'));
            $('#rejectModal').modal('show');
        });

        // Approve form submission
        $('#approveForm').submit(function(e) {
            e.preventDefault();
            
            const formData = {
                review_notes: $('#approveNotes').val()
            };

            $.ajax({
                url: `<?= site_url('/admin/approve-delete-request/') ?>${currentRequestId}`,
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while approving the request.');
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

            $.ajax({
                url: `<?= site_url('/admin/reject-delete-request/') ?>${currentRequestId}`,
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while rejecting the request.');
                }
            });
        });

        // Load dashboard statistics
        function loadStats() {
            $.get('<?= site_url('/admin/dashboard-stats') ?>', function(data) {
                $('#pendingCount').text(data.pending_delete_requests || 0);
                $('#approvedToday').text(data.approved_today || 0);
                $('#rejectedToday').text(data.rejected_today || 0);
                $('#totalCount').text(data.total_requests || 0);
            });
        }

        // Load notifications
        function loadNotifications() {
            $.get('<?= site_url('/admin/notifications') ?>', function(data) {
                const notifications = data.notifications || [];
                const count = data.unread_count || 0;
                
                if (count > 0) {
                    $('#notificationCount').text(count).show();
                    $('#noNotifications').hide();
                    
                    // Build notification list
                    const notificationList = $('#notificationList');
                    notificationList.find('li:not(:first):not(:nth-child(2))').remove();
                    
                    notifications.forEach(function(notif) {
                        const typeClass = notif.type === 'danger' ? 'text-danger' : 
                                        notif.type === 'success' ? 'text-success' : 
                                        notif.type === 'warning' ? 'text-warning' : 'text-info';
                        
                        const li = $('<li class="dropdown-item"></li>');
                        li.html(`
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong class="${typeClass}">${notif.title}</strong><br>
                                    <small>${notif.message}</small>
                                </div>
                                <button class="btn btn-sm btn-link mark-read" data-id="${notif.id}" title="Mark as read">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `);
                        notificationList.append(li);
                    });
                } else {
                    $('#notificationCount').hide();
                    $('#noNotifications').show();
                }
            });
        }

        // Mark notification as read
        $(document).on('click', '.mark-read', function() {
            const notifId = $(this).data('id');
            $.post(`<?= site_url('/admin/mark-notification-read/') ?>${notifId}`, function() {
                loadNotifications();
            });
        });

        // Real-time updates
        function startRealTimeUpdates() {
            loadStats();
            loadNotifications();
            
            // Update every 30 seconds
            setInterval(function() {
                loadStats();
                loadNotifications();
            }, 30000);
        }

        // Initialize real-time updates
        startRealTimeUpdates();
    </script>
</body>
</html>
