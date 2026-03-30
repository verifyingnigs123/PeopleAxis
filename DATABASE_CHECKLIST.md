# Database Fixes - Quick Reference Checklist

## 🎯 CRITICAL ISSUES TO FIX

### Issue #1: Missing Foreign Key Constraints ❌ CRITICAL
**Status:** FIXING
**Files Created:** `2026_03_17_000001_AddForeignKeyConstraints.php`

#### Missing FKs:
- [ ] `employees.department_id` → departments(id)
- [ ] `employees.position_id` → positions(id)  
- [ ] `employees.role_id` → roles(id)
- [ ] `salaries.employee_id` → employees(id)
- [ ] `attendance.employee_id` → employees(id)
- [ ] `attendance_logs.employee_id` → employees(id)
- [ ] `leaves.employee_id` → employees(id)
- [ ] `leave_requests.employee_id` → employees(id)
- [ ] `leave_requests.approved_by_manager` → employees(id)
- [ ] `leave_requests.approved_by_hr` → employees(id)
- [ ] `users.role_id` → roles(id)
- [ ] `audit_logs.user_id` → users(id)
- [ ] `notifications.user_id` → users(id)

**Action Required:** Run migration `2026_03_17_000001_AddForeignKeyConstraints.php`

---

### Issue #2: Duplicate Tables ❌ CRITICAL
**Status:** FIXING
**Files Created:** 
- `2026_03_17_000002_RemoveDuplicateTablesLeaves.php`
- `2026_03_17_000003_RemoveDuplicateTablesAttendance.php`

#### Tables to Remove:
- [ ] `leaves` TABLE (duplicate of `leave_requests`)
- [ ] `attendance` TABLE (duplicate of `attendance_logs`)

**Action Required:** 
1. Run migration `2026_03_17_000002_RemoveDuplicateTablesLeaves.php`
2. Run migration `2026_03_17_000003_RemoveDuplicateTablesAttendance.php`
3. Search & replace in code:
   - [ ] `table('leaves')` → `table('leave_requests')`
   - [ ] `table('attendance')` → `table('attendance_logs')`

**Files to Update:**
- [ ] `app/Controllers/*.php`
- [ ] `app/Models/*.php`
- [ ] `app/Views/*.php`
- [ ] Database queries/seeds

---

### Issue #3: Data Redundancy ⚠️ HIGH
**Status:** REVIEW NEEDED

#### Duplicate Data Fields:
- [ ] `users.name` duplicates data from `employees.first_name + last_name`
- [ ] `users.email` duplicates data from `employees.email`
- [ ] `employees.is_active` duplicates data from `users.is_active`

**Recommendation:**
- Keep data in primary location (users table for auth data)
- Link employees via `user_id` foreign key
- Update code to use consistent source

---

### Issue #4: Column Name Inconsistencies ⚠️ HIGH
**Status:** FIXING

#### Conflicting Column Names:
- [ ] `attendance.check_in` vs `attendance_logs.time_in` - standardize to `time_in`
- [ ] `attendance.check_out` vs `attendance_logs.time_out` - standardize to `time_out`
- [ ] `attendance.attendance_date` vs `attendance_logs.date` - standardize to `date`

**Action Required:** Update all code references:
```bash
Search for: check_in, check_out, attendance_date
Replace with: time_in, time_out, date
```

---

### Issue #5: Non-Normalized Values 🟡 MEDIUM
**Status:** FIXING
**Files Created:**
- `2026_03_17_000004_CreateLeaveTypesTable.php`
- `2026_03_17_000005_CreateAttendanceStatusTable.php`
- `2026_03_17_000006_CreateLeaveRequestStatusTable.php`

#### Hardcoded Values to Normalize:
- [ ] `leave_requests.leave_type` (VARCHAR) → should reference `leave_types` table
- [ ] `attendance_logs.status` (VARCHAR) → should reference `attendance_status` table
- [ ] `leave_requests.status` (VARCHAR) → should reference `leave_request_status` table

**Action Required:** Run lookup table creation migrations

---

## 📋 IMPLEMENTATION CHECKLIST

