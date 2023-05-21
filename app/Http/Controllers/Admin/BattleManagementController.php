<?php

namespace App\Http\Controllers\Admin;

use App\BattleManagement;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBattleManagementRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BattleManagementController extends Controller
{
    public function index()
    {
        $data = BattleManagement::all();
        // dd($data);
        return view('admin.battle_management.index', compact('data'));
    }

    public function edit(BattleManagement $battle_management)
    {
        return view('admin.battle_management.edit', compact('battle_management'));
    }

    public function update(UpdateBattleManagementRequest $request, BattleManagement $battle_management)
    {
        $minimum_amount = $request->minimum_amount;
        $maximum_battle = $request->maximum_battle;
        $battle_management->update([
            'minimum_amount' => $minimum_amount,
            'maximum_battle' => $maximum_battle,
            'updated_at' => Carbon::now()->timestamp,
        ]);
        return redirect()->route('admin.battle_managements.index')->with('success', 'Data Updated Successfully !');
    }
}
