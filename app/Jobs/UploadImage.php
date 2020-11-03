<?php

namespace App\Jobs;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $design;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->design->disk;
        $file_name = $this->design->image;
        $original_file=  storage_path().'/uploads/original/'. $file_name;

        try{
            //Create the large image & save to tmp disk
            Image::make($original_file)
                ->fit(800,600, function ($constraint){
                    $constraint->aspectRatio();
                })->save($large = storage_path('uploads/large/'.$file_name));

            ;

            Image::make($original_file)
                ->fit(250,200, function ($constraint){
                    $constraint->aspectRatio();
                })->save($thumbnail = storage_path('uploads/thumbnail/'.$file_name));

            ;
            //Store image to permanent disk
             if(Storage::put('uploads/designs/original/'.$file_name, fopen($original_file, 'r+'))){
                File::delete($original_file);
             }

            if(Storage::put('uploads/designs/large/'.$file_name, fopen($large, 'r+'))){
                File::delete($large);
            }


            if(Storage::put('uploads/designs/thumbnail/'.$file_name, fopen($thumbnail, 'r+'))){
                File::delete($thumbnail);
            }

            //Update the database record with success flag
            $this->design->update([
                'upload_successful' => true,
            ]);

        }catch (\Exception $e){
            \Log::error($e->getMessage());
        }
    }
}
