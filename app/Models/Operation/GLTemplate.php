<?php

namespace App\Models\Operation;

use App\Actions\IdGeneration\GenerateGLTemplateId;
use Illuminate\Database\Eloquent\Model;

class GLTemplate extends Model
{
    protected $table = 'gl_templates';

    protected $primaryKey = 'gl_tmp_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'gl_tmp_id',
        'data_id',
        'gl_tmp_title',
        'gl_content',
        'signers',
        'signatures',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($glTmp) {
            if (empty($glTmp->gl_tmp_id)) {
                $glTmp->gl_tmp_id = GenerateGLTemplateId::execute();
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

    public function guaranteeLetters()
    {
        return $this->hasMany(GuaranteeLetter::class, 'gl_tmp_id', 'gl_tmp_id');
    }
}
