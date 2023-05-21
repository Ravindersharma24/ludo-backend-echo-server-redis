<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ReferCommission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReferCommissionController extends Controller
{
    public function index(){
        $refer = ReferCommission::orderBy('id','DESC')->first();
        return view('admin.refer_commission.index',compact('refer'));
    }

    public function create()
    {
        $refer_commission = ReferCommission::all();
        return view('admin.refer_commission.create',compact('refer_commission'));
    }

    public function store(Request $request)
    {
        $add_refer = new ReferCommission();
        $add_refer->create([
            'commission_percentage' => $request->commission_percentage,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);
        return redirect()->route('admin.refer_commissions.index')->with('success', 'Commission Percentage Added Successfully !');
    }
}
