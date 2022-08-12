<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function authUser(Request $request)
    {
        $user = User::where(['email' => $request->email, 'active' => true])->first();
        if ($user == null) return redirect('/fail');
        if (!(Hash::check($request->password, $user->password))) return redirect('/fail');
        auth()->login($user);
        if ($user->role == "Admin" || $user->role == "Employee") return redirect('/pet');
        return redirect('/');
    }

    public function update(Request $request, $id)
    {

        if ($request->hasfile("img_path")) {
            $file = $request->file("img_path");
            $filename =  $file->getClientOriginalName();
            $destinationPath = public_path() . '/images/customers';
            $file->move($destinationPath, $filename);
        }

        $customer = User::find($id);
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->img_path = '/images/customers/' . $filename;
        $customer->save();
        return redirect('/');
    }
}
