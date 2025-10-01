<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Authentication\Account;
use App\Models\Authentication\Role;
use App\Models\Authentication\Occupation;
use App\Models\Communication\MessageTemplate;
use App\Models\Operation\BudgetUpdate;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use App\Models\Storage\File;
use App\Models\Audit\Log;

class Data extends Model
{
    use HasFactory;

    protected $table        = 'data';
    protected $primaryKey   = 'data_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = true;

    protected $fillable = [
        'data_id',
        'data_status',
        'created_at',
        'updated_at',
        'archived_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($d) {
            if (empty($d->data_id)) {
                $year = Carbon::now()->year;
                $base = "DATA-{$year}";
                $last = static::where('data_id', 'like', "{$base}-%")->latest('data_id')->value('data_id');
                $seq  = $last ? (int) Str::substr($last, -9) : 0;
                $d->data_id = "{$base}-" . Str::padLeft($seq + 1, 9, '0');
            }
            if (empty($d->created_at)) {
                $d->created_at = Carbon::now();
            }
        });

        static::updating(function ($d) {
            $d->updated_at = Carbon::now();
        });
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'data_id', 'data_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'data_id', 'data_id');
    }

    public function messageTemplates()
    {
        return $this->hasMany(MessageTemplate::class, 'data_id', 'data_id');
    }

    public function budgetUpdates()
    {
        return $this->hasMany(BudgetUpdate::class, 'data_id', 'data_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'data_id', 'data_id');
    }

    public function tariffLists()
    {
        return $this->hasMany(TariffList::class, 'data_id', 'data_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'data_id', 'data_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'data_id', 'data_id');
    }

    public function occupations()
    {
        return $this->hasMany(Occupation::class, 'data_id', 'data_id');
    }
}
