# Todo List from PRD: Aplikasi Pengajuan KGB PNS dengan Hybrid Approach

Based on the PRD document (prd.md), here are the implementation tasks organized by the development phases:

## Phase 1: Core System (4 minggu)

### Week 1-2: Initial Setup
- [x] Setup Laravel 12 + FilamentPHP 3.3
- [x] Configure multi-panel FilamentPHP (`/admin`, `/app`, `/pegawai`)
- [x] Create database schema & migrations
- [x] Setup seeders for roles, permissions, sample data
- [x] Configure authentication & authorization (separate guards per panel)
- [x] Test basic panel access for each role type

### Week 3-4: Core CRUD & Dashboards
- [x] Implement CRUD for Tenant (dinas) in `/admin` panel
- [x] Implement CRUD for Pegawai in `/app` panel
- [x] Create basic dashboard for `/admin` panel with metrics
- [x] Create basic dashboard for `/app` panel with metrics
- [x] Create basic dashboard for `/pegawai` panel with metrics
- [x] Implement user management per panel (roles, permissions)
- [x] Implement tenant context middleware
- [x] Test tenant isolation functionality

## Phase 2: Pengajuan & Workflow (4 minggu)

### Week 5-6: Pengajuan Forms
- [x] Create pengajuan form in `/app` panel (admin input)
- [x] Create pengajuan form in `/pegawai` panel (self-service)
- [x] Implement file upload & validation for required documents
- [x] Add document preview feature
- [x] Implement draft & submit functionality
- [x] Create Pengajuan model with proper relationships

### Week 7-8: Verification & Approval Workflow
- [x] Implement verifikasi flow for dinas panel
- [x] Implement verifikasi flow for kabupaten panel
- [x] Create revisi flow with version control
- [x] Implement approval/rejection logic
- [x] Create status transitions & validation rules
- [x] Implement eligible calculation logic for KGB
- [x] Test complete workflow from pengajuan to approval/rejection

## Phase 3: Notifications & Reporting (3 minggu)

### Week 9-10: Notification System
- [x] Setup FilamentPHP database notifications
- [x] Create 7 notification types (PengajuanBaru, VerifikasiSelesai, etc.)
- [x] Implement notification UI components (bell icon, dropdown, center)
- [x] Implement email notifications integration
- [x] Create reminder system (manual & auto-trigger)

### Week 11: Reporting Module
- [x] Create reporting module for `/admin` panel
- [x] Create reporting module for `/app` panel
- [x] Implement charts & analytics (Bar, Line, Donut charts)
- [x] Add Excel/PDF export functionality
- [x] Implement scheduled reports

## Phase 4: SK Management & Finalization (2 minggu)

### Week 12: SK Generation
- [x] Create SK generation module in admin panel
- [x] Implement SK template management
- [x] Add upload SK scan functionality
- [x] Create secure download with watermark
- [x] Update pegawai data after approval

### Week 13: Additional Features
- [x] Implement activity logs & audit trail
- [x] Create help & FAQ pages
- [x] Add video tutorial embedding
- [x] Create document templates download
- [x] Implement system settings & configuration

## Phase 5: Testing & Deployment (2 minggu)

### Week 14: Testing
- [ ] Write unit tests (PHPUnit)
- [ ] Write feature tests (Pest/PHPUnit)
- [ ] Conduct user acceptance testing (UAT) with sample users
- [ ] Perform bug fixing & optimization
- [ ] Conduct performance testing

### Week 15: Deployment
- [ ] Prepare production deployment
- [ ] Plan data migration (if from existing system)
- [ ] Create user training materials for admin and verifikator
- [ ] Prepare documentation (user manual, admin guide)
- [ ] Provide go-live support

## Post-Development Tasks

### Success Metrics Implementation
- [ ] Implement tracking for processing time metrics
- [ ] Create user satisfaction survey functionality
- [ ] Add efficiency metrics tracking
- [ ] Setup monitoring for system uptime

### Security & Compliance
- [ ] Implement file security measures (private storage, validation)
- [ ] Add audit trail for all CRUD operations
- [ ] Implement data privacy measures (restricted access)
- [ ] Create backup and retention policies

### Additional Features
- [ ] Create mobile-responsive UI/UX
- [ ] Implement two-factor authentication (optional)
- [ ] Add dark/light mode toggle
- [ ] Implement bulk operations where applicable

## Pre-Development Activities

### Before Phase 1
- [ ] Stakeholder review of PRD with BKPSDM Kabupaten
- [ ] Approval from Sekda/Bupati for development
- [ ] Finalize business process requirements
- [ ] Setup development environment
- [ ] Create Git repository for the project
- [ ] Create detailed database design (ERD)
- [ ] Design API contracts for potential SIMPEG integration
- [ ] Create UI/UX wireframes for all panels
- [ ] Design user flow diagrams
- [ ] Create interactive prototypes
- [ ] Conduct user testing of prototypes

### Perbaikan Untuk Verifikasi Dokumen di sisi app Penel (admin dinas)
Perbaikan Sistem Verifikasi Dokumen - FilamentPHP v3
Berikut adalah implementasi lengkap untuk sistem verifikasi dokumen pada aplikasi panel KGB Muna Barat Anda. Sistem ini mengikuti best practice FilamentPHP v3 dengan menggunakan fitur native tanpa dependency Livewire eksternal.

