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
        $this->ensureAdmin();
        // Mengambil semua data user dan access
        $users = User::all();
        $accesses = access::all();

        // dd($users);

        return view('pages.access.user-management', compact('users', 'accesses'));
    }

    public function adduser()
    {
        $this->ensureAdmin();
        return view('pages.access.adduser');
    }

    public function create(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'company' => 'required|in:MLP,KES',
            'location' => 'required|string|max:255',
            'status' => 'required|in:Administrator,Super Admin,Creator,Modified,Viewers,Auditor',
            'hirar' => 'nullable|in:Supervisor,Manager,Deputy General Manager',
            'password' => 'required|string|min:8',
            'access' => 'nullable|array',
        ]);
        $user = User::create($data);

        $userId = $user->id;

        foreach ($request->input('access', []) as $access) {
            // $user->accesses()->create(['type' => $access]);
            $acc = access::create([
                'user_id' => $userId,
                'access' => $access,
            ]);
            // dd($user->accesses());
        }

        return redirect()->route('user-management')->with('success', 'User created successfully.');
    }

    public function destroy($id)
    {
        $this->ensureAdmin();
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function edit($id)
    {
        $this->ensureAdmin();
        $user = User::findOrFail($id);
        // dd($user);
        return view('pages.access.edituser', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $this->ensureAdmin();
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'company' => 'required|in:MLP,KES',
            'location' => 'required|string|max:255',
            'status' => 'required|in:Administrator,Super Admin,Creator,Modified,Viewers,Auditor',
            'hirar' => 'nullable|in:Supervisor,Manager,Deputy General Manager',
            'password' => 'nullable|string|min:8',
            'access' => 'nullable|array',
        ]);
        $user->fill(collect($data)->except(['access', 'password'])->all());
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        $userId = $user->id;
        $user->accesses()->delete();
        foreach ($request->input('access', []) as $access) {
            // $user->accesses()->create(['type' => $access]);
            $acc = access::create([
                'user_id' => $userId,
                'access' => $access,
            ]);
            // dd($user->accesses());
        }

        return redirect()->route('user-management')->with('success', 'User updated successfully.');
    }

    private function ensureAdmin(): void
    {
        abort_unless(in_array(auth()->user()->status, ['Administrator', 'Super Admin'], true), 403);
    }
}
