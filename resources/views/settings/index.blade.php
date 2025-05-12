@extends('layouts.app')
@section('sidebar')
@foreach($entrepreneurs as $e)
<div class="mb-2">
    <a href="{{ route('entrepreneurs.show', $e) }}"
        class="text-decoration-none">
        {{ $e->name }}
    </a>
</div>
@endforeach
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h2>Settings</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settings as $setting)
                    <tr>
                        <td>{{ $setting->name }}</td>
                        <td>
                            <form action="{{ route('settings.update', $setting) }}" method="POST" class="d-flex">
                                @csrf
                                @method('PUT')
                                <input type="text" name="value" value="{{ $setting->value }}" class="form-control me-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection