<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user list')->only(['index']);
        $this->middleware('can:user create')->only(['create', 'store']);
        $this->middleware('can:user edit')->only(['edit', 'update']);
        $this->middleware('can:user delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($roleFilter = $request->input('role')) {
            $query->role($roleFilter);
        }

        if ($statusFilter = $request->input('status')) {
            $query->where('status', $statusFilter === 'active' ? 1 : 0);
        }

        $perPage = $request->input('per_page', 20);
        $perPage = in_array((int) $perPage, [20, 50, 100]) ? (int) $perPage : 20;

        $users = $query->with('roles')->latest()->paginate($perPage)->withQueryString();

        $roles = Role::all();

        return view('backend.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('backend.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits_between:10,15|unique:users,phone',
            'password' => 'required|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $data['password'] = Hash::make($request->password);
        $data['status'] = true;

        $user = User::create($data);
        $user->assignRole($data['role']);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('backend.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('users.index')->with('error', 'The first user cannot be modified.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|digits_between:10,15|unique:users,phone,' . $user->id,
            'password' => 'nullable|min:8',
            'role' => 'required|string|exists:roles,name',
            'status' => 'required|boolean',
        ]);

        if ($user->id === auth()->id() && !$data['status']) {
            return redirect()->route('users.index')->with('error', 'You cannot disable your own account.');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === 1) {
            return redirect()->route('users.index')->with('error', 'The first user cannot be deleted.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
