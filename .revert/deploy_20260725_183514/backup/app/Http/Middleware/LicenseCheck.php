<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PurchaseCode;

class LicenseCheck
{
    public function handle($request, Closure $next)
    {
        $license = PurchaseCode::where('product_type', PurchaseCode::TYPE_MAIN)->first();

        if (!$license || empty($license->code)) {
            if (!$request->is('purchase-code') && !$request->is('purchase-code/*')) {
                return redirect()->route('purchase.code.show');
            }
        }

        return $next($request);
    }
}