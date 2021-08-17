<?php


namespace App\Services;

use App\Models\Image;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ImageService
{
    const NAME_DELIMITER = "-";

    public function save($imageFile): int
    {
        $image = new Image;

        $imageName = Str::uuid() . self::NAME_DELIMITER . $imageFile->getClientOriginalName();
        $path = $imageFile->storeAs('uploads', $imageName, 'public');

        $image->name = $imageName;
        $image->path = '/storage/' . $path;
        $image->save();
        return $image->id;
    }

    public function predict($imageFile): float
    {
        $id = $this->save($imageFile);
        $image = Image::where('id', $id)->firstOrFail();
        $process = new Process(
            [
                '/home/ubuntu/predict_rest/image_quality_assessment/predict',
                '--docker-image',
                'nima-cpu',
                '--base-model-name',
                'MobileNet',
                '--weights-file',
                base_path() . $image->path
            ]
        );
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
