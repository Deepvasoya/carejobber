<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResumePromotionPackage;
use App\Package;
use App\PackageCoupon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PackageCouponController extends Controller
{
    public function index()
    {
        $coupons = PackageCoupon::orderByDesc('id')->paginate(25);

        return view('admin.package_coupon.index', compact('coupons'));
    }

    public function create()
    {
        $packages = Package::orderBy('package_for')->orderBy('package_title')->get();
        $resumePromotionPackages = ResumePromotionPackage::orderBy('duration_days')->get();

        return view('admin.package_coupon.add', compact('packages', 'resumePromotionPackages'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        PackageCoupon::create($data);
        flash(__('Package coupon created.'))->success();

        return redirect()->route('list.package.coupons');
    }

    public function edit($id)
    {
        $coupon = PackageCoupon::findOrFail($id);
        $packages = Package::orderBy('package_for')->orderBy('package_title')->get();
        $resumePromotionPackages = ResumePromotionPackage::orderBy('duration_days')->get();

        return view('admin.package_coupon.edit', compact('coupon', 'packages', 'resumePromotionPackages'));
    }

    public function update(Request $request, $id)
    {
        $coupon = PackageCoupon::findOrFail($id);
        $data = $this->validatedData($request, $coupon->id);
        $coupon->update($data);
        flash(__('Package coupon updated.'))->success();

        return redirect()->route('list.package.coupons');
    }

    public function destroy($id)
    {
        $coupon = PackageCoupon::findOrFail($id);
        $coupon->delete();
        flash(__('Package coupon deleted.'))->success();

        return redirect()->route('list.package.coupons');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $codeRule = 'required|string|max:64|unique:package_coupons,code';
        if ($ignoreId) {
            $codeRule .= ',' . $ignoreId;
        }

        $validated = $request->validate([
            'code' => $codeRule,
            'admin_note' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_package_price' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit_total' => 'nullable|integer|min:1',
            'usage_limit_per_buyer' => 'nullable|integer|min:1',
            'package_for_scope' => 'nullable|in:job_seeker,employer,cv_search,make_featured,resume_promotion',
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'integer|exists:packages,id',
            'resume_promotion_package_ids' => 'nullable|array',
            'resume_promotion_package_ids.*' => 'integer|exists:resume_promotion_packages,id',
            'allow_subscription_packages' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validated['discount_type'] === 'percent' && (float) $validated['discount_value'] > 100) {
            throw ValidationException::withMessages([
                'discount_value' => [__('Percent cannot exceed 100.')],
            ]);
        }

        $ids = $request->input('package_ids');
        $packageIds = is_array($ids) && count($ids) > 0 ? array_values(array_unique(array_map('intval', $ids))) : null;

        return [
            'code' => PackageCoupon::normalizeCode($validated['code']),
            'admin_note' => $validated['admin_note'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'max_discount_amount' => $validated['max_discount_amount'] ?? null,
            'min_package_price' => $validated['min_package_price'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'usage_limit_total' => $validated['usage_limit_total'] ?? null,
            'usage_limit_per_buyer' => $validated['usage_limit_per_buyer'] ?? null,
            'package_for_scope' => $validated['package_for_scope'] ?? null,
            'package_ids' => $packageIds,
            'resume_promotion_package_ids' => $resumePromotionPackageIds,
            'allow_subscription_packages' => $request->boolean('allow_subscription_packages'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
