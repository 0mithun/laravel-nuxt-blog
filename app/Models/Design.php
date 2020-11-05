<?php

namespace App\Models;

use App\Traits\Likeable;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Design extends Model
{
    use Taggable, Likeable;


    protected $fillable = [
        'user_id','image','title','slug','description','close_to_comment','is_live','upload_successful','disk','team_id'
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comments(){
        return $this->morphMany(Comment::class,'commentable')
            ->orderBy('created_at','ASC');
    }

    public function team(){
        return $this->belongsTo(Team::class);
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
