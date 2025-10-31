<?php

namespace App\Models\User;

use App\Actions\DatabaseTableIdGeneration\GenerateClientId;
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
        'client_type',
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
                $client->client_id = GenerateClientId::execute();
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

    public function householdMembers()
    {
        return $this->hasMany(HouseholdMember::class, 'client_id', 'client_id');
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'occupation_id');
    }
}
