<?php

namespace App\Models\Operation;

use App\Actions\IdGeneration\GenerateApplicationId;
use App\Models\Communication\Message;
use App\Models\User\AffiliatePartner;
use App\Models\User\Patient;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';

    protected $primaryKey = 'application_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'application_id',
        'data_id',
        'patient_id',
        'ap_id',
        'exp_range_id',
        'message_id',
        'billed_amount',
        'assistance_amount',
        'application_date',
        'reapplication_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($app) {
            if (empty($app->application_id)) {
                $app->application_id = GenerateApplicationId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function affiliatePartner()
    {
        return $this->belongsTo(AffiliatePartner::class, 'ap_id', 'ap_id');
    }

    public function expenseRange()
    {
        return $this->belongsTo(ExpenseRange::class, 'exp_range_id', 'exp_range_id');
    }

    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id', 'message_id');
    }

    public function guaranteeLetter()
    {
        return $this->hasOne(GuaranteeLetter::class, 'application_id', 'application_id');
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id', 'data_id');
    }
}
