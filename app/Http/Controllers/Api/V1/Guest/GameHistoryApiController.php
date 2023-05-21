<?php

namespace App\Http\Controllers\Api\V1\Guest;

use App\Http\Controllers\Controller;
use App\RoomHistory;
use Illuminate\Http\Request;

class GameHistoryApiController extends Controller
{
    public function gameHistory(){
        $user = auth('sanctum')->user();
        $get_game_details = RoomHistory::where('player_id',$user->id)->select(['game_name','admin_provided_status','player_shared_status','entry_fees','price','created_at as date'])->orderBy('created_at','DESC')->paginate(10);
        return response()->json(['game_history'=>$get_game_details]);
    }
}
