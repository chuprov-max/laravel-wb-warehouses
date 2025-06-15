<?php

namespace App\Http\Controllers;

use App\Models\SuitableCoefficient;
use Illuminate\Http\Request;

class SuitableCoefficientController extends Controller
{
    public function index(Request $request)
    {
        //$coefficients = SuitableCoefficient::orderBy('id', 'desc')->paginate(15);
        $query = SuitableCoefficient::query();

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $coefficients = $query->latest()->paginate(20);

        $warehouses = collect(config('warehouses.acceptancePriority'))->pluck('name', 'id')->toArray();

        return view('coefficients.index', compact('coefficients', 'warehouses'));
    }
}
