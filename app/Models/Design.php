<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Design extends Model
{
    protected $fillable = [
        'user_id','image','title','slug','description','close_to_comment','is_live','upload_successful','disk',
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getImagesAttribute(){
        return [
            'thumbnail' => $this->getImagePath('thumbnail'),
            'original' => $this->getImagePath('original'),
            'large' => $this->getImagePath('large'),
        ];
    }

    public function getImagePath($size){
        return Storage::disk($this->disk)->url("uploads/designs/{$size}/".$this->image);
    }
}