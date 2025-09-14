<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Storage\Data;
use App\Models\Operation\GuaranteeLetter;
use App\Models\User\Sponsor;

class BudgetUpdate extends Model
{
    protected $table = 'budget_updates';
    protected $primaryKey = 'budget_update_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'budget_update_id',
        'data_id',
        'sponsor_id',
        'possessor',
        'amount_accum',
        'amount_recent',
        'amount_before',
        'amount_change',
        'amount_spent',
        'direction',
        'reason',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bdg) {
            if (empty($bdg->budget_update_id)) {
                $year = Carbon::now()->year;
                $base = "BDG-UPD-{$year}";
                $latest = static::where('budget_update_id', 'like', "{$base}%")->latest('budget_update_id')->first();
                $last = $latest ? (int) Str::substr($latest->budget_update_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $bdg->budget_update_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id');
    }

    public function guaranteeLetters()
    {
        return $this->hasMany(GuaranteeLetter::class, 'budget_update_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class, 'sponsor_id');
    }
}
