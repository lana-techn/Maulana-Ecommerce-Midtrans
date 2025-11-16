<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.user', compact('users'));
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:255'],
                'role' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
            $validated['password'] = bcrypt($validated['password']);
            User::create($validated);
            DB::commit();
            return redirect()->back()->with('success', 'User created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'User not created.');
        }
    }

    public function destroy(User $user){
        try{
            DB::beginTransaction();
            $user->delete();
            DB::commit();
            return redirect()->back()
            ->with( 'success', 'User deleted successfully' );
        } catch(Exception $e){
            DB::rollBack();
            return redirect()->back()
            ->with( 'error', 'User not deleted' );
        }
    }


    public function update(Request $request, User $user)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'phone' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:255'],
                'role' => ['required', 'string', 'max:255'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ]);

            // Enkripsi password jika diisi
            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Update user
            $user->update($validated);

            DB::commit();

            return redirect()->back()->with('success', 'User updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->with('error', 'User not updated.');
        }
    }

}
