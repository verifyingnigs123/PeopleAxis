# Database Normalization - Implementation Guide

## 📋 Quick Start: Step-by-Step Implementation

### **Step 1: Backup Your Database** ⚠️ CRITICAL
```bash
# Windows (via MySQL command line)
mysqldump -u root -p peopleaxis > backup_2026_03_17.sql

# Or via PHPMyAdmin: Export > Database > peopleaxis
```

---

## **Step 2: Run Migrations in Order**

Open terminal in your project root and run:

```bash
# Run all pending migrations (includes new FK and normalization)
php spark migrate

# Or run specific migration for testing:
php spark migrate --name 2026_03_17_000001_AddForeignKeyConstraints
```

**Migration execution order (automatic):**
1. ✅ `2026_03_17_000001_AddForeignKeyConstraints` - Add FK constraints
2. ✅ `2026_03_17_000002_RemoveDuplicateTablesLeaves` - Drop leaves table
3. ✅ `2026_03_17_000003_RemoveDuplicateTablesAttendance` - Drop attendance table
4. ✅ `2026_03_17_000004_CreateLeaveTypesTable` - Create leave_types lookup
5. ✅ `2026_03_17_000005_CreateAttendanceStatusTable` - Create attendance_status lookup
6. ✅ `2026_03_17_000006_CreateLeaveRequestStatusTable` - Create leave_request_status lookup

---

## **Step 3: Fix Duplicate Code References**

After dropping `leaves` and `attendance` tables, update your code:

### **Search & Replace in Controllers & Models:**

```bash
# Find all references to 'leaves' table
grep -r "table('leaves')" app/

# Find all references to 'attendance' table  
grep -r "table('attendance')" app/
```

**Replace with:**
- `table('leaves')` → `table('leave_requests')`
- `table('attendance')` → `table('attendance_logs')`

### **In Models, update:**
```php
// Old (leaves model)
public function getEmployeeLeaves($employeeId) {
    return $this->table('leaves')  // ❌ OLD
        ->where('employee_id', $employeeId)
        ->findAll();
}

// New (leave_requests model)
public function getEmployeeLeaves($employeeId) {
    return $this->table('leave_requests')  // ✅ NEW
        ->where('employee_id', $employeeId)
        ->findAll();
}
```

---

## **Step 4: Update Column References**

In attendance/attendance_logs:

```php
// Old column names
$record['check_in']     // ❌ OLD
$record['check_out']    // ❌ OLD
$record['attendance_date']  // ❌ OLD

// New column names (standardized)
$record['time_in']      // ✅ NEW
$record['time_out']     // ✅ NEW
$record['date']         // ✅ NEW
```

**Find and replace:**
```bash
grep -r "check_in\|check_out\|attendance_date" app/Controllers
grep -r "check_in\|check_out\|attendance_date" app/Models
grep -r "check_in\|check_out\|attendance_date" app/Views
```

---

## **Step 5: Verify FK Constraints Work**

Test in your application:

```php
// In a controller or command
$employeeModel = new EmployeeModel();

// This should now fail if department_id doesn't exist
try {
    $employeeModel->insert([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'department_id' => 999,  // Non-existent department
        // ... other fields
    ]);
} catch (\Exception $e) {
    echo "FK Constraint Error (expected): " . $e->getMessage();
}
```

---

## **Step 6: Clean Up Duplicate Data** (Optional but Recommended)

After migrations run successfully, you can remove duplicate data:

### **Remove duplicate email from employees table:**
```sql
-- First, verify data consistency
SELECT e.id, e.email, u.email 
FROM employees e 
JOIN users u ON e.user_id = u.id 
WHERE e.email != u.email;

-- If data is consistent, you can remove employee.email column
-- (after backing up and verifying all code uses users.email)
-- ALTER TABLE employees DROP COLUMN email;
```

### **Consolidate status fields:**
```sql
-- Check for inconsistencies between is_active and status
SELECT id, is_active, status FROM employees WHERE is_active = 0 AND status = 'active';

-- Standardize to use one field (domain decision needed)
```

---

## **Step 7: Update Foreign Key References in Code**

For tables with FK constraints, map old data to new lookup tables:

### **Example: Update leave_requests with leave_type_id**

```php
// If you add leave_type_id column to leave_requests:
$this->db->query("
    UPDATE leave_requests lr
    SET lr.leave_type_id = (
        SELECT id FROM leave_types WHERE name = lr.leave_type LIMIT 1
    )
    WHERE lr.leave_type_id IS NULL
");
```

---

## **Step 8: Update Queries to Use FKs Properly**

