# PRD Final: Aplikasi Pengajuan KGB PNS dengan Hybrid Approach

## Executive Summary

Aplikasi manajemen pengajuan Kenaikan Gaji Berkala (KGB) PNS berbasis **hybrid approach** dengan FilamentPHP 3.3, menggunakan **path-based multi-tenancy** dan **database notifications**. Sistem memiliki 3 portal terpisah: `/admin` (Kabupaten), `/app` (Dinas), dan `/pegawai` (Self-Service) dengan isolasi data per tenant.[1]

## System Architecture

### Multi-Panel Structure
**Central Panel**: `/admin` - Dashboard Kabupaten/BKPSDM  
**Tenant Panel**: `/app` - Dashboard Dinas/OPD  
**Pegawai Panel**: `/pegawai` - Self-Service Portal Pegawai

### Database Strategy
**Single Database dengan Tenant Isolation**:
- Global Tables: `tenants`, `central_users`, `system_settings`, `global_notifications`
- Tenant-Scoped Tables: Semua tabel dengan kolom `tenant_id` untuk isolasi data
- Global Scope Laravel otomatis filter berdasarkan tenant_id dari session
- Spatie Laravel Permission dengan tenant-aware roles

### Notification System
**FilamentPHP Database Notifications**:
- Menggunakan `filament/notifications` package built-in
- Tabel `notifications` Laravel standard
- Real-time notification bell icon di navbar setiap panel
- Notification types: `database` (primary), `broadcast` (optional untuk real-time)
- Actions dalam notification (Lihat Detail, Approve, Revisi)

## User Roles & Access Matrix

| Role | Portal | Dapat Mengajukan | Dapat Verifikasi | Dapat Approve | Akses Data |
|------|--------|------------------|------------------|---------------|------------|
| Super Admin Kabupaten | `/admin` | ✗ | ✓ | ✓ (Final) | Semua Tenant |
| Verifikator Kabupaten | `/admin` | ✗ | ✓ | ✓ | Semua Tenant |
| Admin Dinas | `/app` | ✓ (atas nama pegawai) | ✓ | ✓ (Awal) | Tenant Sendiri |
| Verifikator Dinas | `/app` | ✗ | ✓ | ✗ | Tenant Sendiri |
| Operator Dinas | `/app` | ✓ (atas nama pegawai) | ✗ | ✗ | Tenant Sendiri |
| Pegawai | `/pegawai` | ✓ (Mandiri) | ✗ | ✗ | Data Sendiri |

## Navigation Menu Structure

### `/admin` - Central Panel (Kabupaten/BKPSDM)

```
📊 Dashboard
   └─ Overview statistik semua dinas
   
👥 Manajemen Tenant
   ├─ Daftar Dinas/OPD
   ├─ Tambah Dinas Baru
   ├─ Konfigurasi Tenant
   └─ Status Aktif/Nonaktif
   
📋 Pengajuan KGB
   ├─ Semua Pengajuan (All Tenants)
   ├─ Pending Verifikasi Kabupaten
   ├─ Perlu Ditinjau
   ├─ Sudah Disetujui
   ├─ Ditolak
   └─ Arsip Pengajuan
   
✅ Verifikasi & Approval
   ├─ Antrian Verifikasi (Queue)
   ├─ Review Dokumen
   ├─ Approval Final
   └─ Generate SK KGB
   
📑 Manajemen SK
   ├─ Daftar SK KGB
   ├─ Upload SK Scan
   ├─ Template SK
   └─ Cetak SK Massal
   
📊 Laporan & Analytics
   ├─ Laporan Agregat
   ├─ Laporan per Dinas
   ├─ Statistik Pengajuan
   ├─ Performance Report
   └─ Export Data (Excel/PDF)
   
👤 Manajemen User
   ├─ User Kabupaten
   ├─ Roles & Permissions
   └─ Activity Logs
   
⚙️ Pengaturan Sistem
   ├─ Konfigurasi Global
   ├─ Template Dokumen
   ├─ Notification Settings
   └─ System Logs
   
🔔 Notifikasi
   └─ (Icon bell di navbar - FilamentPHP notifications)
```

### `/app` - Tenant Panel (Dinas/OPD)

