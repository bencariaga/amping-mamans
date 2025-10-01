<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User\Client;

class Household extends Model
{
    protected $table = 'households';
    protected $primaryKey = 'household_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'household_id',
        'client_id',
        'household_name',
        'education_attainment',
        'relation_to_applicant',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($h) {
            if (empty($h->household_id)) {
                $year = Carbon::now()->year;
                $base = "HOUSEHOLD-{$year}";
                $latest = static::where('household_id', 'like', "{$base}%")->latest('household_id')->first();
                $last = $latest ? (int) Str::substr($latest->household_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $h->household_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
