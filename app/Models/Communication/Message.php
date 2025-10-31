<?php

namespace App\Models\Communication;

use App\Actions\DatabaseTableIdGeneration\GenerateMessageId;
use App\Models\Operation\Application;
use App\Models\User\Contact;
use App\Models\User\Staff;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    protected $primaryKey = 'message_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'message_id',
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
                $message->message_id = GenerateMessageId::execute();
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

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'contact_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'message_id', 'message_id');
    }
}
