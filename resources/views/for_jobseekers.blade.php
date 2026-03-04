@extends('layouts.app')

@section('content')

<!-- Header start -->

@include('includes.header')

<!-- Header end --> 



<div class="frsktopbanner">

    <div class="container">

        <div class="row align-items-center justify-content-center frskinfo">

            <div class="col-lg-6">

                <h3>Discover Your Ideal Job Board</h3>

<p>Unlock endless opportunities with trusted employers. Our platform streamlines your job search, making it efficient and stress-free, helping you find healthcare jobs that match your interest and career goals.</p>



                <div class="viewallbtn">

                    @if(Auth::check() && !Auth::guard('company')->check())

                    <a href="{{ route('my.profile') }}">Create a CV</a>

                    @else                    

                    <a href="{{route('login')}}">Create a CV</a>

                    @endif







                </div>

            </div>    

            <div class="col-lg-5">

            <div class="seekerimgtop"><img src="{{asset('/')}}images/for-seeker-top.png" alt="" /></div>	

            



            </div>

        </div>





    </div>

</div>




















<div class="section txtsec1 whitebg">

        <div class="container">

            <div class="dbtitle">

                <h3>Why Choose Us</h3>                

             </div>



        

             <div class="txtdata">

                <div class="row justify-content-center">

                    <div class="col-lg-4">

                     <div class="secimg mb-3"><img src="{{asset('/')}}images/its-free.png" alt="" /></div>

                     <div class="subheading">Job Search Simplified</div>

                        <p>Find the right fit with our user-friendly tools and resources, empowering you to search smarter, not harder.</p>       

                                        

                    </div>

                    <div class="col-lg-4">

                        <div class="secimg mb-3"><img src="{{asset('/')}}images/reelancer-cuate.png" alt="" /></div>

                        <div class="subheading">Only Verified Opportunities</div>

                        <p>We carefully review employers and job postings to help ensure the opportunities you apply for are genuine, relevant, and aligned with your career goals. Discover quality, curated healthcare roles without the stress of endless searching.</p> 

                       

                    </div>

                   

                </div>

             </div>

 



        </div>

    </div>















<div class="section ctabg">

         <div class="container wow bounceInUp animated" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: bounceInUp;">

            <h4>Your dream job is just a click away</h4>

            <p>Begin your healthcare job search today! Create your profile and discover great opportunities</p>

            <div class="viewallbtn">

            @if(Auth::check() && !Auth::guard('company')->check())

                    <a href="{{ route('my.profile') }}">Create a CV</a>

                    @else                    

                    <a href="{{route('login')}}">Create a CV</a>

                    @endif

            </div>

         </div>

      </div>











@include('includes.footer')

@endsection

