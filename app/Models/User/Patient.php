<?php

namespace App\Models\User;

use App\Actions\DatabaseTableIdGeneration\GeneratePatientId;
use App\Models\Operation\Application;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    protected $primaryKey = 'patient_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'client_id',
        'applicant_id',
        'patient_category',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($p) {
            if (empty($p->patient_id)) {
                $p->patient_id = GeneratePatientId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'patient_id', 'patient_id');
    }
}
