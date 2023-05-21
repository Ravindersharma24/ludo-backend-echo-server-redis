<?php

namespace App\Http\Controllers\Api\V1\Guest;

use App\ActivateMannaulSetting;
use App\AdminCommission;
use App\AdminCommissionHistory;
use App\Battle;
use App\BattleManagement;
use App\CommissionLimitManagement;
use App\Events\ConnectPlayers;
use App\Events\JoiningUserInfo;
use App\Events\LatestBattle;
use App\Events\LiveBattles;
use App\Events\WaitingStatus;
use App\Gamelisting;
use App\Http\Controllers\Api\V1\Guest\BattleApiController;
use App\Http\Controllers\Controller;
use App\Jobs\RejectBattle;
use App\Referral;
use App\Room;
use App\RoomHistory;
use App\TempTransaction;
use App\TransactionHistory;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class CreateRoomControl extends Controller
{
    public function createRoom(Request $request)
    {
        // try {
        $user = auth('sanctum')->user();
        // get battle details which user is want to play
        $get_battle = Battle::where('id', $request->battle_id)->where('game_listing_id', $request->game_id)->first();
        // getting game details
        $get_game = Gamelisting::where('id', $request->game_id)->first();

        //check if user has enough wallet balance
        if ($user->balance + $user->winning_cash < $get_battle->entry_fees) {
            return response()->json(["msg" => "Insufficient Balance to play this battle. kindly add more fund"], 400);
        }
        //check if user has any pending games
        $pending_game = RoomHistory::where(['player_id' => $user->id, 'player_shared_status' => '0'])->first();
        if ($pending_game) {
            // check if pending game status is waiting or completed
            $game_details = Room::where('id', $pending_game->room_id)->first();
            if ($game_details->status === '2') {
                // game was completed.
                return response()->json(['success' => false, 'msg' => "Please upload last game result to play new game.", 'roomCode' => $pending_game->room_code, "roomId" => $pending_game->room_id], 400);
            }
        }
        // check if any player is waiting for same game and battle
        $waiting_game = Room::where('game_id', $request->game_id)->where('status', '1')->where('battle_id', $request->battle_id)->first();
        if ($waiting_game) {
            // echo "waiting game";
            // die;
            $room_id = $waiting_game->id;
            $room_code = $waiting_game->code;
            $other_player = RoomHistory::where('room_id', $room_id)->first();

            // check if waiting player is not requested player.
            if ($other_player->player_id == $user->id) {
                return response()->json(['success' => true, 'msg' => "Waiting for other player to join battle.", 'roomCodeWaiting' => true, 'roomCode' => $room_code], 200);
            }

            // check which user create this battle
            $user_create_battle = Battle::select('user_id')->where('id', $request->battle_id)->first();
            if ($user->id != $user_create_battle->user_id) {
                return response()->json(['success' => false, 'msg' => "Someone else is trying to join this battle.Try another battle or try again later.", $waiting_game], 400);
            }

            $other_player_profile_detail = User::select(['name', 'user_image'])->where('id', $other_player->player_id)->first();
            RoomHistory::create([
                'room_id' => $room_id,
                'player_id' => $user->id,
                'game_id' => $request->game_id,
                'game_name' => $get_game->name,
                'player_name' => $user->name,
                'room_code' => $room_code,
                'entry_fees' => $get_battle->entry_fees,
                'price' => $get_battle->price,
                'admin_commission' => $get_battle->admin_commission,
            ]);
            Room::where('id', $room_id)->update(['status' => '2']);

            //When two playes has joined update battle status to ongoing. (display live battle)
            Battle::where('id', $request->battle_id)->update(['battle_status' => '1']);
            // deduct battle fees from user who joins the battle second
            $user = User::find($user->id);
            //calculate amount from winning cash; winning cash hamesha kam rahega.
            // $winning_cash_amount = $get_battle->entry_fees - ($user->balance - $user->winning_cash);
            // if ($winning_cash_amount >= 0) {
            //     $user->decrement('winning_cash', $winning_cash_amount);
            // }else{
            //     $user->update(['winning_cash' => 0]);
            // }
            // $user->decrement('balance', $get_battle->entry_fees);
            // $user->decrement('deposit_cash', $get_battle->entry_fees);
            $deducted_winning_cash = 0;
            if ($user->balance < $get_battle->entry_fees && $user->balance + $user->winning_cash >= $get_battle->entry_fees) {
                $deducted_balance = $user->balance;
                $deducted_winning_cash = $get_battle->entry_fees - $user->balance;
                TempTransaction::create([
                    'user_id' => $user->id,
                    'username' => $user->name,
                    'balance' => $deducted_balance,
                    'deposit_cash' => $deducted_balance,
                    'winning_cash' => $deducted_winning_cash,
                    'transaction_type' => 1,
                    'battle_id' => $request->battle_id,
                ]);
                $user->decrement('balance', $deducted_balance);
                $user->decrement('deposit_cash', $deducted_balance);
                $user->decrement('winning_cash', $deducted_winning_cash);
            } else {
                TempTransaction::create([
                    'user_id' => $user->id,
                    'username' => $user->name,
                    'balance' => $get_battle->entry_fees,
                    'deposit_cash' => $get_battle->entry_fees,
                    'winning_cash' => $deducted_winning_cash,
                    'transaction_type' => 1,
                    'battle_id' => $request->battle_id,
                ]);
                $user->decrement('balance', $get_battle->entry_fees);
                $user->decrement('deposit_cash', $get_battle->entry_fees);
            }

            $data = BattleApiController::index($request->game_id);
            event(new LiveBattles(["gameId" => $request->game_id, "liveBattle" => $data->original]));

            // $user_wallet_update = User::where('id',$user->id)->first();
            // $user_wallet_update->update(['balance'=>$user_wallet_update->balance - $get_battle->entry_fees]);
            event(new ConnectPlayers(["userId" => $other_player->player_id, "code" => $waiting_game->code, "roomId" => $waiting_game->id, "current_user_image" => $user->user_image, "other_user_image" => $other_player_profile_detail->user_image, "battle_fees" => $other_player->entry_fees, "game_id" => $request->game_id]));
            event(new JoiningUserInfo(["username" => "second user", "id" => 2]));
            return response()->json(['success' => true, 'msg' => "Match Found.", 'roomCode' => $waiting_game->code, "roomId" => $waiting_game->id, "battle_id" => $get_battle->id, "entry-fees" => $get_battle->entry_fees, "user-balance" => $user->balance, "user-winning-cash" => $user->winning_cash], 200);
        } else {
            // it means player is requested for a new/free battle
            $open_game = Room::where('game_id', $request->game_id)->where('status', '0')->first();

            if ($open_game) {
                $room_id = $open_game->id;
                $room_code = $open_game->code;
                RoomHistory::create([
                    'room_id' => $room_id,
                    'player_id' => $user->id,
                    'game_id' => $request->game_id,
                    'game_name' => $get_game->name,
                    'player_name' => $user->name,
                    'room_code' => $room_code,
                    'entry_fees' => $get_battle->entry_fees,
                    'price' => $get_battle->price,
                    'admin_commission' => $get_battle->admin_commission,
                ]);
                Room::where('id', $room_id)->update(['status' => '1', 'battle_id' => $request->battle_id]);
                // deduct battle fees from user who joins the battle first
                $user = User::find($user->id);

                //calculate amount from winning cash; winning cash hamesha kam rahega.
                // $winning_cash_amount = (int)$get_battle->entry_fees - ($user->balance - $user->winning_cash);
                // if ($winning_cash_amount >= 0) {
                //     $user->decrement('winning_cash', $winning_cash_amount);
                // }else{
                //     $user->update(['winning_cash' => 0]);
                // }
                $deducted_winning_cash = 0;
                if ($user->balance < $get_battle->entry_fees && $user->balance + $user->winning_cash >= $get_battle->entry_fees) {
                    $deducted_balance = $user->balance;
                    $deducted_winning_cash = $get_battle->entry_fees - $user->balance;
                    TempTransaction::create([
                        'user_id' => $user->id,
                        'username' => $user->name,
                        'balance' => $deducted_balance,
                        'deposit_cash' => $deducted_balance,
                        'winning_cash' => $deducted_winning_cash,
                        'transaction_type' => 1,
                        'battle_id' => $request->battle_id,
                    ]);
                    $user->decrement('balance', $deducted_balance);
                    $user->decrement('deposit_cash', $deducted_balance);
                    $user->decrement('winning_cash', $deducted_winning_cash);
                } else {
                    TempTransaction::create([
                        'user_id' => $user->id,
                        'username' => $user->name,
                        'balance' => $get_battle->entry_fees,
                        'deposit_cash' => $get_battle->entry_fees,
                        'winning_cash' => $deducted_winning_cash,
                        'transaction_type' => 1,
                        'battle_id' => $request->battle_id,
                    ]);
                    $user->decrement('balance', $get_battle->entry_fees);
                    $user->decrement('deposit_cash', $get_battle->entry_fees);
                }

                event(new JoiningUserInfo(["username" => "first user", "id" => 1]));

                event(new WaitingStatus(["battle_id" => $get_battle->id, "status" => "1", "oppPlayerName" => $user->name, "oppPlayerImage" => $user->user_image, "game_id" => $request->game_id, "roomId" => $room_id, "code" => $room_code]));
                return response()->json(['success' => true, 'msg' => "Waiting for other player to join battle.", 'roomCodeWaiting' => true, 'roomCode' => $room_code, "entry-fees" => (int) $get_battle->entry_fees, "user-balance" => $user->balance, "user-winning-cash" => $user->winning_cash], 200);
            } else {
                return response()->json(['success' => false, 'msg' => "No room code available right now. please enter room code to play."], 200);
            }
        }
        // } catch (\Throwable $th) {
        //     print_r($th);
        // }

    }

    public function gameResult(Request $request)
    {
        // try {
        $user = auth('sanctum')->user();
        $room_id = $request->room_id;
        $status = $request->status;
        if ($status === '1') {
            $rules = array(
                'screenshot' => 'required|mimes:jpeg,jpg,png|max:5120',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
            }
        }

        // update battle status to complete when final result is uploaded by users. (remove battle from live battle when complete)
        $game_battle_id = Room::select(['id', 'battle_id', 'game_id', 'code'])->where('id', $room_id)->first();

        // Battle::where('id', $game_battle_id->battle_id)->update(['battle_status' => '2']);
        // // decrement battle created status of user so he can create another battle once the existing battle remove from live battles.
        // $user_created_battle = Battle::select(['user_id'])->where('id', $game_battle_id->battle_id)->first();
        // $update_user_created_battle = User::where('id', $user_created_battle->user_id);
        // $update_user_created_battle->decrement('created_battles', 0.5);

        // $data = BattleApiController::index(1);
        $data = BattleApiController::index($game_battle_id->game_id);
        event(new LiveBattles(["gameId" => $game_battle_id->game_id, "liveBattle" => $data->original]));
        if ($request->file('screenshot')) {
            $screenshot = $this->upload($request->file('screenshot'), $user->id);
        } else {
            $screenshot = 'notFound.png';
        }
        // $validate_player_status = RoomHistory::where('room_id',$room_id)->select('player_shared_status')->first();
        $update_game_status = RoomHistory::where('room_id', $room_id)->where('player_id', $user->id)->update([
            'screenshot' => $screenshot,
            'player_shared_status' => $status,
        ]);
        $room_details = RoomHistory::where('room_id', $room_id)->get();
        $player_one_status = $room_details[0]->player_shared_status;
        $player_two_status = $room_details[1]->player_shared_status;

        // check if both players have uploaded results or not.
        if ($room_details->where('player_shared_status', '!=', '0')->count() < 2 && $player_one_status != 3 && $player_two_status != 3) {
            return response()->json(['wait' => true, 'success' => true, 'msg' => "Waiting for other player to update game result.", "game_id" => $game_battle_id->game_id], 200);
        } else if ($player_one_status == 3 && $player_two_status == 3) {
            // when both user cancel the game in that case money will be refunded automatically
            foreach ($room_details as $detail) {
                $refundUser = User::find($detail->player_id);
                // refund amount to user who join the requested battle.
                $user_wallet_detail = TempTransaction::where('user_id', $detail->player_id)->where('battle_id', $game_battle_id->battle_id)->first();
                $balance = $user_wallet_detail->balance;
                $deposit_cash = $user_wallet_detail->deposit_cash;
                $winning_cash = $user_wallet_detail->winning_cash;
                $refundUser->increment('balance', $balance);
                $refundUser->increment('deposit_cash', $deposit_cash);
                $refundUser->increment('winning_cash', $winning_cash);
                // $refundUser->increment('balance', $detail->entry_fees);
                // $refundUser->increment('deposit_cash', $detail->entry_fees);
            }
            // decrement created_battle status of a user who created this battle.
            $getUserId = Battle::select('user_id')->where('id', $game_battle_id->battle_id)->first();
            $battleCreatedUser = User::where('id', $getUserId->user_id)->first();
            if ($battleCreatedUser->created_battles < 0) {
                $battleCreatedUser->update(['created_battles' => 0]);
            }
            $battleCreatedUser->decrement('created_battles', 1);
            // when both player cancel the battle set the room status to cancel
            $room = Room::where('id', $room_id)->first();
            $room->update(['status' => '4']);
            return response()->json(['success' => true, 'msg' => "Battle Cancelled", "game_id" => $game_battle_id->game_id], 200);
        } else if (($player_one_status != 0 && $player_two_status != 0) && ($player_one_status != 3 && $player_two_status != 3)) {
            $room = Room::where('id', $room_id)->first();
            //check if both users updated same result or not.
            if ($room_details->groupBy('player_shared_status')->count() == 1 && $room_details[0]->player_shared_status != 3) {
                // when they both update same result then room status will set to conflict
                $room->update(['status' => '3']);
                //claimed same result. now admin will decide winner.
                return response()->json(['wait' => false, 'success' => true, 'msg' => "You both have claimed same. now admin will decide winner.", "game_id" => $game_battle_id->game_id], 200);
            } else if ($room_details->groupBy('player_shared_status')->count() == 1 && $player_one_status == 3 || $player_two_status == 3) {

                // update room status to cancel
                $room->update(['status' => '4']);
                // update cancel note when user cancel the battle
                RoomHistory::where('room_id', $room_id)->where('player_id', $user->id)->update([
                    'cancel_note' => $request->cancelNote ? $request->cancelNote : '-',
                ]);
                return response()->json(['wait' => false, 'success' => true, "game_id" => $game_battle_id->game_id, 'msg' => "You both have cancelled the game."], 200);
            } else {
                //game went smooth. players uploaded right results. now update wallets accordingly.
                foreach ($room_details as $detail) {
                    // update admin shared status as player shared status.
                    RoomHistory::where('room_id', $room_id)->where('player_id', $detail->player_id)->update([
                        'admin_provided_status' => $detail->player_shared_status,
                    ]);

                    $user = User::find($detail->player_id);
                    // //calculate amount from winning cash; winning cash hamesha kam rahega.
                    // $winning_cash_amount = $detail->entry_fees - ($user->balance - $user->winning_cash);
                    // if ($winning_cash_amount > 0) {
                    //     $user->decrement('winning_cash', $winning_cash_amount);
                    // }else{
                    //     $user->update(['winning_cash' => 0]);
                    // }

                    $opposition = "";
                    $game_img = "";
                    $game_name = "";
                    $battle_id = "";
                    if ($user->id) {
                        $opposition_player = RoomHistory::where('room_id', $room_id)->where('player_id', '!=', $user->id)->get();
                        foreach ($opposition_player as $key => $opp_player) {
                            $opposition = $opp_player->player_name;
                        }
                        $game_detail = Room::with('gamelisting')->where('id', $room_id)->get();
                        foreach ($game_detail as $key => $game) {
                            $game_img = $game->gamelisting->image;
                            $game_name = $game->game_name;
                            $battle_id = $game->battle_id;
                        }
                    }
                    $transaction = TransactionHistory::create([
                        'user_id' => $user->id,
                        'username' => $user->name,
                        'order_id' => '',
                        'game_image' => $game_img,
                        'opposition_player' => $opposition,
                        'battle_id' => $battle_id,
                        'game_name' => $game_name,
                    ]);

                    // $user->decrement('balance', $detail->entry_fees);
                    // $user->decrement('deposit_cash', $detail->entry_fees);

                    // if user not win the game then add it's transaction with losing detail
                    $transaction->update([
                        'transaction_amount' => $detail->entry_fees,
                        'dr_cr' => 0,
                        'transaction_type' => '4',
                        'closing_balance' => $user->balance + $user->winning_cash,
                    ]);

                    if ($detail->player_shared_status == 1) {
                        // winner
                        // $user->increment('balance', $detail->price);
                        // $user->increment('deposit_cash', $detail->price);
                        $user->increment('winning_cash', $detail->price);

                        // if user win the game then add it's transaction of winning detail
                        $transaction->update([
                            'transaction_amount' => $detail->price - $detail->entry_fees,
                            'dr_cr' => 1,
                            'transaction_type' => '3',
                            'closing_balance' => $user->balance + $user->winning_cash,
                        ]);

                        // calculate commission percentage amount and update transaction
                        $commission_percentage = CommissionLimitManagement::select('refer_commission_percentage')->orderBy('id', 'DESC')->first();
                        $commission_amount = $detail->price * $commission_percentage->refer_commission_percentage / 100;

                        // update commission percentage to the referral user
                        $update_commission = User::where('affiliate_id', $user->referred_by)->first();
                        if (!empty($update_commission)) {
                            $update_commission->update([
                                'refer_cash' => $update_commission->refer_cash + $commission_amount,
                            ]);
                            $bytes = random_bytes(4);
                            $commission_order_id = bin2hex($bytes);

                            // add referral amount data into the referral table.
                            $refer = Referral::create([
                                'user_id' => $update_commission->id,
                                'username' => $update_commission->name,
                                'referral_amount' => $commission_amount,
                                'dr_cr' => 1,
                                'transaction_type' => 1,
                                'referral_closing_balance' => $update_commission->refer_cash,
                                'order_id' => $commission_order_id . $update_commission->id,
                            ]);

                            // get last insert referral record
                            $ref_order_id = $refer->select('order_id')->orderBy('id', 'DESC')->first();
                            // creating transaction
                            TransactionHistory::create([
                                'user_id' => $update_commission->id,
                                'username' => $update_commission->name,
                                'order_id' => $ref_order_id->order_id,
                                'transaction_amount' => $commission_amount,
                                'transaction_type' => '5',
                                // 'closing_balance' => $update_commission->balance,
                                'closing_balance' => $update_commission->balance + $user->winning_cash,
                                'dr_cr' => 1,
                                'game_image' => '',
                                'opposition_player' => '',
                                'battle_id' => '',
                                'game_name' => '',
                            ]);
                        }
                    }
                }

                // update battle status to completed when both user update right result
                Battle::where('id', $game_battle_id->battle_id)->update(['battle_status' => '2']);

                // create transaction for admin commission;
                $complete_battle = Battle::where('id', $game_battle_id->battle_id)->where('battle_status', '2')->first();
                if ($complete_battle) {
                    AdminCommissionHistory::create([
                        'battle_id' => $complete_battle->id,
                        'game_name' => $game_name,
                        'room_id' => $game_battle_id->id,
                        'room_code' => $game_battle_id->code,
                        'entry_fees' => $complete_battle->entry_fees,
                        'price' => $complete_battle->price,
                        'admin_commission' => $complete_battle->admin_commission,
                        'transaction_type' => '1',
                    ]);
                }

                $update_user_created_battle = User::find($user->id);
                if ($update_user_created_battle->created_battles < 0) {
                    $update_user_created_battle->update(['created_battles' => 0]);
                }
                $update_user_created_battle->decrement('created_battles', 1);
                $data = BattleApiController::index($game_battle_id->game_id);
                event(new LiveBattles(["gameId" => $game_battle_id->game_id, "liveBattle" => $data->original]));
                return response()->json(['wait' => false, 'success' => true, 'msg' => "Game Result Updated.", "game_id" => $game_battle_id->game_id, 'battle_detail' => $complete_battle], 200);
            }
        } else if (($player_one_status == 3 && $player_two_status == 1) || $player_two_status == 3 && $player_one_status == 1) {
            // when player 1 cancel the game and 2 player won or vice-versa
            $room = Room::where('id', $room_id)->first();
            $room->update(['status' => '3']); // set room status to conflict
            if (!empty($request->cancelNote)) {
                RoomHistory::where('room_id', $room_id)->where('player_id', $user->id)->update([
                    'cancel_note' => $request->cancelNote ? $request->cancelNote : '-',
                ]);
            }
            return response()->json(['wait' => false, 'success' => true, 'msg' => "Result uploaded ! Admin will update your wallet shortly", "game_id" => $game_battle_id->game_id], 202);
        } else if (($player_one_status == 3 && $player_two_status == 2) || $player_two_status == 3 && $player_one_status == 2) {
            // when player 1 cancel the game and 2 player loss or vice-versa
            $room = Room::where('id', $room_id)->first();
            $room->update(['status' => '3']); // set room status to conflict
            if (!empty($request->cancelNote)) {
                RoomHistory::where('room_id', $room_id)->where('player_id', $user->id)->update([
                    'cancel_note' => $request->cancelNote ? $request->cancelNote : '-',
                ]);
            }
            return response()->json(['wait' => false, 'success' => true, 'msg' => "Result uploaded ! Admin will update your wallet shortly", "game_id" => $game_battle_id->game_id], 202);
        } else {
            $room = Room::where('id', $room_id)->first();
            // update room status to cancel
            RoomHistory::where('room_id', $room_id)->where('player_id', $user->id)->update([
                'cancel_note' => $request->cancelNote ? $request->cancelNote : '-',
            ]);

            return response()->json(['wait' => false, 'success' => true, 'msg' => "You have cancelled the game", "game_id" => $game_battle_id->game_id], 202);
        }
        // } catch (\Throwable $th) {
        //     print_r($th);
        // }
    }

    public function upload($image, $userid)
    {
        try {
            $destination_path = storage_path('app/public/images/game_result/' . $userid . '/');
            $image_name = time() . '.' . $image->getClientOriginalName();
            $img = Image::make($image->getRealPath());
            $img->resize(1920, 1080);
            if (!file_exists($destination_path)) {
                mkdir($destination_path, 0777);
            }
            $img->save($destination_path . '/' . $image_name);
            return $image_name;
        } catch (Exception $err) {
            print_r($err);
        }
    }

    public function addRoomCode(Request $request)
    {
        // create room with requested game or battle if no rooms are available
        // if(!(substr($request->gameRoomCode, 0, 1) == '0' || substr($request->gameRoomCode, 0, 2) == '00') || !(strlen($request->gameRoomCode) == 8)){
        //     return response()->json(['msg' => "Room code starts with 0 and have only 8 digits.","Success" => false], 400);
        // }
        $user = auth('sanctum')->user();
        $rules = array(
            'gameRoomCode' => 'required|digits_between:6,8|numeric',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'msg' => $validator->errors()->first()], 401);
        }
        $battle_id = $request->battle_id;
        $waiting_game = Room::where('game_id', $request->game_id)->where('status', '1')->where('battle_id', $battle_id)->first();
        $open_game = Room::where('game_id', $request->game_id)->where('status', '0')->first();
        $user_create_battle = '';
        if ($waiting_game) {
            // check which user create this battle
            $user_create_battle = Battle::select('user_id')->where('id', $battle_id)->first();
            if ($user->id != $user_create_battle->user_id) {
                return response()->json(['success' => false, 'msg' => "Someone else is trying to join this battle.Try another battle or try again later.", $waiting_game], 200);
            }
        }
        $get_game = Gamelisting::where('id', $request->game_id)->first();
        $check_room_code = Room::where('code', $request->gameRoomCode)->first();
        if ($check_room_code) {
            return response()->json(['msg' => "This Code already exists. Try Different Code", "Success" => false], 400);
        }

        $check_room_exists = Room::where('battle_id', $battle_id)->first();
        if ($check_room_exists) {
            return response()->json(['msg' => "This room is full.Try another battle", "Success" => false], 400);
        }
        Room::create([
            'game_id' => $request->game_id,
            'code' => $request->gameRoomCode,
            'status' => '0',
            'game_name' => $get_game->name,
        ]);

        $latest_room = Room::latest()->first();
        return response()->json(['success' => true, 'msg' => "Room created Successfully."], 200);
    }

    // user creating game battles
    public function addGameBattle(Request $request)
    {
        $user = auth('sanctum')->user();
        if ($request->entry_fees > $user->balance + $user->winning_cash) {
            return response()->json(['success' => false, 'msg' => "Insuficient wallet balance!"], 400);
        }

        //check if user has any pending games
        $pending_game = RoomHistory::where(['player_id' => $user->id, 'player_shared_status' => '0'])->first();
        if ($pending_game) {
            // check if pending game status is waiting or completed
            $game_details = Room::where('id', $pending_game->room_id)->first();
            if ($game_details->status === '2') {
                // game was completed.
                return response()->json(['success' => false, 'msg' => "Please upload last game result to play new game.", 'roomCode' => $pending_game->room_code, "roomId" => $pending_game->room_id], 400);
            }
        }

        $battle_condition = BattleManagement::orderBy('id', 'DESC')->first();
        // echo 'limit   '.$battle_condition->maximum_battle;
        // echo '    current battle   '.$user->created_battles;
        // die;
        if ($request->entry_fees < $battle_condition->minimum_amount) {
            return response()->json(['msg' => "Battle fees should be greater than Rs $battle_condition->minimum_amount", "Success" => false], 400);
            // echo "Entry fees should be greater than Rs .$battle_condition->minimum_amount.";
            // die;
        }
        if ($user->created_battles >= $battle_condition->maximum_battle) {
            return response()->json(['success' => false, 'msg' => "You can't create more than $battle_condition->maximum_battle battles !! "], 400);
            // echo "You can not create more than .$battle_condition->maximum_battle. ";
            // die;
        }
        if ($request->entry_fees % 50 !== 0) {
            return response()->json(['msg' => "Battle should be a denomination of 50", "Success" => false], 400);
        }
        // echo "battle created";
        // die;
        // if ($request->entry_fees > 5000) {
        //     return response()->json(['msg' => "Entry fees should not be more than Rs 5000", "Success" => false], 400);
        // }
        $admin_commission_percetage = AdminCommission::get();

        // try {
        // $admin_commission = ((int)$request->entry_fees  2)  10 / 100;
        // $price = (int)$request->entry_fees * 2 - (int)$admin_commission;
        // for less than condition
        if ($request->entry_fees * 2 < $admin_commission_percetage[0]['from_amount']) {
            $admin_commission = ((int) $request->entry_fees * 2) * $admin_commission_percetage[0]['commission_value'] / 100;

            $price = (int) $request->entry_fees * 2 - (int) $admin_commission;
        }

        // for greater than condition
        if ($request->entry_fees * 2 > $admin_commission_percetage[1]['from_amount']) {
            $admin_commission = ((int) $request->entry_fees * 2) * $admin_commission_percetage[1]['commission_value'] / 100;

            $price = (int) $request->entry_fees * 2 - (int) $admin_commission;
        }
        // for between condition
        if (($request->entry_fees * 2 >= $admin_commission_percetage[2]['from_amount']) && ($request->entry_fees * 2 <= $admin_commission_percetage[2]['to_amount'])) {
            $admin_commission = (int) $admin_commission_percetage[2]['commission_value'];
            $price = (int) $request->entry_fees * 2 - $admin_commission;
        }
        $battles = new Battle();
        // echo $admin_commission;
        // die;
        $battle_new = $battles->create([
            'game_listing_id' => (int) $request->game_id,
            'user_id' => $user->id,
            'price' => (int) $price,
            'entry_fees' => (int) $request->entry_fees,
            'admin_commission' => (int) $admin_commission,
            'battle_status' => '0',
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        // after creating battle update user profile battle_create status increment
        $update_user_created_battle = User::find($user->id);
        $update_user_created_battle->increment('created_battles', 1);
        $latest_battle = Battle::latest()->first();

        $battleList = Battle::select(['battles.id', 'battles.game_listing_id', 'battles.price', 'battles.entry_fees', 'battle_status', 'user_id', 'users.name as created_by', 'room_historys.room_code', 'rooms.status', 'rooms.id As room_id', DB::raw("GROUP_CONCAT(player_name) as player_name"), DB::raw("GROUP_CONCAT(player_id) as player_id")])
            ->leftJoin('users', function ($join) {
                $join->on('battles.user_id', '=', 'users.id');
            })
            ->leftJoin('rooms', function ($join) {
                $join->on('battles.id', '=', 'rooms.battle_id');
            })
            ->leftJoin('room_historys', function ($join) {
                $join->on('rooms.id', '=', 'room_historys.room_id');
            })
            ->where('game_listing_id', $request->game_id)
            ->where('battles.id', $latest_battle->id)
            ->where('battle_status', '!=', '2')
            ->orderBy('battles.id', 'DESC')
            ->get();

        for ($i = 0; $i < count($battleList); $i++) {
            $player_details = explode(",", $battleList[$i]['player_name']);
            $player_id = explode(",", $battleList[$i]['player_id']);
            $battleList[$i]->player_detail = $player_details;
            // $battleList[$i]->player_ids = $player_id;
            $images = User::select('user_image')->whereIn('id', $player_id)->get();
            $user_image = $images;
            $battleList[$i]->user_image = $user_image;
        }
        event(new LatestBattle(["gameId" => $request->game_id, "latestBattle" => $battleList]));
        $this->dispatch((new RejectBattle($battleList[0]))->delay(120));
        return response()->json(['success' => true, 'msg' => "Battle created Successfully.", "latestBattle" => $battleList], 200);
        // } catch (\Throwable $th) {
        //     return response()->json(['success' => false, 'msg' => $th], 400);
        // }
    }

    public function deleteGameBattle(Request $request)
    {
        $user = auth('sanctum')->user();
        $check_battle = Battle::where('id', $request->battle_id)->where('user_id', $user->id)->first();
        if ($check_battle) {
            $check_battle->delete();
            // after creating battle update user profile battle_create status to decrement

            $update_user_created_battle = User::find($user->id);
            if ($update_user_created_battle->created_battles < 0) {
                $update_user_created_battle->update(['created_battles' => 0]);
            }
            $update_user_created_battle->decrement('created_battles', 1);
            $data = BattleApiController::index($request->game_id);
            event(new LiveBattles(["gameId" => $request->game_id, "liveBattle" => $data->original]));
            event(new WaitingStatus(["battle_id" => $check_battle->id, "status" => "0", "game_id" => $request->game_id, "deleted" => true]));
            return response()->json(['success' => true, 'msg' => "Battle deleted !", "battleId" => $check_battle->id], 200);
        } else {
            return response()->json(['success' => false, 'msg' => "It seems you are not created this battle Or Battle not found"], 400);
        }
    }

    public function rejectGameBattle(Request $request)
    {
        $user = auth('sanctum')->user();
        // $room_id = $request->room_id;
        $id = $request->battle_id;
        $game_id = '';
        $room = '';
        if ($id) {
            $room = Room::select(['id'])->where('battle_id', $id)->first();
        }
        if (!empty($room)) {
            $room_details = RoomHistory::where('room_id', $room->id)->first();
            $game_id = $room_details->game_id;
            $opp_player_id = $room_details->player_id;
            $battle_fees = $room_details->entry_fees;
            // refund entry fees of opposite player
            $user = User::find($opp_player_id);

            // refund amount to user who join the requested battle.
            $opp_user_wallet_detail = TempTransaction::where('user_id', $user->id)->where('battle_id', $id)->first();
            $balance = $opp_user_wallet_detail->balance;
            $deposit_cash = $opp_user_wallet_detail->deposit_cash;
            $winning_cash = $opp_user_wallet_detail->winning_cash;
            $user->increment('balance', $balance);
            $user->increment('deposit_cash', $deposit_cash);
            $user->increment('winning_cash', $winning_cash);

            // delete temp data when user reject the battle
            $opp_user_wallet_detail->delete();
            //calculate amount from winning cash; winning cash hamesha kam rahega.
            // $winning_cash_amount = $battle_fees - ($user->balance - $user->winning_cash);
            // if ($winning_cash_amount >= 0) {
            //     $user->increment('winning_cash', $winning_cash_amount);
            // }else{
            //     $user->update(['winning_cash' => 0]);
            // }
            // $user->increment('balance', $battle_fees);
            // $user->increment('deposit_cash', $battle_fees);
            // $user->increment('winning_cash', $battle_fees);

            // deleting room and releated data
            $room_details->delete();
            Room::where('id', $room->id)->delete();
            $battleList = (object) [
                'user_id' => $user->id,
                'id' => $id,
                'gameId' => $game_id,
            ];
            event(new WaitingStatus(["battle_id" => $id, "user_id" => $battleList->user_id, "status" => "0", "game_id" => $game_id]));
            $this->dispatch((new RejectBattle($battleList))->delay(60));
            return response()->json(['success' => true, 'msg' => "Battle rejected! Searching for new player"], 200);
        }

        // if (!empty($room)) {
        //     $room_details = RoomHistory::where('room_id', $room->id)->first();
        //     $opp_player_id = $room_details->player_id;
        //     $battle_fees = $room_details->entry_fees;

        //     // refund entry fees of opposite player
        //     $user = User::find($opp_player_id);
        //     $user->increment('balance', $battle_fees);
        //     $user->increment('deposit_cash', $battle_fees);

        //     // deleting room and releated data
        //     $room_details->delete();
        //     Room::where('id', $room->id)->delete();
        //     return response()->json(['success' => true, 'msg' => "Battle rejected! Searching for new player"], 200);
        // }
    }

    public function joinedUserDetails(Request $request)
    {
        // try {
        $user = auth('sanctum')->user();
        $battle_id = $request->battle_id;
        $battle_created_user_id = Battle::select(['user_id', 'entry_fees'])->where('id', $battle_id)->first();

        $battle_created_user_detail = User::select(['id', 'name', 'user_image'])->where('id', $battle_created_user_id->user_id)->first();
        $battle_created_user_detail->entry_fees = $battle_created_user_id->entry_fees;

        $check_empty_room = Room::where('battle_id', $battle_id)->first();
        // when no user join the room
        if (empty($check_empty_room)) {
            return response()->json(['success' => true, "data" => $battle_created_user_detail, "room_status" => "empty"], 200);
        }

        // check_empty_room_id
        $current_room_id = Room::select(['id', 'code'])->where('battle_id', $battle_id)->first();
        $check_empty_room_history = RoomHistory::where('room_id', $current_room_id->id)->get();
        $battle_created_user_detail->room_id = $current_room_id->id;
        $battle_created_user_detail->code = $current_room_id->code;
        $battle_created_user_detail->current_user_id = $user->id;

        // when one user join the room
        if (count($check_empty_room_history) == 1) {
            $user_create_battle = Battle::select('user_id')->where('id', $battle_id)->first();
            // check when one user is joined than no other user are able to join the battle
            if ($check_empty_room_history[0]['player_id'] != $user->id) {
                return response()->json(['success' => false, 'msg' => "Someone else is trying to join this battle.Try another battle or try again later."], 200);
            }
            return response()->json(['success' => true, "data" => $battle_created_user_detail, 'code' => $current_room_id->code, "room_status" => "single", "battleCreated" => $user_create_battle->user_id, "oneJoin" => $check_empty_room_history[0]['player_id'], "currentUser" => $user->id], 200);
        }

        $pending_battle = RoomHistory::select(['room_id', 'player_shared_status', 'room_code', 'admin_provided_status', 'player_id'])->where([['player_shared_status', '=', '0'], ['player_id', '=', $user->id]])->first();
        // if (!empty($pending_battle)) {
        //     $opposition_player = RoomHistory::select(['room_historys.room_id', 'room_historys.player_id', 'room_historys.player_name', 'room_historys.entry_fees', 'room_historys.room_code'])->where([['room_id', '=', $pending_battle->room_id], ['player_id', '!=', $user->id]])->first();
        //     if(!empty($opposition_player)){
        //         $image = User::select('user_image')->where('id', $opposition_player->player_id)->first();
        //         $opposition_player->user_image = $image->user_image;
        //         return response()->json(['success' => true, "data" => $opposition_player, "player_status" => $pending_battle->player_shared_status, "admin_status" => $pending_battle->admin_provided_status], 200);
        //     }else{
        //         return response()->json(['success' => true,"player_status" => $pending_battle->player_shared_status, "admin_status" => $pending_battle->admin_provided_status], 200);
        //     }
        //     // print_r($opposition_player);
        // }
        // else{
        $joinedUsers = Room::select(['rooms.battle_id', 'room_historys.room_id', 'room_historys.player_id', 'room_historys.player_name', 'room_historys.entry_fees', 'rooms.code'])
            ->leftJoin('room_historys', function ($join) {
                $join->on('rooms.id', '=', 'room_historys.room_id');
            })
            ->where('rooms.battle_id', $battle_id)
            ->where('room_historys.player_id', '!=', $user->id)
            ->first();
        if (!empty($joinedUsers->player_id)) {
            $image = User::select('user_image')->where('id', $joinedUsers->player_id)->first();
            $joinedUsers->user_image = $image->user_image;
            $current_player_status = Room::select(['room_historys.player_shared_status as status', 'room_historys.admin_provided_status as admin_status'])
                ->leftJoin('room_historys', function ($join) {
                    $join->on('rooms.id', '=', 'room_historys.room_id');
                })
                ->where('rooms.battle_id', $battle_id)
                ->where('room_historys.player_id', '=', $user->id)
                ->first();
            // event(new JoiningUserInfo(["username" => "first user", "id" => 1]));
            if (!empty($current_player_status)) {
                return response()->json(['success' => true, "data" => $joinedUsers, "player_status" => $current_player_status->status, "admin_status" => $current_player_status->admin_status], 200);
            }
        }
        // }
        // } catch (\Throwable $th) {
        //     print_r($th);
        // }
    }

    public function getRoomCode($game_id)
    {
        try {
            // $curl = curl_init("http://193.187.129.22:3000/api/roomcode");
            // fetching roomcode api url
            $room_code_url = Gamelisting::select(['room_code_url'])->where('id', $game_id)->first();
            // check roomcode mannual setting
            $mannual_roomcode_status = ActivateMannaulSetting::where('id', '2')->first();
            if ($mannual_roomcode_status->status == '1') {
                // enable manual roomcode functionality
                return response()->json(["msg" => "Enter Room Code"]);
            }
            $curl = curl_init($room_code_url->room_code_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 20);
            $json = curl_exec($curl);
            if ($json) {
                return $json;
            } else {
                return response()->json(['success' => false, "msg" => "No room code available now."], 401);
            }
        } catch (\Throwable $th) {
            print_r($th);
        }
    }
}
