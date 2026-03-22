<?php

namespace App\Http\Controllers\Admin;

use DB;
use Input;
use Redirect;
use App\Package;
use App\SiteSetting;
use App\Helpers\MiscHelper;
use App\Helpers\DataArrayHelper;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\Http\Requests\PackageFormRequest;
use App\Http\Controllers\Controller;

class PackageController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function indexPackages()
    {
        return view('admin.package.index');
    }

    public function createPackage()
    {
        return view('admin.package.add');
    }

    public function storePackage(PackageFormRequest $request)
    {
        $package = new Package();

        $package->package_title = $request->input('package_title');
        $package->package_price = $request->input('package_price');
        $package->package_num_days = $request->input('package_num_days');
        $package->package_num_listings = $request->input('package_num_listings');
        $package->package_for = $request->input('package_for');
        if ($package->package_for === 'employer') {
            $package->type = $request->input('package_type', Package::TYPE_ONE_TIME_CREDITS);
            $package->duration_days = $request->filled('duration_days') ? (int) $request->input('duration_days') : null;
            $package->subscription_unlimited_jobs = $request->boolean('subscription_unlimited_jobs');
            $package->stripe_price_id = $request->filled('stripe_price_id') ? $request->input('stripe_price_id') : null;
            $package->country_code = $request->filled('country_code') ? strtoupper(substr($request->input('country_code'), 0, 2)) : null;
            $package->rebate_percent = $request->filled('rebate_percent') ? (int) $request->input('rebate_percent') : null;
            $package->is_active = $request->boolean('is_active');
        }
        $package->save();
        /*         * ************************************ */
        flash('Package has been added!')->success();
        return \Redirect::route('edit.package', array($package->id));
    }

    public function editPackage($id)
    {
        $package = Package::findOrFail($id);
        return view('admin.package.edit')
                        ->with('package', $package);
    }

    public function updatePackage($id, PackageFormRequest $request)
    {
        $package = Package::findOrFail($id);

        $package->package_title = $request->input('package_title');
        $package->package_price = $request->input('package_price');
        $package->package_num_days = $request->input('package_num_days');
        $package->package_num_listings = $request->input('package_num_listings');
        $package->package_for = $request->input('package_for');
        if ($package->package_for === 'employer') {
            $package->type = $request->input('package_type', Package::TYPE_ONE_TIME_CREDITS);
            $package->duration_days = $request->filled('duration_days') ? (int) $request->input('duration_days') : null;
            $package->subscription_unlimited_jobs = $request->boolean('subscription_unlimited_jobs');
            $package->stripe_price_id = $request->filled('stripe_price_id') ? $request->input('stripe_price_id') : null;
            $package->country_code = $request->filled('country_code') ? strtoupper(substr($request->input('country_code'), 0, 2)) : null;
            $package->rebate_percent = $request->filled('rebate_percent') ? (int) $request->input('rebate_percent') : null;
            $package->is_active = $request->boolean('is_active');
        }

        $package->update();
        flash('Package has been updated!')->success();
        return \Redirect::route('edit.package', array($package->id));
    }

    public function deletePackage(Request $request)
    {
        $id = $request->input('id');
        try {
            $package = Package::findOrFail($id);
            $package->delete();
            return 'ok';
        } catch (ModelNotFoundException $e) {
            return 'notok';
        }
    }

    public function fetchPackagesData(Request $request)
{
    $packages = Package::select([
                'packages.id',
                'packages.package_title',
                'packages.package_price',
                'packages.package_num_days',
                'packages.package_num_listings',
                'packages.package_for',
            ])->orderBy('packages.package_for');

    $siteSetting = SiteSetting::findOrFail(1272);
    $currencyCode = $siteSetting->default_currency_code;
    
    return Datatables::of($packages)
        ->filter(function ($query) use ($request) {
            if ($request->has('package_title') && !empty($request->package_title)) {
                $query->where('packages.package_title', 'like', "%{$request->get('package_title')}%");
            }
            if ($request->has('package_price') && !empty($request->package_price)) {
                $query->where('packages.package_price', 'like', "{$request->get('package_price')}%");
            }
            if ($request->has('package_num_days') && !empty($request->package_num_days)) {
                $query->where('packages.package_num_days', 'like', "{$request->get('package_num_days')}%");
            }

            if ($request->has('package_num_listings') && !empty($request->package_num_listings)) {
                $query->where('packages.package_num_listings', 'like', "{$request->get('package_num_listings')}%");
            }

            if ($request->has('package_for') && !empty($request->package_for)) {
                $query->where('packages.package_for', 'like', "{$request->get('package_for')}");
            }
        })
        ->addColumn('package_price', function ($packages) use ($currencyCode) {
            return $currencyCode . ' ' . number_format($packages->package_price, 2);
        })
        ->addColumn('action', function ($packages) {
            $editButton = '<li>
                <a class="dropdown-item" href="' . route('edit.package', ['id' => $packages->id]) . '">
                <i class="ri ri-pencil-line me-1"></i> Edit</a>
            </li>';

            // Hide delete button if package ID is 9
            $deleteButton = ($packages->id == 9) ? '' : '<li>
                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deletePackage(' . $packages->id . ');">
                <i class="ri ri-delete-bin-line me-1"></i> Delete</a>
            </li>';

            return '
            <div class="btn-group">
                <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action
                </button>
                <ul class="dropdown-menu">
                    ' . $editButton . $deleteButton . '
                </ul>
            </div>';
        })
        ->rawColumns(['action'])
        ->setRowId(function ($packages) {
            return 'packageDtRow' . $packages->id;
        })
        ->make(true);
}


}
