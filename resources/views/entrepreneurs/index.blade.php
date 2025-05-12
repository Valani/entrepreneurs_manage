@extends('layouts.app')

@section('sidebar')
    @foreach($entrepreneurs as $entrepreneur)
        <div class="mb-2">
            <a href="{{ route('entrepreneurs.show', $entrepreneur) }}" class="text-decoration-none">
                {{ $entrepreneur->name }}
            </a>
        </div>
    @endforeach
    
    {{ $entrepreneurs->links() }}
@endsection

@section('content')
    <h1>Select an entrepreneur from the sidebar to view details</h1>
@endsection