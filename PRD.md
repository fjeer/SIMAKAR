# Product Requirements Document (PRD): SIMAKAR

> **Document Status:** Draft~~| In Review | Approved | Archived~~ 
> **Last Updated:** 10 November 2026
> **Mata Kuliah:** Microservice Arsitektur
> **Dosen Pengampu:** Ahmad Halimi
> **Contributors:** 2321500018 | Fahmi Bahrul Widad/Developer

---

## Executive Summary

**Product Name:** SIMAKAR | Sistem Informasi Manajemen Karir, Magang, dan Alumni

**Problem Statement:**

Mahasiswa tingkat akhir sering kali kerepotan dan menghabiskan banyak waktu untuk menyusun *Curriculum Vitae* (CV) berstandar industri (*ATS-friendly*) secara manual dari nol. Di sisi lain, data pendukung karir mahasiswa seperti riwayat akademik, rekam jejak magang (MO-KEL), sertifikasi pendamping (SKPI), dan uji kompetensi (UKom) masih tersebar di berbagai sistem yang terpisah di portal SSO Universitas Nurul Jadid (UNUJA).

**Proposed Solution:**

SIMAKAR hadir sebagai platform agregator berbasis web yang terintegrasi langsung dengan ekosistem SSO kampus. Solusi ini memusatkan seluruh siklus kesiapan profesional mahasiswa untuk mengotomatisasi pembuatan CV ATS dari data tervalidasi, menyediakan "Talent Pool" terpusat bagi prodi untuk memantau portofolio serta kompetensi mahasiswa aktif sebelum lulus, serta memfasilitasi pelacakan dan direktori alumni secara terpusat di masa mendatang.

**Success Metrics:**

* Mahasiswa tingkat akhir Fakultas Teknik berhasil membuat CV ATS melalui SIMAKAR dengan mudah dan tanpa repot.
* Terwujudnya sinkronisasi data lintas aplikasi (SSO, MO-KEL, SKPI, UKom).

**Launch Target:** [TBD - Belum ditentukan]

---

## Background & Context

### Why Now?

Kebutuhan mendesak dari program studi di lingkungan Fakultas Teknik untuk menyiapkan lulusan menghadapi dunia kerja, sekaligus mempermudah program studi dalam melacak sebaran alumni guna keperluan akreditasi. Adanya portal SSO yang sudah mapan memberikan momentum yang tepat untuk mengintegrasikan data tanpa harus membangun pengumpulan data dari nol.
*Sebenarnya hanya untuk tugas kuliah saja hehe😅.*

### User Research & Insights

* **Metode:** Evaluasi keluhan mahasiswa tingkat akhir terkait persiapan berkas kerja.
* **Temuan Utama:** Mahasiswa kesulitan merangkum portofolio magang dan nilai akademik ke dalam satu dokumen ringkas yang disukai HRD perusahaan. Program studi kesulitan mendapatkan data agregasi kompetensi mahasiswa dan respons pelacakan alumni dari alumni yang sudah bekerja.

---

## Goals & Success Criteria

### Business Goals

* Mengotomatisasi sistem penyusunan portofolio kompetensi mahasiswa Fakultas Teknik.
* Menyediakan *Talent Pool* terpusat yang memudahkan program studi dalam memfilter dan merekomendasikan mahasiswa berdasarkan keahlian (contoh: untuk kebutuhan magang industri atau perlombaan).
* Memusatkan *database* pelacakan alumni dan riwayat serapan kerja pasca-kelulusan (jangka panjang).

### User Goals

* Mahasiswa dapat men-*generate* CV PDF berstandar ATS dalam satu klik tanpa perlu mendesain atau mengetik ulang riwayat akademik/magang resminya.
* Dosen, Kaprodi, dan Dekan memiliki visibilitas penuh terhadap data kesiapan karir mahasiswa aktif.

### Non-Goals (Out of Scope)

* **TIDAK melayani mahasiswa di luar Fakultas Teknik.** Rilis ini secara eksklusif dikunci (sementara) hanya untuk *scope* Fakultas Teknik.
* Tidak membangun ulang aplikasi MO-KEL, SKPI, atau UKom, melainkan hanya bertindak sebagai agregator (penarik data).
* **Tidak mengandalkan 100% data API.** Sistem mengadopsi **Data Hibrida**, memungkinkan mahasiswa menginput riwayat di luar kampus (manual) berdampingan dengan data kampus (terverifikasi otomatis via API).

---

## User Personas & Use Cases

### Primary Persona: Mahasiswa Tingkat Akhir (Fakultas Teknik)

* **Peran:** Mahasiswa aktif Fakultas Teknik UNUJA.
* **Tujuan:** Membuat CV profesional secara instan dan mencari referensi alumni.
* **Pain Points:** Bingung format CV ATS, lupa detail data magang tahun lalu.
* **Konteks:** Digunakan saat persiapan melamar magang industri atau pekerjaan pasca-wisuda.

### Secondary Persona: Dekan, Kaprodi, dan Dosen Pembimbing

* **Peran:** Pimpinan fakultas, ketua prodi, dan dosen di Fakultas Teknik.
* **Tujuan:** Memantau tren kompetensi mahasiswa, melihat kelengkapan profil anak bimbingan, dan mencari kandidat berbakat (Talent Pool) untuk kebutuhan industri/akreditasi.
* **Pain Points:** Data keahlian mahasiswa saat ini masih tersebar sehingga sulit memetakan dan merekomendasikan mahasiswa untuk peluang magang atau kerja.

