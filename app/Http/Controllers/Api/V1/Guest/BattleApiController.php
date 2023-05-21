<?php

namespace App\Http\Controllers\Api\V1\Guest;

use App\Battle;
use App\Events\ConnectPlayers;
use App\Events\LiveBattles;
use App\Gamelisting;
use App\RoomHistory;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BattleApiController extends Controller
{
    static public function index($id)
    {
        // try {
            $battleList = Battle::select(['battles.id', 'battles.price', 'battles.entry_fees', 'battle_status', 'user_id', 'users.name as created_by', 'room_historys.room_code', 'rooms.status', 'rooms.id As room_id','rooms.game_id', DB::raw("GROUP_CONCAT(player_name) as player_name"), DB::raw("GROUP_CONCAT(player_id) as player_id")])
            ->leftJoin('users', function ($join) {
                $join->on('battles.user_id', '=', 'users.id');
            })
            ->leftJoin('rooms', function ($join) {
                $join->on('battles.id', '=', 'rooms.battle_id');
            })
            ->leftJoin('room_historys', function ($join) {
                $join->on('rooms.id', '=', 'room_historys.room_id');
            })
            ->where('game_listing_id', $id)
            ->where('battle_status', '!=', '2')
            ->groupBy('battles.id')
            ->orderBy('battles.id', 'DESC')
            ->get();

        for ($i = 0; $i < count($battleList); $i++) {
            $player_details = explode(",", $battleList[$i]['player_name']);
            $player_id = explode(",", $battleList[$i]['player_id']);

            $battleList[$i]->player_detail = $player_details;
            $battleList[$i]->player_id = $player_id;
            for($j = 0; $j<count($player_details); $j++){
                $images[$j] = User::select('user_image')->where('id', $player_id[$j])->first();
            }
            $user_image = $images;
            $battleList[$i]->user_image = $user_image;
        }
        if($id == '1'){
            event(new LiveBattles(["gameId"=>$id,"liveBattle" => $battleList]));
        }
        return response()->json($battleList);
        // } catch (\Throwable $th) {
        //     print_r($th);
        // }
    }

    public function getAllBattle()
    {
        return response()->json(["battles" => Battle::all()]);
        // return response()->json(["battles"=>RoomHistory::all()]);
    }

}
