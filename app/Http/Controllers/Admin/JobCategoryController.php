<?php

namespace App\Http\Controllers\Admin;

use Auth;
use DB;
use Input;
use File;
use Carbon\Carbon;
use ImgUploader;
use Redirect;
use App\JobCategory;
use App\Language;
use App\Helpers\MiscHelper;
use App\Helpers\DataArrayHelper;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\Http\Requests\JobCategoryFormRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Image;

class JobCategoryController extends Controller
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

    public function indexJobCategories()
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        return view('admin.job_category.index')->with('languages', $languages);
    }

    public function createJobCategory()
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        $jobCategories = DataArrayHelper::defaultJobCategoriesArray();
        return view('admin.job_category.add')
                        ->with('languages', $languages)
                        ->with('jobCategories', $jobCategories);
    }

    public function storeJobCategory(JobCategoryFormRequest $request)
    {
        $image = $request->file('image');

        if ($image != '') {
            $nameonly = preg_replace('/\..+$/', '', $request->image->getClientOriginalName());
            $input['imagename'] = $nameonly . '_' . rand(1, 999) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/job_category/thumbnail');
            $img = Image::make($image->getRealPath());
            $img->resize(222, 150, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $input['imagename']);

            $destinationPath = public_path('/uploads/job_category/');
            $image->move($destinationPath, $input['imagename']);
        }
        
        $jobCategory = new JobCategory();
        $jobCategory->job_category = $request->input('job_category');
        $jobCategory->is_active = $request->input('is_active');
        $jobCategory->lang = $request->input('lang');
        $jobCategory->is_default = $request->input('is_default');
        if ($image != '') {
            $jobCategory->image = $input['imagename'];
        } else {
            $jobCategory->image = '';
        }
        $jobCategory->save();
        
        /*         * ************************************ */
        $jobCategory->sort_order = $jobCategory->id;
        if ((int) $request->input('is_default') == 1) {
            $jobCategory->job_category_id = $jobCategory->id;
        } else {
            $jobCategory->job_category_id = $request->input('job_category_id');
        }
        $jobCategory->update();
        flash('Job Category has been added!')->success();
        return \Redirect::route('edit.job.category', array($jobCategory->id));
    }

    public function editJobCategory($id)
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        $jobCategories = DataArrayHelper::defaultJobCategoriesArray();
        $jobCategory = JobCategory::findOrFail($id);
        return view('admin.job_category.edit')
                        ->with('languages', $languages)
                        ->with('jobCategory', $jobCategory)
                        ->with('jobCategories', $jobCategories);
    }

    public function updateJobCategory($id, JobCategoryFormRequest $request)
    {
        $image = $request->file('image');

        if ($image != '') {
            $nameonly = preg_replace('/\..+$/', '', $request->image->getClientOriginalName());
            $input['imagename'] = $nameonly . '_' . rand(1, 999) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/job_category/thumbnail');
            $img = Image::make($image->getRealPath());
            $img->resize(222, 150, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $input['imagename']);

            $destinationPath = public_path('/uploads/job_category/');
            $image->move($destinationPath, $input['imagename']);
        }
        
        $jobCategory = JobCategory::findOrFail($id);
        $jobCategory->job_category = $request->input('job_category');
        $jobCategory->is_active = $request->input('is_active');
        $jobCategory->lang = $request->input('lang');
        $jobCategory->is_default = $request->input('is_default');
        if ($image != '') {
            $jobCategory->image = $input['imagename'];
        }
        if ((int) $request->input('is_default') == 1) {
            $jobCategory->job_category_id = $jobCategory->id;
        } else {
            $jobCategory->job_category_id = $request->input('job_category_id');
        }

        $jobCategory->update();
        flash('Job Category has been updated!')->success();
        return \Redirect::route('edit.job.category', array($jobCategory->id));
    }

    public function deleteJobCategory(Request $request)
    {
        $id = $request->input('id');
        try {
            $jobCategory = JobCategory::findOrFail($id);
            if ((bool) $jobCategory->is_default) {
                JobCategory::where('job_category_id', '=', $jobCategory->job_category_id)->delete();
            } else {
                $jobCategory->delete();
            }
            return 'ok';
        } catch (ModelNotFoundException $e) {
            return 'notok';
        }
    }

    public function bulkDeleteJobCategories(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected'], 400);
        }
        
        try {
            foreach ($ids as $id) {
                $jobCategory = JobCategory::find($id);
                if ($jobCategory) {
                    if ((bool) $jobCategory->is_default) {
                        JobCategory::where('job_category_id', '=', $jobCategory->job_category_id)->delete();
                    } else {
                        $jobCategory->delete();
                    }
                }
            }
            return response()->json(['success' => true, 'message' => 'Selected job categories deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting job categories'], 500);
        }
    }

    public function fetchJobCategoriesData(Request $request)
    {
        $jobCategories = JobCategory::select(['job_categories.id', 'job_categories.job_category', 'job_categories.is_active', 'job_categories.lang', 'job_categories.is_default', 'job_categories.created_at', 'job_categories.updated_at'])->sorted();
        return Datatables::of($jobCategories)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('job_category') && !empty($request->job_category)) {
                                $query->where('job_categories.job_category', 'like', "%{$request->get('job_category')}%");
                            }
                            if ($request->has('lang') && !empty($request->get('lang'))) {
                                $query->where('job_categories.lang', 'like', "%{$request->get('lang')}%");
                            }
                            if ($request->has('is_active') && $request->get('is_active') != -1) {
                                $query->where('job_categories.is_active', '=', "{$request->get('is_active')}");
                            }
                        })
                        ->addColumn('job_category', function ($jobCategories) {
                            $jobCategory = Str::limit($jobCategories->job_category, 100, '...');
                            $direction = MiscHelper::getLangDirection($jobCategories->lang);
                            return '<span dir="' . $direction . '">' . $jobCategory . '</span>';
                        })
                        ->addColumn('checkbox', function ($jobCategories) {
                            return '<input class="checkboxes" type="checkbox" id="check_'.$jobCategories->id.'" name="job_category_ids[]" autocomplete="off" value="'.$jobCategories->id.'">';
                        })
                        ->addColumn('action', function ($jobCategories) {
                            /*                             * ************************* */
                            $activeTxt = 'Make Active';
                            $activeHref = 'makeActive(' . $jobCategories->id . ');';
                            $activeIcon = 'checkbox-blank-line';
                            if ((int) $jobCategories->is_active == 1) {
                                $activeTxt = 'Make InActive';
                                $activeHref = 'makeNotActive(' . $jobCategories->id . ');';
                                $activeIcon = 'check-square-o';
                            }
                            return '
				<div class="btn-group">
					<button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action
						<i class="ri ri-arrow-down-s-line"></i>
					</button>
					<ul class="dropdown-menu">
						<li>
							<a class="dropdown-item" href="' . route('edit.job.category', ['id' => $jobCategories->id]) . '"><i class="ri ri-pencil-line me-1"></i>Edit</a>
						</li>						
						<li>
							<a class="dropdown-item" href="javascript:void(0);" onclick="deleteJobCategory(' . $jobCategories->id . ', ' . $jobCategories->is_default . ');"><i class="ri ri-delete-bin-line me-1"></i>Delete</a>
						</li>
						<li>
						<a class="dropdown-item" href="javascript:void(0);" onClick="' . $activeHref . '" id="onclickActive' . $jobCategories->id . '"><i class="ri ri-' . $activeIcon . ' me-1"></i>' . $activeTxt . '</a>
						</li>																																		
					</ul>
				</div>';
                        })
                        ->rawColumns(['job_category', 'action', 'checkbox'])
                        ->setRowId(function($jobCategories) {
                            return 'jobCategoryDtRow' . $jobCategories->id;
                        })
                        ->make(true);
    }

    public function makeActiveJobCategory(Request $request)
    {
        $id = $request->input('id');
        try {
            $jobCategory = JobCategory::findOrFail($id);
            $jobCategory->is_active = 1;
            $jobCategory->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function makeNotActiveJobCategory(Request $request)
    {
        $id = $request->input('id');
        try {
            $jobCategory = JobCategory::findOrFail($id);
            $jobCategory->is_active = 0;
            $jobCategory->update();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function sortJobCategories()
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        return view('admin.job_category.sort')->with('languages', $languages);
    }

    public function jobCategorySortData(Request $request)
    {
        $lang = $request->input('lang');
        $jobCategories = JobCategory::select('job_categories.id', 'job_categories.job_category', 'job_categories.sort_order')
                ->where('job_categories.lang', 'like', $lang)
                ->orderBy('job_categories.sort_order')
                ->get();
        $str = '<ul id="sortable">';
        if ($jobCategories != null) {
            foreach ($jobCategories as $jobCategory) {
                $str .= '<li id="' . $jobCategory->id . '"><i class="fa fa-sort"></i>' . $jobCategory->job_category . '</li>';
            }
        }
        echo $str . '</ul>';
    }

    public function jobCategorySortUpdate(Request $request)
    {
        $jobCategoryOrder = $request->input('jobCategoryOrder');
        $jobCategoryOrderArray = explode(',', $jobCategoryOrder);
        $count = 1;
        foreach ($jobCategoryOrderArray as $jobCategoryId) {
            $jobCategory = JobCategory::find($jobCategoryId);
            $jobCategory->sort_order = $count;
            $jobCategory->update();
            $count++;
        }
    }
}