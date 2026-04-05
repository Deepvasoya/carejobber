@if (Auth::check())
    <div class="modal fade" id="applyJobListModal" tabindex="-1" aria-labelledby="applyJobListModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content"
                style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <div class="modal-body text-center py-5 text-muted">
                    <div class="spinner-border text-secondary" role="status"><span
                            class="visually-hidden">{{ __('Loading') }}…</span></div>
                </div>
            </div>
        </div>
    </div>

    <div id="applySubmitPageOverlay" class="apply-submit-page-overlay" aria-hidden="true" role="status">
        <div class="apply-submit-page-overlay__card">
            <div class="apply-submit-page-overlay__rings" aria-hidden="true">
                <span class="apply-submit-page-overlay__ring"></span>
                <span class="apply-submit-page-overlay__ring apply-submit-page-overlay__ring--delay"></span>
            </div>
            <p class="apply-submit-page-overlay__title">{{ __('Submitting your application') }}</p>
            <p class="apply-submit-page-overlay__hint">{{ __('Please wait a moment…') }}</p>
        </div>
    </div>
@endif
