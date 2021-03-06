<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Services\ImageService;

class ImageController extends Controller
{
    private $service;

    public function __construct(ImageService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('multiple-image');
    }

    public function storeImage(ImageRequest $request)
    {
        if ($imageFile = $request->file('file')) {
            $this->service->save($imageFile);
        }
        return response()->json('Image uploaded successfully');
    }
}
