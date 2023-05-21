<?php

namespace App\Http\Controllers\Admin;

use App\Battle;
use App\GameHistory;
use App\Gamelisting;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        try{
            return view('admin.room.index');
        }
        catch (\Throwable $th){
            if($th){
                return redirect()->route('admin.rooms.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function create()
    {
        try{
            $gamelisting = Gamelisting::all();
            return view('admin.room.create',compact('gamelisting'));
        }
        catch (\Throwable $th){
            if($th){
                return redirect()->route('admin.rooms.index')->with('error', 'Something went wrong');
            }
        }
    }


    public function store(StoreRoomRequest $request)
    {
        try{
        $game_name = GameListing::where('id',$request->game_id)->select('name')->first();
        $room = new Room();
        $room->create([
            'game_id' => $request->game_id,
            'code' => $request->code,
            'game_name' => $game_name->name,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);
        return redirect()->route('admin.rooms.index')->with('success', 'Room Created Successfully !');
        }
        catch (\Throwable $th){
            if($th){
                return redirect()->route('admin.rooms.index')->with('error', 'Something went wrong');
            }
        }
    }
}
