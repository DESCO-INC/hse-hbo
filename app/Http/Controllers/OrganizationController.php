<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class OrganizationController extends Controller
{
    public function business_unit()
    {
        $business_units = Organization::select('business_unit')->distinct()->pluck('business_unit');

        return response()->json($business_units);
    }
    
    public function company($business_unit)
    {
        $companies = Organization::where('business_unit', $business_unit)->select('company_name')->distinct()->get();
        return response()->json($companies);
    }
}
