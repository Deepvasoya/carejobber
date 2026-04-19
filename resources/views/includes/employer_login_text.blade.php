

@if(Auth::guard('company')->check())
<a href="{{ route('post.job') }}" class="userloginbox postjobbox" style="border-radius: 25px;padding: 25px 20px;">
@else
<a href="{{ route('employer.landing') }}" class="userloginbox postjobbox" style="border-radius: 25px;padding: 25px 20px;">
@endif		

<h3>{{__('Post a Job Today')}}</h3>
<p>{{__('Discover the ideal candidate for your team')}}</p>
<img src="{{asset('/')}}images/postjob.png" alt="Post a Job Today" />
	
</a>
