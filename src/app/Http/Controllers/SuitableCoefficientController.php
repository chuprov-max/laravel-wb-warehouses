<?php

namespace App\Http\Controllers;

use App\Models\SuitableCoefficient;
use Illuminate\Http\Request;

class SuitableCoefficientController extends Controller
{
    public function index()
    {
        // Получаем все записи с пагинацией, например 15 на страницу
        $coefficients = SuitableCoefficient::orderBy('id', 'desc')->paginate(15);

        // Возвращаем view с данными
        return view('coefficients.index', compact('coefficients'));
    }
}
