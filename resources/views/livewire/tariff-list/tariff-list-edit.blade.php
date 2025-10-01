<div id="editTariffModal" @if($show) class="modal fade show" style="display: block; background: rgba(0, 0, 0, 0.5);" @else class="modal fade" style="display: none;" @endif tabindex="-1" role="dialog" aria-hidden="{{ $show ? 'false' : 'true' }}">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="form-legend ms-2">
                    <i class="fas fa-edit fa-fw"></i>
                    <span class="header-title ms-2">{{ $tariffModel->tariff_list_id ?? '' }} (Editing)</span>
                </div>
                <button type="button" wire:click="closeModal" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body px-2 pt-2 pb-0">
                <form wire:submit.prevent="save" id="tariffEditFormModal">
                    <input type="hidden" wire:model="tariffListId">
                    @if($isEffective)
                        <input type="hidden" wire:model="isEffective" value="1">
                    @endif
                    <div class="form-content">
                        <div class="date-row mb-4">
                            <label class="effectivity-date fs-5" for="effectivity-date-modal-edit">Effectivity Date:</label>
                            <div class="date-input-container">
                                <input type="date" id="effectivity-date-modal-edit" name="effectivity_date" class="date-input form-control fs-5" wire:model="effectivity_date" min="{{ now()->toDateString() }}" required>
                            </div>
                        </div>
                        <h5 class="select-service fw-bold mb-3">Manage services to include in this version.</h5>
                        <div class="services-container" id="servicesContainerEditModal">
                            @foreach($services as $service)
                                <div class="form-check mb-3 d-flex align-items-center p-3 service-card ms-3 service-row">
                                    <input type="checkbox" name="services[]" value="{{ $service->service_id }}" id="service_{{ $service->service_id }}" class="form-check-input service-checkbox selector-checkbox" data-service-type="{{ $service->service_type }}" wire:model="selectedServices" value="{{ $service->service_id }}">
                                    <a href="#" class="form-check-label ms-3 service-label fs-5" id="serviceLabel_{{ $service->service_id }}" data-service-type="{{ $service->service_type }}" data-service-id="{{ $service->service_id }}">{{ $service->service_type }}</a>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            @error('selectedServices')
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                                </div>
                            @enderror
                            @error('effectivity_date')
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="range-row my-5">
                            <div id="edit-tariffCarousel" class="carousel slide" data-bs-ride="false" data-bs-interval="false" data-bs-touch="false">
                                <div class="carousel-inner">
                                    @foreach($services as $index => $service)
                                        @php
                                            $serviceType = $service->service_type;
                                            $serviceIdForAttr = $service->service_id;
                                        @endphp
                                        <div class="carousel-item @if($loop->first) active @endif" data-service-type="{{ $serviceType }}" data-service-id="{{ $serviceIdForAttr }}">
                                            <div class="carousel-content pt-4">
                                                <span class="section-title fw-bold">
                                                    <button class="nav-arrow carousel-control-prev pe-1" type="button" data-bs-target="#edit-tariffCarousel" data-bs-slide="prev" aria-label="previous">◀</button>
                                                    {{ $serviceType }}
                                                    <button class="nav-arrow carousel-control-next ps-1" type="button" data-bs-target="#edit-tariffCarousel" data-bs-slide="next" aria-label="next">▶</button>
                                                </span>
                                                <div class="table-responsive table-container">
                                                    <table class="tariff-list-table">
                                                        <thead>
                                                            <tr>
                                                                <th id="tariff-list-table-header-6" class="py-2 fs-5">Minimum</th>
                                                                <th id="tariff-list-table-header-7" class="py-2 fs-5">Maximum</th>
                                                                <th id="tariff-list-table-header-8" class="py-2 fs-5">Discount (%)</th>
                                                                <th id="tariff-list-table-header-9" class="py-2 fs-5">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="service-rows" data-service-id="{{ $serviceIdForAttr }}">
                                                            @foreach($ranges as $key => $tariff)
                                                                @if($tariff['service_id'] == $serviceIdForAttr)
                                                                    <tr class="money-amount-row" data-range-index="{{ $key }}">
                                                                        <td class="money-amount-cell">
                                                                            <div class="input-group px-3">
                                                                                <span class="input-group-text fs-5">₱</span>
                                                                                <input class="form-control form-control-sm tariff-input text-end" type="text" inputmode="numeric" wire:model.live="ranges.{{ $key }}.exp_range_min" wire:key="min-{{ $key }}-{{ $serviceIdForAttr }}" placeholder="0" maxlength="10">
                                                                            </div>
                                                                            @error("ranges.{$key}.exp_range_min")
                                                                                <div class="speech-wrapper justify-content-start">
                                                                                    <div class="speech-bubble speech-left error-bubble small">{{ $message }}</div>
                                                                                </div>
                                                                            @enderror
                                                                        </td>
                                                                        <td class="money-amount-cell">
                                                                            <div class="input-group px-3">
                                                                                <span class="input-group-text fs-5">₱</span>
                                                                                <input class="form-control form-control-sm tariff-input text-end" type="text" inputmode="numeric" wire:model.live="ranges.{{ $key }}.exp_range_max" wire:key="max-{{ $key }}-{{ $serviceIdForAttr }}" placeholder="0" maxlength="10">
                                                                            </div>
                                                                            @error("ranges.{$key}.exp_range_max")
                                                                                <div class="speech-wrapper justify-content-start">
                                                                                    <div class="speech-bubble speech-left error-bubble small">{{ $message }}</div>
                                                                                </div>
                                                                            @enderror
                                                                        </td>
                                                                        <td class="money-amount-cell">
                                                                            <div class="input-group px-3">
                                                                                <input class="form-control form-control-sm tariff-input text-end" type="text" inputmode="numeric" wire:model.live="ranges.{{ $key }}.discount_percent" wire:key="discount-{{ $key }}-{{ $serviceIdForAttr }}" placeholder="0" maxlength="3">
                                                                                <span class="input-group-text fs-5 fw-bold m-0">%</span>
                                                                            </div>
                                                                            @error("ranges.{$key}.discount_percent")
                                                                                <div class="speech-wrapper justify-content-end">
                                                                                    <div class="speech-bubble speech-right error-bubble small">{{ $message }}</div>
                                                                                </div>
                                                                            @enderror
                                                                        </td>
                                                                        <td>
                                                                            <div class="text-center d-flex flex-row align-items-center justify-content-center gap-3 p-3">
                                                                                <button type="button" class="row-add-btn" aria-label="add-row" wire:click.prevent="addRangeAt({{ $key }}, '{{ $serviceIdForAttr }}')">
                                                                                    <i class="fas fa-plus"></i>
                                                                                </button>
                                                                                <button type="button" class="row-remove-btn" aria-label="remove-row" wire:click.prevent="removeRange({{ $key }})">
                                                                                    <i class="fas fa-trash-alt"></i>
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end pb-3" id="tariffEditModalFooter">
                        <button type="button" class="btn btn-secondary action-buttons" wire:click="closeModal">
                            <i class="fas fa-times me-3"></i><span class="fs-5">CANCEL</span>
                        </button>
                        <button type="submit" class="btn btn-primary action-buttons">
                            <i class="fas fa-check me-3"></i><span class="fs-5">CONFIRM CHANGES</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
