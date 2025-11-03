<?php

namespace App\Actions\Archive;

use App\Models\Authentication\Occupation;
use App\Models\Authentication\Role;
use App\Models\Communication\MessageTemplate;
use App\Models\Operation\Application;
use App\Models\Operation\Service;
use App\Models\Operation\TariffList;
use Illuminate\Support\Collection;

class GetArchives
{
    public function execute(?string $type = 'all', ?string $search = null): Collection
    {
        $archives = collect();

        if ($type === 'all' || $type === 'applications') {
            $apps = Application::with(['data', 'patient.applicant.client.member'])
                ->whereHas('data', function ($q) {
                    $q->where('archive_status', 'Archived');
                })
                ->get()
                ->map(function ($app) {
                    $member = $app->patient->applicant->client->member ?? null;
                    $name = $member ? "{$member->last_name}, {$member->first_name}" : 'N/A';

                    return (object) [
                        'id' => $app->application_id,
                        'type' => 'Application',
                        'name' => $name,
                        'archived_at' => $app->data->archived_at ?? null,
                    ];
                });
            $archives = $archives->merge($apps);
        }

        if ($type === 'all' || $type === 'tariff_lists') {
            $tariffs = TariffList::with('data')
                ->whereHas('data', function ($q) {
                    $q->where('archive_status', 'Archived');
                })
                ->get()
                ->map(function ($tariff) {
                    return (object) [
                        'id' => $tariff->tariff_list_id,
                        'type' => 'Tariff List',
                        'name' => $tariff->tariff_list_id,
                        'archived_at' => $tariff->data->archived_at ?? null,
                    ];
                });
            $archives = $archives->merge($tariffs);
        }

        if ($type === 'all' || $type === 'message_templates') {
            $templates = MessageTemplate::with('data')
                ->whereHas('data', function ($q) {
                    $q->where('archive_status', 'Archived');
                })
                ->get()
                ->map(function ($template) {
                    return (object) [
                        'id' => $template->msg_tmp_id,
                        'type' => 'Message Template',
                        'name' => $template->msg_tmp_title,
                        'archived_at' => $template->data->archived_at ?? null,
                    ];
                });
            $archives = $archives->merge($templates);
        }

        if ($type === 'all' || $type === 'roles') {
            $roles = Role::with('data')
                ->whereHas('data', function ($q) {
                    $q->where('archive_status', 'Archived');
                })
                ->get()
                ->map(function ($role) {
                    return (object) [
                        'id' => $role->role_id,
                        'type' => 'Role',
                        'name' => $role->role,
                        'archived_at' => $role->data->archived_at ?? null,
                    ];
                });
            $archives = $archives->merge($roles);
        }

        if ($type === 'all' || $type === 'occupations') {
            $occupations = Occupation::with('data')
                ->whereHas('data', function ($q) {
                    $q->where('archive_status', 'Archived');
                })
                ->get()
                ->map(function ($occupation) {
                    return (object) [
                        'id' => $occupation->occupation_id,
                        'type' => 'Occupation',
                        'name' => $occupation->occupation,
                        'archived_at' => $occupation->data->archived_at ?? null,
                    ];
                });
            $archives = $archives->merge($occupations);
        }

        if ($type === 'all' || $type === 'services') {
            $services = Service::with('data')
                ->whereHas('data', function ($q) {
                    $q->where('archive_status', 'Archived');
                })
                ->get()
                ->map(function ($service) {
                    return (object) [
                        'id' => $service->service_id,
                        'type' => 'Service',
                        'name' => $service->service,
                        'archived_at' => $service->data->archived_at ?? null,
                    ];
                });
            $archives = $archives->merge($services);
        }

        if ($search) {
            $archives = $archives->filter(function ($item) use ($search) {
                return stripos($item->name, $search) !== false || stripos($item->type, $search) !== false;
            });
        }

        return $archives;
    }
}
