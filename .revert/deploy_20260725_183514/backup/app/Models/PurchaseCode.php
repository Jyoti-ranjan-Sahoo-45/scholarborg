<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseCode extends Model
{
    protected $table = 'purchase_code';
    protected $guarded = ['id'];

    const TYPE_MAIN = 'main';
    const TYPE_PLUGIN_BUNDLE = 'plugin_bundle';
    const TYPE_THEME_BUILDER = 'theme_builder';
    const TYPE_MOBILE_APP = 'mobile_app';

    public static function getPurchaseCode()
    {
        $record = self::where('product_type', self::TYPE_MAIN)->first();
        return $record ? $record->code : '';
    }

    public static function getLicenseType()
    {
        $record = self::where('product_type', self::TYPE_MAIN)->first();
        return $record ? $record->license_type : '';
    }

    public static function getPluginBundlePurchaseCode()
    {
        $record = self::where('product_type', self::TYPE_PLUGIN_BUNDLE)->first();
        return $record ? $record->code : '';
    }

    public static function getThemeBuilderPurchaseCode()
    {
        $record = self::where('product_type', self::TYPE_THEME_BUILDER)->first();
        return $record ? $record->code : '';
    }

    public static function getMobileAppPurchaseCode()
    {
        $record = self::where('product_type', self::TYPE_MOBILE_APP)->first();
        return $record ? $record->code : '';
    }

    public static function updatePurchaseCode($code, $type = self::TYPE_MAIN, $licenseType = 'Regular license')
    {
        return self::updateOrCreate(
            ['product_type' => $type],
            [
                'code' => $code,
                'license_type' => $licenseType,
                'updated_at' => now()
            ]
        );
    }

    public static function updatePluginBundlePurchaseCode($code, $licenseType = 'Regular license')
    {
        return self::updatePurchaseCode($code, self::TYPE_PLUGIN_BUNDLE, $licenseType);
    }

    public static function updateThemeBuilderPurchaseCode($code, $licenseType = 'Regular license')
    {
        return self::updatePurchaseCode($code, self::TYPE_THEME_BUILDER, $licenseType);
    }

    public static function updateMobileAppPurchaseCode($code, $licenseType = 'Regular license')
    {
        return self::updatePurchaseCode($code, self::TYPE_MOBILE_APP, $licenseType);
    }
}