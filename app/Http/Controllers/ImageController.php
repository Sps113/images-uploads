<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    public function index()
    {

        return view('multiple-image');
    }

    public function storeImage(Request $request)
    {
        $request->validate([
                               'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                           ]);

        $image = new Image;
//        dd($request);
        if ($request->file('file')) {
            $imagePath = $request->file('file');
            $imageName = $imagePath->getClientOriginalName();

            $path = $request->file('file')->storeAs('uploads', $imageName, 'public');
        }

        $image->name = $imageName;
        $image->path = '/storage/'.$path;
        $image->save();

        return response()->json('Image uploaded successfully');
    }
}
