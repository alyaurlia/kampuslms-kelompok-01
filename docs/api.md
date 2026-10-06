# Dokumentasi API KampusLMS

Dokumentasi lengkap dengan contoh `curl` untuk aplikasi **KampusLMS**.

## 1. Informasi Umum

### Base URL

```text
http://127.0.0.1:8000/api/v1
```

### Autentikasi

API menggunakan **Laravel Sanctum** dengan Bearer Token.

Untuk endpoint yang membutuhkan autentikasi, tambahkan header:

```http
Authorization: Bearer <TOKEN>
Accept: application/json
```

Token diperoleh melalui:

```http
POST /auth/login
```

### Role

API menggunakan tiga role utama:

- `admin`
- `dosen`
- `mahasiswa`

Hak akses endpoint mengikuti role dan kepemilikan data.

### Rate Limiting

- Endpoint API terautentikasi: **60 request/menit** per user/token.
- Login: **5 request/menit** per kombinasi email + IP, dengan batas tambahan **20 request/menit per IP**.
- Jika batas terlampaui, API mengembalikan HTTP `429`.

Contoh:

```json
{
  "message": "Terlalu banyak permintaan. Coba lagi sebentar lagi."
}
```

---

## 2. Format Response

### Response sukses tunggal

Resource tunggal menggunakan format:

```json
{
  "data": {
    "id": 1
  }
}
```

### Response sukses koleksi

Endpoint koleksi menggunakan pagination dan format:

```json
{
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "total": 47
  }
}
```

### Error validasi — 422

```json
{
  "message": "Data yang diberikan tidak valid.",
  "errors": {
    "score": [
      "Nilai wajib diisi."
    ]
  }
}
```

### Tidak terautentikasi — 401

```json
{
  "message": "Anda belum terautentikasi."
}
```

### Tidak berhak — 403

```json
{
  "message": "Anda tidak memiliki akses ke sumber daya ini."
}
```

### Resource tidak ditemukan — 404

```json
{
  "message": "Sumber daya tidak ditemukan."
}
```

### Rate limit — 429

```json
{
  "message": "Terlalu banyak permintaan. Coba lagi sebentar lagi."
}
```

---

# 3. Daftar Endpoint

| No. | Method | Endpoint | Akses |
|---|---|---|---|
| 1 | POST | `/auth/login` | Publik |
| 2 | POST | `/auth/logout` | Auth |
| 3 | GET | `/me` | Auth |
| 4 | GET | `/courses` | Auth |
| 5 | GET | `/courses/{id}` | Auth + scope |
| 6 | GET | `/courses/{id}/materials` | Auth + scope |
| 7 | GET | `/courses/{id}/assignments` | Auth + scope |
| 8 | POST | `/assignments` | Dosen |
| 9 | PUT/PATCH | `/assignments/{id}` | Dosen pemilik |
| 10 | DELETE | `/assignments/{id}` | Dosen pemilik |
| 11 | GET | `/assignments/{id}/submissions` | Auth + scope |
| 12 | POST | `/assignments/{id}/submissions` | Mahasiswa terdaftar |
| 13 | PUT | `/submissions/{id}/grade` | Dosen pemilik |
| 14 | GET | `/notifications` | Auth |
| 15 | POST | `/notifications/{id}/read` | Auth |

---

# 4. Detail Endpoint

## 4.1 Login

### `POST /auth/login`

Login bersifat publik dan mengembalikan Sanctum Bearer Token.

### Request

```bash
curl -X POST "$BASE_URL/auth/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "mahasiswa@example.com",
    "password": "password",
    "device_name": "Postman"
  }'
```

### Body

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `email` | string/email | Ya | Email akun |
| `password` | string | Ya | Password akun |
| `device_name` | string | Tidak | Nama perangkat/token |

### Response `200 OK`

```json
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxx",
  "user": {
      "id": 2,
      "name": "Mahasiswa",
      "email": "mahasiswa@example.com",
      "role": "mahasiswa",
      "nim_nip": "20260001"
    }
  }
}
```

