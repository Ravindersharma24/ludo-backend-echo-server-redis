<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserProfileRequest;
use App\User;
use Carbon\Carbon;
use App\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function index()
    {
        return view('admin.user_profiles.index');
    }

    public function edit(User $user)
    {
        $user->load('user_profiles');
        // dd($user->user_profiles[0]['cash_won']);
        return view('admin.user_profiles.edit', compact('user'));
    }

    public function store(StoreUserProfileRequest $request)
    {
        $check_user_exist = UserProfile::find($request->id);
        UserProfile::updateOrCreate(
            ['user_id' => $request->id,'mobile' => $request->mobile,],
            [
                'kyc_upload' => $request->kyc_upload,
                'kyc_verified' => $request->kyc_verified,
                'user_id' => $request->id,
                'mobile' => $request->mobile,
                'cash_won' => $request->cash_won,
                'battle_played' => $request->battle_played,
                'kyc_link' => $request->kyc_link,
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp,
            ]
        );
        return redirect()->route('admin.user_profiles.index')->with('success', 'Profile Updated Successfully !');

    }
}
