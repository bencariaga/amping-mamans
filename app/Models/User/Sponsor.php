<?php

namespace App\Models\User;

use App\Actions\DatabaseTableIdGeneration\GenerateSponsorId;
use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\GuaranteeLetter;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $table = 'sponsors';

    protected $primaryKey = 'sponsor_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'sponsor_id',
        'tp_id',
        'sponsor_type',
        'designation',
        'organization_name',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($s) {
            if (empty($s->sponsor_id)) {
                $s->sponsor_id = GenerateSponsorId::execute();
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

    public function budgetUpdates()
    {
        return $this->hasMany(BudgetUpdate::class, 'sponsor_id', 'sponsor_id');
    }

    public function guaranteeLetters()
    {
        return $this->hasMany(GuaranteeLetter::class, 'sponsor_id', 'sponsor_id');
    }
}
