<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $redirectRoute = Auth::user()->role === 'kasir' ? route("pos.index") : route("dashboard");
            return redirect($redirectRoute);
        }

        return view("pages.login");
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                "email" => "required|email",
                "password" => "required|min:6",
            ],
            [
                "email.required" => "Email wajib diisi",
                "email.email" => "Email harus valid",
                "password.required" => "Password wajib diisi",
                "password.min" => "Password minimal 6 karakter",
            ],
        );

        if (
            Auth::attempt(
                [
                    "email" => $request->email,
                    "password" => $request->password,
                ],
                $request->input("remember", false),
            )
        ) {
            $request->session()->regenerate();

            $redirectRoute = Auth::user()->role === 'kasir' ? route('pos.index') : route('dashboard');

            return response()->json([
                "success" => true,
                "redirect" => $redirectRoute,
            ]);
        }

        $user = User::where("email", $request->email)->first();
        $errors = !$user
            ? ["email" => ["Email salah"]]
            : ["password" => ["Password salah"]];
        return response()->json(
            [
                "success" => false,
                "errors" => $errors,
            ],
            422,
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route("login");
    }
}
