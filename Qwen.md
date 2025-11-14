# Todo List from PRD: Aplikasi Pengajuan KGB PNS dengan Hybrid Approach

Based on the PRD document (prd.md), here are the implementation tasks organized by the development phases:

## Phase 1: Core System (4 minggu)

### Week 1-2: Initial Setup
- [ ] Setup Laravel 12 + FilamentPHP 3.3
- [ ] Configure multi-panel FilamentPHP (`/admin`, `/app`, `/pegawai`)
- [ ] Create database schema & migrations
- [ ] Setup seeders for roles, permissions, sample data
- [ ] Configure authentication & authorization (separate guards per panel)
- [ ] Test basic panel access for each role type

### Week 3-4: Core CRUD & Dashboards
- [ ] Implement CRUD for Tenant (dinas) in `/admin` panel
- [ ] Implement CRUD for Pegawai in `/app` panel
- [ ] Create basic dashboard for `/admin` panel with metrics
- [ ] Create basic dashboard for `/app` panel with metrics
- [ ] Create basic dashboard for `/pegawai` panel with metrics
- [ ] Implement user management per panel (roles, permissions)
- [ ] Implement tenant context middleware
- [ ] Test tenant isolation functionality

## Phase 2: Pengajuan & Workflow (4 minggu)

### Week 5-6: Pengajuan Forms
- [ ] Create pengajuan form in `/app` panel (admin input)
- [ ] Create pengajuan form in `/pegawai` panel (self-service)
- [ ] Implement file upload & validation for required documents
- [ ] Add document preview feature
- [ ] Implement draft & submit functionality
- [ ] Create Pengajuan model with proper relationships

### Week 7-8: Verification & Approval Workflow
- [ ] Implement verifikasi flow for dinas panel
- [ ] Implement verifikasi flow for kabupaten panel
- [ ] Create revisi flow with version control
- [ ] Implement approval/rejection logic
- [ ] Create status transitions & validation rules
- [ ] Implement eligible calculation logic for KGB
- [ ] Test complete workflow from pengajuan to approval/rejection

## Phase 3: Notifications & Reporting (3 minggu)

### Week 9-10: Notification System
- [ ] Setup FilamentPHP database notifications
- [ ] Create 7 notification types (PengajuanBaru, VerifikasiSelesai, etc.)
- [ ] Implement notification UI components (bell icon, dropdown, center)
- [ ] Implement email notifications integration
- [ ] Create reminder system (manual & auto-trigger)

### Week 11: Reporting Module
- [ ] Create reporting module for `/admin` panel
- [ ] Create reporting module for `/app` panel
- [ ] Implement charts & analytics (Bar, Line, Donut charts)
- [ ] Add Excel/PDF export functionality
- [ ] Implement scheduled reports

## Phase 4: SK Management & Finalization (2 minggu)

### Week 12: SK Generation
- [ ] Create SK generation module in admin panel
- [ ] Implement SK template management
- [ ] Add upload SK scan functionality
- [ ] Create secure download with watermark
- [ ] Update pegawai data after approval

### Week 13: Additional Features
- [ ] Implement activity logs & audit trail
- [ ] Create help & FAQ pages
- [ ] Add video tutorial embedding
- [ ] Create document templates download
- [ ] Implement system settings & configuration

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