<?php

namespace App\Models\Operation;

use App\Actions\IdGeneration\GenerateGuaranteeLetterId;
use Illuminate\Database\Eloquent\Model;

class GuaranteeLetter extends Model
{
    protected $table = 'guarantee_letters';

    protected $primaryKey = 'gl_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'gl_id',
        'gl_tmp_id',
        'application_id',
        'budget_update_id',
        'is_sponsored',
        'is_cancelled',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($gl) {
            if (empty($gl->gl_id)) {
                $gl->gl_id = GenerateGuaranteeLetterId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id', 'application_id');
    }

    public function glTemplate()
    {
        return $this->belongsTo(GLTemplate::class, 'gl_tmp_id', 'gl_tmp_id');
    }

    public function budgetUpdate()
    {
        return $this->belongsTo(BudgetUpdate::class, 'budget_update_id', 'budget_update_id');
    }
}
