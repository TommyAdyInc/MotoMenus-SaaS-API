<?php


namespace App\MotoMenus;


class ImageToBase64
{
    private $image_name;

    public function __construct($image_name)
    {
        $this->image_name = $image_name;
    }

    public function base64()
    {
        try {
            $image = file_get_contents(resource_path('images/' . $this->image_name));
            $type = pathinfo(resource_path('images/' . $this->image_name), PATHINFO_EXTENSION);

            return 'data:image/' . $type . ';base64,' . base64_encode($image);
        } catch (\Exception $e) {
            return '';
        }
    }
}
