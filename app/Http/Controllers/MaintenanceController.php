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
    public function index(Request $request)
    {
        $users = User::paginate(5, ['*'], 'users_page');
        $orgs = Organization::paginate(10, ['*'], 'orgs_page');
        $business_unit = Organization::distinct()->pluck('business_unit');
        return view('maintenance.index', compact('users', 'orgs', 'business_unit'));
    }

    public function organization(Request $request)
    {
        $orgs = Organization::paginate(10, ['*'], 'orgs_page');
        $business_unit = Organization::distinct()->pluck('business_unit');
        return view('maintenance.organization', compact('orgs', 'business_unit'));
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

        // Hash password before saving
        $attributes['password'] = bcrypt($attributes['password']);

        // Create user
        $user = User::create($attributes);

        return redirect()->route('maintenance.index')->with('success', 'User Added Successfully');
    }

    public function destroy_user($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('maintenance.index')->with('success', 'User record deleted successfully.');
    }

    public function store_org(Request $request)
    {
        // Validate request
        $attributes = $request->validate([
            'org_business_unit' => ['required', 'string'],
            'org_company_name' => ['required', 'string'],
        ]);

        // Map validated fields to table columns
        $org = Organization::create([
            'business_unit' => $attributes['org_business_unit'],
            'company_name' => $attributes['org_company_name'],
        ]);

        return redirect()->route('maintenance.organization')->with('success', 'Item Added Successfully');
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
