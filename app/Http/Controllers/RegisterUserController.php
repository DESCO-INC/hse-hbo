<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\Organization;

class RegisterUserController extends Controller
{
    public function index()
    {
        return view('auth.register', [
            'business_unit' => Organization::distinct()->pluck('business_unit'),
        ]);
    }

    public function store()
    {
        //validate
        $attributes = request()->validate([
            'name' => ['required', Rule::unique('users', 'name')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'business_unit' => ['required'],
            'credentials' => ['required'],
            'password' => ['required', Password::min(5), 'confirmed'], //password_confirmation
        ]);

        //create and save user
        $user = User::create($attributes);

        //redirect with flash message
        return redirect('/maintenance')->with('success', 'Registration successful! ');
    }
}
