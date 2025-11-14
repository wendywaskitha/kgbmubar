<?php

namespace App\Models;

use App\Models\Traits\TenantAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkKgb extends Model
{
    use HasFactory, TenantAware;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sk_kgb';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'pegawai_id',
        'pengajuan_kgb_id',
        'no_sk',
        'tanggal_sk',
        'file_path',
        'tanggal_efektif',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_efektif' => 'date',
    ];

    /**
     * Get the tenant that owns the SK.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the pegawai that owns the SK.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Get the pengajuan_kgb that owns the SK.
     */
    public function pengajuanKgb()
    {
        return $this->belongsTo(PengajuanKgb::class);
    }
}