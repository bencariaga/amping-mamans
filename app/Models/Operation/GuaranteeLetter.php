<?php

namespace App\Models\Operation;

use App\Actions\DatabaseTableIdGeneration\GenerateGuaranteeLetterId;
use App\Models\User\Sponsor;
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
        'data_id',
        'application_id',
        'sponsor_id',
        'is_sponsored',
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

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id', 'data_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id', 'application_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class, 'sponsor_id', 'sponsor_id');
    }
}
