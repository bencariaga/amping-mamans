<?php

namespace App\Models\Authentication;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Storage\Data;
use App\Models\User\Member;
use App\Models\User\AffiliatePartner;

class Account extends Model
{
    use HasFactory;

    protected $table        = 'accounts';
    protected $primaryKey   = 'account_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = false;

    protected $fillable = [
        'account_id',
        'data_id',
        'account_status',
        'registered_at',
        'last_deactivated_at',
        'last_reactivated_at',
    ];

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'account_id');
    }

    public function affiliatePartners()
    {
        return $this->hasMany(AffiliatePartner::class, 'account_id');
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id');
    }
}
