<?php

namespace App\Http\Controllers\Admin;

use Auth;
use DB;
use Input;
use Carbon\Carbon;
use Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\FaqCategory;
use App\Helpers\MiscHelper;
use App\Helpers\DataArrayHelper;
use App\Language;
use App\Http\Requests\FaqCategoryFormRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class FaqCategoryController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexFaqCategories()
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        return view('admin.faq_category.index')->with('languages', $languages);
    }

    public function createFaqCategory()
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        return view('admin.faq_category.add')->with('languages', $languages);
    }

    public function storeFaqCategory(FaqCategoryFormRequest $request)
    {
        $faqCategory = new FaqCategory();
        $faqCategory->name = $request->input('name');
        $faqCategory->slug = Str::slug($request->input('name'));
        $faqCategory->description = $request->input('description');
        $faqCategory->lang = $request->input('lang');
        $faqCategory->is_active = $request->input('is_active', 1);
        $faqCategory->save();
        
        $faqCategory->sort_order = $faqCategory->id;
        $faqCategory->update();
        
        flash('FAQ Category has been added!')->success();
        return \Redirect::route('edit.faq.category', array($faqCategory->id));
    }

    public function editFaqCategory($id)
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        $faqCategory = FaqCategory::findOrFail($id);
        return view('admin.faq_category.edit')
                        ->with('languages', $languages)
                        ->with('faqCategory', $faqCategory);
    }

    public function updateFaqCategory($id, FaqCategoryFormRequest $request)
    {
        $faqCategory = FaqCategory::findOrFail($id);
        $faqCategory->name = $request->input('name');
        $faqCategory->slug = Str::slug($request->input('name'));
        $faqCategory->description = $request->input('description');
        $faqCategory->lang = $request->input('lang');
        $faqCategory->is_active = $request->input('is_active', 1);
        $faqCategory->update();
        
        flash('FAQ Category has been updated!')->success();
        return \Redirect::route('edit.faq.category', array($faqCategory->id));
    }

    public function deleteFaqCategory(Request $request)
    {
        $id = $request->input('id');
        try {
            $faqCategory = FaqCategory::findOrFail($id);
            $faqCategory->delete();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    public function fetchFaqCategoriesData(Request $request)
    {
        $faqCategories = FaqCategory::select(
                        [
                            'faq_categories.id',
                            'faq_categories.name',
                            'faq_categories.description',
                            'faq_categories.sort_order',
                            'faq_categories.lang',
                            'faq_categories.is_active',
                            'faq_categories.created_at',
                            'faq_categories.updated_at'
                        ]
        );
        
        return Datatables::of($faqCategories)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('name') && !empty($request->name)) {
                                $query->where('faq_categories.name', 'like', "%{$request->get('name')}%");
                            }
                            if ($request->has('lang') && !empty($request->get('lang'))) {
                                $query->where('faq_categories.lang', 'like', "%{$request->get('lang')}%");
                            }
                        })
                        ->addColumn('name', function ($faqCategory) {
                            $direction = MiscHelper::getLangDirection($faqCategory->lang);
                            return '<span dir="' . $direction . '">' . $faqCategory->name . '</span>';
                        })
                        ->addColumn('description', function ($faqCategory) {
                            $description = Str::limit($faqCategory->description, 100, '...');
                            $direction = MiscHelper::getLangDirection($faqCategory->lang);
                            return '<span dir="' . $direction . '">' . $description . '</span>';
                        })
                        ->addColumn('is_active', function ($faqCategory) {
                            return $faqCategory->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                        })
                        ->addColumn('action', function ($faqCategory) {
                            return '
                <div class="btn-group">
                    <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action
                        <i class="ri ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="' . route('edit.faq.category', ['id' => $faqCategory->id]) . '"><i class="ri ri-pencil-line me-1"></i>Edit</a>
                        </li>						
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="delete_faq_category(' . $faqCategory->id . ');""><i class="ri ri-delete-bin-line me-1"></i>Delete</a>
                        </li>																																							
                    </ul>
                </div>';
                        })
                        ->rawColumns(['name', 'description', 'is_active', 'action'])
                        ->setRowId(function($faqCategory) {
                            return 'faq_category_dt_row_' . $faqCategory->id;
                        })
                        ->make(true);
    }

    public function sortFaqCategories()
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        return view('admin.faq_category.sort')->with('languages', $languages);
    }

    public function faqCategorySortData(Request $request)
    {
        $lang = $request->input('lang');
        $faqCategories = FaqCategory::select('faq_categories.id', 'faq_categories.name', 'faq_categories.sort_order')
                        ->where('faq_categories.lang', 'like', $lang)
                        ->orderBy('faq_categories.sort_order')->get();
        $str = '<ul id="sortable">';
        if ($faqCategories != null) {
            foreach ($faqCategories as $faqCategory) {
                $str .= '<li id="' . $faqCategory->id . '"><i class="fa fa-sort"></i>' . $faqCategory->name . '</li>';
            }
        }
        echo $str . '</ul>';
    }

    public function faqCategorySortUpdate(Request $request)
    {
        $faqCategoryOrder = $request->input('faqCategoryOrder');
        $faqCategoryOrderArray = explode(',', $faqCategoryOrder);
        $count = 1;
        foreach ($faqCategoryOrderArray as $faq_category_id) {
            $faqCategory = FaqCategory::find($faq_category_id);
            $faqCategory->sort_order = $count;
            $faqCategory->update();
            $count++;
        }
    }
}
