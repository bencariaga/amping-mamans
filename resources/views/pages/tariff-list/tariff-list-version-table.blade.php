<div class="data-table-container shadow-sm">
    <div class="table-responsive">
        <table class="tariff-list-table" id="tariffVersionsTable">
            <thead>
                <tr>
                    <th class="text-center tariff-list-table-header">Tariff List ID</th>
                    <th class="text-center tariff-list-table-header">Status</th>
                    <th class="text-center tariff-list-table-header">Date Created</th>
                    <th class="text-center tariff-list-table-header">Effectivity Date</th>
                    <th class="text-center tariff-list-table-header">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($tariffModels as $tariffModel)
                    @php
                        $effDate = \Illuminate\Support\Carbon::parse($tariffModel->effectivity_date);
                        $createdAt = \Illuminate\Support\Carbon::parse($tariffModel->data->created_at);
                        $currentDateTime = \Illuminate\Support\Carbon::now();

                        $serviceIds = \App\Models\Operation\ExpenseRange::where('tariff_list_id', $tariffModel->tariff_list_id)
                            ->whereNotNull('exp_range_min')
                            ->whereNotNull('exp_range_max')
                            ->whereNotNull('coverage_percent')
                            ->where('exp_range_min', '>', 0)
                            ->where('exp_range_max', '>', 0)
                            ->where('coverage_percent', '>', 0)
                            ->distinct()
                            ->pluck('service_id');

                        $hasValidRanges = false;
                        foreach ($serviceIds as $serviceId) {
                            $rangeCount = \App\Models\Operation\ExpenseRange::where('tariff_list_id', $tariffModel->tariff_list_id)
                                ->where('service_id', $serviceId)
                                ->whereNotNull('exp_range_min')
                                ->whereNotNull('exp_range_max')
                                ->whereNotNull('coverage_percent')
                                ->where('exp_range_min', '>', 0)
                                ->where('exp_range_max', '>', 0)
                                ->where('coverage_percent', '>', 0)
                                ->count();
                            if ($rangeCount >= 2) {
                                $hasValidRanges = true;
                                break;
                            }
                        }

                        if (!$hasValidRanges) {
                            // If effectivity date is in the future, mark as Scheduled (even without valid ranges)
                            if ($effDate->startOfDay()->gt($currentDateTime->copy()->startOfDay())) {
                                $hoursUntilEffective = $currentDateTime->diffInHours($effDate->startOfDay(), false);
                                $status = 'Scheduled';
                                if ($hoursUntilEffective <= 24) {
                                    $badgeClass = 'danger';
                                } else {
                                    $badgeClass = 'primary';
                                }
                                $textColorClass = 'white';
                            } else {
                                // Otherwise, mark as Draft if effectivity date has passed
                                $status = 'Draft';
                                $badgeClass = 'warning';
                                $textColorClass = 'black';
                            }
                        } elseif ($effDate->startOfDay()->lte($currentDateTime->copy()->startOfDay())) {
                            $allTariffLists = \App\Models\Operation\TariffList::all();
                            $tariffServiceIds = \App\Models\Operation\ExpenseRange::where('tariff_list_id', $tariffModel->tariff_list_id)
                                ->distinct()
                                ->pluck('service_id')
                                ->toArray();

                            $hasActiveService = false;
                            foreach ($tariffServiceIds as $serviceId) {
                                $latestTariffForService = $allTariffLists
                                    ->filter(function ($tl) use ($serviceId, $currentDateTime) {
                                        $tlEffDate = \Illuminate\Support\Carbon::parse($tl->effectivity_date)->startOfDay();
                                        if ($tlEffDate->gt($currentDateTime->copy()->startOfDay())) {
                                            return false;
                                        }
                                        $hasService = \App\Models\Operation\ExpenseRange::where('tariff_list_id', $tl->tariff_list_id)
                                            ->where('service_id', $serviceId)
                                            ->exists();
                                        return $hasService;
                                    })
                                    ->sortByDesc(function ($tl) {
                                        return \Illuminate\Support\Carbon::parse($tl->effectivity_date)->timestamp;
                                    })
                                    ->first();

                                if ($latestTariffForService && $latestTariffForService->tariff_list_id === $tariffModel->tariff_list_id) {
                                    $hasActiveService = true;
                                    break;
                                }
                            }

                            if ($hasActiveService) {
                                $status = 'Active';
                                $badgeClass = 'success';
                                $textColorClass = 'white';
                            } else {
                                $status = 'Inactive';
                                $badgeClass = 'secondary';
                                $textColorClass = 'white';
                            }
                        } else {
                            $hoursUntilEffective = $currentDateTime->diffInHours($effDate->startOfDay(), false);
                            $status = 'Scheduled';
                            if ($hoursUntilEffective <= 24) {
                                $badgeClass = 'danger';
                            } else {
                                $badgeClass = 'primary';
                            }
                            $textColorClass = 'white';
                        }
                    @endphp

                    <tr>
                        <td class="py-3 align-middle">
                            <div class="d-flex flex-column text-center">
                                <span class="text-wrap fw-bold data-text">{{ $tariffModel->tariff_list_id }}</span>
                            </div>
                        </td>

                        <td class="py-3 text-center align-middle">
                            <span
                                class="d-flex justify-content-start align-items-center badge rounded-pill bg-{{ $badgeClass }} text-{{ $textColorClass }} fw-bold gap-2 px-3 mx-auto w-auto"><i
                                    class="fas fa-circle my-auto ps-1"
                                    style="font-size: 10px; margin-top: 3px;"></i><span
                                    class="ps-1 pe-2">{{ $status }}</span></span>
                        </td>

                        <td class="py-3 text-center align-middle">
                            <div class="d-flex flex-column gap-1">
                                <span class="fw-bold data-text">{{ $createdAt->format('M. d, Y') }}</span>
                                <span class="muted-text small fw-semibold">{{ $createdAt->format('h:i:s A') }}</span>
                            </div>
                        </td>

                        <td class="py-3 text-center align-middle">
                            <div class="d-flex flex-column gap-1">
                                <span class="fw-bold data-text">{{ $effDate->format('M. d, Y') }}</span>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center action-button-group gap-3">
                                <a type="button" href="{{ route('tariff-lists.edit', $tariffModel->tariff_list_id) }}"
                                    class="btn btn-sm btn-info d-flex justify-content-between py-2 gap-1">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </a>

                                <a type="button" href="{{ route('tariff-lists.view', $tariffModel->tariff_list_id) }}"
                                    class="btn btn-sm btn-primary d-flex justify-content-between py-2 gap-1">
                                    <i class="fas fa-eye me-2"></i>View
                                </a>

                                <button type="button" onclick="openDeleteModal('{{ $tariffModel->tariff_list_id }}')"
                                    class="btn btn-sm btn-danger d-flex justify-content-between py-2 gap-1">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div style="padding-top: 1rem; padding-bottom: 4px;">
                                <i class="fas fa-list-alt muted-text mb-3" style="font-size: 3rem;"></i>
                                <p class="muted-text fs-3 fw-semibold">No tariff list version/s found.</p>
                                <p class="muted-text fs-5">Click "Create Tariff List Draft" button to add your first
                                    tariff list.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
