<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User\Client;
use App\Models\User\Patient;
use App\Models\Operation\Application;

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
        'street',
        'job_status',
        'representing_patient',
        'house_occup_status',
        'lot_occup_status',
        'phic_affiliation',
        'phic_category',
    ];

    protected $casts = [
        'phic_category' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($app) {
            if (empty($app->applicant_id)) {
                $year = Carbon::now()->year;
                $base = "APPLICANT-{$year}";
                $latest = static::where('applicant_id', 'like', "{$base}%")->latest('applicant_id')->first();
                $last = $latest ? (int) Str::substr($latest->applicant_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $app->applicant_id = "{$base}-{$next}";
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
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'applicant_id', 'applicant_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'applicant_id');
    }
}