### Phase 1: Add Foreign Key Constraints (TODAY) 🔴
- [ ] Backup database first: `mysqldump -u root -p peopleaxis > backup.sql`
- [ ] Run: `php spark migrate --name 2026_03_17_000001_AddForeignKeyConstraints`
- [ ] Verify FK constraints exist:
  ```sql
  SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
  WHERE CONSTRAINT_NAME LIKE 'fk_%' AND TABLE_SCHEMA = 'peopleaxis';
  ```
- [ ] Test FK validation:
  ```php
  // Try to insert with invalid FK - should fail
  $employee = ['first_name' => 'Test', 'department_id' => 99999];
  $employeeModel->insert($employee);  // Should throw FK error
  ```
- [ ] ✅ Commit to git: `git add . && git commit -m "Add FK constraints for referential integrity"`

---

### Phase 2: Remove Duplicate Tables (THIS WEEK) 🟡
- [ ] Data backup: Verify `leave_requests` has all `leaves` data
- [ ] Run: `php spark migrate --name 2026_03_17_000002_RemoveDuplicateTablesLeaves`
- [ ] Data backup: Verify `attendance_logs` has all `attendance` data
- [ ] Run: `php spark migrate --name 2026_03_17_000003_RemoveDuplicateTablesAttendance`
- [ ] Search codebase for old table references:
  ```bash
  grep -r "table('leaves')" app/
  grep -r "table('attendance')" app/
  grep -r "->leaves" app/
  grep -r "->attendance" app/
  ```
- [ ] Replace all references:
  - `leaves` → `leave_requests`
  - `attendance` → `attendance_logs`
  - Update column names (check_in → time_in, etc.)
- [ ] Test all leave request functionality
- [ ] Test all attendance logging functionality
- [ ] ✅ Commit to git: `git add . && git commit -m "Remove duplicate leaves/attendance tables, update code references"`

---

### Phase 3: Create Lookup Tables (THIS WEEK) 🟡
- [ ] Run: `php spark migrate --name 2026_03_17_000004_CreateLeaveTypesTable`
- [ ] Run: `php spark migrate --name 2026_03_17_000005_CreateAttendanceStatusTable`
- [ ] Run: `php spark migrate --name 2026_03_17_000006_CreateLeaveRequestStatusTable`
- [ ] Verify default values inserted:
  ```sql
  SELECT COUNT(*) FROM leave_types;  -- Should be 8
  SELECT COUNT(*) FROM attendance_status;  -- Should be 6
  SELECT COUNT(*) FROM leave_request_status;  -- Should be 5
  ```
- [ ] Update queries to use lookup tables (optional but recommended)
- [ ] ✅ Commit to git: `git add . && git commit -m "Add lookup tables for data normalization"`

---

### Phase 4: Fix Data Redundancy (NEXT WEEK) 🟢
- [ ] Audit duplicate data: `users.email` vs `employees.email`
- [ ] Decide: Keep one source of truth
- [ ] Document decision in code comments
- [ ] Update all queries to use primary source
- [ ] Consider dropping duplicate columns (after testing)
- [ ] ✅ Commit to git: `git add . && git commit -m "Document and standardize data redundancy decision"`

---

### Phase 5: Testing (ONGOING) 🟢
- [ ] Unit tests pass: `php spark test`
- [ ] Integration tests:
  - [ ] Create employee → works
  - [ ] Link to department → works
  - [ ] Try invalid department_id → fails (FK constraint)
  - [ ] Submit leave request → works
  - [ ] Log attendance → works
- [ ] Manual testing on staging
- [ ] Smoke tests on production
- [ ] ✅ Commit to git: `git tag v1.2.0-normalized-db`

---

## 📊 DATABASE SCORE BEFORE & AFTER

### BEFORE (Current State)
```
Score Summary:
├── Primary Keys:     ✅ 10/10 (Good)
├── Foreign Keys:     ❌ 2/10 (Critical)
├── Normalization:    ⚠️ 6/10 (Partial)
├── Data Consistency: ❌ 3/10 (Poor)
├── Integrity:        ❌ 2/10 (Critical)
└── OVERALL:          🟠 4.6/10 (At Risk)
```

