<?php

namespace App\Models\User;

use App\Actions\IdGeneration\GenerateApplicantId;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $table = 'applicants';

    protected $primaryKey = 'applicant_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'applicant_id',
        'client_id',
        'province',
        'city',
        'municipality',
        'barangay',
        'subdivision',
        'purok',
        'sitio',
        'street',
        'phase',
        'block_number',
        'house_number',
        'job_status',
        'house_occupation_status',
        'lot_occupation_status',
        'phic_affiliation',
        'phic_category',
        'is_also_patient',
        'patient_quantity',
    ];

    protected $casts = [
        'phic_category' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($app) {
            if (empty($app->applicant_id)) {
                $app->applicant_id = GenerateApplicantId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function getRouteKeyName()
    {
        return 'applicant_id';
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'applicant_id', 'applicant_id');
    }
}
