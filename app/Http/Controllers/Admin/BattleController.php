<?php

namespace App\Http\Controllers\Admin;

use App\Battle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Gamelisting;
use Carbon\Carbon;
use App\Http\Requests\StoreBattleRequest;
use App\Http\Requests\UpdateBattleRequest;
use Illuminate\Support\Facades\Redirect;

class BattleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(\Gate::allows('user_access'), 403);
        if(empty($request->search)){
            return view('admin.battles.index');
        }else{
            $gameList = Gamelisting::select("name as value", "id")
            ->where('name', 'LIKE', '%'. $request->search. '%')
            ->get();
            return response()->json($gameList);
        }
    }

    public function getBattleByGameId(Request $request, $gameId){
        $game_name = GameListing::select("name")->where('id', $gameId)->first();
        return view('admin.battles.index',compact('gameId','game_name'));
    }

    public function createBattle(Request $request, $gameId){

        $game_name = GameListing::select("name")->where('id',$gameId)->first();
        return view('admin.battles.create',compact('gameId','game_name'));
    }

    public function create()
    {
        abort_unless(\Gate::allows('user_create'), 403);

        // $roles = Role::all()->pluck('title', 'id');
        $gamelisting = Gamelisting::all();
        return view('admin.battles.create',compact('gamelisting'));
    }

    public function store(Request $request, $gameId)
    {
        // dd("sdsd");
        abort_unless(\Gate::allows('user_create'), 403);
        $game_name = GameListing::select("name")->where('id',$gameId)->first();
        $battles = new Battle();
        $battles->create([
            'game_listing_id' => (int)$gameId,
            'price' => (int)$request->price,
            'entry_fees' => (int)$request->entry_fees,
            'live_player' => (int)$request->live_player,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        return redirect()->route("admin.battles.getBattleByGame", ["gameId" => $gameId])->with('success', 'Battle Added Successfully !');


        // return view('admin.battles.index',compact('game_id','game_name'))->with('success', 'Battle Created Successfully !');
        // return redirect()->route('battles.getBattleByGame', [$game_id]);
        // return view('admin.battles.index',compact('game_id','game_name'));
    }

    public function edit(Battle $battle,Request $request)
    {
        abort_unless(\Gate::allows('user_show'), 403);
        $gameId = $request->gameId;
        return view('admin.battles.edit', compact('battle','gameId'));
    }

    public function update(UpdateBattleRequest $request, Battle $battle)
    {
        abort_unless(\Gate::allows('user_edit'), 403);
        $battle->update([
            'price' => $request->price,
            'entry_fees' => $request->entry_fees,
            'updated_at' => Carbon::now()->timestamp,
        ]);
        return redirect()->route("admin.battles.getBattleByGame", ["gameId" => $request->gameId])->with('success', 'Battle Updated Successfully !');
    }

    public function destroy(Battle $battle)
    {
            // dd($battle);
            // $battle->battles->delete();
            $battle->delete();
            return redirect()->back()->withErrors("Battle Deleted !");
    }

    public function show(Battle $battle){
        dd($battle);
    }

}
