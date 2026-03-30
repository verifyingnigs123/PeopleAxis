# Database Analysis & Normalization Report
**PeopleAxis HR System**

---

## 1. CRITICAL ISSUES FOUND ⚠️

### **Missing Foreign Key Constraints**
Your database has serious structural issues. Many tables reference other tables but **lack proper foreign key constraints**:

| Table | Foreign Key Column | References | Issue |
|-------|-------------------|-----------|-------|
| employees | department_id | departments(id) | ❌ NO FK CONSTRAINT |
| employees | position_id | positions(id) | ❌ NO FK CONSTRAINT |
| employees | user_id | users(id) | ✅ HAS FK (correct) |
| employees | role_id | roles(id) | ❌ NO FK CONSTRAINT |
| leaves | employee_id | employees(id) | ❌ NO FK CONSTRAINT |
| attendance | employee_id | employees(id) | ❌ NO FK CONSTRAINT |
| salaries | employee_id | employees(id) | ❌ NO FK CONSTRAINT |
| audit_logs | user_id | users(id) | ❌ NO FK CONSTRAINT |
| attendance_logs | employee_id | employees(id) | ❌ NO FK CONSTRAINT |
| leave_requests | employee_id | employees(id) | ❌ NO FK CONSTRAINT |
| leave_requests | approved_by_manager | employees(id) | ❌ NO FK CONSTRAINT |
| leave_requests | approved_by_hr | employees(id) | ❌ NO FK CONSTRAINT |
| notifications | user_id | users(id) | ❌ NO FK CONSTRAINT |
| users | role_id | roles(id) | ❌ NO FK CONSTRAINT |

---

## 2. NORMALIZATION ANALYSIS

### **Normalization Level: 2.5NF** (Should be 3NF)

#### **First Normal Form (1NF)** ✅ PASS
- All tables have primary keys
- All values are atomic (no arrays or embedded data)
- No repeating groups

#### **Second Normal Form (2NF)** ✅ PASS
- No partial dependencies (non-key attributes depend on entire primary key)

#### **Third Normal Form (3NF)** ⚠️ PARTIAL FAIL

**Issues:**

1. **Duplicate Data Across Tables:**
   ```
   users.name vs employees.first_name + employees.last_name
   users.email vs employees.email
   ```
   This violates 3NF - same data stored in multiple places

2. **Redundant Status Fields:**
   - `users.is_active` (stored in users table)
   - `employees.status` (duplicates in employees table)
   - `employees.account_status` (separate field, overlapping purpose)
   - These create data inconsistency risks

3. **Leave Type Should Be Normalized:**
   ```
   Current: leave_type stored as VARCHAR in leaves, leave_requests
   Better: Create leave_types table with id, name, description
   ```

4. **Attendance Status Should Be Normalized:**
   ```
   Current: status stored as VARCHAR ('present', 'absent', etc.)
   Better: Create attendance_status lookup table
   ```

---

## 3. CURRENT TABLE STRUCTURE

### **Master/Reference Tables:**
- ✅ `users` - Authentication & access
- ✅ `roles` - Role definitions
- ✅ `departments` - Department info
- ✅ `positions` - Job positions

### **Transactional Tables:**
- ❌ `employees` - Has inconsistent design
- ❌ `leaves` - Duplicate of leave_requests
- ❌ `attendance` - Duplicate of attendance_logs
- ❌ `salaries` - Proper structure but missing constraints
- ❌ `leave_requests` - Better than leaves table
- ❌ `attendance_logs` - Better than attendance table

### **Utility Tables:**
- `audit_logs` - Missing FK on user_id
- `notifications` - Missing FK on user_id
- `otp` - Standalone, properly designed

---

## 4. WHAT SHOULD YOU DO? (Action Plan)

### **PHASE 1: Fix Foreign Key Constraints (HIGH PRIORITY)** 🔴
Add FK constraints to all child tables:

```php
// Migration example:
$this->db->query('ALTER TABLE employees ADD CONSTRAINT fk_employees_department_id 
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL');
```

**Tables needing FK constraints:**
1. `employees` → department_id, position_id, role_id
2. `leaves` → employee_id
3. `attendance` → employee_id
4. `salaries` → employee_id
5. `audit_logs` → user_id
6. `attendance_logs` → employee_id
7. `leave_requests` → employee_id, approved_by_manager, approved_by_hr
8. `notifications` → user_id
9. `users` → role_id

---

### **PHASE 2: Eliminate Duplicate Tables (MEDIUM PRIORITY)** 🟡

**Status:** You have duplicate tables that serve the same purpose:

1. **`leaves` vs `leave_requests`**
   - `leaves` is old design (has employee_id index only)
   - `leave_requests` is new design (has proper approvals with manager/hr fields)
   - ✅ **Action:** Migrate all leaves data to leave_requests, drop leaves table

2. **`attendance` vs `attendance_logs`**
   - Both store similar data
   - `attendance` uses check_in/check_out times
   - `attendance_logs` uses time_in/time_out
   - Same data, different structure = confusion
   - ✅ **Action:** Consolidate into one table (standardize column names)

---

### **PHASE 3: Fix Data Redundancy (MEDIUM PRIORITY)** 🟡

**Remove duplicate user data from employees table:**
```
CURRENT STATE:
users table:      id, email, name, password, role_id, created_at
employees table:  id, email, first_name, last_name, user_id, ...

PROBLEM: Email and name stored in both places
```

**Solution:**
```php
// Option A: Keep employees.email, remove users.name (better for complex names)
// Option B: Keep users data for auth, employees only for HR details

// Recommended approach:
- users: id, email, password, role_id, is_active
- employees: id, user_id(FK), first_name, last_name, phone, ...
```

