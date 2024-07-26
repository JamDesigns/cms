<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class PageController extends Controller
{
    public function show(Request $request) {
        $page_code = str_replace(LaravelLocalization::setLocale()."/", "", $request->path());

        $page = Page::where('code', $page_code)
                ->where('publishing_ends_at', null)
                ->orWhere('publishing_ends_at', '<=', Carbon::now())
                ->first();

        if (null === $page) {
            abort(Response::HTTP_NOT_FOUND);
        }

        SEOTools::setTitle($page->getSEOTitle());
        SEOTools::setDescription($page->getSEODescription());
        SEOTools::jsonLd()->addImage($page->getSEOImageUrl());
        SEOTools::opengraph()->addImage($page->getSEOImageUrl());
        SEOMeta::setKeywords($page->seo_keywords);

        return view('pages.index', [
            'page' => $page,
            'title' => $page->title,
        ]);
    }
}
