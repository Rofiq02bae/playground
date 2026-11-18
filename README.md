# FunFarm Platform
### Sistem Informasi Pertanian, Peternakan, dan Marketplace Terintegrasi

## 1. Deskripsi Proyek

FunFarm Platform merupakan aplikasi berbasis web yang dirancang untuk mengintegrasikan kebutuhan pertanian dan peternakan dalam satu sistem.

Aplikasi ini menyediakan layanan penyewaan alat pertanian, manajemen aktivitas sawah, pencatatan biaya operasional, marketplace produk pertanian, penjualan hasil panen, monitoring IoT, serta penjualan perlengkapan peternakan.

Tujuan utama aplikasi adalah membantu petani mengelola kegiatan pertanian secara lebih terstruktur, mempermudah akses terhadap layanan dan kebutuhan pertanian, serta menyediakan sarana transaksi digital.

## 2. Tujuan Pengembangan

- Digitalisasi pengelolaan aktivitas pertanian dan peternakan.
- Mempermudah penyewaan alat pertanian.
- Menyediakan sistem harga khusus bagi pelanggan berlangganan.
- Membantu petani mencatat biaya operasional dan aktivitas sawah.
- Menyediakan marketplace produk pertanian dan peternakan.
- Memfasilitasi pemasaran hasil panen.
- Menyediakan monitoring kondisi lahan melalui teknologi IoT.

## 3. Modul Utama Aplikasi

### A. Blog dan Artikel

Modul informasi untuk mempublikasikan artikel seputar pertanian, peternakan, dan teknologi.

**Fitur:**
- Create, Read, Update, Delete (CRUD) artikel.
- Kategori artikel.
- Upload thumbnail dan gambar.
- Judul dan deskripsi artikel.
- Slug URL otomatis.
- Status draft dan publikasi.
- Pencarian artikel.

Kategori artikel:
- Pertanian.
- Peternakan.
- Teknologi Pertanian.
- Informasi Umum.

## B. Service Pertanian

Modul Service Pertanian merupakan sistem manajemen proyek pertanian berbasis klien (client). Sistem digunakan untuk mencatat seluruh proses pengelolaan lahan, penggunaan layanan, kebutuhan operasional, pengeluaran, hingga perhitungan pembagian hasil panen.

Konsep utamanya adalah setiap klien memiliki satu atau beberapa proyek pertanian. Setiap proyek memiliki profil lahan dan pencatatan aktivitas yang dapat ditambahkan sesuai kebutuhan selama proyek berlangsung.

### 1. Manajemen Klien

Admin dapat mendaftarkan klien yang menggunakan layanan perusahaan.

Data klien:
- Nama klien.
- Nomor telepon.
- Alamat.
- Informasi tambahan.

Setiap klien dapat memiliki beberapa proyek pertanian.

### 2. Manajemen Proyek Pertanian

Ketika klien menggunakan layanan perusahaan, admin membuat proyek pertanian baru.

Setiap proyek menjadi pusat pencatatan seluruh aktivitas dan biaya pertanian.

Data profil proyek:
- Nama proyek.
- Nama klien.
- Nama atau identitas lahan.
- Luas sawah.
- Lokasi sawah.
- Koordinat geografis.
- Jenis tanaman.
- Varietas tanaman.
- Tanggal mulai proyek.
- Estimasi tanggal panen.
- Status proyek.

Status proyek:
- Persiapan.
- Berjalan.
- Menunggu panen.
- Selesai.
- Dibatalkan.

### 3. Pencatatan Layanan dan Kebutuhan Proyek

Setiap proyek memiliki pencatatan layanan dan kebutuhan operasional yang bersifat opsional.

Admin dapat menambahkan komponen sesuai layanan atau kebutuhan yang digunakan klien.

#### A. Penyewaan Drone Semprot

Jika proyek menggunakan layanan drone semprot, admin dapat menambahkan data penyewaan ke proyek.

Data pencatatan:
- Tanggal penggunaan.
- Luas area penyemprotan.
- Jenis layanan.
- Biaya penyewaan.
- Keterangan.

