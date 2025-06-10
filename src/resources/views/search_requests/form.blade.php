@extends('layouts.app')

@section('title', $requestModel->exists ? 'Редактировать запрос' : 'Добавить запрос')

@section('content')
    <h1>@yield('title')</h1>

    <form method="POST" action="{{ $requestModel->exists ? route('search-requests.update', $requestModel) : route('search-requests.store') }}">
        @csrf
        @if ($requestModel->exists)
            @method('PUT')
        @endif

        <div>
            <label>Тип поставки:</label>
            <select name="box_type_id" required>
                @foreach ([2,5,6] as $type)
                    <option value="{{ $type }}" {{ old('box_type_id', $requestModel->box_type_id ?? '') == $type ? 'selected' : '' }}>{{ \App\Models\SuitableCoefficient::getBoxTypeRussianNameById($type) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Макс. коэффициент:</label>
            <input type="number" name="max_coefficient" value="{{ old('max_coefficient', $requestModel->max_coefficient) }}" required>
        </div>

        <div>
            <label>Статус (включено?):</label>
            <select name="status">
                <option value="0" {{ old('status', $requestModel->status ?? 0 ) == 0 ? 'selected' : '' }} >Отключено</option>
                <option value="1" {{ old('status', $requestModel->status ?? 1 ) == 1 ? 'selected' : '' }}>Включено</option>
            </select>
        </div>

        <button type="submit" class="button-primary">Сохранить</button>
    </form>
@endsection
