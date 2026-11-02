# Product Requirements Document (PRD):SIMAKAR

> **Document Status:** Draft ~~| In Review | Approved | Archived~~  
> **Last Updated:** 2 November 2026  
> **Mata Kuliah:** Microservice Arsitektur
> **Dosen Pengampu:** Ahmad Halimi  
> **Contributors:** 2321500018 | Fahmi Bahrul Widad/Developer

---

## Executive Summary

**Product Name:** SIMAKAR | Sistem Informasi Manajemen Karir, Magang, dan Alumni

**Problem Statement:**  
Mahasiswa tingkat akhir sering kali kerepotan dan menghabiskan banyak waktu untuk menyusun *Curriculum Vitae* (CV) berstandar industri (*ATS-friendly*) secara manual dari nol. Di sisi lain, data pendukung karir mahasiswa seperti riwayat akademik, rekam jejak magang (MO-KEL), sertifikasi pendamping (SKPI), dan uji kompetensi (UKom) masih tersebar di berbagai sistem yang terpisah di portal SSO Universitas Nurul Jadid (UNUJA).

**Proposed Solution:**  
SIMAKAR hadir sebagai platform agregator berbasis web yang terintegrasi langsung dengan ekosistem SSO kampus. Solusi ini memusatkan seluruh siklus kesiapan profesional mahasiswa untuk mengotomatisasi pembuatan CV ATS dari data tervalidasi, menyediakan direktori alumni, serta memfasilitasi formulir *tracer study* secara terpusat.

**Success Metrics:**  
- Mahasiswa tingkat akhir Fakultas Teknik berhasil membuat CV ATS melalui SIMAKAR dengan mudah dan tanpa repot.
- Terwujudnya sinkronisasi data lintas aplikasi (SSO, MO-KEL, SKPI, UKom).

**Launch Target:** [TBD - Belum ditentukan]

---

## Background & Context

### Why Now?
Kebutuhan mendesak dari program studi di lingkungan Fakultas Teknik untuk menyiapkan lulusan menghadapi dunia kerja, sekaligus mempermudah program studi dalam melacak sebaran alumni (*tracer study*) guna keperluan akreditasi. Adanya portal SSO yang sudah mapan memberikan momentum yang tepat untuk mengintegrasikan data tanpa harus membangun pengumpulan data dari nol.
_Sebenarnya hanya untuk tugas kuliah saja hehe😅._

### User Research & Insights
* **Metode:** Evaluasi keluhan mahasiswa tingkat akhir terkait persiapan berkas kerja.
* **Temuan Utama:** Mahasiswa kesulitan merangkum portofolio magang dan nilai akademik ke dalam satu dokumen ringkas yang disukai HRD perusahaan. Program studi kesulitan mendapatkan respons *tracer study* dari alumni yang sudah bekerja.

---

### Market Analysis
- **Market Size:** [TAM/SAM/SOM if relevant]
- **Competitive Landscape:** [Key competitors and how they address this problem]
- **Differentiation:** [What makes our approach unique/better]

---

## Goals & Success Criteria

### Business Goals

* Mengotomatisasi sistem pelaporan portofolio kompetensi lulusan Fakultas Teknik.
* Memusatkan *database* pelacakan alumni dan riwayat serapan kerja pasca-kelulusan.

### User Goals

* Mahasiswa dapat men-*generate* CV PDF berstandar ATS dalam satu klik tanpa perlu mendesain atau mengetik ulang riwayat magang/akademiknya.
* Dosen dan Admin Prodi memiliki visibilitas penuh terhadap data kesiapan karir mahasiswa dan alumni.

### Non-Goals (Out of Scope)

* **TIDAK melayani mahasiswa di luar Fakultas Teknik.** Rilis ini secara eksklusif dikunci (sementara) hanya untuk *scope* Fakultas Teknik.
* Tidak membangun ulang aplikasi MO-KEL, SKPI, atau UKom, melainkan hanya bertindak sebagai agregator (penarik data).

---

## User Personas & Use Cases

### Primary Persona: Mahasiswa Tingkat Akhir (Fakultas Teknik)

* **Peran:** Mahasiswa aktif Fakultas Teknik UNUJA.
* **Tujuan:** Membuat CV profesional secara instan dan mencari referensi alumni.
* **Pain Points:** Bingung format CV ATS, lupa detail data magang tahun lalu.
* **Konteks:** Digunakan saat persiapan melamar magang industri atau pekerjaan pasca-wisuda.

### Secondary Persona: Admin Prodi / Tim Tracer Study

* **Peran:** Staf atau pimpinan prodi di bawah Fakultas Teknik.
* **Tujuan:** Mengumpulkan data pekerjaan pertama alumni untuk borang akreditasi.
* **Pain Points:** *Response rate* kuesioner alumni rendah dan data tersebar.

## Product Requirements

### Functional Requirements

