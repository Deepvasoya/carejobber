@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->
<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>__('Blog')])
<!-- Inner Page Title end -->

<header id="joblisting-headerwrap" class="d-none d-sm-block mt-4 mb-3">
    <div class="container-fluid">
        <section id="joblisting-header" role="search" >
            <div class="container">
                <h2>{{__('Blogs By')}}: {{$category->heading}}</h2>
            </div>
        </section>
    </div>
</header>

<section id="blog-content">
    <div class="container">

        <!-- Blog start -->
        <div class="row">
            <div class="col-lg-9">
                <!-- Blog List start -->
                <div class="blogwrapper">
                    <ul class="blogList row">
                        @if(null!==($blogs))
                        <?php
                        $count = 1;
                        ?>
                        @foreach($blogs as $blog)
                        <?php
                        $cate_ids = explode(",", $blog->cate_id);
                        $data = DB::table('blog_categories')->whereIn('id', $cate_ids)->get();
                        $cate_array = array();
                        foreach ($data as $cat) {
                            $cate_array[] = "<a href='" . url('/blog/category/') . "/" . $cat->slug . "'>$cat->heading</a>";
                        }
                        ?>
                        
                        <li class="col-lg-6">
                            <div class="bloginner">
                                <div class="postimg">{{$blog->printBlogImage()}}</div>

                                <div class="post-header">
                                    <h4><a href="{{route('blog-detail',$blog->slug)}}">{{$blog->heading}}</a></h4>
                                    <div class="postmeta">{{__('Category')}}: {!!implode(', ',$cate_array)!!}
                                    </div>
                                </div>
                                <p>{!! \Illuminate\Support\Str::limit($blog->content, $limit = 150, $end = '...') !!}</p>

                            </div>
                        </li>

                        
                        <?php $count++; ?>
                        @endforeach
                        @endif

                    </ul>
                </div>

                <!-- Pagination -->
                <div class="pagiWrap">
                    <nav aria-label="Page navigation example">
                        @if(isset($blogs) && count($blogs))
                        {{ $blogs->appends(request()->query())->links() }} @endif
                    </nav>
                </div>
            </div>
			 <div class="col-lg-3">
				 
				 <div class="blogsidebar">
          <!-- Search -->
          <div class="widget">
            <h5 class="widget-title">{{__('Search')}}</h5>
            <div class="search">
              <form action="{{route('blog-search')}}" method="GET">
                <input type="text" class="form-control" placeholder="{{__('Search')}}" name="search">
                <button type="submit" class="btn"><i class="fa fa-search"></i></button>
              </form>
            </div>
          </div>
          <!-- Categories -->
          @if(null!==($blogs_categories))
          <div class="widget">
            <h5 class="widget-title">{{__('Categories')}}</h5>
            <ul class="categories">
            @foreach($blogs_categories as $category)
              <li><a href="{{url('/blog/category/').'/'.$category->slug}}">{{$category->heading}}</a></li>
            @endforeach
            </ul>
          </div>
          @endif
        </div>
            </div>
        </div>
        <div class="py-5"></div>
    </div>
</section>




@include('includes.footer')
@endsection