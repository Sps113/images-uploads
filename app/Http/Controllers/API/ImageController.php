<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ImageRequest;
use App\Services\ImageService;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    private $service;

    public function __construct(ImageService $service)
    {
        $this->service = $service;
    }


    public function store(ImageRequest $request)
    {
        if ($imageFile = $request->file('file')) {
            $this->service->save($imageFile);
        }
        return response()->json('Image uploaded successfully');
    }

    public function prediction(ImageRequest $request)
    {
        if ($imageFile = $request->file('file')) {
            $rating = $this->service->predict($imageFile);
        } else {
            throw new FileException('File not uploaded.');
        }
        return response()->json(['rating' => $rating]);
    }
}
