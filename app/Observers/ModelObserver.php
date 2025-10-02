<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Audit\Log;
use App\Models\User\Member;
use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Support\Facades\Auth;

class ModelObserver
{
    public function created(Model $model): void
    {
        $this->logAction($model, 'created', $model->getAttributes(), 'Info');
    }

    public function updated(Model $model): void
    {
        $this->logAction($model, 'updated', $model->getChanges(), 'Info');
    }

    public function deleted(Model $model): void
    {
        $this->logAction($model, 'deleted', $model->getAttributes(), 'Warning');
    }

    protected function logAction(Model $model, string $action, array $data, string $type): void
    {   
        // Check if the model is the Log model itself.
        // This is the crucial step to prevent an infinite loop.
        if ($model instanceof Log) {
            return;
        }

        $memberId = Auth::id() ?? null;
        if (!$memberId) return;
        $member = Member::find($memberId);
        if (!$member) return;
        $staffId = $member->staff->staff_id;

        $primaryKey = $model->getKeyName();
        $payload = $model->toArray();
        LaravelLog::info("Model Action Logged: Staff ID {$staffId}, Action {$action}, Model " . get_class($model) . ", Data: " . json_encode($payload));
        Log::create([
            'staff_id'    => $staffId,
            'log_type'    => $type,
            'log_info'    => json_encode([
                'primary_key' => $primaryKey,
                'primary_value' => $model->$primaryKey,
                'payload' => $data,
                'action' => $action,
                'model' => get_class($model),
            ]),
            'happened_at' => Carbon::now(),
        ]);
    }
}