```
📊 Dashboard
   └─ Overview statistik dinas sendiri
   
👥 Data Pegawai
   ├─ Daftar Pegawai
   ├─ Tambah Pegawai Baru
   ├─ Import Pegawai (Excel)
   ├─ Pegawai Eligible KGB
   └─ Manajemen Akun Pegawai (Self-Service)
   
📝 Pengajuan KGB
   ├─ Daftar Pengajuan Dinas
   ├─ Buat Pengajuan Baru (atas nama pegawai)
   ├─ Draft Pengajuan
   ├─ Pengajuan Aktif
   ├─ Riwayat Pengajuan
   └─ Pengajuan Ditolak
   
✅ Verifikasi Dokumen
   ├─ Antrian Verifikasi Dinas
   ├─ Review & Checklist Dokumen
   ├─ Minta Revisi
   └─ Teruskan ke Kabupaten
   
📋 Monitoring Status
   ├─ Status di Kabupaten
   ├─ Pengajuan Disetujui
   ├─ Tracking Timeline
   └─ Download SK
   
📊 Laporan Dinas
   ├─ Laporan Bulanan
   ├─ Statistik Approval Rate
   ├─ Pegawai per Golongan
   └─ Export Laporan Dinas
   
👤 Manajemen User Dinas
   ├─ Admin & Verifikator Dinas
   ├─ Roles Dinas
   └─ Activity Log Dinas
   
⚙️ Pengaturan Dinas
   ├─ Profil Dinas
   ├─ Self-Service Settings (Enable/Disable)
   ├─ Template Dokumen Dinas
   └─ Notification Preferences
   
🔔 Notifikasi
   └─ (Icon bell di navbar - FilamentPHP notifications)
```

### `/pegawai` - Self-Service Panel (Pegawai)

```
🏠 Beranda
   └─ Dashboard pegawai dengan status KGB
   
👤 Profil Saya
   ├─ Data Kepegawaian (read-only)
   ├─ Update Foto Profil
   ├─ Update Email & No. HP
   └─ Ubah Password
   
📝 Pengajuan KGB Saya
   ├─ Status Kelayakan KGB (Eligible/Belum)
   ├─ Ajukan KGB Baru
   ├─ Pengajuan Aktif
   ├─ Revisi Dokumen
   └─ Riwayat Pengajuan
   
📄 Dokumen Saya
   ├─ Upload Dokumen Persyaratan
   ├─ Preview Dokumen
   ├─ Status Verifikasi per Dokumen
   └─ Download Dokumen Template
   
📊 Tracking Status
   ├─ Timeline Pengajuan
   ├─ Status Verifikasi Real-time
   ├─ Catatan dari Verifikator
   └─ Estimasi Waktu Proses
   
📑 SK KGB
   ├─ Daftar SK KGB Saya
   ├─ Download SK (approved)
   └─ Cetak SK
   
❓ Bantuan & Panduan
   ├─ Panduan Pengajuan KGB
   ├─ FAQ
   ├─ Video Tutorial
   ├─ Template Dokumen
   └─ Kontak Helpdesk Dinas
   
🔔 Notifikasi
   └─ (Icon bell di navbar - FilamentPHP notifications)
```

## Detailed Feature Specifications

### Dashboard Components

#### `/admin/dashboard` - Central Dashboard Kabupaten

**Top Metrics Cards (4 Cards)**:
1. **Total Dinas Aktif**
   - Jumlah dinas/tenant terdaftar dan aktif
   - Icon: building-office
   - Link: ke halaman manajemen tenant

2. **Pengajuan Bulan Ini**
   - Total pengajuan dari semua dinas bulan berjalan
   - Trend: increase/decrease vs bulan lalu
   - Icon: document-text
   - Link: ke daftar pengajuan

3. **Pending Verifikasi**
   - Jumlah pengajuan yang menunggu verifikasi kabupaten
   - Priority indicator (merah jika > 20)
   - Icon: clock
   - Link: ke antrian verifikasi

4. **Approval Rate**
   - Persentase pengajuan disetujui vs total
   - Chart mini sparkline
   - Icon: check-circle
   - Link: ke laporan analytics

**Charts Section**:
1. **Pengajuan per Dinas** (Bar Chart)
   - X-axis: Nama dinas
   - Y-axis: Jumlah pengajuan bulan ini
   - Color: gradient blue
   - Sortable, filterable

2. **Trend Pengajuan 6 Bulan** (Line Chart)
   - Multiple lines: Diajukan, Disetujui, Ditolak
   - Tooltip dengan detail per bulan
   - Legend interactive

3. **Status Distribution** (Donut Chart)
   - Segments: Pending, Verifikasi, Approved, Rejected
   - Center: Total pengajuan
   - Click to filter table

