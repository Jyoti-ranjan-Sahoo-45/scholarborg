<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PurchaseCode;

class MobileAppLicenseCheck
{
    public function handle($request, Closure $next)
    {
        $license = PurchaseCode::where('product_type', PurchaseCode::TYPE_MOBILE_APP)->first();

        if (!$license || empty($license->code)) {
            $adminPrefix = getAdminPanelUrlPrefix();
            if (!$request->is($adminPrefix . '/licenses') && !$request->is($adminPrefix . '/licenses/*')) {
                return redirect()->route('admin.mobile_app.license');
            }
        }

        return $next($request);
    }
}