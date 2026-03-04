@extends('layouts.app')
@section('content') 
<!-- Header start --> 
@include('includes.header') 
<!-- Header end --> 
<!-- Inner Page Title start --> 
@include('includes.inner_page_title', ['page_title'=>__('Submit Your Job Application')]) 
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container"> @include('flash::message')
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="userccount">
                    <div class="formpanel"> {!! Form::open(array('method' => 'post', 'route' => ['post.apply.job', $job_slug])) !!} 
                        <!-- Job Information -->
                        <h5>{{__('You are about to apply for the job')}}: {{$job->title}}</h5>
                        <div class="row">
                           
                            <div class="col-md-12">
                                <div class="formrow{{ $errors->has('cv_id') ? ' has-error' : '' }}"> {!! Form::select('cv_id', [''=>__('Select Resume on File')]+$myCvs, null, array('class'=>'form-control', 'id'=>'cv_id')) !!}
                                    @if ($errors->has('cv_id')) <span class="help-block"> <strong>{{ $errors->first('cv_id') }}</strong> </span> @endif </div>
                            </div>
                        
                        </div>
                        
                        @if($job->jobQuestions->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>{{__('Additional Questions')}}</h5>
                                @foreach($job->jobQuestions as $index => $question)
                                    <div class="formrow mb-3">
                                        <label for="question_{{$question->id}}">{{$question->question_title}}</label>
                                        {!! Form::textarea('question_answers['.$question->id.']', null, array('class'=>'form-control', 'id'=>'question_'.$question->id, 'rows'=>'3', 'placeholder'=>__('Enter your answer'))) !!}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <br>
                        <input type="submit" class="btn" value="{{__('Apply on Job')}}">
                        {!! Form::close() !!} </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
@push('scripts') 
<script>
    $(document).ready(function () {
        $('#salary_currency').typeahead({
            source: function (query, process) {
                return $.get("{{ route('typeahead.currency_codes') }}", {query: query}, function (data) {
                    console.log(data);
                    data = $.parseJSON(data);
                    return process(data);
                });
            }
        });

    });
</script> 
@endpush