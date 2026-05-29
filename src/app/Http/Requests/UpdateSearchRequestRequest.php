<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSearchRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'box_type_id'    => 'required|in:2,5,6',
            'max_coefficient' => 'required|integer|min:1',
            'status'         => 'required|boolean',
            'warehouses'     => 'array',
            'warehouses.*'   => 'integer|exists:warehouses,wb_id',
            'date_from'      => 'nullable|date',
            'date_to'        => 'nullable|date|after_or_equal:date_from',
        ];
    }

    public function messages(): array
    {
        return [
            'date_to.after_or_equal' => 'Дата окончания должна быть позже или равна дате начала.',
        ];
    }
}
