# KampusLMS - Kelompok 01

Sistem manajemen pembelajaran kampus berbasis web menggunakan Laravel.

## Anggota Kelompok

| Nama | NIM |
|---|---|
| Ade Putri Amanda     | 10241002 |
| Adelia Isra Ekaputri | 10241004 |
| Adelia Cyntia Renata | 10241006 |
| Alya Auralia         | 10241008 | 
| Andika Putra Pratama | 10241010 | 

## Cara Instalasi

1. Clone repository
```bash
   git clone https://github.com/alyaurlia/kampuslms-kelompok-01.git
   cd kampuslms-kelompok-01
```

2. Install dependency PHP
```bash
   composer install
```

3. Salin file environment
```bash
   copy .env.example .env
```

4. Generate application key
```bash
   php artisan key:generate
```

5. Jalankan migrasi database (jika ada)
```bash
   php artisan migrate
```

6. Jalankan server
```bash
   php artisan serve
```

7. Buka di browser: `http://127.0.0.1:8000`

## Pembagian Peran

| Nama | Tanggung Jawab |
|---|---|
| Ade Putri Amanda | Frontend |
| Adelia Isra Ekaputri | Backend |
| Adelia Cyntia Renata | UI/UX |
| Alya Auralia | Database |
| Andika Putra Pratama | Backend |