<?php

namespace App\Models\User;

use App\Actions\DatabaseTableIdGeneration\GenerateHouseholdMemberId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseholdMember extends Model
{
    use HasFactory;

    protected $table = 'household_members';

    protected $primaryKey = 'household_member_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'household_member_id',
        'household_id',
        'client_id',
        'educational_attainment',
        'relationship_to_applicant',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hm) {
            if (empty($hm->household_member_id)) {
                $hm->household_member_id = GenerateHouseholdMemberId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function household()
    {
        return $this->belongsTo(Household::class, 'household_id', 'household_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
