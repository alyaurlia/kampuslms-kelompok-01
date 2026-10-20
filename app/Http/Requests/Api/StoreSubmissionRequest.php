<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\StoreSubmissionRequest as BaseStoreSubmissionRequest;

// Aturan, pesan, dan otorisasi sama persis dengan versi web supaya
// web dan API tidak pernah berbeda perilaku. Respons error otomatis
// berupa JSON (403 / 422) karena Laravel mendeteksi request API.
class StoreSubmissionRequest extends BaseStoreSubmissionRequest
{
}