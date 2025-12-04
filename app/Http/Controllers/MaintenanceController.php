<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

use App\Models\User;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        // Give each pagination its own query parameter name
        $users = User::paginate(5, ['*'], 'users_page');

        return view('maintenance.index', compact('users'));
    }


    public function user_destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('maintenance.index')->with('success', 'User record deleted successfully.');
    }

    public function storeBU(Request $request)
    {
        $request->validate([
            'business_unit' => 'required|string|max:255',
        ]);

        BusinessUnit::create([
            'business_unit' => $request->business_unit,
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        return redirect()->route('maintenance.index')
            ->with('success', 'Business Unit added successfully.');
    }
    

    public function storeCompany(Request $request)
    {
        $request->validate([
            'company' => 'required|string|max:255',
        ]);

        Company::create([
            'company' => $request->company,
            'created_by' => auth()->user()->name ?? 'System',
        ]);

        return redirect()->route('maintenance.index')
            ->with('success', 'Company added successfully.');
    }


    public function bu_destroy($id)
    {
        $business_unit = BusinessUnit::findOrFail($id);
        $business_unit->delete();

        return redirect()->route('maintenance.index')->with('success', 'Business Unit record deleted successfully.');
    }
    

    public function company_destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()->route('maintenance.index')->with('success', 'Company record deleted successfully.');
    }
}
