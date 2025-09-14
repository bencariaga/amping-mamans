<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Storage\Data;
use App\Models\Operation\GuaranteeLetter;
use App\Models\Operation\ExpenseRange;

class TariffList extends Model
{
    protected $table = 'tariff_lists';
    protected $primaryKey = 'tariff_list_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'tariff_list_id',
        'data_id',
        'effectivity_date',
        'effectivity_status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tl) {
            if (empty($tl->tariff_list_id)) {
                $year = Carbon::now()->year;
                $base = "TARIFF-LIST-{$year}";
                $latest = static::where('tariff_list_id', 'like', "{$base}%")->latest('tariff_list_id')->first();
                $last = $latest ? (int) Str::substr($latest->tariff_list_id, -3) : 0;
                $next = Str::padLeft((string) ($last + 1), 3, '0');
                $tl->tariff_list_id = "{$base}-{$next}";
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
        return $this->hasMany(GuaranteeLetter::class, 'tariff_list_id');
    }

    public function expenseRanges()
    {
        return $this->hasMany(ExpenseRange::class, 'tariff_list_id');
    }
}
