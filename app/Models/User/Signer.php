<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User\Member;

class Signer extends Model
{
    protected $table = 'signers';
    protected $primaryKey = 'signer_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'signer_id',
        'member_id',
        'post_nominal_letters',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($signer) {
            if (empty($signer->signer_id)) {
                $year = Carbon::now()->year;
                $base = "SIGNER-{$year}";
                $latest = static::where('signer_id', 'like', "{$base}%")->latest('signer_id')->first();
                $last = $latest ? (int) Str::substr($latest->signer_id, -3) : 0;
                $next = Str::padLeft($last + 1, 3, '0');
                $signer->signer_id = "{$base}-{$next}";
            }
        });
    }

    public static function getPrimaryKey()
    {
        return (new static)->getKeyName();
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
