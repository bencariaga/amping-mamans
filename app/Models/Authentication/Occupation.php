<?php

namespace App\Models\Authentication;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Storage\Data;
use App\Models\User\Client;

class Occupation extends Model
{
    use HasFactory;

    protected $table        = 'occupations';
    protected $primaryKey   = 'occupation_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = false;

    protected $fillable = [
        'occupation_id',
        'data_id',
        'occupation',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($o) {
            if (empty($o->occupation_id)) {
                $year = Carbon::now()->year;
                $base = "OCCUP-{$year}";
                $last = static::where('occupation_id', 'like', "{$base}-%")->latest('occupation_id')->value('occupation_id');
                $seq  = $last ? (int) Str::substr($last, -9) : 0;
                $o->occupation_id = "{$base}-" . Str::padLeft($seq + 1, 9, '0');
            }
            if (empty($o->data_id)) {
                $o->data_id = Data::create()->data_id;
            }
        });
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id', 'data_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'occupation_id', 'occupation_id');
    }
}