**Recent Activities Table**:
- 10 aktivitas terbaru lintas dinas
- Kolom: Timestamp, Dinas, Pegawai, Action, Status, User
- Real-time update dengan Livewire polling (optional)
- Search, filter, sort

**Performance Ranking Table**:
- Ranking dinas berdasarkan approval rate
- Kolom: Rank, Dinas, Total Pengajuan, Approved, Rejected, Rate (%)
- Badge untuk top 3 dinas
- Export to Excel

#### `/app/dashboard` - Tenant Dashboard Dinas

**Top Metrics Cards (5 Cards)**:
1. **Total Pegawai Dinas**
   - Jumlah pegawai aktif di dinas
   - Breakdown: PNS, PPPK
   - Icon: users

2. **Eligible untuk KGB**
   - Pegawai yang sudah memenuhi syarat 2 tahun
   - Auto-calculated dari TMT KGB terakhir
   - Icon: user-plus
   - Action: Generate reminder

3. **Pengajuan Aktif**
   - Pengajuan yang sedang dalam proses
   - Status: Draft + Diajukan + Verifikasi
   - Icon: document-duplicate

4. **Pending Verifikasi Dinas**
   - Antrian yang perlu diverifikasi oleh dinas
   - Urgency indicator
   - Icon: exclamation-circle

5. **Disetujui Bulan Ini**
   - Jumlah pengajuan yang sudah approved
   - Celebration icon jika > target
   - Icon: check-badge

**Charts Section**:
1. **Pengajuan per Bulan** (Bar Chart)
   - Data current year
   - Comparison dengan tahun sebelumnya (overlay)

2. **Status Pengajuan** (Donut Chart)
   - Real-time status distribution
   - Click to filter table

**Quick Actions Panel**:
- Button "Buat Pengajuan Baru" (large, prominent)
- Button "Generate Reminder Eligible Pegawai" (kirim notif ke pegawai)
- Button "Export Laporan Dinas"
- Button "Import Data Pegawai"

**Recent Submissions Table**:
- 15 pengajuan terbaru
- Kolom: No, NIP, Nama, Tgl Ajukan, Status, Actions
- Status badge dengan color coding
- Quick actions: View, Edit, Verify, Delete

**Pegawai Eligible KGB Widget**:
- List pegawai yang sudah 2 tahun dari KGB terakhir
- Info: Nama, NIP, TMT KGB Terakhir, Eligible Since
- Action per row: Ajukan KGB, Kirim Reminder
- Pagination

#### `/pegawai/dashboard` - Self-Service Dashboard Pegawai

**Profile Header Card**:
- Foto profil pegawai (avatar)
- Nama Lengkap, NIP
- Jabatan, Golongan/Ruang (large, prominent)
- Unit Kerja, Dinas
- TMT KGB Terakhir
- Button "Edit Profil"

**Status Kelayakan KGB Card** (Hero Section):
- **Jika Eligible**:
  - Badge besar: "✓ Anda Eligible untuk KGB"
  - Text: "Anda sudah memenuhi syarat 2 tahun masa kerja"
  - Button CTA besar: "Ajukan KGB Sekarang" (hijau, prominent)
  
- **Jika Belum Eligible**:
  - Badge: "⏳ Belum Eligible"
  - Text: "Anda bisa mengajukan KGB pada: [tanggal]"
  - Countdown: "X bulan X hari lagi"

**Pengajuan Aktif Card** (jika ada):
- Status badge besar dengan warna sesuai status
- Timeline progress indicator (visual):
  ```
  Diajukan → Verifikasi Dinas → Verifikasi Kabupaten → Disetujui
     ✓              ✓                  (current)           
  ```
- Tanggal submit
- Estimasi waktu selesai
- Catatan terbaru dari verifikator (jika ada)
- Button: "Lihat Detail" atau "Upload Revisi" (jika status revisi)

**Quick Stats Cards**:
1. Total Pengajuan KGB Saya (lifetime)
2. Pengajuan Disetujui
3. Pengajuan Ditolak
4. Pengajuan Sedang Proses

**Riwayat KGB Timeline**:
- Visual timeline vertical dengan milestone KGB
- Setiap node: Tanggal, Golongan, No SK
- Icon check hijau untuk setiap milestone
- Download SK per milestone

**Notifications Feed**:
- 5 notifikasi terbaru inline di dashboard
- Link "Lihat Semua Notifikasi"

**Help & Resources Panel**:
- Quick links ke panduan
- Video tutorial embed (thumbnail)
- FAQ accordion
- Kontak helpdesk dengan tombol WhatsApp/Email