Simpan nilai `token` untuk digunakan pada endpoint yang membutuhkan autentikasi.

### Validasi

```bash
curl -X POST "$BASE_URL/auth/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "email-tidak-valid",
    "password": ""
  }'
```

Response:

```http
422 Unprocessable Entity
```

---

## 4.2 Logout

### `POST /auth/logout`

Mencabut token Sanctum yang sedang digunakan.

### Request

```bash
curl -X POST "$BASE_URL/auth/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Response

```http
204 No Content
```

---

## 4.3 Profil User

### `GET /me`

Mengembalikan profil user yang sedang login beserta role.

### Request

```bash
curl -X GET "$BASE_URL/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Response `200 OK`

```json
{
  "data": {
    "id": 2,
    "name": "Mahasiswa",
    "email": "mahasiswa@example.com",
    "role": "mahasiswa",
    "nim_nip": "20260001"
  }
}
```

---

# 5. Courses

## 5.1 Daftar Mata Kuliah

### `GET /courses`

Daftar mata kuliah disaring berdasarkan role:

- admin: seluruh mata kuliah
- dosen: mata kuliah yang diajar
- mahasiswa: mata kuliah yang diikuti

### Query Parameter

| Parameter | Keterangan |
|---|---|
| `q` | Pencarian berdasarkan nama atau kode mata kuliah |
| `status` | Filter status |
| `page` | Nomor halaman |
| `per_page` | Jumlah data per halaman, maksimal 100 |

### Request

```bash
curl -X GET "$BASE_URL/courses?page=1&per_page=15" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

Contoh pencarian:

```bash
curl -X GET "$BASE_URL/courses?q=Pemrograman&status=active&page=1&per_page=10" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Response `200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "code": "IF101",
      "name": "Pemrograman Web",
      "description": "Mata kuliah pemrograman web",
      "sks": 3,
      "status": "active",
      "lecturer": {
          "id": 1,
          "name": "Dosen",
          "email": "dosen@example.com",
          "role": "dosen",
          "nim_nip": "19800101"
        }
      },
      "counts": {
        "materials": 5,
        "assignments": 3
      },
      "created_at": "2026-10-01T08:00:00+08:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "total": 1
  }
}
```

---

## 5.2 Detail Mata Kuliah

### `GET /courses/{id}`

Mengembalikan detail mata kuliah dan jumlah materi serta tugas.

### Request

```bash
curl -X GET "$BASE_URL/courses/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Response `200 OK`

```json
{
  "data": {
    "id": 1,
    "code": "IF101",
    "name": "Pemrograman Web",
    "description": "Mata kuliah pemrograman web",
    "sks": 3,
    "status": "active",
    "lecturer": {
        "id": 1,
        "name": "Dosen",
        "email": "dosen@example.com",
        "role": "dosen",
        "nim_nip": "19800101"
      }
    },
    "counts": {
      "materials": 5,
      "assignments": 3
    },
    "created_at": "2026-10-01T08:00:00+08:00"
  }
}
```

---

## 5.3 Daftar Materi

### `GET /courses/{id}/materials`

Hanya user yang memiliki akses terhadap mata kuliah yang dapat melihat materi.

### Query Parameter

- `page`
- `per_page`

### Request

```bash
curl -X GET "$BASE_URL/courses/1/materials?page=1&per_page=15" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Response `200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "course_id": 1,
      "uploaded_by": 1,
      "title": "Pengenalan Laravel",
      "description": "Materi pengenalan Laravel",
      "type": "file",
      "original_name": "materi-laravel.pdf",
      "file_size": 1024000,
      "mime_type": "application/pdf",
      "external_url": null,
      "created_at": "2026-10-01T08:00:00+08:00",
      "updated_at": "2026-10-01T08:00:00+08:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "total": 1
  }
}
```

`file_path` tidak dikembalikan karena merupakan lokasi internal storage.

---

# 6. Assignments

## 6.1 Daftar Tugas dalam Mata Kuliah

