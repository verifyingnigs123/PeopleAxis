# Employee Account Approval Workflow - Implementation Summary

## What Was Implemented

### 1. **Database Migration**
- File: `app/Database/Migrations/2026_03_03_000001_AddAccountStatusToEmployees.php`
- Added fields to `employees` table:
  - `account_status` (ENUM): 'pending', 'approved', 'rejected'
  - `user_id` (FK): References the created user account
  - `approval_notes`: Notes for approval/rejection reasons

### 2. **Model Updates**
- **EmployeeModel** (`app/Models/EmployeeModel.php`):
  - Added new allowed fields: `account_status`, `user_id`, `approval_notes`
  - New methods:
    - `getPendingEmployees()`: Gets employees awaiting approval
    - `getApprovedEmployees()`: Gets approved employees with accounts
    - `getRejectedEmployees()`: Gets rejected employee requests

### 3. **Employee Creation Workflow**
When HR Admin adds a new employee:
- Employee status is set to `account_status = 'pending'`
- Notification is sent to **all Super Admins** with:
  - Title: "New Employee Awaiting Approval"
  - Link: `/employee/review/{employee_id}`
  - Message includes employee name and ID

### 4. **Super Admin Approval Workflow**

#### Review Employee (`/employee/review/{id}`)
- Super Admin can view full employee details
- Returns JSON with employee information:
  - Personal details (name, email, phone, DOB)
  - Job details (department, position, joining date)
  - Current approval status

#### Approve Account (`/employee/approve-account/{id}`)
Super Admin action:
- Creates a system user account automatically
- Generates secure password
- Assigns to HR Admin role (configurable)
- Sends welcome email with credentials to employee email:
  - Email address
  - Credentials
  - Security reminder
- Updates employee: `account_status = 'approved'`, links `user_id`
- Notifies **all HR Admins** with success notification:
  - Title: "Employee Account Approved"
  - Message: Includes employee name
  - Links to employee details

#### Reject Account (`/employee/reject-account/{id}`)
Super Admin action:
- Updates employee: `account_status = 'rejected'`
- Stores rejection reason in `approval_notes`
- Notifies **all HR Admins** with rejection notification:
  - Title: "Employee Account Rejected"
  - Includes rejection reason
  - Links to employee details for further action

### 5. **New Controller Methods** (Employees.php)
- `review($employeeId)`: Get employee details for review
- `approveAccount($employeeId)`: Create account and send email
- `rejectAccount($employeeId)`: Reject with reason
- `generatePassword()`: Generate secure random password
- `sendCredentialsEmail()`: Send HTML formatted email with credentials

### 6. **Routes Added** (app/Config/Routes.php)
```php
$routes->get('/employee/review/(:num)', 'Employees::review/$1');
$routes->post('/employee/approve-account/(:num)', 'Employees::approveAccount/$1');
$routes->post('/employee/reject-account/(:num)', 'Employees::rejectAccount/$1');
```

### 7. **Notification Flow**
```
HR Admin creates Employee
    ↓
Employee created with account_status = 'pending'
    ↓
Notification sent to Super Admin(s) 🔔
    ↓
Super Admin clicks notification
    ↓
Reviews employee details
    ↓
DECISION 1: APPROVE
    ├─ User account created (auto-assigned to HR Admin role)
    ├─ Secure password generated
    ├─ Email sent to employee with credentials
    ├─ Employee status: account_status = 'approved'
    └─ HR Admin(s) notified ✅
    
DECISION 2: REJECT
    ├─ Employee status: account_status = 'rejected'
    ├─ Rejection reason stored
    └─ HR Admin(s) notified ❌
```

## Setup Instructions

### 1. **Run Migration**
```bash
php spark migrate
```

### 2. **Configure Email Settings** (app/Config/Email.php)
Ensure these are set in your `.env` file:
```
email.fromEmail = your-email@example.com
email.fromName = PeopleAxis HR System
email.protocol = smtp
email.SMTPHost = smtp.gmail.com (or your provider)
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-app-password
email.SMTPPort = 587
```

### 3. **View Integration** (Optional - For UI)
The employee details review and approval/rejection can be accessed:
- Via notification link: `/employee/review/{id}`
- The Employees controller's `index()` now passes `pendingEmployees` and `isSuperAdmin` to the view
- You can add a "Pending Approvals" section in `app/Views/employee/index.php`

### 4. **Sample Modal for Approval** (Add to employee view)
```html
<!-- Review Employee Modal -->
<div class="modal fade" id="reviewEmployeeModal">
    <!-- Modal content for reviewing employee details and approve/reject buttons -->
</div>

<!-- Approval Form -->
<button onclick="approveEmployeeAccount(employeeId)">Approve & Create Account</button>
<button onclick="openRejectModal(employeeId)">Reject Request</button>
```

## API Endpoints

### Get Employee Details (for review)
```
GET /employee/review/{id}
Response: JSON with employee data
```

### Approve Employee Account
```
POST /employee/approve-account/{id}
Response: {success: true, message: "...", csrf_hash: "..."}
Email sent automatically to employee
```

### Reject Employee Account
```
POST /employee/reject-account/{id}
Body: {rejection_notes: "reason here"}
Response: {success: true, message: "...", csrf_hash: "..."}
```

## Key Features

✅ **Automatic Notifications**: HR Admin and Super Admin notified at each stage  
✅ **Secure Password Generation**: Uses 12-character complex passwords  
✅ **Email Delivery**: HTML-formatted credential emails with security reminders  
✅ **Audit Trail**: Approval notes stored for rejected accounts  
✅ **Role Assignment**: Automatically assigns HR Admin role to new employee accounts  
✅ **Status Tracking**: account_status field tracks workflow state  
✅ **User Linking**: employee.user_id links to created system account  
✅ **Dashboard Visibility**: Super Admin sees pending list in employees index  

## User Status States

- **pending**: Waiting for Super Admin to review and create account
- **approved**: Account created, employee has system access
- **rejected**: Rejected by Super Admin, reason stored in approval_notes

## Notes

- All Super Admins receive the initial notification
- All HR Admins receive approval/rejection notifications
- Email includes a password reset reminder for security
- Employee can change their password on first login (recommended)
- Rejection doesn't delete the employee record; HR Admin can try again later