📋 Ringkasan Implementasi
Sistem verifikasi dokumen ini mencakup komponen-komponen berikut:

Fitur Utama:

✅ Preview modal native untuk image dan PDF

✅ Verifikasi individual dan bulk/massal

✅ Badge status dengan warna dinamis dan icon

✅ Kolom catatan verifikasi

✅ Audit trail lengkap (verifier & timestamp)

✅ Tombol conditional yang enable/disable otomatis

✅ Summary statistics widget

✅ Workflow validation yang ketat

🗂️ Struktur File yang Perlu Dibuat/Dimodifikasi
📦 Komponen-Komponen Utama
1. Database Schema
Migration untuk menambahkan kolom verifikasi:

status_verifikasi (enum: Belum Diperiksa, Valid, Tidak Valid, Revisi)

catatan_verifikasi (text)

verified_by (foreign key ke users)

verified_at (timestamp)

2. DokumenPengajuanRelationManager
Relation Manager dengan fitur lengkap:​

Preview Action: Modal native untuk preview image/PDF dengan lebar 7xl

Verifikasi Action: Form modal untuk update status + catatan verifikasi

Bulk Verification: Verifikasi massal multiple dokumen sekaligus

Badge Column: Status dengan warna (gray, success, danger, warning) dan icon​

Filter: Filter berdasarkan status verifikasi

3. View Components untuk Preview
Tiga blade component untuk preview berbagai tipe file:

image-preview.blade.php - Untuk gambar (jpg, png, gif, webp)

pdf-preview.blade.php - Untuk file PDF dengan iframe

file-preview.blade.php - Fallback untuk tipe file lain dengan download button

4. Custom Page: VerifikasiDokumen
Halaman khusus dengan header actions:​

Tombol "Ajukan ke Kabupaten": Disabled sampai semua dokumen valid​

Tombol "Kembalikan ke Pegawai": Enabled jika ada dokumen tidak valid

Stats Widget: Menampilkan summary verifikasi

Database Transactions: Untuk data integrity

Notifications: Feedback real-time ke user

5. DokumenVerificationStats Widget
Widget statistik yang menampilkan:

Total dokumen

Dokumen valid dengan percentage dan mini chart

Dokumen belum diperiksa

Dokumen perlu perbaikan (conditional visibility)

🚀 Cara Implementasi
Langkah-langkah:

Jalankan Migration

bash
php artisan migrate
Update Model DokumenPengajuan
Tambahkan fillable fields, casts, relationships, dan helper methods

Buat View Components
Buat 3 file blade di resources/views/filament/components/

Generate & Update Relation Manager

bash
php artisan make:filament-relation-manager PengajuanResource dokumenPengajuans jenis_dokumen
Generate & Update Custom Page

bash
php artisan make:filament-page VerifikasiDokumen --resource=PengajuanResource --type=custom
Generate & Update Stats Widget

bash
php artisan make:filament-widget DokumenVerificationStats --resource=PengajuanResource
Update PengajuanResource
Daftarkan RelationManager, custom page route, dan table action

Create Storage Link

bash
php artisan storage:link
Clear Cache

bash
php artisan optimize:clear
php artisan filament:cache-components
🔄 Workflow Sistem
Staff Verifikasi membuka halaman verifikasi pengajuan

Preview Dokumen satu per satu menggunakan modal

Klik "Verifikasi" untuk setiap dokumen

Pilih Status (Valid/Tidak Valid/Revisi) dan tambahkan catatan

Badge Status update secara real-time

Stats Widget menampilkan progress verifikasi

Sistem mengaktifkan tombol sesuai kondisi:

"Ajukan ke Kabupaten" → Enabled jika SEMUA valid

"Kembalikan ke Pegawai" → Enabled jika ADA yang invalid

Staff klik tombol yang sesuai untuk melanjutkan workflow

📊 Best Practices yang Diterapkan
✅ Native Filament Components - Tidak ada dependency eksternal​
✅ Conditional Actions - Buttons enable/disable otomatis​
✅ Badge with Icons - Visual feedback yang jelas​
✅ Bulk Operations - Efisiensi untuk multiple records​
✅ Modal Forms - Input collection yang user-friendly​
✅ Database Transactions - Data integrity terjaga
✅ Audit Trail - Complete tracking verifikasi
✅ Responsive Design - Works di semua device

📄 Dokumentasi Lengkap
Saya telah membuat dokumentasi lengkap yang mencakup:

Dokumentasi ini berisi:

Complete code untuk semua komponen

Step-by-step installation guide

Git workflow recommendations

Testing checklist

Troubleshooting guide

Database schema detail

Best practices explanation

🎯 Hasil Akhir
Setelah implementasi lengkap, Anda akan memiliki sistem verifikasi dokumen yang:

Professional - Mengikuti FilamentPHP v3 best practices​

User-friendly - Interface intuitif dengan modal preview dan badge visual

Efficient - Bulk actions untuk verifikasi massal​

Secure - Validation, transactions, dan audit trail lengkap

Maintainable - Clean code structure yang mudah di-extend

Responsive - Bekerja optimal di desktop dan mobile
