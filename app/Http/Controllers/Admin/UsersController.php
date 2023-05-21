<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Role;
use App\User;

class UsersController extends Controller
{
    public function index()
    {
        try{
        $users = User::orderBy('id', 'DESC')->paginate(10);
        return view('admin.users.index', compact('users'));
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.users.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function create()
    {
        try{
        $roles = Role::all()->pluck('title', 'id');
        return view('admin.users.create', compact('roles'));
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.users.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function store(StoreUserRequest $request)
    {
        try{
            $number = rand(100, 999);
            $character = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $two_character = substr(str_shuffle($character), 0, 2) . $number;
            $referral = str_shuffle($two_character);
            $user = User::create([
                'name'=> $request->name,
                'phone_no'=> $request->phone_no,
                'affiliate_id'=> $referral
            ]);
            // $user->getReferralLink(); // getting unique referral affilate id for each user.
            // $user->roles()->sync($request->input('roles', []));
            return redirect()->route('admin.users.index')->with('success', 'User Created Successfully !');
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.users.index')->with('error', 'Number already taken by other user.try different number');
            }
        }
    }

    public function edit(User $user)
    {
        try{
        $roles = Role::all()->pluck('title', 'id');
        $user->load('roles');
        return view('admin.users.edit', compact('roles', 'user'));
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.users.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function update(StoreUserRequest $request, User $user)
    {
        try {
            $user->update($request->all());
            // $user->roles()->sync($request->input('roles', []));
            return redirect()->route('admin.users.index')->with('success', 'User Updated Successfully !');
        } catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.users.index')->with('error', 'Number already taken by other user.try different number');
            }
        }
    }

    public function show(User $user)
    {
        try{
        $user->load('roles');
        return view('admin.users.show', compact('user'));
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.users.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function destroy(User $user)
    {
        try{
        $user->delete();
        return back();
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.users.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        try{
        User::whereIn('id', request('ids'))->delete();
        return response(null, 204);
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.users.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function toggle(User $user)
    {
        try{
            if ($user->toggle()) {
                return back();
            }
        }
        catch (\Throwable $th) {
            if($th){
                return redirect()->route('admin.users.index')->with('error', 'Something went wrong');
            }
        }
    }
}
