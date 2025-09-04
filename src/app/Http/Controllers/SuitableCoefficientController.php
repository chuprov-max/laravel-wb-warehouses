<?php

namespace App\Http\Controllers;

use App\Models\SuitableCoefficient;
use Illuminate\Http\Request;

class SuitableCoefficientController extends Controller
{
    public function index(Request $request)
    {
        $query = SuitableCoefficient::query();

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $coefficients = $query->latest()->paginate(20);

        $warehouses = SuitableCoefficient::query()
            ->join('warehouses', 'suitable_coefficients.warehouse_id', '=', 'warehouses.wb_id')
            ->orderBy('warehouses.name')
            ->pluck('warehouses.name', 'suitable_coefficients.warehouse_id')
            ->toArray();

        return view('coefficients.index', compact('coefficients', 'warehouses'));
    }
}
