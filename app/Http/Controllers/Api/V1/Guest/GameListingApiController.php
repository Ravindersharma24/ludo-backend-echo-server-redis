<?php

namespace App\Http\Controllers\API\v1\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Requests\StoreGameListingsRequest;
use App\Http\Requests\UpdateGameListingsRequest;
use App\Gamelisting;
use Carbon\Carbon;

class GameListingApiController extends Controller
{
    public function index()
    {
        $gamelistings = Gamelisting::where('status',true)->get();
        return response()->json(['GameListings'=>$gamelistings],200);
    }

    public function store(StoreGameListingsRequest $request)
    {
        $gamelisting = new Gamelisting();
            if (!$image = $this->upload($request->file('image'))) {
                return response()->json(['msg'=>"Could Not Upload Image","Success"=>false],400);
            }
        $gamelisting->create([
            'name' => $request->name,
            'image' => $image,
            'description' => $request->description,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);
        return response()->json(['msg'=>"Game Inserted Successfully","Success"=>true],200);
    }

    public function upload($file)
    {
        $destination_path = 'public/images/game_listing/';
        $image = $file;
        $image_name = time() . '.' . $image->extension();
        if (File::exists('storage/images/game_listing/'.$image_name)) {
            return false;
        }
        $path = $file->storeAs($destination_path, $image_name);
        return $image_name;
    }

    public function edit(Gamelisting $game_listing)
    {
        return response()->json(["GameList"=>$game_listing]);
    }

    public function update(UpdateGameListingsRequest $request, Gamelisting $game_listing)
    {
        $current_path = 'storage/images/game_listing/'.$request->image;
        $destination_path = 'storage/images/game_listing/'. $game_listing->image;
        $image = $game_listing->image;
        if(!empty($request->file('image'))){
            $image = $this->upload($request->file('image'));
        }
        if (!$image) {
            return response()->json(['msg'=>"Could Not Upload Image","Success"=>false],400);
        }
        if($current_path != "storage/images/game_listing/"){
            File::delete($destination_path);
        }
        $game_listing->update([
            'name' => $request->name,
            'image' => $image,
            'description' => $request->description,
            'updated_at' => Carbon::now()->timestamp,
        ]);
        return response()->json(['msg'=>"Game Updated Successfully","Success"=>true],200);
    }

    public function destroy(Gamelisting $game_listing)
    {
        $destination_path = 'storage/images/game_listing/'. $game_listing->image;
        if (File::exists($destination_path)) {
            File::delete($destination_path);
        }
        $game_listing->delete();
        return response()->json(['msg'=>"Game Deleted Successfully","Success"=>true],200);
    }
}
