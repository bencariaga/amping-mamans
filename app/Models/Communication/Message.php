<?php

namespace App\Models\Communication;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Communication\MessageTemplate;
use App\Models\User\Staff;
use App\Models\User\Contact;

class Message extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'message_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'message_id',
        'msg_tmp_id',
        'staff_id',
        'contact_id',
        'message_text',
        'sent_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            if (empty($message->message_id)) {
                $year = Carbon::now()->year;
                $base = "MESSAGE-{$year}";
                $latest = static::where('message_id', 'like', "{$base}%")->latest('message_id')->first();
                $last = $latest ? (int) Str::substr($latest->message_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $message->message_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'msg_tmp_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
