<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Repositories\Contracts\DesignContract;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{

    /**
     * @var DesignContract
     */
    protected $designs;


    public function __construct(DesignContract $designs)
    {
        $this->designs = $designs;
    }

    public function index(){

        $designs = $this->designs->withCriteria([
            new LatestFirst(),
            new IsLive(),
            new ForUser(1),
            new EagerLoad(['user','comments'])
            ,
        ])->all();
        return DesignResource::collection($designs );
    }

    public function findDesign($id){
        return new DesignResource($this->designs->find($id));
    }

    public function findDesignBySlug($slug){
        $design = $this->designs->withCriteria([
            new IsLive(),
            new EagerLoad(['user','comments'])
        ])-> findWhereFirst('slug', $slug);
        return new DesignResource($design);
    }

    public function update(Request $request, $id){
        $design = $this->designs->find($id);
        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required','unique:designs,title,'.$id],
            'description'=> ['required','string','min:20','max:140'],
            'tags'  => ['required'],
            'team'  => ['required_if:assign_to_team,true']

        ]);


       $design =  $this->designs->update($id, [
           'team_id'   => $request->team ? $request->team: null,
            'title'=>$request->title,
            'description'=> $request->description,
            'slug'  => Str::slug($request->title),
            'is_live'   => ! $design->upload_successful ? false: $request->is_live,
        ]);

        //Apply the tags
       $this->designs->applyTags($id, $request->tags);

        return new DesignResource($design);
    }


    public function destroy($id){
        $design = $this->designs->find($id);
        $this->authorize('delete', $design);

        //Delete the files associated to the record
        foreach (['original','large','thumbnail'] as $size){
            if(Storage::disk($design->disk)->exists("uploads/designs/{$size}/".$design->image)){
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/".$design->image);
            }
        }

        //Delete the record
        $this->designs->delete($id);

        return response()->json(['message'=>'Record deleted successfully'], 200);
    }

    public function like($id){
        $this->designs->like($id);

        return response()->json(['message'=>'Successfull'], 200);
    }

    public function checkIfUserHasLiked($designId){
        $isLiked = $this->designs->isLikedByUser($designId);
        return response()->json(['liked'=>$isLiked], 200);
    }


    public function search(Request $request){
        $designs = $this->designs->search($request);

        return DesignResource::collection($designs);
    }


    public function getForTeam($teamId){
        $designs = $this->designs->withCriteria([
            new IsLive(),

        ])->findWhere('team_id', $teamId);

        return DesignResource::collection($designs);
    }

    public function findDesignByUserId($id){
        $designs = $this->designs
            ->withCriteria([
                new IsLive()
            ])
            ->findWhere('user_id', $id);

        return DesignResource::collection($designs);
    }


    public function userOwnsDesign($id){
        $design = $this->designs
            ->withCriteria([
                new ForUser(auth()->id())
            ])
            ->findWhereFirst('id', $id);
        return new DesignResource($design);
    }
}