---

## Product Requirements

### Functional Requirements

#### Must Have (P0) - MVP

1. **Modul Integrasi & Keamanan Dasar**
* **Inisialisasi SSO:** Login terpusat menggunakan `X-Token` API SSO kampus untuk sinkronisasi profil pengguna.
* **Otorisasi Terbatas (RBAC):** Pembatasan *Gate* untuk 5 peran (Mahasiswa, Dosen, Kaprodi, Dekan, Admin) dan memastikan sistem diakses oleh lingkup yang diizinkan.
* **Log Audit:** Implementasi pencatatan aktivitas modifikasi data menggunakan `spatie/laravel-activitylog` sebagai *audit trail*.


2. **Agregator Portofolio Hibrida**
* **Sinkronisasi Otomatis:** Penarikan transkrip akademik jenjang S1 (terverifikasi otomatis) dari API SSO.
* **Manajemen Manual Terkendali:** Antarmuka CRUD bagi mahasiswa untuk menginput Pengalaman, Sertifikasi, dan Keahlian di luar kampus, yang berdampingan dengan data resmi yang terkunci (persiapan untuk API MO-KEL & SKPI).


3. **CV Generator**
* **Auto-Generate CV ATS:** Mesin perender dokumen berbasis PDF (DOMPDF) yang menyusun data profil, pendidikan terverifikasi, serta riwayat hibrida menjadi format dokumen bersih berstandar mesin pelacak (*ATS-Friendly*).


4. **Dashboard Multi-Role & Talent Pool**
* **Dasbor Kaprodi:** Fitur *Talent Pool* dengan tabel pencarian canggih untuk memfilter kompetensi mahasiswa aktif.
* **Dasbor Dekan:** Statistik agregasi data portofolio di tingkat fakultas.



#### Should Have (P1) - Post-Launch

* **Pelacakan Alumni (Tracer Study):** Formulir digital untuk pelacakan karir alumni pasca-lulus.
* **Direktori Alumni:** Fitur pencarian jejaring alumni berdasarkan prodi dan tempat kerja untuk memfasilitasi komunikasi mahasiswa dengan lulusan.

---

## Non-Functional Requirements

* **Performance:** Menggunakan Laravel Octane dengan RoadRunner untuk memproses sinkronisasi API dan *rendering* PDF yang cepat. Cache dan Queue di-*handle* via Redis.
* **Security:** Sistem menggunakan otorisasi *Gate*, *rate-limiting* pada fungsi login, perlindungan aksi (HTTP 403 untuk data SSO), dan *idle-lock* sesi (otomatis *logout* bila tidak ada aktivitas).
* **Platform:** Aplikasi berbasis web responsif, optimal diakses melalui PC maupun peramban *smartphone* mahasiswa.

---

## Technical Specifications

### System Architecture

* **Bahasa & Framework:** PHP 8.4, Laravel 13
* **Server-side Runtime:** Laravel Octane (RoadRunner)
* **Frontend Stack:** Vite, Tailwind CSS v4, DataTables, Tom Select, Flatpickr, SweetAlert2, **DOMPDF** (CV Generator).

### Data Model

* **Basis Data Mandiri (Stand-alone):** Menggunakan basis data lokal (`mysql_simakar`) untuk menyimpan profil pengguna, data CV hibrida, pengaturan peran, dan *Activity Log* (Spatie).
* **Integrasi REST API:** Sinkronisasi data master (profil dasar mahasiswa/dosen) dan akademik (transkrip) dari portal SSO dilakukan 100% menggunakan HTTP Client (API endpoint), **tanpa** melakukan koneksi langsung ke *database* eksternal kampus.

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

* **Tim IT / PDSI UNUJA:** SIMAKAR bergantung pada PDSI untuk membuka akses integrasi tambahan (API REST) menuju modul MO-KEL, SKPI, dan UKom.

### Risks & Mitigation

* **Risiko Akses Data (Tinggi):** PDSI menolak atau menunda pembukaan akses API.
* *Mitigasi:* Sistem telah dirancang dengan arsitektur hibrida. Jika API belum tersedia, mahasiswa tetap dapat menginput data riwayat tersebut secara manual melalui antarmuka CRUD yang telah disediakan.

---

## Timeline & Milestones

### Development Phases

*(Catatan: Tenggat waktu spesifik belum diketahui, mengikuti perkembangan implementasi di lapangan).*

* **Fase 1 (Discovery & Setup):** [TBD]
* Setup Laravel 13, Octane, DB MySQL lokal, Vite, Tailwind v4.
* Konfigurasi autentikasi SSO, RBAC (Gate), & *Activity Log* (Spatie).


* **Fase 2 (Integration & Core CV):** [TBD]
* Integrasi penarikan profil dan transkrip (SSO).
* Pembangunan form CRUD Portofolio Hibrida (Mahasiswa).
* Implementasi fitur CV ATS otomatis (DOMPDF).


* **Fase 3 (Multi-Role & Expansion):** [TBD]
* Dasbor Multi-Role (Dosen, Kaprodi, Dekan, Admin).
* Integrasi lanjutan untuk MO-KEL, SKPI, UKom (menunggu ketersediaan API).


* **Fase 4 (Tracer Study & QA):** [TBD]
* Modul direktori dan formulir pelacakan alumni.
* *Feature Testing* menyeluruh.


* **Full Launch:** [TBD]

---