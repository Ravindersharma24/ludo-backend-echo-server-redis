<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TransactionHistory;
use App\User;
use Yajra\DataTables\Facades\DataTables;

class TransactionHistoryController extends Controller
{

    public function index()
    {
        try{
            return view('admin.transaction_history.index');
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.transaction_history.index')->with('error', 'Something went wrong');
            }
        }
    }
    public function show($user_id)
    {
        try{
            $user_data = User::where('id',$user_id)->first();
            return view('admin.transaction_history.show',compact('user_id','user_data'));
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.transaction_history.index')->with('error', 'Something went wrong');
            }
        }
    }


    public function battle_transactions()
    {
        try{
            return view('admin.battle_transaction_history.index');
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.battle_transaction_history.index')->with('error', 'Something went wrong');
            }
        }
    }
    public function user_battle_transactions($user_id)
    {
        try{
            $user_data = User::where('id',$user_id)->first();
            return view('admin.battle_transaction_history.show',compact('user_id','user_data'));
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.battle_transaction_history.index')->with('error', 'Something went wrong');
            }
        }
    }


    public function wallet_transactions()
    {
        try{
            return view('admin.wallet_transaction_history.index');
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.wallet_transaction_history.index')->with('error', 'Something went wrong');
            }
        }
    }
    public function user_wallet_transactions($user_id)
    {
        try{
            $user_data = User::where('id',$user_id)->first();
            return view('admin.wallet_transaction_history.show',compact('user_id','user_data'));
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.wallet_transaction_history.index')->with('error', 'Something went wrong');
            }
        }
    }


    public function referral_transactions()
    {
        try{
            return view('admin.referral_transaction_history.index');
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.referral_transaction_history.index')->with('error', 'Something went wrong');
            }
        }
    }
    public function user_referral_transactions($user_id)
    {
        try{
            $user_data = User::where('id',$user_id)->first();
            return view('admin.referral_transaction_history.show',compact('user_id','user_data'));
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.referral_transaction_history.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function penalty_histories()
    {
        try{
            return view('admin.penalty_histories.index');
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.penalty_histories.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function user_penalty_histories($user_id)
    {
        try{
            $user_data = User::where('id',$user_id)->first();
            return view('admin.penalty_histories.show',compact('user_id','user_data'));
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.penalty_histories.index')->with('error', 'Something went wrong');
            }
        }
    }
}


