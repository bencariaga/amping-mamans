<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Storage\Data;

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
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tmpl) {
            if (empty($tmpl->msg_tmp_id)) {
                $year = Carbon::now()->year;
                $base = "MSG-TMP-{$year}";
                $latest = static::where('msg_tmp_id', 'like', "{$base}%")->latest('msg_tmp_id')->first();
                $last = $latest ? (int) Str::substr($latest->msg_tmp_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $tmpl->msg_tmp_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'msg_tmp_id');
    }
}
