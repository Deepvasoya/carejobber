<?php

namespace App\Http\Controllers\Api;

use App\Seo;
use App\Cms;
use App\CmsContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;

class CmsController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPage($slug)
    {
        $cms = Cms::where('page_slug', 'like', $slug)->first();
        if (null === $cmsContent = CmsContent::getContentByPageId($cms->id)) {
            echo 'No Content';
            exit;
        }

        $seo = (object) array(
                    'seo_title' => $cms->seo_title,
                    'seo_description' => $cms->seo_description,
                    'seo_keywords' => $cms->seo_keywords,
                    'seo_other' => $cms->seo_other
        );

        $data['cmsContent'] = $cmsContent;
        $data['cms'] = $cms;
        $data['seo'] = $seo;


        //dd($data);

        $success['success'] =  'done';

        return $this->sendResponse($success, $data);


        //return view('cms.cms_page')
                        //->with('cmsContent', $cmsContent)
                        //->with('seo', $seo);
    }

}
