<?php

namespace App\Http\Controllers\Admin;

use Auth;
use DB;
use Redirect;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\FaqSection;
use App\Helpers\DataArrayHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class FaqSectionController extends Controller
{
    /**
     * Display a listing of FAQ sections.
     */
    public function indexFaqSections()
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        return view('admin.faq_section.index')->with('languages', $languages);
    }

    /**
     * Show the form for creating a new FAQ section.
     */
    public function createFaqSection()
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        return view('admin.faq_section.add')->with('languages', $languages);
    }

    /**
     * Store a newly created FAQ section in storage.
     */
    public function storeFaqSection(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'lang' => 'required|max:10',
        ]);

        $faqSection = new FaqSection();
        $faqSection->name = $request->input('name');
        $faqSection->slug = Str::slug($request->input('name'));
        $faqSection->description = $request->input('description');
        $faqSection->lang = $request->input('lang');
        $faqSection->is_active = $request->input('is_active', 1);
        $faqSection->sort_order = $request->input('sort_order', 99999);
        $faqSection->save();

        flash('FAQ Section has been added!')->success();
        return redirect()->route('list.faq.sections');
    }

    /**
     * Show the form for editing the specified FAQ section.
     */
    public function editFaqSection($id)
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        $faqSection = FaqSection::findOrFail($id);
        return view('admin.faq_section.edit')
                        ->with('faqSection', $faqSection)
                        ->with('languages', $languages);
    }

    /**
     * Update the specified FAQ section in storage.
     */
    public function updateFaqSection($id, Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'lang' => 'required|max:10',
        ]);

        $faqSection = FaqSection::findOrFail($id);
        $faqSection->name = $request->input('name');
        $faqSection->slug = Str::slug($request->input('name'));
        $faqSection->description = $request->input('description');
        $faqSection->lang = $request->input('lang');
        $faqSection->is_active = $request->input('is_active', 1);
        $faqSection->sort_order = $request->input('sort_order', 99999);
        $faqSection->update();

        flash('FAQ Section has been updated!')->success();
        return redirect()->route('list.faq.sections');
    }

    /**
     * Remove the specified FAQ section from storage.
     */
    public function deleteFaqSection(Request $request)
    {
        $id = $request->input('id');
        try {
            $faqSection = FaqSection::findOrFail($id);
            $faqSection->delete();
            echo 'ok';
        } catch (ModelNotFoundException $e) {
            echo 'notok';
        }
    }

    /**
     * Fetch FAQ sections data for DataTables.
     */
    public function fetchFaqSectionsData(Request $request)
    {
        $lang = $request->input('lang');
        
        $faqSections = FaqSection::select([
                        'faq_sections.id',
                        'faq_sections.name',
                        'faq_sections.slug',
                        'faq_sections.description',
                        'faq_sections.lang',
                        'faq_sections.is_active',
                        'faq_sections.sort_order',
                        'faq_sections.created_at'
                    ])
                    ->where('faq_sections.lang', 'like', $lang)
                    ->withCount('categories');

        return Datatables::of($faqSections)
                        ->addColumn('name', function ($faqSection) {
                            return '<strong>' . $faqSection->name . '</strong><br><small class="text-muted">' . $faqSection->slug . '</small>';
                        })
                        ->addColumn('categories_count', function ($faqSection) {
                            return '<span class="badge bg-info">' . $faqSection->categories_count . ' categories</span>';
                        })
                        ->addColumn('is_active', function ($faqSection) {
                            return $faqSection->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                        })
                        ->addColumn('action', function ($faqSection) {
                            return '
                <div class="btn-group">
                    <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action
                        <i class="ri ri-arrow-down-s-line"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="' . route('edit.faq.section', ['id' => $faqSection->id]) . '"><i class="ri ri-pencil-line me-1"></i>Edit</a>
                        </li>
                        <li>
                            <a class="dropdown-item delete-faq-section" href="javascript:void(0);" data-id="' . $faqSection->id . '"><i class="ri ri-delete-bin-line me-1"></i>Delete</a>
                        </li>
                    </ul>
                </div>';
                        })
                        ->rawColumns(['name', 'categories_count', 'is_active', 'action'])
                        ->setRowId(function($faqSection) {
                            return 'faq_section_dt_row_' . $faqSection->id;
                        })
                        ->make(true);
    }

    /**
     * Show the form for sorting FAQ sections.
     */
    public function sortFaqSections()
    {
        $languages = DataArrayHelper::languagesNativeCodeArray();
        return view('admin.faq_section.sort')->with('languages', $languages);
    }

    /**
     * Fetch FAQ sections for sorting.
     */
    public function faqSectionSortData(Request $request)
    {
        $lang = $request->input('lang');
        $faqSections = FaqSection::select('faq_sections.id', 'faq_sections.name', 'faq_sections.sort_order')
                ->where('faq_sections.lang', 'like', $lang)
                ->orderBy('faq_sections.sort_order', 'ASC')
                ->get();
        $str = '<ul id="sortable">';
        foreach ($faqSections as $faqSection) {
            $str .= '<li id="' . $faqSection->id . '"><i class="ri ri-drag-move-line"></i>' . $faqSection->name . '</li>';
        }
        $str .= '</ul>';
        echo $str;
    }

    /**
     * Update FAQ section sort order.
     */
    public function faqSectionSortUpdate(Request $request)
    {
        $sectionIdsInOrder = $request->input('ids');
        $count = 1;
        foreach ($sectionIdsInOrder as $id) {
            $faqSection = FaqSection::findOrFail($id);
            $faqSection->sort_order = $count;
            $faqSection->update();
            $count++;
        }
    }
}
