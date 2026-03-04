<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function companies()
    {
        return response()->json(['message' => 'Companies sitemap']);
    }
}
