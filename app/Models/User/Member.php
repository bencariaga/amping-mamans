<?php

namespace App\Models\User;

use App\Actions\DatabaseTableIdGeneration\GenerateMemberId;
use App\Models\Authentication\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

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
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $appends = ['full_name'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            if (empty($member->member_id)) {
                $member->member_id = GenerateMemberId::execute();
            }
        });
    }

    public function getFullNameAttribute(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name, $this->suffix])->filter()->implode(' ');
    }

    public function toSearchableArray(): array
    {
        return [
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'member_id' => $this->member_id,
        ];
    }

    public function getAuthPassword()
    {
        return $this->staff?->password;
    }

    public static function generateSequentialId(string $prefix = 'MEMBER'): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $latest = static::where('member_id', 'like', "{$base}%")->latest('member_id')->first();
        $lastNumber = $latest ? (int) Str::substr($latest->member_id, -9) : 0;
        $nextNumber = Str::padLeft($lastNumber + 1, 9, '0');

        return "{$base}-{$nextNumber}";
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
}