## Pengajuan KGB Workflow Detail

### Hybrid Workflow Options

#### Option A: Pengajuan oleh Admin Dinas (Traditional)

**Step 1: Admin Dinas Buat Pengajuan**
- Akses: `/app/pengajuan/create`
- Form Fields:
  - Select Pegawai (searchable dropdown dengan NIP + Nama)
  - Auto-fill: Golongan, TMT KGB Terakhir, Unit Kerja
  - Auto-calculate: TMT KGB Baru, Eligible Status
  - Upload 5 Dokumen:
    1. SK Pangkat Terakhir
    2. SK KGB Terakhir
    3. Rekap Absensi 6 Bulan
    4. SK Pengantar UNOR (optional)
    5. SKP 2 Tahun Terakhir
  - File validation: PDF only, max 5MB each
  - Preview dokumen sebelum upload
  - Catatan tambahan (optional)
- Actions:
  - "Simpan Draft" (status: Draft)
  - "Submit Pengajuan" (status: Diajukan)
  - "Batal"

**Notifikasi**: Ke Verifikator Dinas (database notification)

#### Option B: Pengajuan oleh Pegawai (Self-Service)

**Step 1: Pegawai Ajukan Mandiri**
- Akses: `/pegawai/pengajuan-saya/create`
- Guard: Cek eligible, cek ada pengajuan aktif atau tidak
- Form sama dengan Option A, tapi data pegawai auto-fill dari user login
- Simplified UI/UX, more guidance tooltips
- Help text untuk setiap jenis dokumen
- Actions sama

**Notifikasi**: Ke Verifikator Dinas dan Admin Dinas (database notification)

### Verification Flow

#### Stage 1: Verifikasi Dinas

**Verifikator Dinas Access**: `/app/verifikasi/{id}`

**Verification Checklist Table** (per dokumen):
| Dokumen | Preview | Status | Catatan | Action |
|---------|---------|--------|---------|--------|
| SK Pangkat Terakhir | 👁️ Preview PDF | ☑️ Valid / ☐ | Text field | - |
| SK KGB Terakhir | 👁️ Preview PDF | ☑️ Valid / ☐ | Text field | - |
| Rekap Absensi 6 Bulan | 👁️ Preview PDF | ☑️ Valid / ☐ | Text field | - |
| SK UNOR | 👁️ Preview PDF | ☑️ Valid / ☐ | Text field | - |
| SKP 2 Tahun | 👁️ Preview PDF | ☑️ Valid / ☐ | Text field | - |

**Catatan Verifikasi Global** (textarea):
- Untuk feedback keseluruhan pengajuan

**Decision Actions**:
1. **Teruskan ke Kabupaten** (semua dokumen valid)
   - Confirmation modal
   - Status: Diteruskan ke Kabupaten
   - Notifikasi ke: Verifikator Kabupaten, Admin Dinas, Pegawai (jika self-service)

2. **Minta Revisi** (ada dokumen tidak valid)
   - Wajib isi catatan untuk dokumen yang tidak valid
   - Status: Revisi Dokumen
   - Notifikasi ke: Admin Dinas atau Pegawai (yang mengajukan)
   - Email notification dengan detail catatan

3. **Tolak Pengajuan** (tidak memenuhi syarat)
   - Wajib isi alasan penolakan
   - Confirmation modal dengan warning
   - Status: Ditolak Dinas
   - Notifikasi dan email ke pembuat pengajuan

#### Stage 2: Revisi (jika diminta)

**Admin Dinas atau Pegawai Access**: `/app/pengajuan/{id}/revisi` atau `/pegawai/pengajuan-saya/{id}/revisi`

**View**:
- Tampilkan catatan verifikator per dokumen (highlighted)
- List dokumen yang perlu direvisi (red badge)
- Upload ulang dokumen bermasalah
- Version history dokumen
- Compare old vs new document (side by side preview)

**Limit Revisi**: Maksimal 3x revisi
- Jika sudah 3x revisi masih tidak valid → Auto-reject
- Counter revisi visible

**Re-submit**:
- Button "Submit Revisi"
- Kembali ke Verifikasi Dinas
- Status: Diajukan (kembali ke antrian verifikasi)
- Notifikasi ke Verifikator Dinas

#### Stage 3: Verifikasi Kabupaten

**Verifikator Kabupaten Access**: `/admin/verifikasi/{id}`

