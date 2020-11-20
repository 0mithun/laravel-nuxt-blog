<?php


namespace App\Repositories\Eloquent;


use App\Models\User;
use App\Repositories\Contracts\UserContract;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class UserRepository extends BaseRepository implements UserContract
{
    public function model(){
        return User::class;
    }

    public function findByEmail(string  $email){
        return $this->model->where('email', $email)->first();
    }

    public function search(Request $request){
        $query = (new $this->model)->newQuery();

        //Only designer who have designs
        if($request->has_designs){
            $query->has('designs');
        }

        //check for available for hire
        if($request->available_to_hire){
            $query->where('available_to_hire', true);
        }

        //Geographic Search
        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance;
        $unit = $request->unit;

        if($lat && $lng){
            $point = new Point($lat, $lng);
            $unit == 'km' ? $dist*=1000 : $dist*= 1609.34;

            $query->distanceSphereExcludingSelf('location', $point, $dist);
        }

        if($request->orderBy == 'closet'){
            $query->orderByDistanceSphere('location',$point, 'ASC');
        }else if($request->orderBy=='latest'){
            $query->latest();
        }else{
            $query->oldest();
        }

        return $query->get();
    }
}