### `GET /courses/{id}/assignments`

### Query Parameter

| Parameter | Keterangan |
|---|---|
| `status` | `draft` atau `published` |
| `page` | Nomor halaman |
| `per_page` | Jumlah data per halaman |

### Request

```bash
curl -X GET "$BASE_URL/courses/1/assignments?status=published&page=1&per_page=15" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Response `200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "course_id": 1,
      "title": "Tugas API Laravel",
      "instructions": "Buat REST API menggunakan Laravel.",
      "due_at": "2026-10-20T23:59:00+08:00",
      "max_score": 100,
      "allow_late": true,
      "status": "published",
      "created_at": "2026-10-01T08:00:00+08:00",
      "updated_at": "2026-10-01T08:00:00+08:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "total": 1
  }
}
```

Mahasiswa tidak melihat assignment dengan status `draft`.

---

## 6.2 Membuat Tugas

### `POST /assignments`

Hanya dosen yang dapat membuat tugas, dan dosen harus menjadi pengampu mata kuliah tersebut.

### Request

```bash
curl -X POST "$BASE_URL/assignments" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $DOSEN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "course_id": 1,
    "title": "Tugas REST API",
    "instructions": "Buat REST API menggunakan Laravel.",
    "due_at": "2026-10-20 23:59:00",
    "max_score": 100,
    "allow_late": true,
    "status": "published"
  }'
```

### Response `201 Created`

```json
{
  "data": {
    "id": 10,
    "course_id": 1,
    "title": "Tugas REST API",
    "instructions": "Buat REST API menggunakan Laravel.",
    "due_at": "2026-10-20T15:59:00+00:00",
    "max_score": 100,
    "allow_late": true,
    "status": "published",
    "created_at": "2026-10-06T12:00:00+00:00",
    "updated_at": "2026-10-06T12:00:00+00:00"
  }
}
```

---

## 6.3 Mengubah Tugas

### `PUT /assignments/{id}`

Hanya dosen pemilik/pengampu mata kuliah yang dapat mengubah tugas.

### Request PUT

Semua field utama wajib dikirim.

```bash
curl -X PUT "$BASE_URL/assignments/10" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $DOSEN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Tugas REST API Laravel",
    "instructions": "Buat dan dokumentasikan REST API.",
    "due_at": "2026-10-22 23:59:00",
    "max_score": 100,
    "allow_late": true,
    "status": "published"
  }'
```

### PATCH

Untuk perubahan sebagian:

```bash
curl -X PATCH "$BASE_URL/assignments/10" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $DOSEN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "published"
  }'
```

### Response `200 OK`

```json
{
  "data": {
    "id": 10,
    "course_id": 1,
    "title": "Tugas REST API Laravel",
    "instructions": "Buat dan dokumentasikan REST API.",
    "due_at": "2026-10-22T15:59:00+00:00",
    "max_score": 100,
    "allow_late": true,
    "status": "published",
    "created_at": "2026-10-06T12:00:00+00:00",
    "updated_at": "2026-10-06T12:05:00+00:00"
  }
}
```

---

## 6.4 Menghapus Tugas

### `DELETE /assignments/{id}`

Hanya dosen pemilik/pengampu yang dapat menghapus tugas.

### Request

```bash
curl -X DELETE "$BASE_URL/assignments/10" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $DOSEN_TOKEN"
```

### Response

```http
204 No Content
```

---

# 7. Submissions

## 7.1 Melihat Submission

### `GET /assignments/{id}/submissions`

Akses:

- admin: seluruh submission
- dosen: submission tugas pada mata kuliah yang dia ampu
- mahasiswa: hanya submission miliknya pada mata kuliah yang diikutinya

### Query Parameter

- `page`
- `per_page`

### Request dosen

```bash
curl -X GET "$BASE_URL/assignments/10/submissions?page=1&per_page=15" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $DOSEN_TOKEN"
```

### Response `200 OK`

```json
{
  "data": [
    {
      "id": 25,
      "assignment_id": 10,
      "student": {
          "id": 2,
          "name": "Mahasiswa",
          "email": "mahasiswa@example.com",
          "role": "mahasiswa",
          "nim_nip": "20260001"
        }
      },
      "original_name": "tugas-api.pdf",
      "file_size": 1024000,
      "note": null,
      "submitted_at": "2026-10-10T10:00:00+08:00",
      "is_late": false,
      "grade": null
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "total": 1
  }
}
```

`file_path` tidak dikembalikan dalam response.

---

## 7.2 Mengumpulkan Tugas

### `POST /assignments/{id}/submissions`

Hanya mahasiswa yang terdaftar pada mata kuliah tugas yang dapat mengumpulkan.

Request menggunakan `multipart/form-data`.

### Request

```bash
curl -X POST "$BASE_URL/assignments/10/submissions" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $MAHASISWA_TOKEN" \
  -F "file=@/path/to/tugas-api.pdf"
