<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User\Member;
use App\Models\User\Applicant;
use App\Models\User\Contact;
use App\Models\User\Household;
use App\Models\Authentication\Occupation;

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

    public function households()
    {
        return $this->hasMany(Household::class, 'client_id', 'client_id');
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class, 'occupation_id', 'occupation_id');
    }
}
