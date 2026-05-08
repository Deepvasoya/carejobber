@extends('layouts.app')

@section('title', "Job Expired — {$job->title} | Medojob")

@section('content')
<div class="container mt-5">
    @include('medo.partials.breadcrumbs', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Jobs', 'url' => url('/jobs')],
        ['label' => $category->name, 'url' => route('medo.jobs.category', $category)],
        ['label' => 'Expired Job', 'url' => null],
    ]])

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <h1 class="h3 mb-3">This Job Has Expired</h1>
                    <p class="text-muted mb-4">The position "{{ $job->title }}" at {{ $job->employer?->name ?? 'this employer' }} is no longer accepting applications.</p>
                    
                    <a href="{{ route('medo.jobs.category.province.city', [$category, $province, $city]) }}" class="btn btn-primary">
                        View Active {{ $category->name }} Jobs in {{ $city->name }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
