{{-- Same blocks as resources/views/user/build_resume.blade.php (sidebar /build-resume link unchanged) --}}
<div class="userccount" id="resume-builder-section">
    <div class="formpanel mt0">
        <div class="editprofilebox">
            <h3>{{ __('Get seen faster by employers by adding more details about you below') }}</h3>
            @include('user.forms.cv.cvs')
            @include('user.forms.project.projects')
            @include('user.forms.experience.experience')
            @include('user.forms.education.education')
            @include('user.forms.skill.skills')
            @include('user.forms.language.languages')
        </div>
    </div>
</div>