#### Must Have (P0) - MVP
1. **Modul Integrasi & Keamanan Dasar**
    * **Inisialisasi SSO:** Login terpusat menggunakan `X-Token` API SSO kampus untuk sinkronisasi profil pengguna.
    * **Otorisasi Terbatas (RBAC):** Pembatasan *Gate* untuk memastikan sistem hanya bisa diakses oleh *scope* Fakultas Teknik (Teknik Informatika, Teknik Elektro, dll.).
    * **Log Audit:** Implementasi pencatatan aktivitas sistem (*audit trail*).

2. **Agregator Portofolio Data**
    * **Sinkronisasi Akademik:** Penarikan transkrip nilai dan daftar matakuliah dari API SSO.
    * **Penarikan MO-KEL & SKPI:** Terhubung dengan database/API MO-KEL, SKPI, dan UKom untuk mengambil riwayat magang dan sertifikat.

3. **CV Generator**
    * **Auto-Generate CV ATS:** Mesin perender PDF yang menyusun data agregator (identitas, transkrip unggulan, magang, portofolio) menjadi format dokumen standar ATS.

4. **Tracer Study**
    * **Form Kuesioner:** Formulir digital untuk pelacakan karir alumni pasca-lulus.

#### Should Have (P1) - Post-Launch

* **Direktori Alumni:** Fitur pencarian jejaring alumni berdasarkan prodi dan tempat kerja untuk memfasilitasi komunikasi mahasiswa dengan lulusan.

---

### Non-Functional Requirements

* **Performance:** Menggunakan Laravel Octane dengan FrankenPHP untuk memproses sinkronisasi API dan *rendering* PDF yang cepat. Cache dan Queue di-*handle* via Redis.
* **Security:** Sistem menggunakan otorisasi *Gate*, *rate-limiting* pada fungsi login, dan *idle-lock* sesi (otomatis *logout* bila tidak ada aktivitas).
* **Platform:** Aplikasi berbasis web responsif, optimal diakses melalui PC maupun peramban *smartphone* mahasiswa (Frontend: Vite + Tailwind CSS v4).

---

## Technical Specifications

### System Architecture
* **Bahasa & Framework:** PHP 8.4, Laravel 13
* **Server-side Runtime:** Laravel Octane (FrankenPHP)
* **Frontend Stack:** Vite, Tailwind CSS v4, DataTables, Tom Select, Flatpickr, SweetAlert2.

### Data Model
* **MySQL Multi-Koneksi:** Basis data lokal (`mysql_simakar`) untuk log audit, draf CV, kuesioner tracer study.
* Koneksi eksternal/API untuk sinkronisasi data master dari SSO (`sso.unuja.ac.id/api`).

### API Requirements
* `X-Token` Header diperlukan untuk semua permintaan ke SSO.
* **Endpoints:** `/master/mahasiswa`, `/master/prodi`, `/master/alumni`, `/akademik/transkrip`, `/akademik/matakuliah`.

---

## Go-to-Market Strategy

### Launch Channels (Sosialisasi)
* Distribusi informasi dilakukan secara resmi melalui **surat edaran program studi**, baik dalam format daring (grup Telegram/WhatsApp angkatan) maupun luring (ditempel di mading fakultas atau ruang program studi).

**Target Audience untuk Rilis:** Seluruh pimpinan, dosen, dan mahasiswa tingkat akhir di lingkungan Fakultas Teknik UNUJA.

---

## Dependencies & Risks

### Dependencies
* **Tim IT / PDSI UNUJA:** SIMAKAR bergantung pada PDSI untuk membuka akses integrasi tambahan (baik melalui API baru atau akses baca *database*) menuju aplikasi MO-KEL, SKPI, dan UKom. Jika akses ini tidak diberikan, fungsi otomatisasi agregator portofolio akan terhambat.

### Risks & Mitigation
* **Risiko Akses Data (Tinggi):** PDSI menolak atau menunda pembukaan akses database/API.
* *Mitigasi:* Berkoordinasi secepatnya. Jika gagal, siapkan fitur *fallback* agar mahasiswa menginput data secara manual terverifikasi.
   - [Same structure as above]

---

## Timeline & Milestones

### Development Phases
*(Catatan: Tenggat waktu spesifik belum diketahui, mengikuti perkembangan implementasi di lapangan).*

* **Fase 1 (Discovery & Setup):** [TBD]
* Setup Laravel 13, Octane, DB MySQL, Vite, Tailwind v4.
* Konfigurasi RBAC & Log Audit.


* **Fase 2 (Integration):** [TBD]
* Integrasi *X-Token* SSO.
* Koordinasi dengan PDSI & penarikan data MO-KEL, SKPI, UKom, Akademik.


* **Fase 3 (Core Dev):** [TBD]
* Fitur CV ATS otomatis.
* Modul Tracer study dan direktori alumni.


* **QA & Testing:** [TBD]
* *Unit Testing* & *Feature Testing*.


* **Full Launch:** [TBD]

---