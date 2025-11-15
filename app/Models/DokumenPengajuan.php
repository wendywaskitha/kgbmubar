<?php

namespace App\Models;

use App\Models\Traits\TenantAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenPengajuan extends Model
{
    use HasFactory, TenantAware;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dokumen_pengajuan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'pengajuan_kgb_id',
        'nama_file',
        'jenis_dokumen',
        'path_file',
        'tipe_file',
        'status_verifikasi',
        'catatan_verifikasi',
        'versi',
        'verifikator_id',
        'tanggal_upload',
        'tanggal_verifikasi',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_upload' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the dokumen.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the pengajuan_kgb that owns the dokumen.
     */
    public function pengajuanKgb()
    {
        return $this->belongsTo(PengajuanKgb::class);
    }

    /**
     * Get the user who verified the dokumen.
     */
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}
