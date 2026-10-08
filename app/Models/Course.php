<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'sks',
        'lecturer_id',
        'status',
    ];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('enrolled_at')
            ->withTimestamps();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

     public function isLecturedBy(User $u): bool
    {
        return $u->role === 'dosen' && (int) $this->lecturer_id === (int) $u->id;
    }

    public function hasStudent(User $u): bool
    {
        return $u->role === 'mahasiswa'
            && $this->students()->whereKey($u->id)->exists();
    }

    // Admin atau dosen pengampu
    public function isManagedBy(User $u): bool
    {
        return $u->role === 'admin' || $this->isLecturedBy($u);
    }


/** dosen: MK yang diajar; mahasiswa: MK yang diikuti. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match ($user->role) {
            'admin' => $query,
            'dosen' => $query->where('lecturer_id', $user->id),
            'mahasiswa' => $query->whereHas('students', fn (Builder $q) => $q->whereKey($user->id)),
            default => $query->whereRaw('1 = 0'),
        };
    }
}