<?php

namespace App\Http\Controllers\Admin;

use App\AdminCommissionHistory;
use App\Battle;
use App\CommissionLimitManagement;
use App\Http\Controllers\Controller;
use App\PenaltyHistory;
use Illuminate\Http\Request;
use App\RoomHistory;
use App\ReferCommission;
use App\TransactionHistory;
use App\Referral;
use App\Room;
use App\TempTransaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoomHistoryController extends Controller
{
    // public function index()
    // {
    //     return view('admin.room_history.index');
    // }
    public function getGameByRoomId($roomId)
    {
        $gameDetails = RoomHistory::with('user')->where('room_id', $roomId)->get();
        // dd($gameDetails);
        // return view('admin.room_history.gameDetail',compact('gameDetails'));
        // $group = RoomHistory::where('room_id',$roomId)->get();
        // return $gameDetails;
        return view('admin.room_history.gameDetail', compact('gameDetails'));
        // $player = "player";
        // $rooms = DB::table('room_historys')->where('room_id',5)->select(DB::raw("GROUP_CONCAT(player_name) as player_name, GROUP_CONCAT(player_shared_status) as player_shared_status"))
        // ->groupBy('room_id')
        // ->first();

    }

    // public function edit(RoomHistory $room_history)
    // {
    //     return view('admin.room_history.edit', compact('room_history'));
    // }

    public function update(Request $request)
    {
        $room_history_id = $request->roomHistoryId;
        $admin_provided_status = $request->admin_provided_status;
        $room_id = $request->room_id;
        $player_id = $request->player_id;

        $game_battle_id = Room::select(['id','battle_id', 'game_id','code'])->where('id', $room_id)->first();

        RoomHistory::where('room_id', $room_id)->where('player_id', $player_id)->update([
            'player_shared_status' => $admin_provided_status,
            'admin_provided_status' => $admin_provided_status,
        ]);
        RoomHistory::where('room_id', $room_id)->where('player_id', '!=', $player_id)->update([
            'player_shared_status' => '2',
            'admin_provided_status' => '2',
        ]);
        // update wallet balance by admin in case when both user sent same game result
        $room_details = RoomHistory::where('room_id', $room_id)->get();
        foreach ($room_details as $detail) {
            // update admin shared status as player shared status.

            $user = User::find($detail->player_id);
            //calculate amount from winning cash;
            // $winning_cash_amount = $detail->entry_fees - ($user->balance - $user->winning_cash);
            // if ($winning_cash_amount > 0) {
            //     $user->decrement('winning_cash', $winning_cash_amount);
            // }

            // code for updating transaction history
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
                'game_image' =>  $game_img,
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
                if ($commission_percentage != null) {
                    $commission_amount = $detail->price * $commission_percentage->refer_commission_percentage / 100;
                    // update commission percentage to the referral user
                    $update_commission = User::where('affiliate_id', $user->referred_by)->first();
                    if (!empty($update_commission)) {
                        $update_commission->update([
                            'refer_cash' => $update_commission->refer_cash + $commission_amount,
                        ]);
                        $bytes = random_bytes(4);
                        $commission_order_id =  bin2hex($bytes);

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
                            'closing_balance' => $update_commission->balance + $update_commission->winning_cash,
                            'dr_cr' => 1,
                            'game_image' =>  '',
                            'opposition_player' => '',
                            'battle_id' => '',
                            'game_name' => '',
                        ]);
                    }
                }
            }
        }

        //update room status
        Room::where('id', $room_id)->update(['status' => '2']);
        // update battle status in battle table to completed (2)
        Battle::where('id', $battle_id)->update(['battle_status' => '2']);

        // create transaction for admin commission;
        $complete_battle = Battle::where('id',$game_battle_id->battle_id)->where('battle_status','2')->first();
        if($complete_battle){
            AdminCommissionHistory::create([
                'battle_id'=>$complete_battle->id,
                'game_name'=>$game_name,
                'room_id'=>$game_battle_id->id,
                'room_code'=>$game_battle_id->code,
                'entry_fees'=>$complete_battle->entry_fees,
                'price'=>$complete_battle->price,
                'admin_commission'=>$complete_battle->admin_commission,
                'transaction_type'=>'1'
            ]);
        }


        // decrement battle created status of user so he can create another battle once the existing battle remove from live battles.
        $user_created_battle = Battle::select(['user_id'])->where('id', $battle_id)->first();
        $update_user_created_battle = User::where('id', $user_created_battle->user_id);
        // if ($update_user_created_battle->created_battles < 0) {
        //     $update_user_created_battle->update(['created_battles' => 0]);
        // }
        $update_user_created_battle->decrement('created_battles', 1);

        return redirect()->back()->withMessage('Game result updated!');
        // return redirect()->route('admin.room_historys.index')->with('success', 'Game Result Updated !');
    }

    public function cancel(Request $request)
    {
        $room_history_id = $request->roomHistoryId;
        $admin_provided_status = $request->admin_provided_status;
        $room_id = $request->room_id;
        $player_id = $request->player_id;

        // $roomHistory = RoomHistory::where('room_id', $room_id)->get();
        // $player_one_cancel_note = $roomHistory[0]->cancel_note;
        // $player_two_cancel_note = $roomHistory[1]->cancel_note;
        RoomHistory::where('room_id', $room_id)->update([
            'player_shared_status' => '3',
            'admin_provided_status' => '3',
            'cancel_note' => 'Battle is cancelled by admin',
        ]);

        //update room status to cancel

        $room = Room::where('id', $room_id)->first();
        $room->update(['status' => '4']);

        // update battle status in battle table to completed (2)
        Battle::where('id', $room->battle_id)->update(['battle_status' => '2']);
        // decrement battle created status of user so he can create another battle once the existing battle remove from live battles.
        $user_created_battle = Battle::select(['user_id'])->where('id', $room->battle_id)->first();
        $update_user_created_battle = User::where('id', $user_created_battle->user_id);
        $update_user_created_battle->decrement('created_battles', 1);
        // if ($update_user_created_battle->created_battles < 0) {
        //     $update_user_created_battle->update(['created_battles' => 0]);
        // }

        // return battles fees to both the user when battle is finally cancelled by the admin
        $room_details = RoomHistory::where('room_id', $room_id)->get();
        foreach ($room_details as $detail) {
            $user = User::find($detail->player_id);
            // refund amount to user who join the requested battle.
            $user_wallet_detail = TempTransaction::where('user_id',$detail->player_id)->where('battle_id',$room->battle_id)->first();
            $balance = $user_wallet_detail->balance;
            $deposit_cash = $user_wallet_detail->deposit_cash;
            $winning_cash = $user_wallet_detail->winning_cash;
            $user->increment('balance', $balance);
            $user->increment('deposit_cash', $deposit_cash);
            $user->increment('winning_cash', $winning_cash);
            // $user->increment('balance', $detail->entry_fees);
            // $user->increment('deposit_cash', $detail->entry_fees);
        }

        return redirect()->back()->withMessage('Battle Cancelled!');
    }

    // public function penalty(Request $request){
    //     $room_history_id = $request->roomHistoryId;
    //     $room_detail = RoomHistory::where('id',$room_history_id)->first();
    //     $room = Room::where('id',$room_detail->room_id)->select('battle_id')->first();

    //     $penalty_amt = CommissionLimitManagement::select(['pending_game_penalty_amt','wrong_result_penalty_amt'])->orderBy('id', 'DESC')->first();
    //     $get_user = User::where('id',$room_detail->player_id)->first();
    //     $deducted_winning_cash = 0;
    //     // apply pending game penalty
    //     if($room_detail->player_shared_status === '0')
    //     {
    //         // dd($penalty_amt->pending_game_penalty_amt);
    //         if($get_user->balance >= $penalty_amt->pending_game_penalty_amt)
    //         {
    //             // deduct money from balance
    //             $deducted_balance = $penalty_amt->pending_game_penalty_amt;

    //             $get_user->decrement('balance', $deducted_balance);
    //             $get_user->decrement('deposit_cash', $deducted_balance);
    //             $get_user->decrement('winning_cash', $deducted_winning_cash);
    //             PenaltyHistory::create([
    //                 'user_id' => $get_user->id,
    //                 'username' => $get_user->name,
    //                 'mobile_no' => $get_user->phone_no,
    //                 'battle_id' => $room->battle_id,
    //                 'penalty_type' => '2',
    //                 'transaction_type' => '1',
    //                 'penalty_amount' => $penalty_amt->pending_game_penalty_amt,
    //             ]);
    //             $room_detail->update(['penalty_status' => '1']);
    //             // creating transaction
    //             TransactionHistory::create([
    //                 'user_id' => $get_user->id,
    //                 'username' => $get_user->name,
    //                 'order_id' => '',
    //                 'transaction_amount' => $penalty_amt->pending_game_penalty_amt,
    //                 'transaction_type' => '6',
    //                 // 'closing_balance' => $update_commission->balance,
    //                 'closing_balance' => $get_user->balance + $get_user->winning_cash,
    //                 'dr_cr' => 0,
    //                 'game_image' =>  '',
    //                 'opposition_player' => '',
    //                 'battle_id' => $room->battle_id,
    //                 'game_name' => '',
    //             ]);
    //             return redirect()->back()->withMessage('Pending game penalty applied!');
    //         }
    //         if($get_user->balance < $penalty_amt->pending_game_penalty_amt && $get_user->balance + $get_user->winning_cash >= $penalty_amt->pending_game_penalty_amt){
    //             // deduct money from the total of both balance
    //             $deducted_balance = $get_user->balance;
    //             $deducted_winning_cash = $penalty_amt->pending_game_penalty_amt - $get_user->balance;

    //             $get_user->decrement('balance', $deducted_balance);
    //             $get_user->decrement('deposit_cash', $deducted_balance);
    //             $get_user->decrement('winning_cash', $deducted_winning_cash);
    //             PenaltyHistory::create([
    //                 'user_id' => $get_user->id,
    //                 'username' => $get_user->name,
    //                 'mobile_no' => $get_user->phone_no,
    //                 'battle_id' => $room->battle_id,
    //                 'penalty_type' => '2',
    //                 'transaction_type' => '1',
    //                 'penalty_amount' => $penalty_amt->pending_game_penalty_amt,
    //             ]);
    //             $room_detail->update(['penalty_status' => '1']);
    //             // creating transaction
    //             TransactionHistory::create([
    //                 'user_id' => $get_user->id,
    //                 'username' => $get_user->name,
    //                 'order_id' => '',
    //                 'transaction_amount' => $penalty_amt->pending_game_penalty_amt,
    //                 'transaction_type' => '6',
    //                 // 'closing_balance' => $update_commission->balance,
    //                 'closing_balance' => $get_user->balance + $get_user->winning_cash,
    //                 'dr_cr' => 0,
    //                 'game_image' =>  '',
    //                 'opposition_player' => '',
    //                 'battle_id' => $room->battle_id,
    //                 'game_name' => '',
    //             ]);
    //             return redirect()->back()->withMessage('Pending game penalty applied!');
    //         }
    //         if($get_user->balance + $get_user->winning_cash < $penalty_amt->pending_game_penalty_amt){
    //             return redirect()->back()->withMessage('Insufficient balane to apply penalty!');
    //         }

    //     }


    //     // apply wrong result penalty
    //     if($room_detail->player_shared_status === '1' || $room_detail->player_shared_status === '2' || $room_detail->player_shared_status === '3')
    //     {
    //         // dd($penalty_amt->wrong_result_penalty_amt);
    //         if($get_user->balance >= $penalty_amt->wrong_result_penalty_amt)
    //         {
    //             // deduct money from balance
    //             $deducted_balance = $penalty_amt->wrong_result_penalty_amt;
    //             $get_user->decrement('balance', $deducted_balance);
    //             $get_user->decrement('deposit_cash', $deducted_balance);
    //             $get_user->decrement('winning_cash', $deducted_winning_cash);
    //             PenaltyHistory::create([
    //                 'user_id' => $get_user->id,
    //                 'username' => $get_user->name,
    //                 'mobile_no' => $get_user->phone_no,
    //                 'battle_id' => $room->battle_id,
    //                 'penalty_type' => '1',
    //                 'transaction_type' => '1',
    //                 'penalty_amount' => $penalty_amt->wrong_result_penalty_amt,
    //             ]);
    //             $room_detail->update(['penalty_status' => '1']);
    //             // creating transaction
    //             TransactionHistory::create([
    //                 'user_id' => $get_user->id,
    //                 'username' => $get_user->name,
    //                 'order_id' => '',
    //                 'transaction_amount' => $penalty_amt->wrong_result_penalty_amt,
    //                 'transaction_type' => '6',
    //                 // 'closing_balance' => $update_commission->balance,
    //                 'closing_balance' => $get_user->balance + $get_user->winning_cash,
    //                 'dr_cr' => 0,
    //                 'game_image' =>  '',
    //                 'opposition_player' => '',
    //                 'battle_id' => $room->battle_id,
    //                 'game_name' => '',
    //             ]);
    //             return redirect()->back()->withMessage('Wrong result game penalty applied!');
    //         }
    //         if($get_user->balance < $penalty_amt->wrong_result_penalty_amt && $get_user->balance + $get_user->winning_cash >= $penalty_amt->wrong_result_penalty_amt){
    //             // deduct money from the total of both balance
    //             $deducted_balance = $get_user->balance;
    //             $deducted_winning_cash = $penalty_amt->wrong_result_penalty_amt - $get_user->balance;

    //             $get_user->decrement('balance', $deducted_balance);
    //             $get_user->decrement('deposit_cash', $deducted_balance);
    //             $get_user->decrement('winning_cash', $deducted_winning_cash);
    //             PenaltyHistory::create([
    //                 'user_id' => $get_user->id,
    //                 'battle_id' => $room->battle_id,
    //                 'penalty_type' => '1',
    //                 'penalty_amount' => $penalty_amt->wrong_result_penalty_amt,
    //             ]);
    //             $room_detail->update(['penalty_status' => '1']);
    //             // creating transaction
    //             TransactionHistory::create([
    //                 'user_id' => $get_user->id,
    //                 'username' => $get_user->name,
    //                 'order_id' => '',
    //                 'transaction_amount' => $penalty_amt->wrong_result_penalty_amt,
    //                 'transaction_type' => '6',
    //                 // 'closing_balance' => $update_commission->balance,
    //                 'closing_balance' => $get_user->balance + $get_user->winning_cash,
    //                 'dr_cr' => 0,
    //                 'game_image' =>  '',
    //                 'opposition_player' => '',
    //                 'battle_id' => $room->battle_id,
    //                 'game_name' => '',
    //             ]);
    //             return redirect()->back()->withMessage('Wrong result game penalty applied!');
    //         }
    //         if($get_user->balance + $get_user->winning_cash < $penalty_amt->wrong_result_penalty_amt){
    //             return redirect()->back()->withMessage('Insufficient balane to apply penalty!');
    //         }
    //     }

    // }

    public function penalty(Request $request){
        $room_history_id = $request->roomHistoryId;
        $room_detail = RoomHistory::where('id',$room_history_id)->first();
        $room = Room::where('id',$room_detail->room_id)->select('battle_id')->first();

        $penalty_amt = CommissionLimitManagement::select(['pending_game_penalty_amt','wrong_result_penalty_amt'])->orderBy('id', 'DESC')->first();
        $get_user = User::where('id',$room_detail->player_id)->first();
        $deducted_winning_cash = 0;
        $battle_id = $room->battle_id;
        $penalty_transaction_type = '1';
        $transaction_type = '6';
        $game_name = $room_detail->game_name;
        $dr_cr = 0;
        // apply pending game penalty
        if($room_detail->player_shared_status === '0')
        {
            if($get_user->balance >= $penalty_amt->pending_game_penalty_amt)
            {
                // deduct money from balance
                $deducted_balance = $penalty_amt->pending_game_penalty_amt;
                $penalty_type = '2';
                $message = 'Pending game penalty applied!';
                $this->applyPenalty($get_user,$deducted_balance,$battle_id,$deducted_winning_cash,$room_detail,$penalty_type,$penalty_transaction_type,$transaction_type,$dr_cr,$game_name);
                return redirect()->back()->withMessage($message);
            }
            if($get_user->balance < $penalty_amt->pending_game_penalty_amt && $get_user->balance + $get_user->winning_cash >= $penalty_amt->pending_game_penalty_amt){
                // deduct money from the total of both balance
                $deducted_balance = $get_user->balance;
                $deducted_winning_cash = $penalty_amt->pending_game_penalty_amt - $get_user->balance;
                $penalty_type = '2';
                $message = 'Pending game penalty applied!';
                $this->applyPenalty($get_user,$deducted_balance,$battle_id,$deducted_winning_cash,$room_detail,$penalty_type,$penalty_transaction_type,$transaction_type,$dr_cr,$game_name);
                return redirect()->back()->withMessage($message);
            }
            if($get_user->balance + $get_user->winning_cash < $penalty_amt->pending_game_penalty_amt){
                return redirect()->back()->withMessage('Insufficient balane to apply penalty!');
            }

        }

        // apply wrong result penalty
        if($room_detail->player_shared_status === '1' || $room_detail->player_shared_status === '2' || $room_detail->player_shared_status === '3')
        {
            if($get_user->balance >= $penalty_amt->wrong_result_penalty_amt)
            {
                // deduct money from balance
                $deducted_balance = $penalty_amt->wrong_result_penalty_amt;
                $penalty_type = '1';
                $message = 'Wrong result game penalty applied!';
                $this->applyPenalty($get_user,$deducted_balance,$battle_id,$deducted_winning_cash,$room_detail,$penalty_type,$penalty_transaction_type,$transaction_type,$dr_cr,$game_name);
                return redirect()->back()->withMessage($message);
            }
            if($get_user->balance < $penalty_amt->wrong_result_penalty_amt && $get_user->balance + $get_user->winning_cash >= $penalty_amt->wrong_result_penalty_amt){
                // deduct money from the total of both balance
                $deducted_balance = $get_user->balance;
                $deducted_winning_cash = $penalty_amt->wrong_result_penalty_amt - $get_user->balance;
                $penalty_type = '1';
                $message = 'Wrong result game penalty applied!';
                $this->applyPenalty($get_user,$deducted_balance,$battle_id,$deducted_winning_cash,$room_detail,$penalty_type,$penalty_transaction_type,$transaction_type,$dr_cr,$game_name);
                return redirect()->back()->withMessage($message);
            }
            if($get_user->balance + $get_user->winning_cash < $penalty_amt->wrong_result_penalty_amt){
                return redirect()->back()->withMessage('Insufficient balane to apply penalty!');
            }
        }
    }

    public function applyPenalty($get_user,$deducted_balance,$battle_id,$deducted_winning_cash,$room_detail,$penalty_type,$penalty_transaction_type,$transaction_type,$dr_cr,$game_name)
    {
        $get_user->decrement('balance', $deducted_balance);
        $get_user->decrement('deposit_cash', $deducted_balance);
        $get_user->decrement('winning_cash', $deducted_winning_cash);
        PenaltyHistory::create([
            'user_id' => $get_user->id,
            'username' => $get_user->name,
            'mobile_no' => $get_user->phone_no,
            'battle_id' => $battle_id,
            'penalty_type' => $penalty_type,
            'transaction_type' => $penalty_transaction_type,
            'penalty_amount' => $deducted_balance,
        ]);
        $room_detail->update(['penalty_status' => '1']);
        // creating transaction
        TransactionHistory::create([
            'user_id' => $get_user->id,
            'username' => $get_user->name,
            'order_id' => '',
            'transaction_amount' => $deducted_balance,
            'transaction_type' => $transaction_type,
            'closing_balance' => $get_user->balance + $get_user->winning_cash,
            'dr_cr' => $dr_cr,
            'game_image' =>  '',
            'opposition_player' => '',
            'battle_id' => $battle_id,
            'game_name' => $game_name,
        ]);
    }
}
