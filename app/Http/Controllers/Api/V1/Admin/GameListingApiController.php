<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Gamelisting;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGameListingsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class GameListingApiController extends Controller
{
    public function index()
    {
        $gamelistings = Gamelisting::all();
        return response()->json(['GameListings'=>$gamelistings],200);
    }

    public function store(StoreGameListingsRequest $request)
    {
        dd($request->all());
        $gamelisting = new Gamelisting();
            if (!$image = $this->upload($request->file('image'))) {
                return response()->json(['Error'=>"Could Not Upload Image"],400);
            }
        $gamelisting->create([
            'name' => $request->name,
            'image' => $image,
            'description' => $request->description,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);
        // return redirect()->route('admin.game_listings.index')->with('success', 'Game Added Successfully !');
        return response()->json(['Success'=>"Data Inserted Successfully","Data"=>$gamelisting],200);
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
}
