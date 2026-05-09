@extends('layouts.app')

@section('content')
@include('includes.header')
@include('flash::message')

<main class="medo-pseo-wrap">
    <div class="container">
        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ $province->name }} {{ __('salary guide') }}</p>
                <h1>{{ $category->name }} {{ __('salary guide for') }} {{ $province->name }}</h1>
                <p class="mb-0">{{ __('Salary data uses seeded union wage grids first, with active posting wages shown as a market signal.') }}</p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Union wage range') }}</span>
                <strong>
                    @if(!empty($gridRange['min']) || !empty($gridRange['max']))
                        ${{ number_format($gridRange['min'] ?: $gridRange['max'], 2) }} - ${{ number_format($gridRange['max'] ?: $gridRange['min'], 2) }}
                    @else
                        {{ __('Pending') }}
                    @endif
                </strong>
            </div>
        </section>

        <section class="medo-pseo-panel">
            <h2>{{ __('Union wage grid') }}</h2>
            @if(isset($salaryGrid) && $salaryGrid->count())
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Step') }}</th>
                                <th>{{ __('Hourly rate') }}</th>
                                <th>{{ __('Union') }}</th>
                                <th>{{ __('Effective') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salaryGrid as $row)
                                <tr>
                                    <td>{{ $row->step }}</td>
                                    <td>${{ number_format($row->hourly_rate, 2) }}</td>
                                    <td>{{ optional($row->union)->acronym ?? optional($row->union)->name }}</td>
                                    <td>{{ optional($row->effective_date)->format('M j, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="medo-muted-box">{{ __('Union wage grid data has not been seeded for this category yet.') }}</div>
            @endif
        </section>

        <section class="medo-pseo-panel">
            <h2>{{ __('Posted wage signal') }}</h2>
            @if($salary['count'] > 0)
                <div class="medo-pseo-grid">
                    <div><span>{{ __('Average low') }}</span><strong>${{ number_format($salary['avg_min'], 2) }}</strong></div>
                    <div><span>{{ __('Average high') }}</span><strong>${{ number_format($salary['avg_max'], 2) }}</strong></div>
                    <div><span>{{ __('Observed range') }}</span><strong>${{ number_format($salary['min'], 2) }} - ${{ number_format($salary['max'], 2) }}</strong></div>
                </div>
            @else
                <div class="medo-muted-box">{{ __('Salary data will appear here once imported jobs include wage ranges.') }}</div>
            @endif
        </section>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
