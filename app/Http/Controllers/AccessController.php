<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\access;

class AccessController extends Controller
{
    public function index()
    {
        // Mengambil semua data user dan access
        $users = User::all();
        $accesses = access::all();

        // dd($users);

        return view('pages.access.user-management', compact('users', 'accesses'));
    }

    public function adduser()
    {
        return view('adduser');
    }

    public function create(Request $request)
    {
        // dd($request->access);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

        $userId = $user->id;

        foreach ($request->access as $access) {
            // $user->accesses()->create(['type' => $access]);
            $acc = access::create([
                'user_id' => $userId,
                'access' => $access,
            ]);
            // dd($user->accesses());
        }

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('edituser', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $userId = $user->id;
        $user->accesses()->delete();
        foreach ($request->access as $access) {
            // $user->accesses()->create(['type' => $access]);
            $acc = access::create([
                'user_id' => $userId,
                'access' => $access,
            ]);
            // dd($user->accesses());
        }

        return redirect()->route('user_center')->with('success', 'User updated successfully.');
    }
}
