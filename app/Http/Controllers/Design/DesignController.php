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
            new EagerLoad(['user']),
        ])->all();
        return DesignResource::collection($designs );
    }

    public function findDesign($id){
        return new DesignResource($this->designs->find($id));
    }


    public function update(Request $request, $id){
        $design = $this->designs->find($id);
        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required','unique:designs,title,'.$id],
            'description'=> ['required','string','min:20','max:140'],
            'tags'  => ['required']

        ]);


       $design =  $this->designs->update($id, [
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
}
