#!/usr/bin/env bash
set -u

# ============================================================
# KampusLMS - Authorization API Test (Prompt C)
# Base API: /api/v1
#
# Cara pakai:
#   1. Jalankan Laravel:
#      php artisan serve
#   2. Isi token dan ID yang sesuai dengan database:
#
#      export BASE_URL="http://127.0.0.1:8000/api/v1"
#      export DOSEN_TOKEN="..."
#      export MAHASISWA_TOKEN="..."
#      export MAHASISWA2_TOKEN="..."       # mahasiswa yang TIDAK terdaftar
#      export OTHER_DOSEN_TOKEN="..."      # dosen yang BUKAN pengampu
#      export COURSE_ID="1"
#      export OTHER_COURSE_ID="2"
#      export ASSIGNMENT_ID="10"
#      export SUBMISSION_ID="25"
#      export NOTIFICATION_ID="uuid-notification"
#
#   3. Jalankan:
#      bash scripts/test-api.sh
#
# Catatan:
# - Script ini fokus pada OTORISASI. Request yang dipakai untuk
#   menguji penolakan dipilih agar tidak mengubah data.
# - Token dan ID sengaja menggunakan environment variable agar
#   tidak menyimpan credential/data project di repository.
# ============================================================

BASE_URL="${BASE_URL:-http://127.0.0.1:8000/api/v1}"

DOSEN_TOKEN="${DOSEN_TOKEN:-}"
MAHASISWA_TOKEN="${MAHASISWA_TOKEN:-}"
MAHASISWA2_TOKEN="${MAHASISWA2_TOKEN:-}"
OTHER_DOSEN_TOKEN="${OTHER_DOSEN_TOKEN:-}"

COURSE_ID="${COURSE_ID:-1}"
OTHER_COURSE_ID="${OTHER_COURSE_ID:-2}"
ASSIGNMENT_ID="${ASSIGNMENT_ID:-10}"
SUBMISSION_ID="${SUBMISSION_ID:-25}"
NOTIFICATION_ID="${NOTIFICATION_ID:-uuid-notification}"

PASS=0
FAIL=0
SKIP=0

require_var() {
    local name="$1"
    if [ -z "${!name:-}" ]; then
        echo "SKIP: $name belum diisi"
        SKIP=$((SKIP + 1))
        return 1
    fi
    return 0
}

get_status() {
    local method="$1"
    local url="$2"
    local token="${3:-}"
    local data="${4:-}"

    if [ -n "$token" ]; then
        if [ -n "$data" ]; then
            curl -sS -o /dev/null -w "%{http_code}" \
                -X "$method" "$url" \
                -H "Accept: application/json" \
                -H "Authorization: Bearer $token" \
                -H "Content-Type: application/json" \
                -d "$data"
        else
            curl -sS -o /dev/null -w "%{http_code}" \
                -X "$method" "$url" \
                -H "Accept: application/json" \
                -H "Authorization: Bearer $token"
        fi
    else
        if [ -n "$data" ]; then
            curl -sS -o /dev/null -w "%{http_code}" \
                -X "$method" "$url" \
                -H "Accept: application/json" \
                -H "Content-Type: application/json" \
                -d "$data"
        else
            curl -sS -o /dev/null -w "%{http_code}" \
                -X "$method" "$url" \
                -H "Accept: application/json"
        fi
    fi
}

check() {
    local name="$1"
    local expected="$2"
    local actual="$3"

    if [ "$actual" = "$expected" ]; then
        echo "PASS  [$actual] $name"
        PASS=$((PASS + 1))
    else
        echo "FAIL  [expected $expected, got $actual] $name"
        FAIL=$((FAIL + 1))
    fi
}

echo "============================================================"
echo "KampusLMS - Authorization API Test"
echo "BASE_URL: $BASE_URL"
echo "============================================================"
echo

# ------------------------------------------------------------
# 1. Endpoint terlindungi tanpa token -> 401
# ------------------------------------------------------------
status=$(get_status "GET" "$BASE_URL/me")
check "GET /me tanpa token harus ditolak" "401" "$status"

