<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Constructor - HANYA ADMIN yang bisa mengakses manajemen user
     */
   public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || $user->role !== 'admin') {
                abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
            }
            return $next($request);
        });
    }
        
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Role filter
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        
        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Trashed filter
        if ($request->filled('trashed') && $request->trashed === 'true') {
            $query->onlyTrashed();
        }
        
        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        
        // Menggunakan constants yang sesuai dengan database
        $roles = [
            'admin' => 'Administrator',
            'cashier' => 'Cashier',
            'barber' => 'Barber',
        ];
        
        $statistics = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'admin' => User::where('role', 'admin')->count(),
            'cashier' => User::where('role', 'cashier')->count(),
            'barber' => User::where('role', 'barber')->count(),
        ];
        
        return view('users.index', compact('users', 'roles', 'statistics'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = [
            'admin' => 'Administrator',
            'cashier' => 'Cashier',
            'barber' => 'Barber',
        ];
        
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => ['required', Rule::in(['admin', 'cashier', 'barber'])],
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);
        
        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = [
            'admin' => 'Administrator',
            'cashier' => 'Cashier',
            'barber' => 'Barber',
        ];
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => ['required', Rule::in(['admin', 'cashier', 'barber'])],
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Remove the specified user from storage (soft delete).
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }
        
        // Prevent deleting the last admin
        $adminCount = User::where('role', 'admin')->count();
        if ($user->role === 'admin' && $adminCount <= 1) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus admin terakhir!');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus!');
    }
    
    /**
     * Force delete user (permanent)
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }
        
        // Prevent deleting the last admin
        $adminCount = User::where('role', 'admin')->count();
        if ($user->role === 'admin' && $adminCount <= 1) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus admin terakhir!');
        }
        
        $user->forceDelete();
        
        return redirect()->route('users.index', ['trashed' => 'true'])
            ->with('success', 'User berhasil dihapus permanen!');
    }
    
    /**
     * Restore soft deleted user
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dipulihkan!');
    }
    
    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('users.index')
            ->with('success', "Password untuk {$user->name} berhasil direset!");
    }
    
    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // Prevent toggling own account
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mengubah status akun sendiri!');
        }
        
        // Prevent disabling the last admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($adminCount <= 1 && $user->is_active) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menonaktifkan admin terakhir!');
            }
        }
        
        $user->update([
            'is_active' => !$user->is_active,
        ]);
        
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('users.index')
            ->with('success', "User berhasil {$status}!");
    }
    
    /**
     * Change own password (untuk semua user)
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }
        
        $user = auth()->user();
        
        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->with('error', 'Password lama tidak sesuai!');
        }
        
        // Update password baru
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return redirect()->back()
            ->with('success', 'Password berhasil diubah!');
    }
}