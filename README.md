# INFS730-Project

Employee Management System for **ABC Gas Station** — a PHP + MySQL web application built as coursework for INFS 730.

The system supports role-based authentication (Employee / Admin), admin-initiated employee onboarding via secure invitation links, and token-based account activation.

---

## 📁 Folder Structure

```
INFS730-Project/
├── index.php              ← Entry point (redirects to login)
├── config/                ← App & database settings
├── includes/              ← Shared PHP helpers & layout
├── public/                ← CSS & JavaScript assets
├── pages/                 ← All user-facing pages
│   ├── admin/             ← Admin-only pages
│   └── employee/          ← Employee-only pages
├── actions/               ← POST handlers (logic only, no HTML)
├── activation/            ← Token-based account activation flow
├── database/              ← SQL schema & migrations
└── docs/                  ← Project documentation
```

### Where to put new files

| I need to…                              | Put it in…                         |
|------------------------------------------|------------------------------------|
| Add a new **admin page** (e.g. dashboard) | `pages/admin/dashboard.php`       |
| Add a new **employee page**              | `pages/employee/my_page.php`      |
| Add a new **public page** (no login)     | `pages/my_page.php`               |
| Handle a **form submission** (POST)      | `actions/my_action.php`           |
| Add a new **database table or migration**| `database/my_migration.sql`       |
| Add **shared PHP functions**             | `includes/functions.php`          |
| Add a new **reusable include** (partial) | `includes/my_partial.php`         |
| Add **CSS styles**                       | `public/css/style.css`            |
| Add **JavaScript**                       | `public/js/my_script.js`          |
| Change **DB credentials or app settings**| `config/database.php` or `config/app.php` |
| Add **project documentation**            | `docs/`                           |

### Key files to know

| File                       | Purpose                                          |
|----------------------------|--------------------------------------------------|
| `config/app.php`           | `BASE_URL`, timezone, `MAIL_ENABLED` flag         |
| `config/database.php`      | DB host, name, user, password + PDO singleton     |
| `includes/functions.php`   | All shared helpers (escaping, redirects, tokens)  |
| `includes/auth_guard.php`  | `requireLogin('admin')` or `requireLogin('employee')` — include at top of any protected page |
| `includes/header.php`      | Shared HTML `<head>` — set `$pageTitle` before including |
| `includes/footer.php`      | Closing HTML — optionally set `$footerScripts` array |
| `public/css/style.css`     | Single unified stylesheet for the entire app      |
| `public/js/validation.js`  | Client-side form validation (login + activation)  |

---

## 🚀 How to Run

### Prerequisites

- **MAMP** (macOS) or **XAMPP** (Windows) with Apache & MySQL running
- Project folder placed in the `htdocs` directory

> **Note:** The config auto-detects MAMP vs XAMPP — no manual changes needed! It handles the different ports, passwords, and connection methods automatically.

### Step 1 — Set up the database

1. Open **phpMyAdmin**
   - MAMP: `http://localhost:8888/phpMyAdmin`
   - XAMPP: `http://localhost/phpmyadmin`
2. Click the **SQL** tab at the top (make sure no database is selected)
3. Copy and paste the **entire** contents of `database/schema.sql`
4. Click **Go** — this creates the database, tables, and seeds a default admin account

### Step 2 — Open in browser

- **MAMP (macOS):** `http://localhost:8888/INFS730-Project/`
- **XAMPP (Windows):** `http://localhost/INFS730-Project/`

You will be redirected to the login page.

### Default Admin Login

| Field    | Value              |
|----------|--------------------|
| Email    | `admin@abcgas.com` |
| Password | `Admin@123`        |
| Role     | Admin              |

From the Admin Console, use **Add Employee** to create employee accounts via invitation links.

---

## 🤝 Contributing

We love contributions! However, to keep the **main** branch stable, we have a few safety rules in place:

- **No direct pushes:** Direct pushes to `main` are blocked.
- **Pull Requests:** All changes must be submitted via a Pull Request.
- **Review Required:** At least **1 approval** from a maintainer is required before any code can be merged.

👉 **Important:** Please read our [Full Step-by-Step Contribution Guide](CONTRIBUTING.md) before you start coding to ensure your workflow matches our repository settings.

---
