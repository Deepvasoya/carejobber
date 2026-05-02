<?php

namespace App\Http\Controllers\Admin;

use App\Certification;
use App\Helpers\DataArrayHelper;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DataTables;
use App\Http\Controllers\Controller;

class CertificationController extends Controller
{
    public function index()
    {
        return view('admin.certification.index');
    }

    public function create()
    {
        return view('admin.certification.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200|unique:certifications,name',
        ]);

        $cert = Certification::create([
            'name'      => $request->input('name'),
            'is_active' => $request->input('is_active', 1),
        ]);
        $cert->sort_order = $cert->id;
        $cert->save();

        flash('Certification has been added!')->success();
        return redirect()->route('edit.certification', $cert->id);
    }

    public function edit($id)
    {
        $certification = Certification::findOrFail($id);
        return view('admin.certification.edit', compact('certification'));
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:200|unique:certifications,name,' . $id,
            'is_active' => 'required',
        ]);

        $cert = Certification::findOrFail($id);
        $cert->name      = $request->input('name');
        $cert->is_active = $request->input('is_active');
        $cert->save();

        flash('Certification has been updated!')->success();
        return redirect()->route('edit.certification', $cert->id);
    }

    public function delete(Request $request)
    {
        try {
            Certification::findOrFail($request->input('id'))->delete();
            return 'ok';
        } catch (ModelNotFoundException $e) {
            return 'notok';
        }
    }

    public function fetchData(Request $request)
    {
        $query = Certification::select(['id', 'name', 'is_active', 'sort_order', 'created_at']);

        return Datatables::of($query)
            ->filter(function ($q) use ($request) {
                if ($request->filled('name')) {
                    $q->where('name', 'like', '%' . $request->name . '%');
                }
                if ($request->get('is_active') != -1 && $request->has('is_active')) {
                    $q->where('is_active', $request->is_active);
                }
            })
            ->addColumn('action', function ($row) {
                $activeTxt  = $row->is_active ? 'Make Inactive' : 'Make Active';
                $activeFunc = $row->is_active ? 'makeNotActive(' . $row->id . ')' : 'makeActive(' . $row->id . ')';
                return '
                <div class="btn-group">
                    <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">Action <i class="ri ri-arrow-down-s-line"></i></button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="' . route('edit.certification', $row->id) . '"><i class="ri ri-pencil-line me-1"></i>Edit</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="deleteCertification(' . $row->id . ')"><i class="ri ri-delete-bin-line me-1"></i>Delete</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);" onclick="' . $activeFunc . '"><i class="ri ri-toggle-line me-1"></i>' . $activeTxt . '</a></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['action'])
            ->setRowId(fn($r) => 'certDtRow' . $r->id)
            ->make(true);
    }

    public function makeActive(Request $request)
    {
        try {
            $c = Certification::findOrFail($request->input('id'));
            $c->is_active = 1; $c->save(); echo 'ok';
        } catch (ModelNotFoundException $e) { echo 'notok'; }
    }

    public function makeNotActive(Request $request)
    {
        try {
            $c = Certification::findOrFail($request->input('id'));
            $c->is_active = 0; $c->save(); echo 'ok';
        } catch (ModelNotFoundException $e) { echo 'notok'; }
    }
}