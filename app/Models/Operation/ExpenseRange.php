<?php

namespace App\Models\Operation;

use App\Actions\DatabaseTableIdGeneration\GenerateExpenseRangeId;
use Illuminate\Database\Eloquent\Model;

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
        'coverage_percent',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($er) {
            if (empty($er->exp_range_id)) {
                $er->exp_range_id = GenerateExpenseRangeId::execute();
            }
        });
    }

    public function tariffList()
    {
        return $this->belongsTo(TariffList::class, 'tariff_list_id', 'tariff_list_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'exp_range_id', 'exp_range_id');
    }
}
