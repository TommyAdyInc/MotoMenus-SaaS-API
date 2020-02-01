<?php


namespace App\Http\Controllers;

use App\MotoMenus\ImageToBase64;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;

class TenantLogoController extends Controller
{
    protected $directory;

    public function __construct()
    {
        $this->directory = app(\Hyn\Tenancy\Website\Directory::class);
    }

    public function store()
    {
        request()->validate([
            'file' => ['required'],
        ]);

        try {
            $file = request()->file('file') ?: request()->file('upload');
            $file_name = 'logo.png';

            $img = InterventionImage::make($file)->orientate()->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            if (!Storage::put($this->directory->path('images/') . $file_name, (string)$img->encode('png'))) {
                throw new \Exception('Unable to upload image. Please try again.');
            }

            return response()->json([
                'status' => 'OK',
                'url'    => (new ImageToBase64('logo.png'))->base64(),
                'name'   => $file_name,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show()
    {
        try {
            return response()->json(['path' => (new ImageToBase64('logo.png'))->base64()], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
