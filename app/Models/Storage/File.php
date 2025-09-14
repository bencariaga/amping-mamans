<?php

namespace App\Models\Storage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Storage\Data;
use App\Models\User\Member;
use App\Models\Operation\Application;
use App\Models\Audit\Report;

class File extends Model
{
    use HasFactory;

    protected $table        = 'files';
    protected $primaryKey   = 'file_id';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public    $timestamps   = false;

    protected $fillable = [
        'file_id',
        'data_id',
        'member_id',
        'file_type',
        'filename',
        'file_extension',
        'purpose',
        'downloaded_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($file) {

            if (empty($file->file_id)) {
                $year = Carbon::now()->year;
                $base = "FILE-{$year}";
                $last = static::where('file_id', 'like', "{$base}-%")->latest('file_id')->value('file_id');
                $seq  = $last ? (int) Str::substr($last, -9) : 0;
                $file->file_id = "{$base}-" . Str::padLeft($seq + 1, 9, '0');
            }

            if (empty($file->data_id)) {
                $data = Data::create([
                    'data_status' => 'Unarchived',
                ]);

                $file->data_id = $data->data_id;
            }
        });
    }

    public function data()
    {
        return $this->belongsTo(Data::class, 'data_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'file_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'file_id');
    }
}
