<?php

namespace App\Models\User;

use App\Actions\IdGeneration\GenerateContactId;
use App\Models\Communication\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contact extends Model
{
    protected $table = 'contacts';

    protected $primaryKey = 'contact_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $attributes = [
        'contact_type' => 'Application',
    ];

    protected $fillable = [
        'contact_id',
        'client_id',
        'contact_type',
        'contact_number',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($c) {
            if (empty($c->contact_id)) {
                $c->contact_id = GenerateContactId::execute();
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'contact_id', 'contact_id');
    }

    public function setContactNumberAttribute($value)
    {
        $n = Str::squish($value);

        if (Str::startsWith($n, '+639')) {
            $n = '09'.Str::substr($n, 4);
        } elseif (Str::startsWith($n, '+63')) {
            $n = '0'.Str::substr($n, 3);
        } elseif (Str::startsWith($n, '639')) {
            $n = '09'.Str::substr($n, 3);
        } elseif (Str::startsWith($n, '63')) {
            $n = '0'.Str::substr($n, 2);
        }

        $this->attributes['contact_number'] = $n;
    }
}
