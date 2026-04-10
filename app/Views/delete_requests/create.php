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
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
        }
        .required::after {
            content: " *";
            color: #dc3545;
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-plus text-primary"></i> Create Delete Request</h2>
                    <a href="<?= site_url('/delete-requests') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Requests
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="deleteRequestForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="employee_id" class="form-label required">Employee to Delete</label>
                                        <select class="form-select" id="employee_id" name="employee_id" required>
                                            <option value="">Select Employee</option>
                                            <?php foreach ($employees as $employee): ?>
                                                <option value="<?= $employee['id'] ?>">
                                                    <?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?> - 
                                                    <?= esc($employee['position']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Requesting User</label>
                                        <input type="text" class="form-control" value="<?= esc($user['name']) ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Details Card (shown after selection) -->
                            <div id="employeeDetails" class="employee-card" style="display: none;">
                                <h6><i class="fas fa-user"></i> Employee Details</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> <span id="empName"></span></p>
                                        <p><strong>Email:</strong> <span id="empEmail"></span></p>
                                        <p><strong>Phone:</strong> <span id="empPhone"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Position:</strong> <span id="empPosition"></span></p>
                                        <p><strong>Department:</strong> <span id="empDepartment"></span></p>
                                        <p><strong>Hire Date:</strong> <span id="empHireDate"></span></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label required">Reason for Deletion</label>
                                <textarea class="form-control" id="reason" name="reason" rows="4" 
                                          placeholder="Please provide a detailed reason for requesting employee deletion..." required></textarea>
                                <div class="form-text">Minimum 10 characters, maximum 1000 characters</div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> 
                                This action will submit a request to delete the selected employee. The Super Admin will review this request and either approve or reject it. If approved, the employee will be soft deleted from the system.
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?= site_url('/delete-requests') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-paper-plane"></i> Submit Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check"></i> Request Submitted</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Your delete request has been submitted successfully.</p>
                    <p>The Super Admin will review your request and you will be notified of the decision.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="goToRequests">View Requests</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Character counter for reason field
        $('#reason').on('input', function() {
            const length = $(this).val().length;
            const remaining = 1000 - length;
            const counter = $(this).siblings('.form-text');
            
            if (remaining < 0) {
                counter.html('<span class="text-danger">Maximum 1000 characters exceeded</span>');
            } else if (length < 10) {
                counter.html(`Minimum 10 characters required (${length}/1000)`);
            } else {
                counter.html(`${length}/1000 characters`);
            }
        });

        // Load employee details when selected
        $('#employee_id').change(function() {
            const employeeId = $(this).val();
            
            if (employeeId) {
                $.get(`<?= site_url('/delete-requests/get-employee/') ?>${employeeId}`, function(data) {
                    if (data.success) {
                        const emp = data.employee;
                        $('#empName').text(emp.first_name + ' ' + emp.last_name);
                        $('#empEmail').text(emp.email || 'N/A');
                        $('#empPhone').text(emp.phone || 'N/A');
                        $('#empPosition').text(emp.position || 'N/A');
                        $('#empDepartment').text(emp.department || 'N/A');
                        $('#empHireDate').text(emp.hire_date ? new Date(emp.hire_date).toLocaleDateString() : 'N/A');
                        $('#employeeDetails').show();
                    } else {
                        $('#employeeDetails').hide();
                        alert('Error loading employee details: ' + data.message);
                    }
                });
            } else {
                $('#employeeDetails').hide();
            }
        });

        // Form submission
        $('#deleteRequestForm').submit(function(e) {
            e.preventDefault();
            
            const formData = {
                employee_id: $('#employee_id').val(),
                reason: $('#reason').val()
            };

            // Validate
            if (!formData.employee_id) {
                alert('Please select an employee.');
                return;
            }

            if (formData.reason.length < 10) {
                alert('Reason must be at least 10 characters long.');
                return;
            }

            if (formData.reason.length > 1000) {
                alert('Reason cannot exceed 1000 characters.');
                return;
            }

            // Disable submit button
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

            $.ajax({
                url: '<?= site_url('/delete-requests/store') ?>',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#successModal').modal('show');
                    } else {
                        alert('Error: ' + response.message);
                        if (response.errors) {
                            console.log('Validation errors:', response.errors);
                        }
                    }
                },
                error: function(xhr) {
                    let message = 'An error occurred while submitting the request.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                },
                complete: function() {
                    // Re-enable submit button
                    $('#submitBtn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Request');
                }
            });
        });

        // Handle modal redirect
        $('#goToRequests').click(function() {
            window.location.href = '<?= site_url('/delete-requests') ?>';
        });
    </script>
</body>
</html>