**View Similar to Verifikasi Dinas, plus**:
- Info verifikator dinas: Nama, tanggal verifikasi, catatan
- Cross-check data dengan SIMPEG (jika integrasi ada):
  - Validasi NIP
  - Validasi Golongan
  - Validasi TMT KGB Terakhir
  - Status Kepegawaian
- Calculation validator: TMT KGB Baru otomatis benar atau tidak

**Decision Actions**:
1. **Setujui Pengajuan**
   - Generate Nomor SK otomatis (format: XXX/KGB/BKPSDM/YYYY)
   - Status: Disetujui
   - Notifikasi ke: Admin Dinas, Pegawai (jika ada akun)
   - Trigger workflow: Generate SK (optional auto-generate PDF)

2. **Tolak & Kembalikan ke Dinas**
   - Catatan alasan penolakan
   - Status: Ditolak Kabupaten
   - Notifikasi ke: Verifikator Dinas, Admin Dinas, Pegawai
   - Pengajuan bisa direvisi dan diajukan ulang

### SK Generation & Finalization

**Super Admin Access**: `/admin/sk-kgb/{id}`

**SK Management**:
1. **Auto-Generate SK Draft** (optional feature):
   - Template SK dengan placeholder (Nama, NIP, Golongan, TMT, dll)
   - Render PDF dari template
   - Preview sebelum finalisasi

2. **Manual Upload SK Scan**:
   - Upload SK yang sudah ditandatangani (scan PDF)
   - Watermark otomatis dengan nomor SK
   - File validation

3. **Distribute SK**:
   - Status: Selesai
   - Notifikasi ke: Admin Dinas, Pegawai dengan link download SK
   - Email dengan attachment SK
   - Update data pegawai: TMT KGB Terakhir, Golongan (jika naik)

**Download SK** (by Pegawai or Admin Dinas):
- Akses: `/pegawai/sk/{id}/download` atau `/app/sk/{id}/download`
- Secure download dengan authentication
- Watermark: "SK RESMI - [Nama Pegawai] - [NIP]"
- Log download activity (audit trail)

## Notification System Detail (FilamentPHP)

### Notification Types & Triggers

#### 1. Pengajuan Baru
**Trigger**: Saat pengajuan di-submit  
**Recipients**: Verifikator Dinas, Admin Dinas (jika diajukan pegawai)  
**Content**:
```
Title: "Pengajuan KGB Baru"
Body: "[Nama Pegawai] - [NIP] telah mengajukan KGB"
Icon: document-text (blue)
Actions:
  - Lihat Detail (link to /app/verifikasi/{id})
  - Tandai Sudah Dibaca
```

#### 2. Verifikasi Selesai - Lolos
**Trigger**: Verifikator Dinas meneruskan ke Kabupaten  
**Recipients**: Verifikator Kabupaten, Admin Dinas, Pegawai (jika self-service)  
**Content**:
```
Title: "Pengajuan Diteruskan ke Kabupaten"
Body: "Pengajuan KGB [Nama Pegawai] telah lolos verifikasi dinas"
Icon: arrow-up-circle (green)
Actions:
  - Lihat Status (link to detail)
```

#### 3. Revisi Diminta
**Trigger**: Verifikator meminta revisi dokumen  
**Recipients**: Admin Dinas atau Pegawai yang mengajukan  
**Content**:
```
Title: "⚠️ Revisi Dokumen Diperlukan"
Body: "Pengajuan KGB [Nama Pegawai] memerlukan perbaikan dokumen"
Icon: exclamation-triangle (orange)
Actions:
  - Upload Revisi (link to /app/pengajuan/{id}/revisi)
  - Lihat Catatan (modal dengan detail catatan per dokumen)
```

#### 4. Pengajuan Ditolak
**Trigger**: Verifikator/Approval menolak pengajuan  
**Recipients**: Admin Dinas, Pegawai (jika self-service)  
**Content**:
```
Title: "❌ Pengajuan Ditolak"
Body: "Pengajuan KGB [Nama Pegawai] ditolak. Klik untuk lihat alasan."
Icon: x-circle (red)
Actions:
  - Lihat Alasan (modal dengan detail)
  - Ajukan Ulang (jika masih eligible)
```

#### 5. Pengajuan Disetujui
**Trigger**: Verifikator Kabupaten approve pengajuan  
**Recipients**: Admin Dinas, Pegawai  
**Content**:
```
Title: "🎉 Pengajuan KGB Disetujui!"
Body: "Selamat! KGB [Nama Pegawai] telah disetujui dengan No SK: [Nomor]"
Icon: check-circle (green)
Actions:
  - Lihat SK (link to SK detail)
  - Download SK (jika sudah upload)
```

