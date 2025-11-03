<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Audit\AuditLog;
use App\Models\Authentication\Role;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role', 'All');
        $type = $request->input('type', 'all');
        $sortBy = $request->input('sort_by', 'latest');
        $perPage = $request->input('per_page', 5);

        $query = AuditLog::with(['staff.member', 'staff.role']);

        if ($search) {
            $term = "%{$search}%";
            $query->where(function ($q) use ($term) {
                $q->where('al_text', 'like', $term)
                    ->orWhereHas('staff.member', function ($qm) use ($term) {
                        $qm->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$term])
                            ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", [$term]);
                    });
            });
        }

        if ($role !== 'All') {
            $query->whereHas('staff.role', function ($q) use ($role) {
                $q->where('role', $role);
            });
        }

        if ($type !== 'all') {
            $query->where('al_type', $type);
        }

        if ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if ($perPage === 'all') {
            $logs = $query->get()->map(function ($log) {
                return $this->formatLog($log);
            });
        } else {
            $logs = $query->paginate($perPage);
            $logs->getCollection()->transform(function ($log) {
                return $this->formatLog($log);
            });
        }

        $roles = Role::all();

        return view('pages.dashboard.system.audit-logs', compact('logs', 'roles'));
    }

    private function formatLog($log)
    {
        $member = $log->staff->member ?? null;
        $staffName = $member ? "{$member->last_name}, {$member->first_name}" : 'N/A';
        $role = $log->staff->role->role ?? 'N/A';

        $log->staff_name = $staffName;
        $log->role = $role;

        return $log;
    }
}
