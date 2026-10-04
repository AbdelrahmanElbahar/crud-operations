<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    // Same limits as the old backend: jpeg/png/webp/gif, at most 5 MB.
    protected const IMAGE_RULES = 'nullable|image|mimes:jpeg,png,webp,gif|max:5120';

    /**
     * Saves the "image" upload to public/uploads/{folder} and returns its filename,
     * or null when no file was sent.
     */
    protected function storeImage(Request $request, string $folder): ?string
    {
        return $request->hasFile('image')
            ? basename($request->file('image')->store($folder, 'uploads'))
            : null;
    }
}
