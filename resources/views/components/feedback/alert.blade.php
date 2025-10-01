<link href="{{ asset('css/components/feedback/alert.css') }}" rel="stylesheet">
<script src="{{ asset('js/components/feedback/alert.js') }}"></script>

<div id="feedback-message-container" aria-live="polite" aria-atomic="true">
    @if(session('success'))
        <div class="alert-message alert alert-success d-flex align-items-start" role="alert">
            <div class="message-left d-flex align-items-center">
                <i class="fas fa-check icon" aria-hidden="true"></i>
            </div>

            <div class="message-body flex-grow-1 ms-3">
                <div class="message-title">Success:</div>
                <div class="message-text">{{ session('success') }}</div>
            </div>

            <button type="button" class="close-btn btn btn-link p-0 ms-3" aria-label="Close">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-message alert alert-danger d-flex align-items-start" role="alert">
            <div class="message-left d-flex align-items-center">
                <i class="fas fa-ban icon" aria-hidden="true"></i>
            </div>

            <div class="message-body flex-grow-1 ms-3">
                <div class="message-title">Error:</div>
                <div class="message-text">{{ session('error') }}</div>
            </div>

            <button type="button" class="close-btn btn btn-link p-0 ms-3" aria-label="Close">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert-message alert alert-warning d-flex align-items-start" role="alert">
            <div class="message-left d-flex align-items-center">
                <i class="fas fa-exclamation-triangle icon" aria-hidden="true"></i>
            </div>

            <div class="message-body flex-grow-1 ms-3">
                <div class="message-title">Warning:</div>
                <div class="message-text">{{ session('warning') }}</div>
            </div>

            <button type="button" class="close-btn btn btn-link p-0 ms-3" aria-label="Close">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert-message alert alert-info d-flex align-items-start" role="alert">
            <div class="message-left d-flex align-items-center">
                <i class="fas fa-info-circle icon" aria-hidden="true"></i>
            </div>

            <div class="message-body flex-grow-1 ms-3">
                <div class="message-title">Info:</div>
                <div class="message-text">{{ session('info') }}</div>
            </div>

            <button type="button" class="close-btn btn btn-link p-0 ms-3" aria-label="Close">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-message alert alert-danger d-flex align-items-start" role="alert">
            <div class="message-left d-flex align-items-center">
                <i class="fas fa-ban icon" aria-hidden="true"></i>
            </div>

            <div class="message-body flex-grow-1 ms-3">
                <div class="message-title">Error:</div>
                <div class="message-text">
                    <ul class="error-list">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <button type="button" class="close-btn btn btn-link p-0 ms-3" aria-label="Close">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    @endif
</div>