#### B. Penyewaan Alat Tanam

Jika proyek menggunakan mesin tanam, admin dapat menambahkan pencatatan penggunaan alat.

Data pencatatan:
- Tanggal penggunaan.
- Jenis alat.
- Luas area pengerjaan.
- Biaya penyewaan.
- Keterangan.

#### C. Penggunaan Pupuk

Admin dapat menambahkan pupuk yang digunakan selama proyek berlangsung.

Data pencatatan:
- Nama pupuk.
- Jenis pupuk.
- Jumlah penggunaan.
- Satuan.
- Harga satuan.
- Total biaya.
- Tanggal penggunaan.

#### D. Penggunaan Obat dan Pestisida

Pencatatan kebutuhan obat tanaman selama proyek berlangsung.

Data pencatatan:
- Nama obat.
- Jenis obat.
- Jumlah penggunaan.
- Satuan.
- Harga satuan.
- Total biaya.
- Tanggal penggunaan.

#### E. Pengeluaran Lainnya

Admin dapat menambahkan pengeluaran tambahan yang tidak termasuk kategori sebelumnya.

Contoh:
- Biaya tenaga kerja.
- Biaya transportasi.
- Biaya pengolahan lahan.
- Biaya pembelian benih.
- Biaya operasional lainnya.

Setiap pencatatan memiliki tanggal, deskripsi, jumlah, dan nominal biaya.

**Catatan:** Seluruh komponen di atas bersifat opsional. Jika klien tidak menggunakan layanan tertentu, admin tidak perlu menambahkan pencatatan tersebut.

### 4. Rekapitulasi Pengeluaran Proyek

Sistem mengumpulkan seluruh biaya yang telah dicatat dalam proyek.

Kategori pengeluaran:
- Penyewaan drone.
- Penyewaan alat tanam.
- Pembelian pupuk.
- Pembelian obat dan pestisida.
- Biaya tenaga kerja.
- Pengeluaran lainnya.

Sistem menghitung total pengeluaran secara otomatis.

Contoh rekapitulasi:

| Keterangan | Total |
|---|---:|
| Sewa drone semprot | Rp500.000 |
| Sewa mesin tanam | Rp700.000 |
| Pembelian pupuk | Rp1.400.000 |
| Pembelian obat | Rp300.000 |
| Tenaga kerja | Rp500.000 |
| **Total Pengeluaran** | **Rp3.400.000** |

### 5. Pencatatan Hasil Panen

Setelah proyek memasuki masa panen, admin dapat mencatat hasil produksi pertanian.

Data hasil panen:
- Tanggal panen.
- Jenis komoditas.
- Varietas tanaman.
- Total hasil panen.
- Satuan hasil panen.
- Harga jual per satuan.
- Total nilai hasil panen.
- Keterangan.

Sistem menghitung nilai hasil panen berdasarkan jumlah produksi dan harga jual.

### 6. Perhitungan dan Pembagian Hasil

Setelah seluruh pengeluaran dan hasil panen dicatat, sistem menyediakan fitur perhitungan pembagian hasil antara perusahaan dan klien.

Komponen perhitungan:
- Total hasil penjualan panen.
- Total pengeluaran proyek.
- Hasil bersih proyek.
- Persentase pembagian hasil.
- Nominal bagian perusahaan.
- Nominal bagian klien.

Contoh simulasi:

| Keterangan | Nominal |
|---|---:|
| Total nilai hasil panen | Rp15.000.000 |
| Total pengeluaran | Rp3.400.000 |
| **Hasil bersih** | **Rp11.600.000** |

Contoh pembagian hasil dengan rasio 50:50:

| Penerima | Persentase | Nominal |
|---|---:|---:|
| Perusahaan | 50% | Rp5.800.000 |
| Klien | 50% | Rp5.800.000 |

*Persentase pembagian hasil merupakan contoh dan dapat disesuaikan berdasarkan kesepakatan antara perusahaan dan klien.*