---

### **PHASE 4: Normalize Enum/Lookup Values (LOW PRIORITY)** 🟢

**Current Issues:**
- Leave types stored as VARCHAR
- Status fields hardcoded ('active', 'pending', 'approved', etc.)
- These should be in lookup tables for consistency

**Create these lookup tables:**
```php
// Leave Types Table
leaves_type (id, name, description, is_active)
  - Casual, Sick, Earned, Unpaid, etc.

// Employee Status Table
employee_status (id, code, description)
  - Active, Inactive, On Leave, Terminated, etc.

// Attendance Status Table
attendance_status (id, code, description)
  - Present, Absent, Late, Half-day, etc.

// Leave Request Status Table
leave_request_status (id, code, description)
  - Pending, Approved, Rejected, Cancelled, etc.
```

---

### **PHASE 5: Add Missing Indexes (LOW PRIORITY)** 🟢

**Current gaps:**
- `employees.user_id` - Has FK but should have index for lookups
- `salaries.employee_id` - Used for lookups, needs index
- Date fields used for filtering should have indexes
- Status columns used for filtering need indexes

---

## 5. RECOMMENDED TABLE RELATIONSHIPS

```
users (id, username, email, password, role_id)
  └─ FK: role_id → roles(id)

roles (id, name, description, deleted_at)

employees (id, user_id, first_name, last_name, phone, department_id, position_id, role_id, status)
  ├─ FK: user_id → users(id)
  ├─ FK: department_id → departments(id)
  ├─ FK: position_id → positions(id)
  └─ FK: role_id → roles(id)

departments (id, name, description, manager_id, is_active)
  └─ FK: manager_id → employees(id)

positions (id, name, description, is_active)

salaries (id, employee_id, base_salary, allowances, deductions, net_salary, effective_from)
  └─ FK: employee_id → employees(id)

leave_requests (id, employee_id, leave_type_id, start_date, end_date, status, approved_by_manager, approved_by_hr)
  ├─ FK: employee_id → employees(id)
  ├─ FK: leave_type_id → leave_types(id)
  ├─ FK: approved_by_manager → employees(id)
  └─ FK: approved_by_hr → employees(id)

attendance_logs (id, employee_id, attendance_date, time_in, time_out, status_id)
  ├─ FK: employee_id → employees(id)
  └─ FK: status_id → attendance_status(id)

audit_logs (id, user_id, action, description, timestamp)
  └─ FK: user_id → users(id)

notifications (id, user_id, message, is_read, type, created_at)
  └─ FK: user_id → users(id)

otp (id, email, otp, created_at, expires_at, is_used, attempts)
```

---

## 6. SUMMARY TABLE

| Aspect | Status | Score | Issue |
|--------|--------|-------|-------|
| **Primary Keys** | ✅ Good | 10/10 | All tables have PKs |
| **Foreign Keys** | ❌ Critical | 2/10 | Only 1 FK properly defined |
| **Normalization** | ⚠️ Partial | 6/10 | Duplicate data, redundant columns |
| **Data Consistency** | ❌ Poor | 3/10 | Multiple tables same data |
| **Duplicate Tables** | ❌ Bad | 2/10 | leaves & attendance tables duplicate |
| **Status Inconsistency** | ❌ Poor | 2/10 | Multiple status fields overlap |
| **Indexes** | ⚠️ Partial | 5/10 | Some missing on FK columns |

---

## 7. QUICK FIXES (Do These First)

### 1. Add FK Constraints Script
```sql
-- Add FK to employees
ALTER TABLE employees ADD CONSTRAINT fk_emp_dept FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;
ALTER TABLE employees ADD CONSTRAINT fk_emp_pos FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE SET NULL;
ALTER TABLE employees ADD CONSTRAINT fk_emp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL;

-- Add FK to child tables
ALTER TABLE salaries ADD CONSTRAINT fk_sal_emp FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE;
ALTER TABLE attendance_logs ADD CONSTRAINT fk_att_emp FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE;
ALTER TABLE leave_requests ADD CONSTRAINT fk_leave_emp FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE;

-- Add FK to users
ALTER TABLE users ADD CONSTRAINT fk_user_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL;
ALTER TABLE audit_logs ADD CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE notifications ADD CONSTRAINT fk_notify_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
```

### 2. Drop Duplicate Tables
```sql
-- Migrate leaves to leave_requests first, then drop
DROP TABLE leaves;
DROP TABLE attendance;
```

### 3. Standardize Column Names
- attendance_logs: Use consistent naming (check_in/check_out OR time_in/time_out)
- attendance_date vs date: Pick one

---

## 8. IMPLEMENTATION PRIORITY

1. **IMMEDIATE (Week 1):** Add FK constraints - prevents data corruption
2. **URGENT (Week 2):** Remove duplicate tables - simplifies queries
3. **IMPORTANT (Week 3):** Remove redundant columns - improves performance
4. **NICE-TO-HAVE (Week 4):** Create lookup tables - standardizes values

---

## Conclusion

Your database is **"at-risk"** due to missing foreign key constraints. While structurally it's about 60% normalized, the lack of referential integrity could lead to:
- ❌ Orphaned records (leaves without employees)
- ❌ Data inconsistency (duplicate email fields)
- ❌ Update anomalies (changing email in users doesn't update employees)
- ❌ Query complexity (manual joins instead of FK relationships)

**Start with Phase 1 immediately** - adding FK constraints is the quickest way to improve data integrity.
