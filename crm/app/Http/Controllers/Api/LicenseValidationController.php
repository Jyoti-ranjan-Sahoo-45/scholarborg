<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LicenseValidationController extends Controller
{
    public function validateLicense(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required|string',
            'domain' => 'required|string',
            'product' => 'nullable|string'
        ]);

        $purchaseCode = $request->input('purchase_code');
        $domain = $request->input('domain');
        
        // Query the software_licenses table directly
        $license = DB::table('software_licenses')
            ->where('license_key', $purchaseCode)
            ->first();

        if (!$license) {
            return response()->json([
                'valid' => false,
                'error' => 'invalid_code',
                'message' => 'Purchase code is invalid or not registered in our CRM.'
            ]);
        }

        // If the license is not active, return error
        if ($license->status !== 'active') {
            return response()->json([
                'valid' => false,
                'error' => 'inactive_license',
                'message' => 'This license has been deactivated or suspended.'
            ]);
        }

        // Auto-activate on first use if domain is empty or null
        if (empty($license->activated_domain)) {
            DB::table('software_licenses')
                ->where('id', $license->id)
                ->update([
                    'activated_domain' => $domain,
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'valid' => true,
                'license_type' => $license->product_name ?? 'Regular license',
                'message' => 'License successfully activated for domain: ' . $domain
            ]);
        }

        // Check for domain mismatch
        $cleanRequestDomain = $this->cleanDomain($domain);
        $cleanLicenseDomain = $this->cleanDomain($license->activated_domain);

        if ($cleanRequestDomain !== $cleanLicenseDomain) {
            return response()->json([
                'valid' => false,
                'error' => 'domain_mismatch',
                'registered_domain' => $license->activated_domain,
                'message' => 'This purchase code is already registered to domain: ' . $license->activated_domain
            ]);
        }

        return response()->json([
            'valid' => true,
            'license_type' => $license->product_name ?? 'Regular license'
        ]);
    }

    private function cleanDomain($url)
    {
        $url = strtolower(trim($url));
        $url = preg_replace('#^https?://#', '', $url);
        $url = preg_replace('#^www\.#', '', $url);
        return explode('/', $url)[0];
    }
}