### 7. Laporan Proyek Pertanian

Sistem menyediakan laporan lengkap untuk setiap proyek.

Informasi laporan:
- Profil klien.
- Profil lahan.
- Riwayat layanan yang digunakan.
- Riwayat penggunaan pupuk dan obat.
- Riwayat pengeluaran.
- Total biaya produksi.
- Total hasil panen.
- Nilai penjualan hasil panen.
- Hasil bersih proyek.
- Rincian pembagian hasil.

Laporan dapat digunakan sebagai dokumentasi dan transparansi pengelolaan proyek kepada klien.

### 8. Alur Kerja Service Pertanian

```text
Klien Datang
     |
     v
Registrasi / Pilih Klien
     |
     v
Buat Proyek Pertanian
     |
     v
Lengkapi Profil Proyek
(Sawah, Luas, Lokasi, Tanaman)
     |
     v
Proyek Dimulai
     |
     v
Pencatatan Kebutuhan Proyek
     |
     +----> Add Sewa Drone (Opsional)
     |
     +----> Add Alat Tanam (Opsional)
     |
     +----> Add Pupuk (Opsional)
     |
     +----> Add Obat (Opsional)
     |
     +----> Add Pengeluaran Lain (Opsional)
     |
     v
Rekapitulasi Total Pengeluaran
     |
     v
Proses Masa Panen
     |
     v
Input Hasil Panen
     |
     v
Hitung Nilai Hasil Panen
     |
     v
Hitung Hasil Bersih
     |
     v
Perhitungan Pembagian Hasil
     |
     v
Laporan Akhir Proyek
     |
     v
Proyek Selesai
```

### 9. Rancangan Database Service Pertanian

Rancangan tabel yang dibutuhkan:

| Nama Tabel | Fungsi |
|---|---|
| clients | Menyimpan data klien |
| agricultural_projects | Data proyek pertanian |
| project_fields | Profil sawah dalam proyek |
| project_rentals | Pencatatan penyewaan alat |
| project_expenses | Seluruh pengeluaran proyek |
| expense_categories | Kategori pengeluaran |
| project_activities | Riwayat aktivitas proyek |
| planting_cycles | Data siklus tanam |
| harvest_records | Pencatatan hasil panen |
| profit_sharing | Konfigurasi pembagian hasil |
| profit_sharing_details | Rincian nominal pembagian hasil |

**Relasi konseptual:**
- Satu klien dapat memiliki banyak proyek.
- Satu proyek dapat memiliki satu atau beberapa profil lahan.
- Satu proyek dapat memiliki banyak pencatatan penyewaan.
- Satu proyek dapat memiliki banyak pengeluaran.
- Satu proyek dapat memiliki banyak aktivitas.
- Satu proyek dapat memiliki satu atau beberapa catatan hasil panen.
- Satu proyek memiliki konfigurasi dan rincian pembagian hasil.

**Prinsip utama:** Seluruh transaksi dan pencatatan biaya harus terhubung dengan proyek masing-masing agar laporan pengeluaran dan pembagian hasil dapat dihitung secara akurat.

## C. Marketplace

Marketplace menyediakan fasilitas jual beli produk pertanian, peternakan, dan hasil panen.

### 1. Shop Product List

Katalog produk yang tersedia untuk pelanggan.

Kategori produk:
- Pupuk.
- Benih.
- Pestisida dan obat tanaman.
- Peralatan pertanian.
- Peralatan peternakan.

Fitur:
- Daftar produk.
- Detail produk.
- Kategori produk.
- Harga dan stok.
- Pencarian produk.
- Keranjang belanja.
- Checkout.
- Riwayat transaksi.


### 2. Penjualan produk

Fasilitas untuk memasarkan produk.

Informasi produk:
- Nama komoditas.
- Jenis dan varietas.
- Jumlah hasil panen.
- Harga per kilogram.
- Kualitas produk.
- Lokasi hasil panen.
- Foto produk.
- Ketersediaan stok.

