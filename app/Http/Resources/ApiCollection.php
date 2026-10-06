<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class ApiCollection extends ResourceCollection
{
    /**
     * Kontrak dosen: koleksi hanya berisi `data` dan `meta`
     * {current_page, last_page, total}. Bawaan Laravel menambah `links`
     * dan meta lain, jadi dibuang di sini.
     */
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'last_page' => $paginated['last_page'],
                'total' => $paginated['total'],
            ],
        ];
    }
}