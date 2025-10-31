<?php

namespace App\Models\Operation;

use App\Actions\DatabaseTableIdGeneration\GenerateBudgetUpdateId;
use App\Models\User\Sponsor;
use Illuminate\Database\Eloquent\Model;

class BudgetUpdate extends Model
{
    protected $table = 'budget_updates';

    protected $primaryKey = 'budget_update_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'budget_update_id',
        'sponsor_id',
        'possessor',
        'amount_accum',
        'amount_spent',
        'amount_recent',
        'amount_before',
        'amount_change',
        'direction',
        'reason',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bdg) {
            if (empty($bdg->budget_update_id)) {
                $bdg->budget_update_id = GenerateBudgetUpdateId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class, 'sponsor_id', 'sponsor_id');
    }
}
