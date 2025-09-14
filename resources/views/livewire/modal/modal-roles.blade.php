<div id="modal-roles-root">
    <div id="roles-modal-overlay" class="modal-overlay" style="display: {{ $isOpen ? 'flex' : 'none' }};">
        <div id="roles-modal-container" class="modal-container">
            <div class="modal-header">
                <h2>Manage Roles</h2>
                <button type="button" class="modal-close" onclick="closeRolesModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-section" id="form-section">
                    <h3 class="fw-bold">Add New Role</h3>
                    <form wire:submit.prevent="addRole">
                        <div class="form-group">
                            <label for="new-role-name" class="fw-bold">Role Name <span class="required-asterisk">*</span></label>
                            <input type="text" id="new-role-name" wire:model.defer="newRoleName">
                            @error('newRoleName') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="fw-bold">Allowed Actions</label>
                            <div id="new-allowed-actions-container" class="checklist-container">
                                @foreach($allowedActionsOptions as $option)
                                    <div class="checkbox-item">
                                        <input type="checkbox" wire:model="newAllowedActions" value="{{ $option }}" id="action-{{ md5($option) }}">
                                        <label for="action-{{ md5($option) }}"><span class="checkbox-label-text">{{ $option }}</span></label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="fw-bold">Access Scope</label>
                            <div id="new-access-scope-container" class="checklist-container">
                                @foreach($accessScopeOptions as $option)
                                    <div class="checkbox-item">
                                        <input type="checkbox" wire:model="newAccessScope" value="{{ $option }}" id="scope-{{ md5($option) }}">
                                        <label for="scope-{{ md5($option) }}"><span class="checkbox-label-text">{{ $option }}</span></label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">ADD ROLE</button>
                    </form>
                </div>

                <div class="list-section">
                    <h3 class="fw-bold">Existing Roles</h3>
                    <ul class="roles-list">
                        @foreach($roles as $role)
                            <li id="role-item-{{ $role['role_id'] }}" class="role-item">
                                @if($editingRoleId === $role['role_id'])
                                    <form wire:submit.prevent="updateRole">
                                        <div class="form-group">
                                            <input type="text" wire:model.defer="editingRoleName">
                                            @error('editingRoleName') <span class="error-message">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="fw-bold">Allowed Actions</label>
                                            <div class="editing-allowed-actions-container checklist-container">
                                                @foreach($allowedActionsOptions as $option)
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" wire:model="editingAllowedActions" value="{{ $option }}" id="edit-action-{{ md5($option) }}-{{ $role['role_id'] }}">
                                                        <label for="edit-action-{{ md5($option) }}-{{ $role['role_id'] }}"><span class="checkbox-label-text">{{ $option }}</span></label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="fw-bold">Access Scope</label>
                                            <div class="editing-access-scope-container checklist-container">
                                                @foreach($accessScopeOptions as $option)
                                                    <div class="checkbox-item">
                                                        <input type="checkbox" wire:model="editingAccessScope" value="{{ $option }}" id="edit-scope-{{ md5($option) }}-{{ $role['role_id'] }}">
                                                        <label for="edit-scope-{{ md5($option) }}-{{ $role['role_id'] }}"><span class="checkbox-label-text">{{ $option }}</span></label>
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
                                    <div class="role-details">
                                        <div class="role-name">{{ $role['role'] }}</div>
                                        <div class="role-meta"><strong>Allowed Actions:</strong>
                                            @if(!empty($role['allowed_actions_list']))
                                                <ul style="list-style: disc;">
                                                    @foreach($role['allowed_actions_list'] as $item)
                                                        <li style="margin-left: 14px;">{{ $item }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </div>
                                        <div class="role-meta"><strong>Access Scope:</strong>
                                            @if(!empty($role['access_scope_list']))
                                                <ul style="list-style: disc;">
                                                    @foreach($role['access_scope_list'] as $item)
                                                        <li style="margin-left: 14px;">{{ $item }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="role-actions">
                                        <button type="button" wire:click="startEdit('{{ $role['role_id'] }}')" class="btn btn-info btn-sm edit-role-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293z"/>
                                            </svg>
                                        </button>
                                        <button type="button" wire:click="deleteRole('{{ $role['role_id'] }}')" class="btn btn-danger btn-sm delete-role-btn">
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
                <button type="button" class="btn btn-secondary" onclick="closeRolesModal()">CANCEL</button>
                <button type="button" class="btn btn-success" onclick="closeRolesModal()">CONFIRM CHANGES</button>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('openRolesModal', function () {
            var el = document.getElementById('roles-modal-overlay');
            if (el) el.style.display = 'flex';
        });

        window.addEventListener('closeRolesModal', function () {
            var el = document.getElementById('roles-modal-overlay');
            if (el) el.style.display = 'none';
        });

        document.addEventListener('DOMContentLoaded', function () {
            var overlay = document.getElementById('roles-modal-overlay');
            if (overlay) {
                overlay.addEventListener('click', function (e) {
                    if (e.target === overlay) closeRolesModal();
                });
            }
        });
    </script>
</div>