```

Ukuran file maksimal **10 MB**.

### Response `201 Created`

```json
{
  "data": {
    "id": 25,
    "assignment_id": 10,
    "student": {
        "id": 2,
        "name": "Mahasiswa",
        "email": "mahasiswa@example.com",
        "role": "mahasiswa",
        "nim_nip": "20260001"
      }
    },
    "original_name": "tugas-api.pdf",
    "file_size": 1024000,
    "note": null,
    "submitted_at": "2026-10-10T10:00:00+08:00",
    "is_late": false,
    "grade": null
  }
}
```

### Submission duplikat

Satu mahasiswa hanya dapat memiliki satu submission untuk satu assignment.

Jika mahasiswa mengirim submission yang sama lagi:

```http
409 Conflict
```

```json
{
  "message": "Anda sudah mengumpulkan tugas ini."
}
```

---

# 8. Grading

## 8.1 Memberikan atau Mengubah Nilai

### `PUT /submissions/{id}/grade`

Hanya dosen pengampu mata kuliah assignment tersebut yang dapat memberi atau mengubah nilai.

Endpoint menggunakan `updateOrCreate` karena satu submission hanya memiliki satu grade.

### Request

```bash
curl -X PUT "$BASE_URL/submissions/25/grade" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $DOSEN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "score": 85,
    "feedback": "Tugas sudah baik."
  }'
```

### Response pertama kali — `201 Created`

```json
{
  "data": {
    "id": 1,
    "submission_id": 25,
    "score": 85,
    "feedback": "Tugas sudah baik.",
    "graded_by": 1,
    "graded_at": "2026-10-10T12:00:00+08:00"
  }
}
```

### Penilaian ulang — `200 OK`

Panggil endpoint yang sama:

```bash
curl -X PUT "$BASE_URL/submissions/25/grade" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $DOSEN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "score": 90,
    "feedback": "Tugas sudah diperbaiki dengan baik."
  }'
```

Response:

```http
200 OK
```

Nilai pada grade yang sama diperbarui, bukan membuat grade baru.

### Validasi score

Nilai harus berada pada rentang `0` sampai `max_score` assignment.

Contoh:

```bash
curl -X PUT "$BASE_URL/submissions/25/grade" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $DOSEN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "score": 150,
    "feedback": "Testing"
  }'
```

Response:

```http
422 Unprocessable Entity
```

```json
{
  "message": "Data yang diberikan tidak valid.",
  "errors": {
    "score": [
      "Nilai tidak boleh melebihi nilai maksimum tugas."
    ]
  }
}
```

---

# 9. Notifications

## 9.1 Daftar Notifikasi

### `GET /notifications`

Hanya notifikasi milik user yang sedang login yang dikembalikan.

### Query Parameter

- `page`
- `per_page`

### Request

```bash
curl -X GET "$BASE_URL/notifications?page=1&per_page=15" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Response `200 OK`

```json
{
  "data": [
    {
      "id": "uuid-notification",
      "type": "App\\Notifications\\AssignmentCreated",
      "data": {
        "assignment_id": 10,
        "title": "Tugas baru tersedia"
      },
      "read_at": null,
      "created_at": "2026-10-06T12:00:00+00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "total": 1
  }
}
```

