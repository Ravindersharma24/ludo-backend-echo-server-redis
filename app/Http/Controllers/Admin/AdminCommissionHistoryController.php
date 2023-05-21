<?php

namespace App\Http\Controllers\Admin;

use App\AdminCommissionHistory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCommissionHistoryController extends Controller
{
    public function index(){
        try{
            $total_commission = AdminCommissionHistory::sum('admin_commission');
            return view('admin.admin_commission_history.index',compact('total_commission'));
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.admin_commission_history.index')->with('error', 'Something went wrong');
            }
        }
    }
}
