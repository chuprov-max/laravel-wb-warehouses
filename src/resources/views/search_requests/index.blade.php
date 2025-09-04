@extends('layouts.app')

@section('title', 'Запросы поиска')

@section('content')
    <h1>Поисковые запросы</h1>

    <a href="{{ route('search-requests.create') }}" class="button-success">+ Добавить</a>

    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; text-align:center; margin-top:1rem;">
        <thead>
        <tr>
            <th>ID</th>
            <th>Пользователь</th>
            <th>Тип</th>
            <th>Макс. коэффициент</th>
            <th>Статус</th>
            <th>Искать с</th>
            <th>Искать по</th>
            <th>Склады</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($requests as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->user->name ?? '-' }}</td>
                <td>{{ \App\Models\SuitableCoefficient::getBoxTypeRussianNameById($r->box_type_id)}}</td>
                <td>{{ $r->max_coefficient }}</td>
                <td>
                    @if($r->status)
                        <span class="text-green-600 font-bold">Вкл</span>
                    @else
                        <span class="text-red-600 font-bold">Выкл</span>
                    @endif
                </td>
                <td>{{ optional($r->date_from)->format('d.m.Y') ?? '-' }}</td>
                <td>{{ optional($r->date_to)->format('d.m.Y') ?? '-' }}</td>
                <td>
                    @if($r->warehouse_names)
                        {{ implode(', ', $r->warehouse_names) }}
                    @else
                        -
                    @endif
                </td>
                <td><a href="{{ route('search-requests.edit', $r) }}" class="button-primary">Редактировать</a></td>
            </tr>
        @empty
            <tr><td colspan="8">Нет данных</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem;">
        {{ $requests->links() }}
    </div>
@endsection
