<?php

namespace App\Models\User;

use App\Models\Authentication\Occupation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $primaryKey = 'client_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'member_id',
        'occupation_id',
        'birthdate',
        'age',
        'sex',
        'civil_status',
        'monthly_income',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (empty($client->client_id)) {
                $year = Carbon::now()->year;
                $base = "CLIENT-{$year}";
                $latest = static::where('client_id', 'like', "{$base}%")->latest('client_id')->first();
                $last = $latest ? (int) Str::substr($latest->client_id, -9) : 0;
                $next = Str::padLeft($last + 1, 9, '0');
                $client->client_id = "{$base}-{$next}";
            }
        });
    }

    public static function generateSequentialId(string $prefix = 'CLIENT'): string
    {
        $year = Carbon::now()->year;
        $base = "{$prefix}-{$year}";
        $latest = static::where('client_id', 'like', "{$base}%")->latest('client_id')->first();
        $lastNumber = $latest ? (int) Str::substr($latest->client_id, -9) : 0;
        $nextNumber = Str::padLeft($lastNumber + 1, 9, '0');

        return "{$base}-{$nextNumber}";
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function applicant()
    {
        return $this->hasOne(Applicant::class, 'client_id', 'client_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'client_id', 'client_id');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'client_id', 'client_id');
    }

    public function households()
    {
        return $this->hasMany(Household::class, 'client_id', 'client_id');
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'occupation_id');
    }
}
