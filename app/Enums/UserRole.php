<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'Administrator';
    case MANAGER = 'Manager';
    case STAFF = 'Staff';
    case ENCODER = 'Encoder';
    case VIEWER = 'Viewer';

    public function permissions(): array
    {
        return match($this) {
            self::ADMIN => [
                'manage_users',
                'manage_roles',
                'manage_applications',
                'manage_budget',
                'manage_tariffs',
                'manage_sponsors',
                'manage_reports',
                'view_audit_logs',
            ],
            self::MANAGER => [
                'manage_applications',
                'manage_budget',
                'manage_tariffs',
                'view_reports',
            ],
            self::STAFF => [
                'create_applications',
                'update_applications',
                'view_applications',
                'view_budget',
            ],
            self::ENCODER => [
                'create_applications',
                'view_applications',
            ],
            self::VIEWER => [
                'view_applications',
                'view_reports',
            ],
        };
    }

    public function canManageUsers(): bool
    {
        return $this === self::ADMIN;
    }

    public function canManageBudget(): bool
    {
        return in_array($this, [self::ADMIN, self::MANAGER]);
    }

    public function canApproveApplications(): bool
    {
        return in_array($this, [self::ADMIN, self::MANAGER]);
    }
}
