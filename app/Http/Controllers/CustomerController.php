<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Event\CustomerCreated;
use Yajra\Datatables\Datatables;

class CustomerController extends Controller
{

    public function get()
    {
        $customers = Customer::with(['user'])->get();
        return Datatables::of($customers)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                return $row->user->active ? "Active" : "Inactive";
            })
            ->addColumn('img', function ($row) {
                $url = asset('images/customer/' . $row->img_path);
                $img = '<img src=' . $url . ' alt = "I am a pic" height="50" width="50">';;
                return $img;
            })
            ->addColumn('action', function ($row) {
                $btn = "<a href=" . route('customer.deactivate', ['id' => $row->user_id]) . ">Deactivate</a>";
                $btn = $btn . "<a href=" . route('customer.activate', ['id' => $row->user_id]) . ">Activate</a>";
                return $btn;
            })
            ->rawColumns(['status', 'img', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            if ($request->hasfile("img_path")) {
                $file = $request->file("img_path");
                $filename =  $file->getClientOriginalName();
                $destinationPath = public_path() . '/images/customers';
                $file->move($destinationPath, $filename);
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' =>  Hash::make($request->password),
                'role' => "Customer",
                'img_path' => '/images/customers/' . $filename,
            ]);

            Customer::create(['user_id' => $user->id,]);
            event(new CustomerCreated($request->email));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
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
        $customer->email = $request->email;
        $customer->img_path = '/images/customers/' . $filename;
        $customer->save();
        return redirect('/customer');
    }

    public function deactivate($id)
    {
        $customer = User::find($id);
        $customer->active = false;
        $customer->save();
        return redirect('/customer');
    }

    public function activate($id)
    {
        $customer = User::find($id);
        $customer->active = true;
        $customer->save();
        return redirect('/customer');
    }

    public function getProfile()
    {
        $user = auth()->user();
        return view('user.profile', ['user' => $user]);
    }
}
