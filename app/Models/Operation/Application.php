<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Operation\GuaranteeLetter;
use App\Models\Operation\ExpenseRange;
use App\Models\User\AffiliatePartner;
use App\Models\User\Applicant;
use App\Models\User\Patient;

class Application extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'application_id',
        'applicant_id',
        'patient_id',
        'affiliate_partner_id',
        'exp_range_id',
        'billed_amount',
        'applied_at',
        'reapply_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($app) {
            if (empty($app->application_id)) {
                $year = Carbon::now()->year;
                $base = "APPLICATION-{$year}";
                $latest = static::where('application_id', 'like', "{$base}%")->latest('application_id')->first();
                $last = $latest ? (int) Str::substr($latest->application_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $app->application_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function expenseRange()
    {
        return $this->belongsTo(ExpenseRange::class, 'exp_range_id');
    }

    public function affiliatePartner()
    {
        return $this->belongsTo(AffiliatePartner::class, 'affiliate_partner_id');
    }

    public function guaranteeLetter()
    {
        return $this->hasOne(GuaranteeLetter::class, 'application_id');
    }
}
