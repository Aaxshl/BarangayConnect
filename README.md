# BarangayConnect — Barangay Information & Citizen Service Management System

A modern, full-featured web platform that digitalizes barangay operations — from resident record management and document issuance to citizen issue reporting and community announcements.

---

## 🌟 Key Features

### 🖥️ Admin Panel
- **📊 Dashboard** — Real-time KPIs, monthly request/document statistics, quick-action shortcuts, recent citizen reports feed, and notification alerts.
- **👥 Resident Record Management** — Complete resident profiling with photo upload, QR code generation, age auto-calculation, civil status, occupation, purok/zone, and status tracking.
- **📄 Document Issuance — Progressive Workflow**
  - Issue Barangay Clearance, Certificate of Residency, Certificate of Indigency, Business Clearance, and Barangay Permits.
  - **5-step progressive status tracking:** `Requested` → `Under Review` (auto on admin open) → `Processing` → `Ready for Pickup` → `Released` — with optional rejection and reason.
  - **Customizable Document Templates** — Edit body text with dynamic tags (`{RESIDENT_NAME}`, `{PURPOSE}`, `{DOC_NUMBER}`, `{ISSUE_DATE}`, `{BARANGAY_NAME}`, etc.), header/footer text, signatory name, and optional custom logo per document type.
  - Clean PDF streaming via DomPDF for official certificate printing.
- **📝 Citizen Requests & Issue Reports — Progressive Workflow**
  - Citizen issue/complaint reporting with photo attachment and GPS geolocation.
  - **5-step status tracking:** `Pending` → `Under Review` → `Assigned` → `In Progress` → `Resolved`.
  - Personnel assignment before investigation begins.
  - Resolution notes and auto-collapsible resolved section.
  - Interactive **Leaflet.js map** with report pin clusters, heatmap view, and Google Maps directions integration.
  - Convert citizen requests into blotter/service log records.
- **📢 Announcements** — Publish community announcements with banner images, category tags, and scheduling.
- **🔍 QR Verification** — Generate and scan resident QR codes for fast authentication.
- **📈 Reports & Analytics** — Population demographics, document issuance metrics, issue resolution rates; export to PDF and Excel.
- **⚙️ Barangay Settings** — Configure barangay name, address, contact info, logo, captain name, and system name — all reflected live across the portal and printed documents.
- **🔒 Maintenance Mode** — Toggle to suspend citizen portal access during maintenance while preserving full admin access.

### 🌐 Citizen Resident Portal
- **Unified Login** — Single login page for both residents and staff/admin; automatically detects account type.
- **Resident Registration** — Full self-registration collecting all required details: name, birthdate, gender, civil status, address, purok/zone, contact number, occupation, and photo.
- **Document Requests** — Submit document requests online; track real-time 5-step progress with pickup notification and release confirmation.
- **Issue Reporting** — Report community issues with location and optional photo; track status progression in real time.
- **Announcements** — View latest community news and announcements.
- **Request Tracking** — Unified tracking page for all submitted reports and document requests with live search.
- **Dynamic Barangay Branding** — Portal header, footer, and all labels automatically update when admin changes barangay settings.

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.1+ / Laravel 10 |
| **Frontend** | Blade Templates, Bootstrap 5, Tabler Icons, Vanilla JS |
| **Database** | MySQL 8+ / SQLite 3 |
| **Maps** | Leaflet.js + OpenStreetMap |
| **PDF** | Barryvdh Laravel DomPDF |
| **QR Code** | Endroid QR Code |

---

## 📋 Requirements

- PHP >= 8.1 with extensions: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` or `imagick`
- Composer >= 2.x
- MySQL >= 8.0 **or** SQLite 3
- Node.js >= 16.x & NPM *(only needed if compiling frontend assets)*

---

## 🚀 Installation & Setup

```bash
# 1. Clone the repository
git clone https://github.com/Aaxshl/BarangayConnect.git
cd BarangayConnect

# 2. Install PHP dependencies
composer install

# 3. Copy environment file and configure
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure your database in .env
# For SQLite (default, zero-config):
#   DB_CONNECTION=sqlite
#   DB_DATABASE=/absolute/path/to/database/database.sqlite

# For MySQL:
#   DB_CONNECTION=mysql
#   DB_HOST=127.0.0.1
#   DB_PORT=3306
#   DB_DATABASE=barangayconnect
#   DB_USERNAME=root
#   DB_PASSWORD=

# 6. Run migrations and seed initial data
php artisan migrate --seed

# 7. Create storage symlink (required for photo & file uploads)
php artisan storage:link

# 8. Start the development server
php artisan serve
```

Visit **http://127.0.0.1:8000** for the resident portal  
Visit **http://127.0.0.1:8000/login** to log in as admin or resident

---

## 🔑 Default Admin Accounts

| Role | Email | Password | Access Level |
|---|---|---|---|
| **Administrator** | `admin@brgy.gov.ph` | `password` | Full system access |
| **Secretary** | `secretary@brgy.gov.ph` | `password` | Records & documents |
| **Staff** | `staff@brgy.gov.ph` | `password` | Service logs & requests |

> Residents register through the portal at `/register`.

---

## 📄 Document Workflow

```
[1] Requested       — Resident submits online or staff creates manually
      ↓  (auto on admin first-view)
[2] Under Review    — Admin opens the request; viewed_at is recorded
      ↓  (Approve & Prepare)
[3] Processing      — Document is being prepared
      ↓  (Mark Ready for Pickup)
[4] Ready for Pickup — Resident is notified to claim at the Barangay Hall
      ↓  (Release to Resident)
[5] Released        — Document officially handed over; released_at recorded

❌ Cancelled  — Rejected at any active stage (reason required)
```

---

## 📝 Citizen Request Workflow

```
[1] Pending       — Submitted by resident or reported by staff
      ↓
[2] Under Review  — Admin acknowledges and reviews the report
      ↓  (Assign Personnel first)
[3] Assigned      — Specific staff/personnel assigned to handle
      ↓
[4] In Progress   — Active investigation or resolution underway
      ↓
[5] Resolved      — Issue addressed; resolution note added

❌ Rejected  — Closed without resolution (reason required)
```

---

## 📁 Project Structure

```
BarangayConnect/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Resource & action controllers
│   │   └── Middleware/         # Auth, ResidentAuth, MaintenanceMode
│   └── Models/                 # Eloquent models & relationships
├── config/                     # App & service configurations
├── database/
│   ├── migrations/             # Database schema migrations
│   └── seeders/                # Initial admin & demo data seeders
├── public/
│   ├── css/                    # Admin & portal stylesheets
│   └── js/                     # Client-side scripts & map integration
├── resources/
│   └── views/
│       ├── admin/              # All admin panel views & modules
│       ├── auth/               # Unified login view
│       ├── layouts/            # Admin & resident portal layouts
│       └── resident/           # Resident portal views
└── routes/
    └── web.php                 # All web & admin routes
```
