<?php

namespace App\Models\Audit;

use App\Actions\DatabaseTableIdGeneration\GenerateReportId;
use App\Models\User\Staff;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'report_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'report_id',
        'staff_id',
        'report_type',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            if (empty($report->report_id)) {
                $report->report_id = GenerateReportId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'staff_id');
    }
}
