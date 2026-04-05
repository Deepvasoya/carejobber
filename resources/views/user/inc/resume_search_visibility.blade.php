{{-- Employer resume search visibility (also on Privacy & Data Settings) --}}
<hr class="my-3">
<div class="row">
    <div class="col-md-12">
        <h5 class="mb-2">{{ __('Employer resume search') }}</h5>
        <p class="text-muted small mb-2">{{ __('Control whether employers can find your profile when they search the resume database.') }}</p>
        <input type="hidden" name="visible_in_employer_resume_search" value="0" />
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" name="visible_in_employer_resume_search" value="1" id="visible_in_employer_resume_search"
                {{ old('visible_in_employer_resume_search', ($user->visible_in_employer_resume_search ?? true) ? 1 : 0) ? 'checked' : '' }} />
            <label class="form-check-label" for="visible_in_employer_resume_search">{{ __('Show my profile in employer resume search') }}</label>
        </div>
    </div>
</div>
