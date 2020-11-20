<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request){
        //Validate image
        $this->validate($request,[
            'image' =>['required','mimes:jpeg,png,gif,bmp','max:2048'],
        ]);

        //generate file name
        $image = $request->file('image');
        $image_path = $image->getPathname();
        $filename = time().'_'. preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));

        //move the image to temporary location
        $tmp = $image->storeAs('uploads/original', $filename,'tmp');

        //create the database record for the design
        $design = auth()->user()->designs()->create([
            'image' => $filename,
            'disk'  => config('site.upload_disk'),
        ]);

        //dispatch a job for image manipulation
        $this->dispatch(new UploadImage($design));

        return response()->json($design, 200);

    }
}