---

## 9.2 Menandai Notifikasi Sudah Dibaca

### `POST /notifications/{id}/read`

User hanya dapat menandai notifikasi miliknya sendiri sebagai sudah dibaca.

### Request

```bash
curl -X POST "$BASE_URL/notifications/uuid-notification/read" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

### Response `200 OK`

```json
{
  "data": {
    "id": "uuid-notification",
    "type": "App\\Notifications\\AssignmentCreated",
    "data": {
      "assignment_id": 10,
      "title": "Tugas baru tersedia"
    },
    "read_at": "2026-10-06T12:10:00+00:00",
    "created_at": "2026-10-06T12:00:00+00:00"
  }
}
```

---

# 10. Contoh Variabel curl

Agar contoh di atas lebih mudah digunakan di terminal, buat variabel:

```bash
BASE_URL="http://127.0.0.1:8000/api/v1"
TOKEN="PASTE_TOKEN_DI_SINI"
DOSEN_TOKEN="PASTE_TOKEN_DOSEN_DI_SINI"
MAHASISWA_TOKEN="PASTE_TOKEN_MAHASISWA_DI_SINI"
```

Contoh:

```bash
curl -X GET "$BASE_URL/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```

Untuk Windows PowerShell:

```powershell
$BASE_URL="http://127.0.0.1:8000/api/v1"
$TOKEN="PASTE_TOKEN_DI_SINI"
```

Kemudian:

```powershell
curl.exe -X GET "$BASE_URL/me" `
  -H "Accept: application/json" `
  -H "Authorization: Bearer $TOKEN"
```

---

# 11. Ringkasan Status HTTP

| Status | Kondisi |
|---|---|
| `200` | Request berhasil |
| `201` | Resource baru berhasil dibuat |
| `204` | Berhasil tanpa response body |
| `401` | Belum login / token tidak valid |
| `403` | User login tetapi tidak memiliki hak akses |
| `404` | Resource tidak ditemukan |
| `409` | Konflik, misalnya submission sudah pernah dibuat |
| `422` | Data request tidak valid |
| `429` | Rate limit terlampaui |

## Catatan khusus grading

Endpoint:

```text
PUT /submissions/{id}/grade
```

menggunakan `updateOrCreate`.

Karena `grades.submission_id` bersifat `unique`:

- penilaian pertama → `201 Created`
- penilaian ulang → `200 OK`

Dengan demikian pemanggilan endpoint grading berulang tidak membuat data grade duplikat.

---

# 12. API Resource

API menggunakan Laravel API Resource untuk membatasi data yang dikirim ke client.

Resource yang digunakan:

- `UserResource`
- `CourseResource`
- `MaterialResource`
- `AssignmentResource`
- `SubmissionResource`
- `GradeResource`
- `NotificationResource`

Collection menggunakan resource collection dengan metadata pagination:

```json
{
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "total": 0
  }
}
```

Data internal seperti `password`, `remember_token`, dan lokasi internal `file_path` tidak dikirim melalui API Resource.

---

# 13. Keamanan dan Akses

API menggunakan beberapa lapisan pembatasan:

1. **Laravel Sanctum** untuk autentikasi Bearer Token.
2. **Role-based access** untuk membatasi aksi berdasarkan role.
3. **Scope/ownership** untuk memastikan user hanya mengakses mata kuliah, tugas, submission, dan notifikasi yang menjadi haknya.
4. **Form Request validation** untuk validasi input.
5. **Rate limiting** untuk mengurangi penyalahgunaan endpoint.
6. **API Resource** untuk membatasi data yang diekspos.
7. File submission disimpan pada storage internal dan `file_path` tidak dikirim ke client.
8. Submission dibatasi satu data per mahasiswa untuk setiap assignment.
9. Grade menggunakan `updateOrCreate` sehingga penilaian ulang tidak menghasilkan grade duplikat.
