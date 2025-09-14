<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User\Client;
use App\Models\Communication\Message;

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
        'phone_number',
        'phone_number_other',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($c) {
            if (empty($c->contact_id)) {
                $year = Carbon::now()->year;
                $base = "CONTACT-{$year}";
                $latest = static::where('contact_id', 'like', "{$base}%")->latest('contact_id')->first();
                $last = $latest ? (int) Str::substr($latest->contact_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $c->contact_id = "{$base}-{$next}";
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

    public function setPhoneNumberAttribute($value)
    {
        $n = Str::squish($value);

        if (Str::startsWith($n, '+639')) {
            $n = '09' . Str::substr($n, 4);
        } elseif (Str::startsWith($n, '+63')) {
            $n = '0' . Str::substr($n, 3);
        } elseif (Str::startsWith($n, '639')) {
            $n = '09' . Str::substr($n, 3);
        } elseif (Str::startsWith($n, '63')) {
            $n = '0' . Str::substr($n, 2);
        }

        $this->attributes['phone_number'] = $n;
    }

    public function setPhoneNumberOtherAttribute($value)
    {
        $numbers = collect(Str::explode(',', $value))->map(fn($seg) => Str::squish($seg))->map(function ($n) {
            if (Str::startsWith($n, '+639')) {
                return '09' . Str::substr($n, 4);
            } elseif (Str::startsWith($n, '+63')) {
                return '0' . Str::substr($n, 3);
            } elseif (Str::startsWith($n, '639')) {
                return '09' . Str::substr($n, 3);
            } elseif (Str::startsWith($n, '63')) {
                return '0' . Str::substr($n, 2);
            }

            return $n;
        })->filter()->implode(', ');

        $this->attributes['phone_number_other'] = $numbers;
    }
}
