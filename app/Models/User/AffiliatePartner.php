<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Authentication\Account;
use App\Models\Operation\Application;

class AffiliatePartner extends Model
{
    protected $table = 'affiliate_partners';
    protected $primaryKey = 'affiliate_partner_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'affiliate_partner_id',
        'account_id',
        'affiliate_partner_name',
        'affiliate_partner_type',
        'affiliate_partner_contact',
        'affiliate_partner_address',
        'affiliate_partner_email',
        'affiliate_partner_status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ap) {
            if (empty($ap->affiliate_partner_id)) {
                $year = Carbon::now()->year;
                $base = "AP-{$year}";
                $latest = static::where('affiliate_partner_id', 'like', "{$base}%")->latest('affiliate_partner_id')->first();
                $last = $latest ? (int) Str::substr($latest->affiliate_partner_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $ap->affiliate_partner_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'affiliate_partner_id');
    }
}
