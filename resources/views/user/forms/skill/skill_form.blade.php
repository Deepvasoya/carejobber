<div class="modal-body">
    <div class="form-body">
        <div class="formrow" id="div_job_skill_id">
            @php
            $job_skill_id = (isset($profileSkill) ? $profileSkill->job_skill_id : null);
            $jobSkillsWithOther = (isset($jobSkills) ? $jobSkills : []) + ['0' => __('Other (specify)')];
            @endphp
            {!! Form::select('job_skill_id', [''=>__('Select skill')]+$jobSkillsWithOther, $job_skill_id, array('class'=>'form-control', 'id'=>'job_skill_id')) !!} <span class="help-block job_skill_id-error"></span> </div>
        <div class="formrow" id="custom_job_skill_wrap" style="display:none;">
            <label>{{ __('Custom skill name') }}</label>
            {!! Form::text('custom_job_skill_name', old('custom_job_skill_name'), array('class'=>'form-control', 'id'=>'custom_job_skill_name', 'maxlength'=>200, 'placeholder'=>__('Type the skill name'))) !!}
            <span class="help-block custom_job_skill_name-error"></span>
        </div>
        <div class="formrow" id="div_job_experience_id">
            <?php
            $job_experience_id = (isset($profileSkill) ? $profileSkill->job_experience_id : null);
            ?>
            {!! Form::select('job_experience_id', [''=>__('Year of experience')]+$jobExperiences, $job_experience_id, array('class'=>'form-control', 'id'=>'job_experience_id')) !!} <span class="help-block job_experience_id-error"></span> </div>
    </div>
</div>
<script>
(function () {
    function syncCustomSkillBox() {
        var $s = $('#job_skill_id');
        var v = $s.val();
        $('#custom_job_skill_wrap').toggle(v === '0' || v === 0);
    }
    $(document).off('change.userSkillOther', '#job_skill_id').on('change.userSkillOther', '#job_skill_id', syncCustomSkillBox);
    syncCustomSkillBox();
})();
</script>