# ------------------------------------------------------------
# 2. Mahasiswa tidak boleh membuat assignment -> 403
# ------------------------------------------------------------
if require_var MAHASISWA_TOKEN; then
    payload='{
        "course_id": '"$COURSE_ID"',
        "title": "Authorization Test",
        "instructions": "Test otorisasi",
        "due_at": "2026-12-31 23:59:00",
        "max_score": 100,
        "allow_late": true,
        "status": "draft"
    }'

    status=$(get_status "POST" "$BASE_URL/assignments" "$MAHASISWA_TOKEN" "$payload")
    check "Mahasiswa POST /assignments harus ditolak" "403" "$status"
fi

# ------------------------------------------------------------
# 3. Mahasiswa tidak boleh memberi nilai -> 403
# ------------------------------------------------------------
if require_var MAHASISWA_TOKEN; then
    payload='{
        "score": 80,
        "feedback": "Authorization test"
    }'

    status=$(get_status "PUT" "$BASE_URL/submissions/$SUBMISSION_ID/grade" "$MAHASISWA_TOKEN" "$payload")
    check "Mahasiswa PUT /submissions/{id}/grade harus ditolak" "403" "$status"
fi

# ------------------------------------------------------------
# 4. Dosen tidak boleh submit tugas -> 403
# ------------------------------------------------------------
if require_var DOSEN_TOKEN; then
    status=$(get_status "POST" "$BASE_URL/assignments/$ASSIGNMENT_ID/submissions" "$DOSEN_TOKEN")
    check "Dosen POST /assignments/{id}/submissions harus ditolak" "403" "$status"
fi

# ------------------------------------------------------------
# 5. Mahasiswa yang tidak terdaftar tidak boleh mengakses
#    course -> 403
# ------------------------------------------------------------
if require_var MAHASISWA2_TOKEN; then
    status=$(get_status "GET" "$BASE_URL/courses/$OTHER_COURSE_ID" "$MAHASISWA2_TOKEN")
    check "Mahasiswa yang tidak punya akses ke course harus ditolak" "403" "$status"
fi

# ------------------------------------------------------------
# 6. Dosen yang bukan pemilik tidak boleh mengubah assignment
#    -> 403
# ------------------------------------------------------------
if require_var OTHER_DOSEN_TOKEN; then
    payload='{
        "status": "published"
    }'

    status=$(get_status "PATCH" "$BASE_URL/assignments/$ASSIGNMENT_ID" "$OTHER_DOSEN_TOKEN" "$payload")
    check "Dosen bukan pemilik PATCH /assignments/{id} harus ditolak" "403" "$status"
fi

# ------------------------------------------------------------
# 7. Dosen yang bukan pemilik tidak boleh menghapus assignment
#    -> 403
# ------------------------------------------------------------
if require_var OTHER_DOSEN_TOKEN; then
    status=$(get_status "DELETE" "$BASE_URL/assignments/$ASSIGNMENT_ID" "$OTHER_DOSEN_TOKEN")
    check "Dosen bukan pemilik DELETE /assignments/{id} harus ditolak" "403" "$status"
fi

# ------------------------------------------------------------
# 8. Dosen yang bukan pengampu tidak boleh melihat submissions
#    -> 403
# ------------------------------------------------------------
if require_var OTHER_DOSEN_TOKEN; then
    status=$(get_status "GET" "$BASE_URL/assignments/$ASSIGNMENT_ID/submissions" "$OTHER_DOSEN_TOKEN")
    check "Dosen bukan pengampu GET /assignments/{id}/submissions harus ditolak" "403" "$status"
fi

# ------------------------------------------------------------
# 9. User hanya boleh menandai notifikasi miliknya.
#    ID di bawah harus menggunakan notification milik user lain.
# ------------------------------------------------------------
if require_var MAHASISWA_TOKEN && [ "$NOTIFICATION_ID" != "uuid-notification" ]; then
    status=$(get_status "POST" "$BASE_URL/notifications/$NOTIFICATION_ID/read" "$MAHASISWA_TOKEN")
    check "Menandai notifikasi milik user lain harus ditolak" "403" "$status"
else
    echo "SKIP  [notification] Isi NOTIFICATION_ID dengan UUID notifikasi milik user lain untuk menguji ownership."
    SKIP=$((SKIP + 1))
fi

echo
echo "============================================================"
echo "HASIL"
echo "PASS : $PASS"
echo "FAIL : $FAIL"
echo "SKIP : $SKIP"
echo "============================================================"

if [ "$FAIL" -gt 0 ]; then
    exit 1
fi

exit 0