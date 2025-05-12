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
    <h1>Виберіть підприємця зі списку, щоб переглянути деталі</h1>
@endsection