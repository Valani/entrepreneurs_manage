@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>KVEDs</h1>
        <a href="{{ route('kveds.create') }}" class="btn btn-primary">Add New KVED</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kveds as $kved)
                    <tr>
                        <td>{{ $kved->number }}</td>
                        <td>{{ $kved->name }}</td>
                        <td>
                            <a href="{{ route('kveds.edit', $kved) }}" class="btn btn-sm btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $kveds->links() }}
@endsection