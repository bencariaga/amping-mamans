<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User\Staff;
use App\Models\Storage\File;

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
        'file_id',
        'report_type',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            if (empty($report->report_id)) {
                $year = Carbon::now()->year;
                $base = "REPORT-{$year}";
                $latest = static::where('report_id', 'like', "{$base}%")->latest('report_id')->first();
                $last = $latest ? (int) Str::substr($latest->report_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $report->report_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
