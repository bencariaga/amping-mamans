<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Operation\TariffList;
use App\Models\Operation\Service;

class ExpenseRange extends Model
{
    protected $table = 'expense_ranges';
    protected $primaryKey = 'exp_range_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'exp_range_id',
        'tariff_list_id',
        'service_id',
        'exp_range_min',
        'exp_range_max',
        'assist_amount',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($er) {
            if (empty($er->exp_range_id)) {
                $year = Carbon::now()->year;
                $base = "EXP-RANGE-{$year}";
                $latest = static::where('exp_range_id', 'like', "{$base}%")->latest('exp_range_id')->first();
                $last = $latest ? (int) Str::substr($latest->exp_range_id, -3) : 0;
                $next = Str::padLeft((string) ($last + 1), 9, '0');
                $er->exp_range_id = "{$base}-{$next}";
            }
        });
    }

    public function tariffList()
    {
        return $this->belongsTo(TariffList::class, 'tariff_list_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
