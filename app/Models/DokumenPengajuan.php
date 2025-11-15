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
        // corrected to match DB:
        'nama_file',
        'jenis_dokumen',
        'path_file',
        'ukuran_file',
        'tipe_file',
        'keterangan',
        // add other correct columns if needed
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ukuran_file' => 'integer',
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
}
