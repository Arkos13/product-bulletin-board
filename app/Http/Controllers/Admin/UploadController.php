<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * @param Request $request
     * @return string
     * @throws \Illuminate\Validation\ValidationException
     */
    public function image(Request $request): string
    {
        $this->validate($request, [
            'file' => 'required|image|mimes:jpg,jpeg,png',
        ]);
        $file = $request->file('file');
        return '/storage/' . $file->store('images', 'public');
    }
}