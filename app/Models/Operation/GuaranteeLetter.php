<?php

namespace App\Models\Operation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Operation\Application;
use App\Models\Operation\BudgetUpdate;

class GuaranteeLetter extends Model
{
    protected $table = 'guarantee_letters';
    protected $primaryKey = 'gl_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'gl_id',
        'application_id',
        'budget_update_id',
        'gl_status',
        'signers',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($gl) {
            if (empty($gl->gl_id)) {
                $year = Carbon::now()->year;
                $base = "GL-{$year}";
                $latest = static::where('gl_id', 'like', "{$base}%")->latest('gl_id')->first();
                $last = $latest ? (int) Str::substr($latest->gl_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $gl->gl_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function budgetUpdate()
    {
        return $this->belongsTo(BudgetUpdate::class, 'budget_update_id');
    }
}
