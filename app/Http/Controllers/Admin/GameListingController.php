<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreGameListingsRequest;
use App\Gamelisting;
use App\Http\Requests\UpdateGameListingsRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class GameListingController extends Controller
{
    public function index()
    {
        try{
            return view('admin.game_listings.index');
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.game_listings.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function create()
    {
        try{
            return view('admin.game_listings.create');
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.game_listings.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function store(StoreGameListingsRequest $request)
    {
        try{
            $gamelisting = new Gamelisting();
                if (!$image = $this->upload($request->file('image'))) {
                    return redirect()->back()->withErrors("Could Not Upload Image");
                }
            $gamelisting->create([
                'name' => $request->name,
                'image' => $image,
                'description' => $request->description,
                'status' => true,
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp,
            ]);
            return redirect()->route('admin.game_listings.index')->with('success', 'Game Added Successfully !');
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.game_listings.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function upload($file)
    {
        try{
            $destination_path = 'public/images/game_listing/';
            $image = $file;
            $image_name = time() . '.' . $image->extension();
            if (File::exists('storage/images/game_listing/'.$image_name)) {
                return false;
            }
            $path = $file->storeAs($destination_path, $image_name);
            return $image_name;
        }
        catch (\Throwable $th) {
            if($th){
                return $th;
            }
        }
    }

    public function edit(Gamelisting $game_listing)
    {
        try{
            return view('admin.game_listings.edit', compact('game_listing'));
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.game_listings.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function update(UpdateGameListingsRequest $request, Gamelisting $game_listing)
    {
        try{
            $current_path = 'storage/images/game_listing/'.$request->image;
            $destination_path = 'storage/images/game_listing/'. $game_listing->image;
            $image = $game_listing->image;
            if(!empty($request->file('image'))){
                $image = $this->upload($request->file('image'));
            }
            if (!$image) {
                return redirect()->back()->withErrors("Could Not Upload Image");
            }
            if($current_path != "storage/images/game_listing/"){
                File::delete($destination_path);
            }
            $game_listing->update([
                'name' => $request->name,
                'image' => $image,
                'description' => $request->description,
                'status' => $request->status,
                'room_code_url' => $request->room_code_url,
                'updated_at' => Carbon::now()->timestamp,
            ]);
            return redirect()->route('admin.game_listings.index')->with('success', 'Game Updated Successfully !');
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.game_listings.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function destroy(Gamelisting $game_listing, Request $req)
    {
        // $currentURL = URL::current(); // for getting current url of the page/route
        try{
            $game_listing->delete();
            $destination_path = 'storage/images/game_listing/'. $game_listing->image;
            if (File::exists($destination_path)) {
                File::delete($destination_path);
            } else {
                return redirect()->back()->withErrors("File Not Exists");
            }
            return redirect()->back()->withErrors("Game Deleted !");
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.game_listings.index')->with('error', 'Something went wrong');
            }
        }
    }
}
