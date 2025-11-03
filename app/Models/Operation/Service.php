<?php

namespace App\Models\Operation;

use App\Actions\IdGeneration\GenerateServiceId;
use Illuminate\Database\Eloquent\Model;

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
        'service',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($svc) {
            if (empty($svc->service_id)) {
                $svc->service_id = GenerateServiceId::execute();
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
        return $this->hasMany(ExpenseRange::class, 'service_id', 'service_id');
    }
}
