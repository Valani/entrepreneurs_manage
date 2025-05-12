@extends('layouts.app')

@section('content')
    <h1>Створити новий КВЕД</h1>

    <form action="{{ route('kveds.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="number" class="form-label">Номер</label>
            <input type="text" class="form-control @error('number') is-invalid @enderror"
                   id="number" name="number" value="{{ old('number') }}" required>
            @error('number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Назва</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Створити КВЕД</button>
    </form>
@endsection