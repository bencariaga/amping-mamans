<?php

namespace App\Models\User;

use App\Actions\IdGeneration\GenerateHouseholdId;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    protected $table = 'households';

    protected $primaryKey = 'household_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'household_id',
        'household_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($h) {
            if (empty($h->household_id)) {
                $h->household_id = GenerateHouseholdId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function householdMembers()
    {
        return $this->hasMany(HouseholdMember::class, 'household_id', 'household_id');
    }
}
