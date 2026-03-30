# Database Entity-Relationship Diagram (ERD)

## Current vs Recommended Structure

```
┌─────────────────────────────────────────────────────────────┐
│          PEOPLEAXIS HRM DATABASE STRUCTURE                  │
└─────────────────────────────────────────────────────────────┘

1. RECOMMENDED STRUCTURE (After Fixes)
════════════════════════════════════════════════════════════════

    ┌──────────────────┐
    │      ROLES       │
    │──────────────────│
    │ PK: id           │
    │ • name           │
    │ • description    │
    │ • deleted_at     │
    └────────┬─────────┘
             │
             │ 1:N
             ├──────────────────────┐
             │                      │
    ┌────────▼──────────┐  ┌───────▼──────────────┐
    │      USERS        │  │    EMPLOYEES        │
    │──────────────────│  │─────────────────────│
    │ PK: id           │  │ PK: id              │
    │ FK: role_id      │◄─┤ FK: user_id (NEW)   │
    │ • username       │  │ FK: department_id   │
    │ • email (unique) │  │ FK: position_id     │
    │ • password       │  │ FK: role_id (NEW)   │
    │ • is_active      │  │ • employee_id       │
    │ • deleted_at     │  │ • first_name        │
    │ • created/updated│  │ • last_name         │
    └─────────┬────────┘  │ • phone             │
              │           │ • date_hired        │
              │           │ • status            │
              │           │ • account_status    │
              │           │ • biometric_id      │
              │           │ • created/updated   │
              │           └─────────┬───────────┘
              │                     │
              │                     │ 1:N
              │         ┌───────────┼───────────┬──────────────┐
              │         │           │           │              │
              │    ┌────▼─────┐  ┌──▼────────┐  │   ┌─────────▼──────┐
              │    │ LEAVES/  │  │ATTENDANCE │  │   │    SALARIES    │
              │    │LEAVE_REQ │  │_LOGS (NEW)│  │   │────────────────│
              │    │─────────│  │───────────│  │   │ PK: id         │
              │    │PK: id    │  │PK: id     │  │   │ FK: employee_id│
              │    │FK: emp_id│  │FK: emp_id │  │   │ • base_salary  │
              │    │FK: emp_id│  │ • date    │  │   │ • allowances   │
              │    │(mgr)     │  │ • time_in │  │   │ • deductions   │
              │    │FK: emp_id│  │ • time_out│  │   │ • net_salary   │
              │    │(hr)      │  │ • status  │  │   │ • effective_from
              │    │ • type   │  │           │  │   │ • statutory    │
              │    │ • dates  │  │ • created │  │   │   deductions   │
              │    │ • status │  │ • updated │  │   │ • created/upd  │
              │    │ • reason │  └───────────┘  │   └────────────────┘
              │    │ • created│                 │
              │    │ • updated│         ┌───────▼────────────┐
              │    └──────────┘         │  DEPARTMENTS       │
              │                         │────────────────────│
              │                         │ PK: id             │
              │              ┌──────────┤ FK: manager_id (EMP)
              │              │          │ • name             │
              │              │          │ • description      │
              │              │          │ • is_active        │
              │              │          │ • created/updated  │
              │              │          └────────────────────┘
              │              │
              │         ┌────▼──────────────┐
              │         │   POSITIONS       │
              │         │───────────────────│
              │         │ PK: id            │
              │         │ • name            │
              │         │ • description     │
              │         │ • is_active       │
              │         │ • created/updated │
              │         └───────────────────┘
              │
    ┌─────────▼──────────────────────────────────────────┐
    │           AUDIT & NOTIFICATIONS                   │
    │────────────────────────────────────────────────────│
    │                                                    │
    │  AUDIT_LOGS                  NOTIFICATIONS        │
    │  ─────────────────           ────────────────     │
    │  PK: id                      PK: id                │
    │  FK: user_id                 FK: user_id           │
    │  • action                    • message             │
    │  • description               • is_read             │
    │  • timestamp                 • type                │
    │                              • created_at          │
    │                                                    │
    └────────────────────────────────────────────────────┘

────────────────────────────────────────────────────────────

2. NEW LOOKUP TABLES (Normalization)
════════════════════════════════════════════════════════════════

    ┌──────────────────────────┐
    │     LEAVE_TYPES (NEW)    │
    │──────────────────────────│
    │ PK: id                   │
    │ • name (unique)          │
    │ • description            │
    │ • is_active              │
    │ • created/updated        │
    └────────┬─────────────────┘
             │ 1:N
             │
         (Future enhancement:)
    ┌────────▼──────────────────────┐
    │    LEAVE_REQUESTS (updated)    │
    │────────────────────────────────│
    │ PK: id                         │
    │ FK: employee_id                │
    │ FK: leave_type_id (optional)   │
    │ FK: approved_by_manager        │
    │ FK: approved_by_hr             │
    │ • start_date/end_date          │
    │ • reason                       │
    │ • status (or FK: status_id)    │
    │ • created/updated              │
    └────────────────────────────────┘

    ┌──────────────────────────────────┐
    │   ATTENDANCE_STATUS (NEW)        │
    │──────────────────────────────────│
    │ PK: id                           │
    │ • code (PRESENT, ABSENT, etc.)   │
    │ • name                           │
    │ • description                    │
    │ • color (UI display)             │
    │ • is_active                      │
    │ • created/updated                │
    └────────┬──────────────────────────┘
             │ 1:N
             │
         (Future enhancement:)
    ┌────────▼──────────────────────┐
    │    ATTENDANCE_LOGS (updated)    │
    │────────────────────────────────│
    │ PK: id                         │
    │ FK: employee_id                │
    │ FK: status_id (optional)       │
    │ • date                         │
    │ • time_in/time_out             │
    │ • created/updated              │
    └────────────────────────────────┘

    ┌────────────────────────────────────┐
    │ LEAVE_REQUEST_STATUS (NEW)        │
    │────────────────────────────────────│
    │ PK: id                            │
    │ • code (PENDING, APPROVED, etc.)   │
    │ • name                            │
    │ • description                     │
    │ • color                           │
    │ • is_approved (boolean)           │
    │ • is_active                       │
    │ • created/updated                 │
    └────────────────────────────────────┘

────────────────────────────────────────────────────────────

3. UTILITY TABLES
════════════════════════════════════════════════════════════════

    ┌──────────────────┐
    │   OTP (Secure)   │
    │──────────────────│
    │ PK: id           │
    │ • email          │
    │ • otp            │
    │ • attempts       │
    │ • is_used        │
    │ • created_at     │
    │ • expires_at     │
    └──────────────────┘

────────────────────────────────────────────────────────────

4. BEFORE vs AFTER: KEY DIFFERENCES
════════════════════════════════════════════════════════════════

ISSUE 1: Missing Foreign Keys
─────────────────────────────
❌ BEFORE:
   employees.department_id → NO CONSTRAINT
   (Can set to any value, no validation)

✅ AFTER:
   employees.department_id → FK departments(id)
   (Database enforces referential integrity)


ISSUE 2: Duplicate Tables
─────────────────────────
❌ BEFORE:
   leaves (old design)
   leave_requests (new design)
   Both store leave data! ❌

✅ AFTER:
   leave_requests only
   (Single source of truth)


ISSUE 3: Duplicate Column Names
──────────────────────────────
❌ BEFORE:
   users.name
   employees.first_name, employees.last_name
   (Same data in 2 places)

✅ AFTER:
   users.email, users.username
   employees.first_name, employees.last_name
   (Separated by concern)


ISSUE 4: Hardcoded Strings
──────────────────────────
❌ BEFORE:
   leave_type: 'Casual', 'Sick', etc. (VARCHAR)
   status: 'pending', 'approved', etc. (VARCHAR)

✅ AFTER:
   leave_type_id → FK leave_types(id)
   status_id → FK attendance_status(id)
   (Normalized values)


ISSUE 5: Attendance Inconsistency
────────────────────────────────
❌ BEFORE:
   attendance: check_in, check_out, attendance_date
   attendance_logs: time_in, time_out, date
   (Same data, different column names!)

✅ AFTER:
   attendance_logs only (standardized)
   (Single source of truth)

────────────────────────────────────────────────────────────

5. FOREIGN KEY SPECIFICATIONS
════════════════════════════════════════════════════════════════

All foreign keys use:
- ON DELETE: 
  • CASCADE: Delete child records (transactional data)
  • SET NULL: Keep record, null FK (temporary data)
  
- Events: Employees, Positions, Departments
  → ON DELETE CASCADE (if department removed, related records removed)
  
- References: Audit, Notifications
  → ON DELETE SET NULL (keep audit even if user deleted)

────────────────────────────────────────────────────────────

6. INDEXING STRATEGY
════════════════════════════════════════════════════════════════

PRIMARY KEYS: All tables (auto-indexed)

FOREIGN KEYS: All FK columns automatically indexed
  • employees.department_id
  • employees.position_id
  • salaries.employee_id
  • etc.

DATE-BASED QUERIES: Add indexes on:
  • attendance_logs.date
  • leave_requests.start_date
  • salaries.effective_from

STATUS FILTERING: Add indexes on:
  • employees.status
  • leave_requests.status
  • attendance_logs.status

LOOKUPS: Add indexes on:
  • users.email (UNIQUE)
  • users.username
  • leave_types.name (UNIQUE)

────────────────────────────────────────────────────────────

This normalization achieves:
✅ 3NF Compliance
✅ Referential Integrity
✅ Single Source of Truth
✅ Data Consistency
✅ Query Performance
✅ Reduced Redundancy
