<?php

namespace App\Models\Authentication;

use App\Actions\DatabaseTableIdGeneration\GenerateAccountId;
use App\Models\Operation\Data;
use App\Models\User\Member;
use App\Models\User\ThirdParty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $primaryKey = 'account_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'data_id',
        'account_status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if (empty($account->account_id)) {
                $account->account_id = GenerateAccountId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id', 'data_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'account_id', 'account_id');
    }

    public function thirdParties()
    {
        return $this->hasMany(ThirdParty::class, 'account_id', 'account_id');
    }
}
