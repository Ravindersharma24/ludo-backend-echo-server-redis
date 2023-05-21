<?php

namespace App\Http\Controllers\Admin;

use App\ActivateMannaulSetting;
use App\Http\Controllers\Controller;

class ActivateMannualSettingController extends Controller
{
    public function index(){
        try{
            $activate_settings = ActivateMannaulSetting::all();
            return view('admin.activate_mannual_setting.index',compact('activate_settings'));
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.activate_mannual_settings.index')->with('error', 'Something went wrong');
            }
        }
    }

    public function toggle(ActivateMannaulSetting $activate_mannual_setting)
    {
        try{
            if ($activate_mannual_setting->toggle()) {
                return redirect()->route('admin.activate_mannual_settings.index')->with('success','Setting updated!');
            }
        }
        catch(\Throwable $th){
            if($th){
                return redirect()->route('admin.activate_mannual_settings.index')->with('error', 'Something went wrong');
            }
        }
    }
}
