<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center card-body p-0">
        <div class="table-responsive">
            <table class="tariff-list-table mb-0" id="tariffVersionsTable">
                <thead>
                    <tr>
                        <th class="text-center tariff-list-table-header" id="tariff-list-table-header-1">ID Name</th>
                        <th class="text-center tariff-list-table-header" id="tariff-list-table-header-2">Service Type/s Involved</th>
                        <th class="text-center tariff-list-table-header" id="tariff-list-table-header-3">Status</th>
                        <th class="text-center tariff-list-table-header" id="tariff-list-table-header-4">Effectivity Date</th>
                        <th class="text-center tariff-list-table-header" id="tariff-list-table-header-5">Date Created</th>
                        <th class="text-center tariff-list-table-header" id="tariff-list-table-header-6">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tariffModels as $data_id => $tariffModel)
                        @php
                            $servicesList = $groupedTariffs[$data_id] ?? collect();
                            $effDate = \Carbon\Carbon::parse($tariffModel->effectivity_date);
                            $createdDate = \Carbon\Carbon::parse($tariffModel->created_at);
                            $status = $tariffModel->effectivity_status;
                            $textColorClass = 'white';
                            $badgeClass = 'secondary';

                            if ($status === 'Active') {
                                $badgeClass = 'success';
                            } elseif ($status === 'Scheduled') {
                                if ($effDate->isFuture()) {
                                    $badgeClass = 'primary';
                                } else {
                                    $badgeClass = 'danger';
                                }
                            } elseif ($status === 'Unused') {
                                $badgeClass = 'secondary';
                            } elseif ($status === 'Inactive') {
                                $textColorClass = 'black';
                                $badgeClass = 'warning';
                            }
                        @endphp

                        <tr>
                            <td class="px-2 py-3 text-center align-middle tariff-list-id-text fs-5 fw-semibold">{{ $tariffModel->tariff_list_id }}</td>

                            <td class="px-3 py-3 text-center align-middle">
                                <span id="statusBadge" class="badge bg-{{ $badgeClass }} text-{{ $textColorClass }} text-capitalize d-inline-flex align-items-center justify-content-center px-3 py-2">
                                    <i class="fas fa-circle me-3" style="font-size: 0.5rem;"></i> {{ $status }}
                                </span>
                            </td>

                            <td class="px-3 py-3 text-center align-middle">
                                <div class="d-flex flex-column">
                                    <small class="effectivity-date fw-semibold">{{ $effDate->format('M. d, Y') }}</small>
                                    <h6 class="muted-text mt-2">{{ $effDate->diffForHumans() }}</h6>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center align-middle">
                                <div class="d-flex flex-column">
                                    <small class="created-date fw-semibold">{{ $createdDate->format('M. d, Y') }}</small>
                                    <h6 class="muted-text mt-2">{{ $createdDate->diffForHumans() }}</h6>
                                </div>
                            </td>

                            <td class="px-3 py-3 text-center align-middle">
                                <div class="d-flex justify-content-center action-button-group">
                                    <button type="button" onclick="openEditModal('{{ $tariffModel->tariff_list_id }}')" class="btn btn-primary btn-edit">
                                        <i class="fas fa-edit me-2"></i>View/Edit
                                    </button>

                                    <button type="button" onclick="openDeleteModal('{{ $tariffModel->tariff_list_id }}')" class="btn btn-danger btn-delete">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-list-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No tariff lists found</h5>
                                    <p class="text-muted">Click the "Create New Version" button to add your first tariff list.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
