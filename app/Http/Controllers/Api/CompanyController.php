<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;

class CompanyController extends Controller
{
    // GET /api/companies (nombre, colores y datos de contacto de Bomosa y Agrinsa)
    public function index()
    {
        $companies = Company::orderBy('name')->get();

        return response()->json($companies, 200);
    }
}