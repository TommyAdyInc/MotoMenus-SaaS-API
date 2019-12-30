<?php


namespace App\MotoMenus;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageToBase64
{
    private $image_name;
    public $directory;

    public function __construct($image_name)
    {
        $this->image_name = $image_name;
        $this->directory = app(\Hyn\Tenancy\Website\Directory::class);
    }

    public function base64()
    {
        try {
            if(Storage::exists($this->directory->path('images/logo.png'))) {
                $image = Storage::get($this->directory->path('images/logo.png'));
                $type = File::extension(Storage::url($this->directory->path('images/logo.png')));
            } else {
                $image = file_get_contents(resource_path('images/' . $this->image_name));
                $type = File::extension(resource_path('images/' . $this->image_name));
            }
            return 'data:image/' . $type . ';base64,' . base64_encode($image);
        } catch (\Exception $e) {
            return '';
        }
    }
}
