<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Operation\Application;
use App\Models\User\Member;
use App\Models\User\Applicant;

class Patient extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'patient_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'patient_id',
        'applicant_id',
        'member_id',
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

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
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