#### 6. SK Tersedia untuk Diunduh
**Trigger**: Super Admin upload SK scan  
**Recipients**: Admin Dinas, Pegawai  
**Content**:
```
Title: "📄 SK KGB Siap Diunduh"
Body: "SK KGB [Nama Pegawai] - [No SK] sudah tersedia"
Icon: document-download (blue)
Actions:
  - Download SK (direct download link)
  - Cetak SK
```

#### 7. Reminder Eligible KGB
**Trigger**: Manual oleh Admin Dinas atau Auto (cron job)  
**Recipients**: Pegawai yang eligible  
**Content**:
```
Title: "⏰ Anda Sudah Eligible untuk KGB"
Body: "Anda sudah memenuhi syarat 2 tahun untuk mengajukan KGB"
Icon: bell (yellow)
Actions:
  - Ajukan Sekarang (link to /pegawai/pengajuan-saya/create)
  - Ingatkan Saya Nanti (snooze 7 hari)
```

### Notification UI Components

**Navbar Bell Icon**:
- Bell icon dengan badge counter (unread count)
- Dropdown notification panel (max 5 terbaru)
- Link "Lihat Semua Notifikasi" di footer dropdown
- Mark all as read button
- Auto-refresh dengan Livewire polling (optional)

**Notification Center Page**:
- Full list notifikasi (paginated)
- Tabs: Semua, Belum Dibaca, Sudah Dibaca
- Filter by type, date range
- Search notifications
- Bulk actions: Mark as read, Delete
- Export notification history

**In-notification Actions**:
- Primary action button (CTA)
- Secondary actions (dropdown menu)
- Timestamp relative (e.g., "5 menit lalu", "2 jam lalu")
- Avatar user yang trigger notification (jika applicable)

## Reporting & Analytics

### `/admin/laporan` - Laporan Kabupaten

**Laporan Agregat**:
- Filter: Periode (tanggal), Dinas, Status
- Metrics:
  - Total pengajuan periode
  - Approved vs Rejected ratio
  - Rata-rata waktu proses (hari)
  - Total SK diterbitkan
  - Breakdown per dinas
- Visualization: Charts untuk trend dan comparison
- Export: Excel (detailed), PDF (summary report)

**Laporan per Dinas**:
- Pilih dinas dari dropdown
- Deep-dive data dinas tertentu:
  - Jumlah pegawai
  - Jumlah pengajuan
  - Approval rate
  - Dokumen yang sering ditolak
  - Average revision count
  - Top 10 pegawai by golongan
- Comparison chart dengan dinas lain
- Export per dinas

**Statistik Pengajuan**:
- Trend pengajuan bulanan/tahunan (line chart)
- Distribution by golongan (bar chart)
- Status distribution (pie chart)
- Processing time distribution (histogram)
- Aging report (pengajuan > 30 hari)

**Performance Report**:
- Ranking dinas by performance metrics
- Verifikator performance (response time, approval rate)
- SLA compliance report
- Bottleneck analysis

### `/app/laporan` - Laporan Dinas

**Laporan Bulanan**:
- Summary bulan berjalan dan comparison dengan bulan lalu
- List pengajuan bulan ini dengan status
- Pegawai yang sudah menerima KGB bulan ini
- Export Excel

**Statistik Approval Rate**:
- Trend approval rate dinas over time
- Breakdown alasan penolakan
- Revisi rate
- Average processing time

**Pegawai per Golongan**:
- Distribution chart pegawai by golongan
- Projection KGB eligible next 6 months
- Budget estimation untuk KGB (optional)

**Export Options**:
- Format: Excel (detailed with pivot), PDF (formatted report)
- Template customizable
- Schedule auto-send email report (monthly)

### `/pegawai/riwayat` - Riwayat Pegawai

**Riwayat Pengajuan Saya**:
- Timeline visual semua pengajuan lifetime
- Per pengajuan: Status akhir, tanggal submit, tanggal selesai, No SK
- Download SK per pengajuan yang approved
- Total hari proses per pengajuan
- Filter by tahun, status

**Statistik Personal**:
- Total KGB yang pernah diterima
- Projection KGB berikutnya (tanggal eligible)
- Grafik perkembangan golongan over time

## Security & Compliance

### Authentication
- Email/Username + Password
- Separate guard untuk `/admin`, `/app`, `/pegawai`
- Session timeout: 120 menit
- Remember me option
- Password requirements: min 8 karakter, kombinasi huruf & angka

