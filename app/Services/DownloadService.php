<?php

namespace App\Services;

use App\Models\DownloadLog;
use App\Models\ProductAsset;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadService
{
    public function url(User $user, ProductAsset $asset): string
    {
        $entitlement = $user->entitlements()->where('product_id', $asset->product_id)->where('status', 'active')->firstOrFail();
        return URL::temporarySignedRoute('downloads.asset', now()->addMinutes(config('commerce.download_url_lifetime', 10)), ['asset' => $asset->id, 'token' => Str::random(20)]);
    }

    public function serve(User $user, ProductAsset $asset, string $ip = null, string $agent = null): StreamedResponse
    {
        $entitlement = $user->entitlements()->where('product_id', $asset->product_id)->where('status', 'active')->firstOrFail();
        DownloadLog::create(['entitlement_id' => $entitlement->id, 'user_id' => $user->id, 'product_id' => $asset->product_id, 'product_asset_id' => $asset->id, 'order_id' => $entitlement->order_id, 'downloaded_at' => now(), 'ip_address' => $ip, 'user_agent' => $agent]);
        return \Illuminate\Support\Facades\Storage::disk($asset->disk)->download($asset->path, $asset->name);
    }
}
