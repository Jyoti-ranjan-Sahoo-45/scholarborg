<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseCode;

class LicensesController extends Controller
{
    public function index()
    {
        // Simple authorization check (common in Rocket LMS)
        $this->authorize("admin_settings_licenses");

        $main = PurchaseCode::where('product_type', 'main')->first();
        $plugin = PurchaseCode::where('product_type', 'plugin_bundle')->first();
        $theme = PurchaseCode::where('product_type', 'theme_builder')->first();
        $mobile = PurchaseCode::where('product_type', 'mobile_app')->first();

        $mainLicense = $main ? [
            'code' => $main->code,
            'license_type' => $main->license_type ?? 'Regular license',
            'status' => true
        ] : ['code' => null, 'license_type' => null, 'status' => null];

        $pluginBundleLicense = $plugin ? [
            'code' => $plugin->code,
            'license_type' => $plugin->license_type ?? 'Regular license',
            'status' => true
        ] : ['code' => null, 'license_type' => null, 'status' => null];

        $themeBuilderLicense = $theme ? [
            'code' => $theme->code,
            'license_type' => $theme->license_type ?? 'Regular license',
            'status' => true
        ] : ['code' => null, 'license_type' => null, 'status' => null];

        $mobileAppLicense = $mobile ? [
            'code' => $mobile->code,
            'license_type' => $mobile->license_type ?? 'Regular license',
            'status' => true
        ] : ['code' => null, 'license_type' => null, 'status' => null];

        $pageTitle = 'Licenses';

        return view('admin.licenses.index', compact(
            'mainLicense',
            'pluginBundleLicense',
            'themeBuilderLicense',
            'mobileAppLicense',
            'pageTitle'
        ));
    }
}