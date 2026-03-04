@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->

@if(null!==($blog))


<div class="listpgWraper pt-5">
<section id="blog-content">
    <div class="container">
        <?php
        $cate_ids = explode(",", $blog->cate_id);
        $data = DB::table('blog_categories')->whereIn('id', $cate_ids)->get();
        $cate_array = array();
        foreach ($data as $cat) {
            $cate_array[] = "<a href='" . url('/blog/category/') . "/" . $cat->slug . "'>$cat->heading</a>";
        }
        ?>
        <!-- Blog start -->
        <div class="row">
       
            <div class="col-lg-9">
                <!-- Blog List start -->
                <div class="blogdetailwrap">
                  <h1>{{$blog->heading}}</h1>

                        <div class="postimg">{{$blog->printBlogImage()}}</div>

                        <div class="post-header">
                            
                            <div class="postmeta">{{__('Category')}}: {!!implode(', ',$cate_array)!!}</div>
                        </div>
                        <p>{!! $blog->content !!}</p>


                      
                </div>


            </div>
			
			 <div class="col-lg-3">
				 
				 <div class="blogsidebar"> 
          <!-- Search -->
          <div class="widget mb-5">
            <h5 class="widget-title">{{__('Search')}}</h5>
            <div class="search">
              <form action="{{route('blog-search')}}" method="GET">
                <input type="text" class="form-control" placeholder="{{__('Search')}}" name="search">
                <button type="submit" class="btn"><i class="fa fa-search"></i></button>
              </form>
            </div>
          </div>
          <!-- Categories -->
          @if(null!==($categories))
          <div class="widget">
            <h5 class="widget-title">{{__('Categories')}}</h5>
            <ul class="categories">
            @foreach($categories as $category)
              <li><a href="{{url('/blog/category/').'/'.$category->slug}}">{{$category->heading}}</a></li>
            @endforeach
            </ul>
          </div>
          @endif
        </div>
			</div>
			

        </div>
    </div>
</section>
</div>

@endif
@include('includes.footer')
@endsection