### Authorization
- Spatie Laravel Permission dengan tenant-aware
- Role-based access control (RBAC)
- Permission granular per resource (view, create, update, delete)
- Tenant isolation enforced di model level (Global Scope)
- Policy untuk setiap model

### File Security
- Storage location: `storage/app/private/dokumen/{tenant_id}/{pengajuan_id}/`
- Access via authenticated route only
- File validation: MIME type, size, content scan (optional antivirus)
- Encryption at rest (optional, Laravel encryption)
- Watermark pada download dokumen

### Audit Trail
- Log semua CRUD operations
- Log approval/rejection dengan reason
- Log file upload/download
- Log user login/logout
- Searchable activity logs dengan filter
- Retention policy: 2 tahun

### Data Privacy
- Data pegawai sensitif (NIP, dokumen) restricted access
- Export data dengan anonymization option
- GDPR-compliant (right to access, right to delete)
- Data retention policy sesuai aturan kepegawaian

## Technical Implementation Notes

### FilamentPHP 3.3 Multi-Panel Setup

**Panel Providers**:
```
app/Providers/Filament/
├── AdminPanelProvider.php      // /admin panel (Kabupaten)
├── AppPanelProvider.php        // /app panel (Dinas)
└── PegawaiPanelProvider.php    // /pegawai panel (Self-Service)
```

**Configuration per Panel**:
- Path prefix (`/admin`, `/app`, `/pegawai`)
- Auth guard (admin, dinas, pegawai)
- Login page customization
- Colors & branding per panel
- Navigation groups & icons
- Widgets registration
- Notification database channel

### Database Notifications Setup

**Installation**: Built-in FilamentPHP 3.3

**Configuration**:
```php
// Di setiap Panel Provider
->databaseNotifications()
->databaseNotificationsPolling('30s') // optional
```

**Notification Class Structure**:
```
app/Notifications/
├── PengajuanBaruNotification.php
├── VerifikasiSelesaiNotification.php
├── RevisiDimintaNotification.php
├── PengajuanDitolakNotification.php
├── PengajuanDisetujuiNotification.php
├── SKTersediaNotification.php
└── ReminderEligibleKGBNotification.php
```

**Each Notification**:
- Implement `toDatabase()` method
- Return array dengan: title, body, icon, color, actions (array of action objects)
- Actions: label, url, openInNewTab, color

### Tenant Context Middleware

**Middleware**: `TenantContext`
- Retrieve user's tenant_id dari auth
- Set global tenant_id di config atau session
- Apply global scope ke Eloquent models
- Inject tenant info ke views

**Global Scope** (di BaseModel atau Trait):
```php
// Trait TenantScope
// Auto-filter query by tenant_id
// Auto-set tenant_id on create
```

### File Storage Structure

```
storage/app/private/
└── dokumen/
    └── {tenant_id}/
        └── {pengajuan_id}/
            ├── sk_pangkat_[timestamp].pdf
            ├── sk_kgb_terakhir_[timestamp].pdf
            ├── absensi_6bulan_[timestamp].pdf
            ├── sk_unor_[timestamp].pdf
            └── skp_2tahun_[timestamp].pdf
```

### Performance Optimization
- Database indexing pada: tenant_id, pegawai_id, status, created_at
- Eager loading untuk relationships
- Query caching untuk statistics (5 menit cache)
- Pagination untuk large datasets
- Lazy loading images di dashboard
- CDN untuk static assets (optional)

## Implementation Phases (Revised)

### Phase 1: Core System (4 minggu)
**Week 1-2**:
- Setup Laravel 12 + FilamentPHP 3.3
- Multi-panel configuration (`/admin`, `/app`, `/pegawai`)
- Database schema & migrations
- Seeders untuk roles, permissions, sample data
- Authentication & authorization setup

**Week 3-4**:
- CRUD Tenant (dinas) di `/admin`
- CRUD Pegawai di `/app`
- Basic dashboard di 3 panel
- User management per panel
- Tenant context middleware

### Phase 2: Pengajuan & Workflow (4 minggu)
**Week 5-6**:
- Form pengajuan KGB di `/app` (admin input)
- Form pengajuan KGB di `/pegawai` (self-service)
- File upload & validation
- Document preview feature
- Draft & submit functionality

**Week 7-8**:
- Verifikasi flow (dinas & kabupaten)
- Revisi flow dengan version control
- Approval/rejection logic
- Status transitions & validations
- Eligible calculation logic

