<?php

namespace App\Models\User;

use App\Models\Operation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
                $year = Carbon::now()->year;
                $base = "PATIENT-{$year}";
                $latest = static::where('patient_id', 'like', "{$base}%")->latest('patient_id')->first();
                $last = $latest ? (int) Str::substr($latest->patient_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $p->patient_id = "{$base}-{$next}";
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
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'patient_id');
    }
}
