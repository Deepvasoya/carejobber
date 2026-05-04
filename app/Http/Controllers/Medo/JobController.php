<?php

namespace App\Http\Controllers\Medo;

use App\Http\Controllers\Controller;

class JobController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Medo\Category::all();
        return view('medo.jobs.index', compact('categories'));
    }
}
