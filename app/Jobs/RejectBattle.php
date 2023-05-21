<?php

namespace App\Jobs;

use App\Battle;
use App\Events\LiveBattles;
use App\Events\WaitingStatus;
use App\Http\Controllers\Api\V1\Guest\BattleApiController;
use App\Room;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RejectBattle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $battleList;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($battleList)
    {
        $this->battleList = $battleList;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $user_id = $this->battleList->user_id;
        $check_battle = Battle::where('id', $this->battleList->id)->where('user_id', $user_id)->first();
        // check whether any user join the battle or not
        $room_info = Room::select('status')->where('battle_id', $this->battleList->id)->first();
        if (empty($room_info) || $room_info->status == 0) {
            if ($check_battle) {
                $check_battle->delete();
                // after creating battle update user profile battle_create status to decrement
                $update_user_created_battle = User::find($user_id);
                $update_user_created_battle->decrement('created_battles', 1);

                $data = BattleApiController::index($this->battleList->game_listing_id);
                event(new LiveBattles(["gameId"=>$this->battleList->game_listing_id,"liveBattle" => $data->original]));
                event(new WaitingStatus(["battle_id" => $this->battleList->id,"user_id"=>$this->battleList->user_id, "status" => "0","game_id"=> $this->battleList->game_listing_id, 'battleExpired'=>true]));
                return response()->json(['success' => true, 'msg' => "Battle deleted !", "battleId" => $check_battle->id], 200);
            }
        }
        // Battle::where('id', $this->battleList[0]->id)->delete();
    }
}
