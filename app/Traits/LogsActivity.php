<?php

namespace App\Traits;

use App\Models\Audit\AuditLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    public function logActivity(string $action, ?string $description = null): void
    {
        if (!$this->shouldLogActivity($action)) {
            return;
        }

        AuditLog::create([
            'audit_log_id' => $this->generateAuditLogId(),
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'description' => $description ?? $this->getActivityDescription($action),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'changes' => $this->getChangesForLog(),
            'performed_at' => now(),
        ]);
    }

    protected function shouldLogActivity(string $action): bool
    {
        return true;
    }

    protected function getActivityDescription(string $action): string
    {
        $modelName = class_basename($this);
        return ucfirst($action) . ' ' . $modelName;
    }

    protected function getChangesForLog(): ?array
    {
        if (!$this->wasChanged()) {
            return null;
        }

        return [
            'old' => $this->getOriginal(),
            'new' => $this->getAttributes(),
        ];
    }

    private function generateAuditLogId(): string
    {
        return 'AUDIT-' . date('Y') . '-' . str_pad((string) (AuditLog::count() + 1), 9, '0', STR_PAD_LEFT);
    }
}
