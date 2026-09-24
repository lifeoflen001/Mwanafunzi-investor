<?php

namespace App\Http\Controllers;

use App\Models\ProductAsset;
use App\Services\DownloadService;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function __invoke(Request $request, ProductAsset $asset, DownloadService $downloads)
    {
        return $downloads->serve($request->user(), $asset, $request->ip(), $request->userAgent());
    }
}
