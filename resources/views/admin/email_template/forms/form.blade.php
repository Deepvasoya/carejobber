{!! APFrmErrHelp::showErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body">
    <div class="row">
        <div class="col-md-8">
            <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'name') !!}">
                {!! Form::label('name', 'Template Name', ['class' => 'bold']) !!}                    
                {!! Form::text('name', null, array('class'=>'form-control', 'id'=>'name', 'placeholder'=>'Template Name')) !!}
                {!! APFrmErrHelp::showErrors($errors, 'name') !!}                                       
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'is_active') !!}">
                {!! Form::label('is_active', 'Status', ['class' => 'bold']) !!}                    
                {!! Form::select('is_active', [1 => 'Active', 0 => 'Inactive'], null, array('class'=>'form-control', 'id'=>'is_active')) !!}
                {!! APFrmErrHelp::showErrors($errors, 'is_active') !!}                                       
            </div>
        </div>
    </div>
    
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'subject') !!}">
        {!! Form::label('subject', 'Email Subject', ['class' => 'bold']) !!}                    
        {!! Form::text('subject', null, array('class'=>'form-control', 'id'=>'subject', 'placeholder'=>'Email Subject')) !!}
        {!! APFrmErrHelp::showErrors($errors, 'subject') !!}
        <small class="form-text text-muted">You can use shortcodes in the subject line</small>
    </div>
    
    <div class="form-group mb-3 {!! APFrmErrHelp::hasError($errors, 'body') !!}">
        {!! Form::label('body', 'Email Body', ['class' => 'bold']) !!}                    
        {!! Form::textarea('body', null, array('class'=>'form-control', 'id'=>'body', 'rows' => 15)) !!}
        {!! APFrmErrHelp::showErrors($errors, 'body') !!}                                       
    </div>

    @if(isset($emailTemplate))
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <strong>Available Shortcodes</strong>
        </div>
        <div class="card-body">
            <p class="mb-2">Click on a shortcode to copy it to clipboard:</p>
            <div class="row">
                @php
                    $shortcodes = $emailTemplate->getShortcodesArray();
                @endphp
                @foreach($shortcodes as $shortcode => $description)
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" value="{{ $shortcode }}" readonly>
                        <button class="btn btn-sm btn-outline-secondary copy-shortcode" type="button" data-shortcode="{{ $shortcode }}">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <small class="text-muted">{{ $description }}</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    
    <div class="form-actions">
        {!! Form::button('Update <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>', array('class'=>'btn btn-large btn-primary', 'type'=>'submit')) !!}
        @if(isset($emailTemplate))
        <a href="{{ route('preview.email.template', $emailTemplate->id) }}" class="btn btn-info" target="_blank">
            <i class="fa fa-eye"></i> Preview
        </a>
        @endif
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Copy shortcode to clipboard
        $('.copy-shortcode').on('click', function() {
            var shortcode = $(this).data('shortcode');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(shortcode).select();
            document.execCommand("copy");
            $temp.remove();
            
            // Show feedback
            $(this).html('<i class="fa fa-check"></i>');
            setTimeout(() => {
                $(this).html('<i class="fa fa-copy"></i>');
            }, 1000);
        });
    });
</script>
@include('admin.shared.tinyMCE')
@endpush
