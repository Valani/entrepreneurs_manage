@extends('layouts.app')
@section('sidebar')
@foreach($entrepreneurs as $e)
<a href="{{ route('entrepreneurs.show', $e) }}"
    class="entrepreneur-item">
    <i class="fas fa-user-tie"></i>
    {{ $e->name }}
</a>
@endforeach
@endsection
@section('content')
<h1>Overview</h1>
<style>
    .secondary-nav {
        display: none;
    }
</style>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Quarter {{ $currentQuarter }} ({{ $currentYear }}) Reports Overview</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Entrepreneur</th>
                        @foreach($reports as $report)
                        <th>{{ $report->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($entrepreneurs as $entrepreneur)
                    <tr>
                        <td>
                            <a href="{{ route('entrepreneurs.show', $entrepreneur) }}">
                                {{ $entrepreneur->name }}
                            </a>
                        </td>
                        @foreach($reports as $report)
                        <td>
                            <div class="form-check">
                                <input type="checkbox"
                                    class="form-check-input report-status"
                                    data-entrepreneur="{{ $entrepreneur->id_entrepreneurs }}"
                                    data-report="{{ $report->id_report }}"
                                    data-quarter="{{ $currentQuarter }}"
                                    data-year="{{ $currentYear }}"
                                    {{ isset($reportStatuses[$entrepreneur->id_entrepreneurs][$report->id_report]) && 
                                               $reportStatuses[$entrepreneur->id_entrepreneurs][$report->id_report]->first()->done ? 'checked' : '' }}
                                    style="cursor: pointer;">
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4 mt-4">
    <div class="card-header">
        <h5 class="mb-0">Upcoming Key Expirations</h5>
    </div>
    <div class="card-body">
        @if($expiringKeys->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Entrepreneur</th>
                        <th>Key Type</th>
                        <th>Expiration Date</th>
                        <th>Days Left</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringKeys as $key)
                    <tr>
                        <td>
                            <a href="{{ route('entrepreneurs.show', $key->entrepreneur) }}">
                                {{ $key->entrepreneur->name }}
                            </a>
                        </td>
                        <td>{{ ucfirst($key->type) }} Key</td>
                        <td>{{ $key->date_end->format('Y-m-d') }}</td>
                        <td>
                            @php
                            $daysLeft = floor(now()->diffInDays($key->date_end, false));
                            $textClass = $daysLeft <= 30 ? 'text-danger' : ($daysLeft <=90 ? 'text-warning' : 'text-success' );
                                @endphp
                                <span class="{{ $textClass }}">
                                {{ $daysLeft }} days
                                </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted">No upcoming key expirations found.</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.report-status').change(function() {
            const checkbox = $(this);
            const originalState = checkbox.prop('checked');

            $.ajax({
                url: '{{ route("entrepreneurs.update-report") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    entrepreneur_id: checkbox.data('entrepreneur'),
                    report_id: checkbox.data('report'),
                    quarter: checkbox.data('quarter'),
                    year: checkbox.data('year'),
                    done: checkbox.is(':checked')
                },
                success: function(response) {
                    if (!response.success) {
                        checkbox.prop('checked', !originalState);
                        alert('Failed to update report status');
                    }
                },
                error: function() {
                    checkbox.prop('checked', !originalState);
                    alert('Failed to update report status');
                }
            });
        });
    });
</script>
@endpush
@endsection