<?php

namespace App\Models\Operation;

use App\Actions\IdGeneration\GenerateTariffListId;
use Illuminate\Database\Eloquent\Model;

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
        'tl_status',
        'effectivity_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tl) {
            if (empty($tl->tariff_list_id)) {
                $tl->tariff_list_id = GenerateTariffListId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id', 'data_id');
    }

    public function expenseRanges()
    {
        return $this->hasMany(ExpenseRange::class, 'tariff_list_id', 'tariff_list_id');
    }
}
