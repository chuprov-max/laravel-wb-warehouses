@extends('layouts.app')

@section('title', 'Коэффициенты')

@section('content')
    <h1>Коэффициенты</h1>

    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
        <tr style="background-color: #f0f0f0;">
            <th>ID</th>
            <th>Склад</th>
            <th>Коэффициент</th>
            <th>Дата приема</th>
            <th>Тип</th>
            <th>Дата обнаружения</th>
        </tr>
        <tr>
            <form method="GET" action="{{ route('coefficients') }}">
                <td></td>
                <td>
                    <select name="warehouse_id" onchange="this.form.submit()">
                        <option value="">-- Все склады --</option>
                        @foreach($warehouses as $id => $name)
                            <option value="{{ $id }}" {{ request('warehouse_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td colspan="4"></td>
            </form>
        </tr>
        </thead>
        <tbody>
        @forelse ($coefficients as $coefficient)
            <tr>
                <td>{{ $coefficient->id }}</td>
                <td>{{ \App\Helpers\WarehouseHelper::getNameById($coefficient->warehouse_id) ?? '-' }}</td>
                <td>{{ $coefficient->coefficient ?? '-' }}</td>
                <td>{{ $coefficient->accept_date ?? '-' }}</td>
                <td>{{ $coefficient->getBoxTypeRussianName() ?? '-' }}</td>
                <td>{{ $coefficient->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center;">Нет данных</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $coefficients->appends(request()->query())->links() }}
    </div>
@endsection
