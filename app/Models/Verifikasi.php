<?php

namespace App\Models;

use App\Models\Traits\TenantAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    use HasFactory, TenantAware;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'verifikasi';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'pengajuan_kgb_id',
        'verifikator_id',
        'jenis_verifikasi', // dinas or kabupaten
        'status', // pending, verified, rejected
        'catatan',
        'tanggal_verifikasi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_verifikasi' => 'datetime',
    ];

    /**
     * Get the tenant that owns the verifikasi.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the pengajuan_kgb that owns the verifikasi.
     */
    public function pengajuanKgb()
    {
        return $this->belongsTo(PengajuanKgb::class);
    }

    /**
     * Get the user who performed the verifikasi.
     */
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}