<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RoomHistory;
use App\Gamelisting;
use App\Battle;
use Illuminate\Support\Facades\DB;

class GameHistory extends Controller
{
    public function index(Request $request)
    {
        try{
            $game = GameListing::select('id', 'name')->get();
            $battle = Battle::select('entry_fees')->groupBy('entry_fees')->orderBy('entry_fees', 'DESC')->get();
            $selected_id = [];
            $selected_id['game_id'] = $request->game_id;

            $selected_entry_fees = [];
            $selected_entry_fees['entry_fees'] = $request->entry_fees;

            // search records by game
            if ($request->game_id) {
                $rooms = DB::table('room_historys')->select('room_code', 'room_id', 'game_name', 'entry_fees', 'price','admin_commission', 'created_at', DB::raw("GROUP_CONCAT(player_name) as player_name, GROUP_CONCAT(player_shared_status) as player_shared_status ,GROUP_CONCAT(admin_provided_status) as admin_provided_status, GROUP_CONCAT(screenshot) as screenshot, GROUP_CONCAT(player_id) as player_id,GROUP_CONCAT(cancel_note) as cancel_note"))->where('game_id', 'like', '%' .
                    $request->game_id . '%')->where('entry_fees', 'like', '%' . $request->entry_fees . '%')->where('player_name', 'like', '%' . $request->player_name . '%')
                    ->groupBy('room_id')->orderBy('created_at', 'DESC')
                    ->paginate(10);
                // return json_encode($rooms);
                return view('admin.game_history.index', compact('rooms', 'game', 'selected_id', 'battle', 'selected_entry_fees'));
            }
            // search records by entry_fees
            if ($request->entry_fees) {
                $rooms = DB::table('room_historys')->select('room_code', 'room_id', 'game_name', 'entry_fees', 'price','admin_commission', 'created_at', DB::raw("GROUP_CONCAT(player_name) as player_name, GROUP_CONCAT(player_shared_status) as player_shared_status ,GROUP_CONCAT(admin_provided_status) as admin_provided_status, GROUP_CONCAT(screenshot) as screenshot, GROUP_CONCAT(player_id) as player_id,GROUP_CONCAT(cancel_note) as cancel_note"))->where('entry_fees', 'like', '%' . $request->entry_fees . '%')
                    ->groupBy('room_id')->orderBy('created_at', 'DESC')
                    ->paginate(10);
                // return json_encode($rooms);
                return view('admin.game_history.index', compact('rooms', 'game', 'selected_id', 'battle', 'selected_entry_fees'));
            }

            $rooms = DB::table('room_historys')->select('room_code', 'room_id', 'game_name', 'entry_fees', 'price','admin_commission', 'created_at', DB::raw("GROUP_CONCAT(player_name) as player_name, GROUP_CONCAT(player_shared_status) as player_shared_status ,GROUP_CONCAT(admin_provided_status) as admin_provided_status, GROUP_CONCAT(screenshot) as screenshot, GROUP_CONCAT(player_id) as player_id,GROUP_CONCAT(cancel_note) as cancel_note"))
                ->groupBy('room_id')->orderBy('created_at', 'DESC')
                ->paginate(10);
            //  return json_encode($rooms);
            return view('admin.game_history.index', compact('rooms', 'game', 'selected_id', 'battle', 'selected_entry_fees'));
        }
        catch(\Throwable $th){
            return $th;
        }
    }
}
