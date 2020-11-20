<?php


namespace App\Traits;


use App\Models\Like;

/**
 * Trait Likeable
 * @package App\Traits
 */
trait Likeable
{
    public static function bootLikeable(){
        static::deleting(function ($model){
            $model->removeLikes();
        });
    }

    /**
     * @return mixed
     */
    public function likes(){
        return $this->morphMany(Like::class,'likeable');
    }


    /**
     *
     */
    public function like(){
        if(!auth()->check()){
            return;
        }

        //Check if the current user already like the model
        if($this->isLiked()){
            return;
        }

        //Create like for this model with
        $this->likes()->create(['user_id'=> auth()->id()]);
    }

    /**
     *
     */
    public function unlike(){
        if(!auth()->check()){
            return;
        }

        //Check if the current user like the model
        if(! $this->isLiked()){
            return;
        }

        $this->likes()->where('user_id',auth()->id())->delete();

    }

    /**
     * @return bool
     */
    public function isLiked(){
        return (bool) $this->likes()->where('user_id', auth()->id())->count();
    }

    //Delete likes when model being deleted
    public function removeLikes(){
        if($this->likes()->count()){
            $this->likes()->delete();
        }
    }
}
