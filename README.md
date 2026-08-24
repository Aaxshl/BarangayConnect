# SmartBarangay Information and Citizen Service Management System

A modern, full-featured web-based platform designed to digitalize barangay operations, resident record management, document issuance and template customization, service tracking, geographic issue mapping, and citizen engagement.

---

## 🌟 Key Features

- **📊 Admin Dashboard:** Real-time analytics, monthly request statistics, quick shortcuts, recent citizen reports, and interactive notification alerts.
- **👥 Resident Record Management:** Complete resident profiling, status tracking, age/civil status records, and resident photo upload with live preview.
- **🏠 Household Profiling:** Household records, Head of Household assignment, family member list, and dynamic member assignment/removal.
- **📄 Document Issuance & Custom Templates:**
  - Issue Barangay Clearance, Certificate of Residency, Certificate of Indigency, Business Clearance, and Barangay Permits.
  - **Customizable Document Templates:** Edit body texts with dynamic tags (`{RESIDENT_NAME}`, `{PURPOSE}`, `{DOC_NUMBER}`, `{ISSUE_DATE}`, etc.), header/footer texts, signatory titles, and toggle/upload custom Barangay seals & logos per document type.
  - Clean PDF streaming and official certificate printing.
- **📝 Citizen's Requests/Reports & Blotter Management:**
  - Citizen issue reporting with photo attachment and GPS geolocation.
  - Interactive **Leaflet.js map** displaying pinpoint report locations with Google Maps directions integration.
  - Status progression tracking (`pending` ➔ `under_review` ➔ `assigned` ➔ `in_progress` ➔ `resolved`).
  - Convert citizen requests directly into blotter/service log records.
- **🗺️ Geographic Issue Mapping:** Heatmap and cluster map visualizer of all barangay issues.
- **📱 Citizen Resident Portal:**
  - Responsive web & mobile interface with bottom navigation.
  - Online document request submission and status tracking.
  - Community announcements feed with banner image previews.
- **🔍 QR Verification:** Generate and scan QR codes for quick resident authentication.
- **📈 Reports & Analytics:** Population demographics, document issuance metrics, resolution rates, and exportable reports in PDF and Excel formats.
- **🔒 System Maintenance Mode:** Toggle switch in Admin Settings to gate citizen portal access during scheduled maintenance while preserving admin access.

---

## 🛠️ Tech Stack

- **Backend:** PHP 8.1+ / Laravel 10
- **Frontend:** Blade Templates, Bootstrap 5, Tabler Icons, Vanilla JavaScript
- **Database:** MySQL / SQLite
- **Maps:** Leaflet.js + OpenStreetMap
- **PDF Generation:** Barryvdh Laravel DomPDF
- **QR Code:** Endroid QR Code

---

## 📋 Requirements

- PHP >= 8.1 (with `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd` or `imagick`)
- Composer >= 2.x
- MySQL >= 8.0 or SQLite 3
- Node.js >= 16.x & NPM

---

## 🚀 Installation & Setup

```bash
# 1. Clone the repository
git clone https://github.com/your-username/smartbarangay.git
cd smartbarangay

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure your database in .env
# For MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=smartbarangay
# DB_USERNAME=root
# DB_PASSWORD=

# For SQLite:
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database/database.sqlite

# 6. Run migrations and database seeders
php artisan migrate --seed

# 7. Create storage symlink (important for photo and document uploads)
php artisan storage:link

# 8. Start local development server
php artisan serve
```

---

## 🔑 Default Accounts

| Role | Email | Password | Access |
|------|-------|----------|--------|
| **Administrator** | `admin@brgy.gov.ph` | `password` | Full System Access |
| **Secretary** | `secretary@brgy.gov.ph` | `password` | Records & Documents |
| **Staff** | `staff@brgy.gov.ph` | `password` | Service Logs & Requests |

---

## 📁 Project Structure

```
smartbarangay/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Resource & action controllers
│   │   └── Middleware/        # Auth & System Maintenance middleware
│   └── Models/                # Eloquent models & relationships
├── config/                    # Application & service configurations
├── database/
│   ├── migrations/            # Database schema migrations
│   └── seeders/               # Initial demo & admin data seeders
├── public/
│   ├── css/                   # Admin & portal stylesheets
│   └── js/                    # Client-side scripts
├── resources/
│   └── views/
│       ├── admin/             # Admin portal views & modules
│       ├── layouts/           # Admin & resident portal layouts
│       └── resident/          # Resident portal views
└── routes/
    ├── web.php                # Web routes & admin routes
    └── console.php            # Artisan console commands
```