Fitur:
- Penawaran hasil panen.
- Daftar hasil panen.
- Detail hasil panen.
- Pemesanan hasil panen.
- Pengelolaan status pesanan.

## D. Dashboard Admin

Dashboard merupakan pusat pengelolaan seluruh aktivitas aplikasi.

Informasi dashboard:
- Total pengguna.
- Total member.
- Total penyewaan.
- Pendapatan penyewaan.
- Total produk.
- Total transaksi marketplace.
- Total penawaran hasil panen.
- Grafik penjualan.
- Grafik penyewaan.
- Monitoring perangkat IoT.

### IoT Monitoring

Modul monitoring kondisi lahan menggunakan perangkat Internet of Things.

Data yang dapat dimonitor:
- Kelembapan tanah.
- Suhu lingkungan.
- Ketinggian atau kondisi air.
- Data sensor lainnya sesuai perangkat.

Fitur:
- Registrasi perangkat IoT.
- Identitas perangkat.
- Monitoring data sensor.
- Grafik pembacaan sensor.
- Riwayat data sensor.
- Status perangkat.

Integrasi:
- REST API atau MQTT.
- Database penyimpanan data sensor.
- Dashboard visualisasi menggunakan grafik.

Catatan: Integrasi IoT membutuhkan perangkat sensor dan mekanisme pengiriman data yang sesuai.

## E. Struktur Navigasi Aplikasi

```text
Dashboard
│
├── Blog & Artikel
│   ├── Daftar Artikel
│   ├── Kategori Artikel
│   └── Tambah Artikel
│
├── Service Pertanian
│   ├── Penyewaan Drone
│   ├── Penyewaan Mesin Tanam
│   ├── Data Pelanggan / Member
│   ├── Profil Sawah
│   ├── Pencatatan Pupuk
│   ├── Pencatatan Obat
│   ├── Kalender Tanam & Panen
│   └── Laporan Biaya Produksi
│
├── Marketplace
│   ├── Shop Product
│   ├── Kategori Produk
│   ├── Pesanan & Transaksi
│   ├── IoT Monitoring
│   └── Hasil Panen
│
├── Peternakan
│   ├── Katalog Alat Penetas Telur
│   ├── Manajemen Produk
│   └── Pesanan
│
└── Pengaturan
    ├── Manajemen Pengguna
    ├── Role & Permission
    ├── Pengaturan Harga
    ├── Pengaturan Member
    └── Laporan
```

## F. Rancangan Database

Berikut rancangan tabel database awal.

| Nama Tabel | Fungsi |
|---|---|
| users | Menyimpan data pengguna |
| roles | Menyimpan role pengguna |
| memberships | Menyimpan data keanggotaan |
| articles | Menyimpan artikel |
| article_categories | Kategori artikel |
| services | Data layanan penyewaan |
| service_prices | Harga normal dan member |
| rental_orders | Transaksi penyewaan |
| fields | Data profil sawah |
| planting_cycles | Siklus masa tanam |
| farm_activities | Aktivitas pertanian |
| farm_expenses | Biaya operasional |
| products | Data produk marketplace |
| product_categories | Kategori produk |
| orders | Transaksi pembelian |
| order_items | Detail transaksi |
| harvest_products | Penawaran hasil panen |
| iot_devices | Data perangkat IoT |
| sensor_readings | Data pembacaan sensor |

### Relasi Database Konseptual

- Users memiliki banyak profil sawah.
- Users dapat memiliki status membership.
- Users dapat melakukan banyak penyewaan.
- Services memiliki konfigurasi harga.
- Fields memiliki banyak siklus tanam.
- Planting cycles memiliki banyak aktivitas pertanian.
- Farm activities dapat memiliki catatan biaya.
- Products memiliki kategori.
- Orders memiliki banyak order items.
- IOT devices memiliki banyak sensor readings.

Catatan: Struktur database dapat dikembangkan dengan tabel pembayaran, pengiriman, stok, notifikasi, dan audit log sesuai kebutuhan implementasi.

## G. Alur Sistem

### Alur Penyewaan

