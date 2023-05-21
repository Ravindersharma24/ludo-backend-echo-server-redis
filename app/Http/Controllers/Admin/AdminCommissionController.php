<?php

namespace App\Http\Controllers\Admin;

use App\AdminCommission;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminCommissionRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminCommissionController extends Controller
{
    public function index()
    {
        try{
            $data = AdminCommission::all();
            return view('admin.admin_commission.index', compact('data'));
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.admin_commissions.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function edit(AdminCommission $admin_commission)
    {
        try{
            return view('admin.admin_commission.edit', compact('admin_commission'));
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.admin_commissions.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function update(UpdateAdminCommissionRequest $request, AdminCommission $admin_commission)
    {
        try{
        $from_amount = $request->from_amount;
        $to_amount = $request->to_amount;
        $commission_type = $request->commission_type;
        $commission_value = $request->commission_value;
        $admin_commission->update([
            'from_amount' => $from_amount,
            'to_amount' => $to_amount,
            'commission_type' => $commission_type,
            'commission_value' => $commission_value,
            'updated_at' => Carbon::now()->timestamp,
        ]);
        return redirect()->route('admin.admin_commissions.index')->with('success', 'Data Updated Successfully !');
    }
    catch(\Throwable $th){
        if($th){
            return redirect()->route('admin.admin_commissions.index')->with('error', 'Something went wrong');
        }
    }
    }
}
