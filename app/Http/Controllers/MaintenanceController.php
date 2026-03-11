<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

use App\Models\User;
use App\Models\Organization;
use App\Models\AuditTrail;

class MaintenanceController extends Controller
{
    public function user(Request $request)
    {
        $business_unit = Organization::distinct()->pluck('business_unit');
        $query = User::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $query
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'users_page')
            ->withQueryString(); // keeps search term in pagination links

        return view('maintenance.user', compact('users', 'business_unit'));
    }

    public function store_user(Request $request)
    {
        // Validate request
        $attributes = $request->validate([
            'name' => ['required', 'string'],
            'email' => [
                'required',
                'email',
                Rule::unique('admin.users', 'email'), // 👈 IMPORTANT
            ],
            'business_unit' => ['required'],
            'credentials' => ['required'],
            'password' => ['required', Password::min(5), 'confirmed'],
        ]);

        // Convert only business_unit and credentials to uppercase
        $attributes['business_unit'] = strtoupper($attributes['business_unit']);
        $attributes['credentials'] = strtoupper($attributes['credentials']);

        // Hash password before saving
        $attributes['password'] = bcrypt($attributes['password']);

        // Create user
        $user = User::create($attributes);

        return redirect()->route('maintenance.user')->with('success', 'User Added Successfully');
    }

    public function update_user(Request $request, User $user)
    {
        $attributes = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('admin.users', 'email')->ignore($user->id)],
            'business_unit' => ['required'],
            'credentials' => ['required'],
            'password' => ['nullable', Password::min(5), 'confirmed'], // optional
        ]);

        // Convert only business_unit and credentials to uppercase
        $attributes['business_unit'] = strtoupper($attributes['business_unit']);
        $attributes['credentials'] = strtoupper($attributes['credentials']);

        if (!empty($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        } else {
            unset($attributes['password']);
        }

        $user->update($attributes);
        return back()->with('success', 'User Updated Successfully');
    }

    public function destroy_user($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User record deleted successfully.');
    }

    public function organization(Request $request)
    {
        $query = Organization::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('business_unit', 'like', "%{$search}%")->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $orgs = $query
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'orgs_page')
            ->withQueryString(); // 👈 keeps search when paginating

        $business_unit = Organization::distinct()->pluck('business_unit');

        return view('maintenance.organization', compact('orgs', 'business_unit'));
    }

    public function store_org(Request $request)
    {
        // Validate request
        $attributes = $request->validate([
            'business_unit' => ['required', 'string'],
            'company_name' => ['required', 'string'],
        ]);

        // Convert all fields to uppercase
        $attributes = array_map(function ($value) {
            return strtoupper($value);
        }, $attributes);

        // Create organization record
        $org = Organization::create([
            'business_unit' => $attributes['business_unit'],
            'company_name' => $attributes['company_name'],
        ]);

        return redirect()->route('maintenance.organization')->with('success', 'Item Added Successfully');
    }

    public function update_org(Request $request, Organization $org)
    {
        // Validate request
        $attributes = $request->validate([
            'business_unit' => ['required', 'string'],
            'company_name' => ['required', 'string'],
        ]);

        // Convert all fields to uppercase
        $attributes = array_map(function ($value) {
            return strtoupper($value);
        }, $attributes);

        // Update organization record
        $org->update([
            'business_unit' => $attributes['business_unit'],
            'company_name' => $attributes['company_name'],
        ]);

        return back()->with('success', 'Organization Updated Successfully');
    }

    public function destroy_org($id)
    {
        $org = Organization::findOrFail($id);
        $org->delete();

        return redirect()->route('maintenance.organization')->with('success', 'Item record deleted successfully.');
    }

    public function profile()
    {
        return view('maintenance.profile');
    }

    public function profile_update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'credentials' => 'required|string',
            'password' => 'nullable|confirmed|min:5',
        ]);

        if (empty($data['password'])) {
            unset($data['password']); // Don't update password if blank
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        Auth::logout();

        return redirect()->route('login')->with('success', 'Profile updated. Please login again.');
    }

    public function audit_trail()
    {
        $trails = AuditTrail::orderBy('id', 'desc')->paginate(10, ['*'], 'trails_page');
        return view('maintenance.audit_trail', compact('trails'));
    }
}