### **Before (no FK relations):**
```php
// Inefficient: manual joins
$leaves = $this->db->table('leave_requests')
    ->join('employees', 'leave_requests.employee_id = employees.id')
    ->select('leave_requests.*, employees.first_name, employees.last_name')
    ->where('leave_requests.employee_id', $employeeId)
    ->get()
    ->getResult();
```

### **After (with FK relations & models):**
```php
// Better: use relationships in models
class LeaveRequestModel extends Model {
    protected $table = 'leave_requests';
    protected $dates = ['created_at', 'updated_at'];
    
    public function employee() {
        return $this->belongsTo(EmployeeModel::class, 'employee_id', 'id');
    }
}

// Simple query
$leaves = $this->leaveRequestModel
    ->with('employee')
    ->where('employee_id', $employeeId)
    ->findAll();
```

---

## **Step 9: Test Everything**

### **Unit Tests:**
```bash
php spark test  # Run all tests
```

### **Manual Testing Checklist:**
- [ ] Can create employees with valid departments
- [ ] Cannot create employees with invalid department_id (FK constraint)
- [ ] Leave requests submit successfully
- [ ] Cannot delete a department if employees exist (referential integrity)
- [ ] Attendance logging works with attendance_logs table
- [ ] All reports/queries still work

---

## **Step 10: Monitor & Document**

### **Check for FK constraint errors:**
```bash
# View error logs
tail -f writable/logs/*.log | grep -i "foreign\|constraint"
```

### **Document any custom queries:**
Create a file `docs/QUERIES_TO_UPDATE.md` listing:
- Any raw SQL queries that reference old table/column names
- Custom reports that need updating
- External API contracts that might be affected

---

## **Rollback (If Needed)**

```bash
# Rollback specific migration
php spark migrate:rollback --batch=1

# Full rollback to previous state
php spark migrate:refresh  # ⚠️ WARNING: Deletes all data!
```

---

## **Expected Changes Summary**

| Change | Before | After | Impact |
|--------|--------|-------|--------|
| **FK Constraints** | 1/14 | 14/14 | Prevents orphaned data |
| **Duplicate Tables** | 2 tables | 0 tables | Cleaner schema |
| **Column Names** | Inconsistent | Standardized | Easier maintenance |
| **Lookup Tables** | Hardcoded strings | 3 new tables | Better normalization |
| **Data Integrity** | Low | High | Fewer bugs |

---

## **Testing Migrations Before Production**

```bash
# Test on local copy first
cd C:\xampp\htdocs\PeopleAxis

# Create test database
# Import backup: peopleaxis_test

# Run migrations on test DB
php spark migrate --name 2026_03_17_000001_AddForeignKeyConstraints

# Verify structure
php spark db:seed DatabaseSeeder  # Or your seed

# Run full test suite
php spark test

# Check for errors, warnings
# Then apply to production
```

---

## **Common Issues & Solutions**

### **Issue: "FOREIGN KEY constraint fails"**
```
Error: Integrity constraint violation
Cause: You're trying to update/delete data that has foreign key references
Solution: Update related records first, then the parent record
```

### **Issue: "Table 'leaves' doesn't exist"**
```
Error: SQLSTATE[42S02]: Table or view not found
Cause: Code still references old leaves table
Solution: Update code to use leave_requests instead
```

### **Issue: Migration fails with "Duplicate entry"**
```
Error: Duplicate entry '123' for key 'leave_requests.employee_id'
Cause: Data migration had duplicate records
Solution: Clean up duplicates before running migration
```

---

## **Next Steps**

1. **Immediate (Today):** Run FK constraint migration, test locally
2. **This Week:** Update code references, test everything
3. **Next Week:** Deploy to staging, run full QA
4. **Production:** Rolling backup → Deploy → Monitor logs

---

## **Files Created for You**

📁 **Migration Files** (auto-run with `php spark migrate`):
- `2026_03_17_000001_AddForeignKeyConstraints.php`
- `2026_03_17_000002_RemoveDuplicateTablesLeaves.php`
- `2026_03_17_000003_RemoveDuplicateTablesAttendance.php`
- `2026_03_17_000004_CreateLeaveTypesTable.php`
- `2026_03_17_000005_CreateAttendanceStatusTable.php`
- `2026_03_17_000006_CreateLeaveRequestStatusTable.php`

📄 **Documentation Files:**
- `DATABASE_ANALYSIS.md` - Detailed audit report
- `DATABASE_IMPLEMENTATION_GUIDE.md` - This file
- `DATABASE_ER_DIAGRAM.md` - Visual schema

---

**Questions?** Check the DATABASE_ANALYSIS.md file for detailed explanations of each issue.
