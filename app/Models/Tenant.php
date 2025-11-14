<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'is_active',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the pegawai records associated with the tenant.
     */
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }

    /**
     * Get the pengajuan_kgb records associated with the tenant.
     */
    public function pengajuanKgbs()
    {
        return $this->hasMany(PengajuanKgb::class);
    }

    /**
     * Get the users associated with the tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the verifikasi records associated with the tenant.
     */
    public function verifikasis()
    {
        return $this->hasMany(Verifikasi::class);
    }

    /**
     * Get the sk_kgb records associated with the tenant.
     */
    public function skKgbs()
    {
        return $this->hasMany(SkKgb::class);
    }

    /**
     * Get the dokumen_pengajuan records associated with the tenant.
     */
    public function dokumenPengajuans()
    {
        return $this->hasMany(DokumenPengajuan::class);
    }
}
