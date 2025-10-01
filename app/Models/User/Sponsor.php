<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User\Member;
use App\Models\Operation\BudgetUpdate;

class Sponsor extends Model
{
    protected $table = 'sponsors';
    protected $primaryKey = 'sponsor_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'sponsor_id',
        'member_id',
        'sponsor_type',
        'designation',
        'organization_name'
    ];

    protected $appends = ['sponsor_name'];
    protected $with = ['member'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($s) {
            if (empty($s->sponsor_id)) {
                $year = Carbon::now()->year;
                $base = "SPONSOR-{$year}";
                $latest = static::where('sponsor_id', 'like', "{$base}%")->orderBy('sponsor_id', 'desc')->first();
                $last = $latest ? (int) Str::substr($latest->sponsor_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $s->sponsor_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function budgetUpdates()
    {
        return $this->hasMany(BudgetUpdate::class, 'sponsor_id');
    }

    public function getSponsorNameAttribute()
    {
        return $this->member->full_name ?? '';
    }
}
