<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PluginBundleLicenseService
{
    const ERROR_NO_CODE = 'no_code';
    const ERROR_DOMAIN_MISMATCH = 'domain_mismatch';
    const ERROR_PRODUCT_MISMATCH = 'product_mismatch';
    const ERROR_INVALID_CODE = 'invalid_code';
    const ERROR_SERVER_ERROR = 'server_error';

    public function func3847291650($purchaseCode, $forceCheck = false)
    {
        try {
            $response = Http::asJson()->post('https://scholarb.org/crm/api/validate-license', [
                'purchase_code' => $purchaseCode,
                'domain' => request()->getHost(),
                'product' => 'plugin_bundle'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'valid' => $data['valid'] ?? false,
                    'license_type' => $data['license_type'] ?? 'Regular license',
                    'error' => $data['error'] ?? null,
                    'registered_domain' => $data['registered_domain'] ?? null,
                    'message' => $data['message'] ?? null
                ];
            }
        } catch (\Exception $e) {
            // Fallback
        }

        return ['valid' => false, 'error' => self::ERROR_SERVER_ERROR];
    }

    public function func5629384175($licenseType)
    {
        return ['valid' => true];
    }
}