### AFTER (Expected State)
```
Score Summary:
├── Primary Keys:     ✅ 10/10 (Excellent)
├── Foreign Keys:     ✅ 10/10 (Excellent)
├── Normalization:    ✅ 9/10 (Excellent)
├── Data Consistency: ✅ 9/10 (Excellent)
├── Integrity:        ✅ 9/10 (Excellent)
└── OVERALL:          🟢 9.4/10 (Excellent)
```

---

## 🚨 POTENTIAL ISSUES & SOLUTIONS

### Issue A: "Migration failed - table already exists"
```
Cause: You already ran part of the migration
Solution: Check current migration status
  php spark migrate:status
  
Then either:
- Run only pending migrations
- Rollback and re-run
```

### Issue B: "FK constraint violation when inserting"
```
Cause: Foreign key reference doesn't exist
Example: department_id = 99 but department(99) doesn't exist

Solution: 
- Verify reference data exists first
- Change insert to use valid FK
- Or check if you meant to use NULL
```

### Issue C: "Code references old 'leaves' table"
```
Solution: One-by-one replacement:
  1. Find: grep -r "leaves" app/
  2. Replace: leaves → leave_requests
  3. Also fix column names (see Phase 2)
  4. Test: php spark test
```

### Issue D: "Attendance column mismatch"
```
Old column names (deprecated):
- check_in → time_in
- check_out → time_out
- attendance_date → date

Update all code references
```

---

## 📝 MIGRATION COMMAND QUICK REFERENCE

```bash
# Run all pending migrations
php spark migrate

# Run specific migration
php spark migrate --name 2026_03_17_000001_AddForeignKeyConstraints

# Check migration status
php spark migrate:status

# Rollback last batch
php spark migrate:rollback

# Refresh all migrations (⚠️ DELETES DATA)
php spark migrate:refresh

# Run seeds after migration
php spark db:seed DatabaseSeeder
```

---

## 📂 FILES CREATED FOR YOU

### Migrations (Run with `php spark migrate`)
```
app/Database/Migrations/
├── 2026_03_17_000001_AddForeignKeyConstraints.php
├── 2026_03_17_000002_RemoveDuplicateTablesLeaves.php
├── 2026_03_17_000003_RemoveDuplicateTablesAttendance.php
├── 2026_03_17_000004_CreateLeaveTypesTable.php
├── 2026_03_17_000005_CreateAttendanceStatusTable.php
└── 2026_03_17_000006_CreateLeaveRequestStatusTable.php
```

### Documentation
```
├── DATABASE_ANALYSIS.md
├── DATABASE_IMPLEMENTATION_GUIDE.md
├── DATABASE_ER_DIAGRAM.md
└── DATABASE_CHECKLIST.md (this file)
```

---

## ⏱️ ESTIMATED TIMELINE

| Phase | Task | Effort | Timeline |
|-------|------|--------|----------|
| 1 | Add FK constraints | 30 min | TODAY |
| 2 | Remove duplicate tables | 2-3 hours | This week |
| 2 | Update code references | 4-6 hours | This week |
| 3 | Create lookup tables | 30 min | This week |
| 4 | Fix redundancy | 4-8 hours | Next week |
| 5 | Full testing | 8+ hours | Ongoing |
| **TOTAL** | **All phases** | **~24 hours** | **2-3 weeks** |

---

## 🎓 KEY LEARNINGS

```
1. Foreign Keys: Prevent orphaned/invalid data
2. Normalization: Reduce redundancy, improve consistency
3. Lookup Tables: Make values maintainable & reportable
4. Referential Integrity: Database enforces relationships
5. Single Source of Truth: Reduces update anomalies
```

---

## ✅ SUCCESS CRITERIA

After implementing all changes, you should have:

- [x] Zero orphaned records (FK constraints prevent them)
- [x] Single tables for each entity (no duplicates)
- [x] Consistent column names across tables
- [x] Lookup tables for all status/type values
- [x] All tests passing
- [x] No code references to dropped tables
- [x] Documentation updated
- [x] Team trained on new structure

---

**Last Updated:** March 17, 2026
**Status:** Ready for Implementation
**Estimated Completion:** March 28-31, 2026

---

Got questions? Review:
1. `DATABASE_ANALYSIS.md` - Detailed audit
2. `DATABASE_IMPLEMENTATION_GUIDE.md` - Step-by-step guide
3. `DATABASE_ER_DIAGRAM.md` - Visual schemas