```text
Pelanggan
    |
    v
Memilih Layanan
    |
    v
Memilih Tanggal Penyewaan
    |
    v
Sistem Memeriksa Status Member
    |
    v
Menghitung Harga
    |
    v
Mengajukan Penyewaan
    |
    v
Admin Melakukan Konfirmasi
    |
    v
Penyewaan Disetujui
    |
    v
Layanan Dilaksanakan
    |
    v
Penyewaan Selesai
```

### Alur Manajemen Sawah

```text
Pelanggan
    |
    v
Membuat Profil Sawah
    |
    v
Membuat Siklus Tanam
    |
    v
Mencatat Aktivitas Pertanian
    |
    v
Mencatat Penggunaan Pupuk dan Obat
    |
    v
Mencatat Biaya Operasional
    |
    v
Memantau Masa Tanam
    |
    v
Mencatat Hasil Panen
    |
    v
Laporan Biaya dan Hasil Produksi
```

## H. Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 12 |
| Frontend | Blade + Tailwind CSS / Bootstrap |
| Database | MySQL |
| Grafik | Chart.js |
| Peta | Leaflet + OpenStreetMap |
| IoT | MQTT / REST API |
| Authentication | Laravel Authentication |
| Version Control | Git & GitHub |

Arsitektur awal menggunakan Laravel Monolith untuk mempermudah pengembangan, pengujian, dan pemeliharaan aplikasi.

## I. Hak Akses Pengguna

Rancangan role awal:

| Role | Hak Akses |
|---|---|
| Super Admin | Mengelola seluruh sistem |
| Admin | Mengelola layanan, produk, transaksi, dan konten |
| Petani / Pelanggan | Mengelola profil sawah dan melakukan transaksi |
| Member | Mendapatkan fasilitas harga khusus |
| Pembeli | Membeli produk dan hasil panen |

Hak akses dapat diatur menggunakan Role-Based Access Control (RBAC).

## J. Tahapan Pengembangan

Pengembangan aplikasi dibagi menjadi empat fase.

### Fase 1 — Core System

- Setup Laravel.
- Setup database.
- Authentication.
- Role dan permission.
- Dashboard admin.
- Blog CRUD.
- Katalog produk.

### Fase 2 — Agricultural Service

- Sistem penyewaan drone.
- Sistem penyewaan mesin tanam.
- Harga member dan non-member.
- Manajemen profil sawah.
- Pencatatan pupuk dan obat.
- Kalender tanam dan panen.
- Laporan biaya produksi.

### Fase 3 — Marketplace

- Keranjang belanja.
- Checkout dan transaksi.
- Manajemen stok.
- Penjualan hasil panen.
- Katalog alat penetas telur.
- Pengelolaan pesanan.

### Fase 4 — Smart Farming

- Integrasi perangkat IoT.
- Monitoring sensor.
- Grafik data sensor.
- Riwayat pembacaan sensor.
- Analisis biaya dan hasil panen.

## K. Pengembangan Selanjutnya

Beberapa fitur yang dapat dikembangkan setelah sistem utama berjalan:

- Notifikasi jadwal pemupukan dan panen.
- Prediksi masa panen.
- Analisis produktivitas lahan.
- Perhitungan keuntungan per musim tanam.
- Rekomendasi penggunaan pupuk.
- Integrasi pembayaran digital.
- Integrasi pengiriman produk.
- Manajemen peternakan lebih lengkap.

## L. Kesimpulan

FunFarm Platform dirancang sebagai sistem informasi agribisnis terintegrasi yang menggabungkan layanan pertanian, peternakan, marketplace, dan teknologi IoT.

Fokus utama pengembangan adalah menyediakan kemudahan bagi petani dalam mengelola aktivitas pertanian, menyewa alat, mencatat biaya operasional, dan memasarkan hasil panen.

Pengembangan dilakukan secara bertahap dengan Laravel sebagai fondasi backend agar sistem dapat diperluas sesuai kebutuhan bisnis dan perkembangan teknologi pertanian.