### Phase 3: Notifications & Reporting (3 minggu)
**Week 9-10**:
- Database notifications setup (FilamentPHP)
- Notification classes untuk 7 types
- Notification UI components (bell, dropdown, center)
- Email notifications integration
- Reminder system (manual & auto)

**Week 11**:
- Reporting module di `/admin`
- Reporting module di `/app`
- Charts & analytics
- Export Excel/PDF functionality
- Scheduled reports (optional)

### Phase 4: SK Management & Finalization (2 minggu)
**Week 12**:
- SK generation module
- SK template management
- Upload SK scan functionality
- Secure download dengan watermark
- Update pegawai data after approval

**Week 13**:
- Activity logs & audit trail
- Help & FAQ pages
- Video tutorial embedding
- Document templates download
- System settings & configuration

### Phase 5: Testing & Deployment (2 minggu)
**Week 14**:
- Unit testing (PHPUnit)
- Feature testing (Pest/PHPUnit)
- User acceptance testing (UAT) dengan sample users
- Bug fixing & optimization
- Performance testing

**Week 15**:
- Production deployment
- Data migration (jika dari sistem lama)
- User training (admin, verifikator)
- Documentation (user manual, admin guide)
- Go-live support

**Total Duration**: 15 minggu (3.5 bulan)

## Success Metrics (KPIs)

### Operational Metrics
- **Processing Time**: Rata-rata waktu dari submit hingga approval ≤ 14 hari
- **First-Time Approval Rate**: ≥ 70% pengajuan lolos tanpa revisi
- **System Uptime**: ≥ 99% availability
- **User Adoption Rate**: ≥ 80% dinas aktif menggunakan sistem dalam 3 bulan

### User Satisfaction Metrics
- **Admin Dinas Satisfaction**: ≥ 4.0/5.0 (survey)
- **Pegawai Satisfaction** (self-service): ≥ 4.0/5.0 (survey)
- **Support Ticket Volume**: < 10 tiket per minggu setelah 1 bulan go-live

### Efficiency Metrics
- **Reduction in Manual Work**: ≥ 60% pengurangan waktu admin untuk data entry
- **Document Rejection Rate**: ≤ 20% dokumen ditolak karena format/kelengkapan
- **Notification Response Time**: Rata-rata user merespon notifikasi dalam ≤ 24 jam

## Risk Management

| Risk | Probability | Impact | Mitigation |
|------|------------|--------|------------|
| User resistance (pegawai tidak mau self-service) | Medium | Medium | Hybrid approach, training, helpdesk support, incentive |
| Server/storage capacity insufficient | Low | High | Cloud hosting, auto-scaling, monitoring alerts |
| Data migration dari sistem lama gagal | Medium | High | Dry-run, parallel run 1 bulan, rollback plan |
| Document upload abuse (file besar/banyak) | Medium | Medium | File size limit, quota per user, antivirus scan |
| Performance degradation saat peak usage | Medium | Medium | Load balancing, caching, database optimization |
| Security breach | Low | Critical | Penetration testing, regular security audit, WAF |

## Next Steps untuk Development

1. **Stakeholder Review** (1 minggu)
   - Present PRD ke BKPSDM Kabupaten
   - Get approval dari Sekda/Bupati
   - Finalize business process

2. **Technical Planning** (1 minggu)
   - Setup development environment
   - Create project repository (Git)
   - Database design detail (ERD)
   - API contract design (jika integrasi SIMPEG)

3. **UI/UX Design** (2 minggu)
   - Wireframe 3 panel (Figma/Adobe XD)
   - User flow diagram
   - Prototype interactive
   - User testing prototype

4. **Development Kick-off** (Week 1 Phase 1)
   - Sprint planning
   - Team assignment
   - Development start

***

**Catatan Penting**:
- PRD ini sudah final dengan **hybrid approach** dan **database notifications FilamentPHP**
- Path-based routing `/admin`, `/app`, `/pegawai` sudah confirmed
- Self-service pegawai adalah **optional** dan configurable per dinas
- Database notifications akan menggunakan built-in FilamentPHP 3.3 feature
- Tidak ada coding di stage ini, hanya perencanaan dan design[1]

**Approval Required**: BKPSDM Kabupaten Muna Barat untuk proceed ke tahap development.

[1](https://ppl-ai-file-upload.s3.amazonaws.com/web/direct-files/attachments/images/74613787/4a1d66fd-091c-4a6a-abac-2e735225bf99/1000189236.jpg)
