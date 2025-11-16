<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'phone' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:255'],
            ], [
                'email.unique' => 'The email has already been taken.',
                'password.confirmed' => 'The password confirmation does not match.',
                'password.min' => 'The password must be at least 8 characters.',
            ]);

            // Memulai transaksi database
            DB::beginTransaction();

            try {
                // Membuat pengguna baru
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                ]);
                event(new Registered($user));
                Auth::login($user);
                DB::commit();
                return redirect()->route('home')->with('success', 'Registration successful!' . $user->name);
            } catch (QueryException $e) {
                DB::rollBack();
                Log::error('Error creating user: ' . $e->getMessage());
                return redirect()->route('register')->with('error', 'Registration failed!');
            }
        } catch (Exception $e) {
            DB::rollBack();
             return redirect()->back()
             ->withErrors( $e->getMessage())
             ->withErrors( $e->getMessage() );
        }catch(Exception $e){
            Log::error('Unexpected error during registration: ' . $e->getMessage());
            return redirect()->back()
            ->with('error', 'An unexpected error occurred during registration.')
            ->withInput( $request->except('password' , 'password_confirmation'));
        }
    }

    public function login(Request $request){
        try{
            $credential = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ], [
                'email.required' => 'Email is required.',
                'email.email' => 'Invalid email format.',
                'password.required' => 'Password is required.',
            ]);
            if(Auth::attempt($credential , $request->boolean('remember'))){
                $request->session()->regenerate();
                $user = Auth::user();
                $redirectTo = $user->isAdmin() ? route('admin.dashboard') : route('home');
                return redirect()->intended($redirectTo);
            }
        }catch(Exception $e){

        }
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
