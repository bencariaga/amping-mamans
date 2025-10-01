<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User\Staff;

class Log extends Model
{
    protected $table = 'logs';
    protected $primaryKey = 'log_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'log_id',
        'staff_id',
        'log_type',
        'log_info',
        'happened_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (empty($log->log_id)) {
                $year = Carbon::now()->year;
                $base = "LOG-{$year}";
                $latest = static::where('log_id', 'like', "{$base}%")->latest('log_id')->first();
                $last = $latest ? (int) Str::substr($latest->log_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $log->log_id = "{$base}-{$next}";
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
}
