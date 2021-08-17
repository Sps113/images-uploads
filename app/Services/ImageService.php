<?php


namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ImageService
{

    public function save($imageFile): int
    {
        $image = new Image;

        $imageName = Str::uuid().$imageFile->getClientOriginalName();
        $path = $imageFile->storeAs('uploads', $imageName, 'public');

        $image->name = $imageName;
        $image->path = '/storage/' . $path;
        $image->save();
        return $image->id;
    }

    public function predict($imageFile):float
    {
        $id = $this->save($imageFile);
        $image = Image::where('id', $id)->firstOrFail();
        $process = new Process(['rm', $image->path]);
        $process->run();
        return 9.9;
    }
}
