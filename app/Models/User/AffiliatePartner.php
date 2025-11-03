<?php

namespace App\Models\User;

use App\Actions\IdGeneration\GenerateAffiliatePartnerId;
use App\Models\Operation\Application;
use Illuminate\Database\Eloquent\Model;

class AffiliatePartner extends Model
{
    protected $table = 'affiliate_partners';

    protected $primaryKey = 'ap_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ap_id',
        'tp_id',
        'ap_name',
        'ap_type',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ap) {
            if (empty($ap->ap_id)) {
                $ap->ap_id = GenerateAffiliatePartnerId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function thirdParty()
    {
        return $this->belongsTo(ThirdParty::class, 'tp_id', 'tp_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'ap_id', 'ap_id');
    }
}
