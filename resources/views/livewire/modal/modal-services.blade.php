<div id="modal-services-root">
    <div id="services-modal-overlay" class="modal-overlay" style="display: {{ $isOpen ? 'flex' : 'none' }};">
        <div id="services-modal-container" class="modal-container">
            <div class="modal-header">
                <h2>Manage Services</h2>
                <button type="button" class="modal-close" onclick="closeServicesModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-section" id="form-section">
                    <h3 class="fw-bold">Add New Service</h3>
                    <form wire:submit.prevent="addService">
                        <div class="form-group">
                            <label for="new-service-type" class="fw-bold">Service Type <span class="required-asterisk">*</span></label>
                            <input type="text" id="new-service-type" wire:model.defer="newServiceType">
                            @error('newServiceType') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="fw-bold">Assistance Scope</label>
                            <div id="new-assist-scope-container" class="checklist-container">
                                @foreach($assistScopeOptions as $option)
                                    <div class="checkbox-item">
                                        <input type="checkbox" wire:model="newAssistScope" value="{{ $option }}" id="assist-{{ md5($option) }}">
                                        <label for="assist-{{ md5($option) }}"><span class="checkbox-label-text">{{ $option }}</span></label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">ADD SERVICE</button>
                    </form>
                </div>

                <div class="list-section">
                    <h3 class="fw-bold">Existing Services</h3>
                    <ul class="services-list">
                        @foreach($services as $svc)
                            <li id="service-item-{{ $svc['service_id'] }}" class="service-item">
                                @if($editingServiceId === $svc['service_id'])
                                    <form wire:submit.prevent="updateService">
                                        <div class="form-group">
                                            <input type="text" wire:model.defer="editingServiceType">
                                            @error('editingServiceType') <span class="error-message">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="fw-bold">Assistance Scope</label>
                                            <div class="editing-assist-scope-container checklist-container">
                                                @foreach($assistScopeOptions as $option)
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" wire:model="editingAssistScope" value="{{ $option }}" id="edit-assist-{{ md5($option) }}-{{ $svc['service_id'] }}">
                                                        <label for="edit-assist-{{ md5($option) }}-{{ $svc['service_id'] }}"><span class="checkbox-label-text">{{ $option }}</span></label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="button-group">
                                            <button type="submit" class="btn btn-success btn-sm">SAVE</button>
                                            <button type="button" wire:click="cancelEdit" class="btn btn-secondary btn-sm">CANCEL</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="service-details">
                                        <div class="service-name">{{ $svc['service_type'] }}</div>
                                        <div class="service-meta"><strong>Assistance Scope:</strong>
                                            @if(!empty($svc['assist_scope_list']))
                                                <ul style="list-style: disc;">
                                                    @foreach($svc['assist_scope_list'] as $item)
                                                        <li style="margin-left: 14px;">{{ $item }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="service-actions">
                                        <button type="button" wire:click="startEdit('{{ $svc['service_id'] }}')" class="btn btn-info btn-sm edit-service-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293z"/>
                                            </svg>
                                        </button>
                                        <button type="button" wire:click="deleteService('{{ $svc['service_id'] }}')" class="btn btn-danger btn-sm delete-service-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeServicesModal()">CANCEL</button>
                <button type="button" class="btn btn-success" onclick="closeServicesModal()">CONFIRM CHANGES</button>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('openServicesModal', function () {
            var el = document.getElementById('services-modal-overlay');
            if (el) el.style.display = 'flex';
        });

        window.addEventListener('closeServicesModal', function () {
            var el = document.getElementById('services-modal-overlay');
            if (el) el.style.display = 'none';
        });

        document.addEventListener('DOMContentLoaded', function () {
            var overlay = document.getElementById('services-modal-overlay');
            if (overlay) {
                overlay.addEventListener('click', function (e) {
                    if (e.target === overlay) closeServicesModal();
                });
            }
        });
    </script>
</div>
