<?php

namespace App\Models;

use App\Models\Traits\TenantAware;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory, TenantAware;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pegawai';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'nip',
        'name',
        'nrk',
        'pangkat_golongan',
        'jabatan',
        'unit_kerja',
        'tmt_pangkat_terakhir',
        'tmt_kgb_terakhir',
        'tmt_kgb_berikutnya',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'status_kepegawaian',
        'email',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tmt_pangkat_terakhir' => 'date',
        'tmt_kgb_terakhir' => 'date',
        'tmt_kgb_berikutnya' => 'date',
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the pegawai.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the pengajuan_kgb records associated with the pegawai.
     */
    public function pengajuanKgbs()
    {
        return $this->hasMany(PengajuanKgb::class);
    }
}