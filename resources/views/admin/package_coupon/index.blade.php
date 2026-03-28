@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li><a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i></li>
                <li><a href="{{ route('list.packages') }}">Packages</a> <i class="fa fa-circle"></i></li>
                <li><span>{{ __('Package coupons') }}</span></li>
            </ul>
        </div>
        <h3 class="page-title">{{ __('Package coupons') }} <small>{{ __('Discount codes for package checkout') }}</small></h3>
        @include('flash::message')

        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-ticket font-dark"></i>
                    <span class="caption-subject font-dark bold uppercase">{{ __('Coupons') }}</span>
                </div>
                <div class="actions">
                    <a href="{{ route('create.package.coupon') }}" class="btn btn-sm btn-success">
                        <i class="fa fa-plus"></i> {{ __('Add coupon') }}
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Value') }}</th>
                                <th>{{ __('Valid') }}</th>
                                <th>{{ __('Uses') }}</th>
                                <th>{{ __('Active') }}</th>
                                <th width="160">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $c)
                            <tr>
                                <td><strong>{{ $c->code }}</strong><br><small class="text-muted">{{ $c->admin_note }}</small></td>
                                <td>{{ $c->discount_type === 'percent' ? __('Percent') : __('Fixed') }}</td>
                                <td>
                                    @if($c->discount_type === 'percent')
                                        {{ rtrim(rtrim(number_format($c->discount_value, 2), '0'), '.') }}%
                                        @if($c->max_discount_amount)
                                            <br><small>{{ __('Max') }} ${{ number_format($c->max_discount_amount, 2) }}</small>
                                        @endif
                                    @else
                                        ${{ number_format($c->discount_value, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        @if($c->starts_at){{ $c->starts_at->format('Y-m-d H:i') }} — @endif
                                        @if($c->ends_at){{ $c->ends_at->format('Y-m-d H:i') }}@else ∞ @endif
                                    </small>
                                </td>
                                <td>{{ $c->redemptions()->count() }}@if($c->usage_limit_total) / {{ $c->usage_limit_total }}@endif</td>
                                <td>@if($c->is_active)<span class="label label-success">{{ __('Yes') }}</span>@else<span class="label label-default">{{ __('No') }}</span>@endif</td>
                                <td>
                                    <a href="{{ route('edit.package.coupon', $c->id) }}" class="btn btn-xs btn-primary">{{ __('Edit') }}</a>
                                    <form action="{{ route('delete.package.coupon', $c->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Delete this coupon?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted">{{ __('No coupons yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $coupons->links() }}
            </div>
        </div>

        <div class="portlet light bordered">
            <div class="portlet-body">
                <h4>{{ __('Where coupons apply') }}</h4>
                <ul>
                    <li>{{ __('Employer: Stripe Checkout for job posting packages and subscriptions (except subscriptions that use a fixed Stripe Price ID).') }}</li>
                    <li>{{ __('Job seeker / company legacy Stripe card form (Pay with Stripe) for packages.') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
