<?php

namespace App\Models\User;

use App\Actions\DatabaseTableIdGeneration\GenerateThirdPartyId;
use App\Models\Authentication\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdParty extends Model
{
    use HasFactory;

    protected $table = 'third_parties';

    protected $primaryKey = 'tp_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'tp_id',
        'account_id',
        'tp_type',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tp) {
            if (empty($tp->tp_id)) {
                $tp->tp_id = GenerateThirdPartyId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function affiliatePartner()
    {
        return $this->hasOne(AffiliatePartner::class, 'tp_id', 'tp_id');
    }

    public function sponsor()
    {
        return $this->hasOne(Sponsor::class, 'tp_id', 'tp_id');
    }
}
