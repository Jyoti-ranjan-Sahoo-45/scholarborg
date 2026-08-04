<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PurchaseCode;

class PluginBundleLicenseCheck
{
    public function handle($request, Closure $next)
    {
        $license = PurchaseCode::where('product_type', PurchaseCode::TYPE_PLUGIN_BUNDLE)->first();

        if (!$license || empty($license->code)) {
            $adminPrefix = getAdminPanelUrlPrefix();
            if (!$request->is($adminPrefix . '/licenses') && !$request->is($adminPrefix . '/licenses/*')) {
                return redirect()->route('admin.plugin.license');
            }
        }

        return $next($request);
    }
}