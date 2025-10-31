<?php

namespace App\Models\User;

use App\Actions\DatabaseTableIdGeneration\GenerateSignerId;
use Illuminate\Database\Eloquent\Model;

class Signer extends Model
{
    protected $table = 'signers';

    protected $primaryKey = 'signer_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'signer_id',
        'member_id',
        'post_nominal_letters',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($signer) {
            if (empty($signer->signer_id)) {
                $signer->signer_id = GenerateSignerId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
}
