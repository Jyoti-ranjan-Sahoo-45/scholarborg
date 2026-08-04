<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $activeTheme = getActiveTheme();
        $homeLanding = $activeTheme->homeLanding;

        $seoSettings = getSeoMetas('home');
        $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('home.home_title');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('home.home_title');
        $pageRobot = getPageRobot('home');

        // Fetch parent categories and subcategories
        $categories = \App\Models\Category::getCategories();

        // Fetch featured or popular webinars
        $webinars = \App\Models\Webinar::where('status', 'active')
            ->where('is_featured', 1)
            ->limit(12)
            ->get();

        if ($webinars->isEmpty()) {
            $webinars = \App\Models\Webinar::where('status', 'active')
                ->limit(12)
                ->get();
        }

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
            'activeTheme' => $activeTheme,
            'homeLanding' => $homeLanding,
            'categories' => $categories,
            'webinars' => $webinars,
        ];

        return view('design_1.web.home.savemyexams_home', $data);
    }
}
