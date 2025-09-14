<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Storage\Data;
use App\Models\Operation\TariffList;

class Service extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'service_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'service_id',
        'data_id',
        'service_type',
        'assist_scope',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($svc) {
            if (empty($svc->service_id)) {
                $year = Carbon::now()->year;
                $base = "SERVICE-{$year}";
                $latest = static::where('service_id', 'like', "{$base}%")->orderBy('service_id', 'desc')->first();
                $last = $latest ? (int) Str::substr($latest->service_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $svc->service_id = "{$base}-{$next}";
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

    public function tariffLists()
    {
        return $this->hasMany(TariffList::class, 'service_id');
    }
}
