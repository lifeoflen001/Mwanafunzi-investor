<?php

namespace App\Http\Controllers;

use App\Models\Redirect;
use Illuminate\Http\Request;

class PublicRedirectController extends Controller
{
    public function handle(Request $request, string $path)
    {
        $source = '/'.ltrim($path, '/');
        $redirect = Redirect::where('source_path', $source)->where('is_enabled', true)->first();
        if (! $redirect) abort(404);

        return redirect()->to($redirect->destination_path, $redirect->status_code);
    }
}
