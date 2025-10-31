<?php

namespace App\Models\Communication;

use App\Actions\DatabaseTableIdGeneration\GenerateMessageTemplateId;
use App\Models\Operation\Data;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    protected $table = 'message_templates';

    protected $primaryKey = 'msg_tmp_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'msg_tmp_id',
        'data_id',
        'msg_tmp_text',
        'msg_tmp_title',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tmpl) {
            if (empty($tmpl->msg_tmp_id)) {
                $tmpl->msg_tmp_id = GenerateMessageTemplateId::execute();
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
}
