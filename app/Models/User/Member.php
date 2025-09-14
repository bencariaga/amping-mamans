<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use App\Models\Authentication\Account;
use App\Models\User\Staff;
use App\Models\User\Client;
use App\Models\User\Sponsor;
use App\Models\User\Signer;
use App\Models\Storage\File;

class Member extends Authenticatable
{
    use HasFactory, Notifiable, Searchable;

    protected $table = 'members';
    protected $primaryKey = 'member_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'account_id',
        'member_type',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'full_name',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return Str::of("{$this->first_name} {$this->middle_name} {$this->last_name} {$this->suffix}")->trim();
    }

    public function toSearchableArray(): array
    {
        return ['full_name' => $this->full_name];
    }

    public function getAuthPassword()
    {
        return $this->staff?->password;
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'member_id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'member_id');
    }

    public function sponsor()
    {
        return $this->hasOne(Sponsor::class, 'member_id');
    }

    public function signer()
    {
        return $this->hasOne(Signer::class, 'member_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'member_id');
    }

    public function profilePictures()
    {
        return $this->hasMany(File::class, 'member_id');
    }
}
