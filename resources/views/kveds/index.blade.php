@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>КВЕДи</h1>
        <a href="{{ route('kveds.create') }}" class="btn btn-primary">Додати новий КВЕД</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Номер</th>
                    <th>Назва</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kveds as $kved)
                    <tr>
                        <td>{{ $kved->number }}</td>
                        <td>{{ $kved->name }}</td>
                        <td>
                            <a href="{{ route('kveds.edit', $kved) }}" class="btn btn-sm btn-primary">Редагувати</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $kveds->links() }}
@endsection