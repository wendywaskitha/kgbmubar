<?php

namespace App\Models;

use App\Models\Traits\TenantAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanKgb extends Model
{
    use HasFactory, TenantAware;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengajuan_kgb';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'pegawai_id',
        'user_pengaju_id',
        'no_sk',
        'tanggal_sk',
        'tmt_kgb_baru',
        'status',
        'catatan',
        'catatan_verifikasi_dinas',
        'catatan_verifikasi_kabupaten',
        'jumlah_revisi',
        'tanggal_pengajuan',
        'tanggal_verifikasi_dinas',
        'tanggal_verifikasi_kabupaten',
        'tanggal_approve',
        'tanggal_selesai',
        'jenis_pengajuan',
        'file_sk_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_sk' => 'date',
        'tmt_kgb_baru' => 'date',
        'tanggal_pengajuan' => 'datetime',
        'tanggal_verifikasi_dinas' => 'datetime',
        'tanggal_verifikasi_kabupaten' => 'datetime',
        'tanggal_approve' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    /**
     * Get the tenant that owns the pengajuan.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the pegawai that owns the pengajuan.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Get the user who made the pengajuan.
     */
    public function userPengaju()
    {
        return $this->belongsTo(User::class, 'user_pengaju_id');
    }

    /**
     * Get the verifikasi records associated with the pengajuan.
     */
    public function verifikasis()
    {
        return $this->hasMany(Verifikasi::class);
    }

    /**
     * Get the dokumen_pengajuan records associated with the pengajuan.
     */
    public function dokumenPengajuans()
    {
        return $this->hasMany(DokumenPengajuan::class);
    }
}
