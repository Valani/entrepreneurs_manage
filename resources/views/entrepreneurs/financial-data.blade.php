@extends('layouts.app')


@section('sidebar')
@foreach($entrepreneurs as $e)
    <a href="{{ route('entrepreneurs.show', $e) }}"
       class="entrepreneur-item {{ $entrepreneur->id_entrepreneurs === $e->id_entrepreneurs ? 'active' : '' }}">
        <i class="fas fa-user-tie"></i>
        {{ $e->name }}
    </a>
@endforeach
@endsection

@section('secondary-nav')
<div class="list-group">
    <a href="{{ route('entrepreneurs.show', $entrepreneur) }}"
        class="list-group-item list-group-item-action">
        Загальна інформація
    </a>
    <a href="{{ route('entrepreneurs.financial-data', $entrepreneur) }}"
        class="list-group-item list-group-item-action active">
        Книга обліку доходів
    </a>
    <a href="{{ route('entrepreneurs.tax-payments', $entrepreneur) }}" class="list-group-item list-group-item-action">
    Податкові платежі
    </a>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Облік доходів - {{ $entrepreneur->name }}</h2>
            <div class="btn-group">
                <label class="btn btn-secondary" for="importFile">
                    Імпорт
                    <input type="file" id="importFile" class="d-none" accept=".csv,.xlsx">
                </label>
                <a href="{{ route('entrepreneurs.financial-data.export', [
                    'entrepreneur' => $entrepreneur->id_entrepreneurs,
                    'year' => $year,
                    'month' => $month
                ]) }}" class="btn btn-secondary">Експорт</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <div class="btn-group">
                <a href="?view_mode=month" class="btn btn-{{ $viewMode === 'month' ? 'primary' : 'outline-primary' }}">Місяць</a>
                <a href="?view_mode=quarter" class="btn btn-{{ $viewMode === 'quarter' ? 'primary' : 'outline-primary' }}">Квартал</a>
                <a href="?view_mode=year" class="btn btn-{{ $viewMode === 'year' ? 'primary' : 'outline-primary' }}">Рік</a>
            </div>

            <div class="d-inline-block ms-3">
                @if($viewMode === 'month')
                <select class="form-select d-inline-block w-auto" id="month">
                    @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ Carbon\Carbon::create(null, $m, 1)->format('F') }}
                    </option>
                    @endforeach
                </select>
                @elseif($viewMode === 'quarter')
                <select class="form-select d-inline-block w-auto" id="quarter">
                    @foreach(range(1, 4) as $q)
                    <option value="{{ $q }}" {{ $quarter == $q ? 'selected' : '' }}>
                        Квартал {{ $q }}
                    </option>
                    @endforeach
                </select>
                @endif

                <select class="form-select d-inline-block w-auto" id="year">
                    @foreach(range(now()->year - 5, now()->year) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Готівка</th>
                        <th>Без готівка</th>
                        <th>Загалом</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totalCash = 0;
                    $totalNonCash = 0;
                    @endphp

                    @if($viewMode === 'month')
                    @foreach($data as $day => $record)
                    @php
                    $totalCash += $record['cash'];
                    $totalNonCash += $record['non_cash'];
                    $isToday = $record['date']->isToday();
                    @endphp
                    <tr class="{{ $isToday ? 'table-primary' : '' }}">
                        <td>{{ $record['date']->format('d.m.Y') }}</td>
                        <td>
                            <input type="number"
                                class="form-control form-control-sm financial-input"
                                data-type="cash"
                                data-date="{{ $record['date']->format('Y-m-d') }}"
                                value="{{ number_format($record['cash'], 2, '.', '') }}"
                                step="0.01">
                        </td>
                        <td>
                            <input type="number"
                                class="form-control form-control-sm financial-input"
                                data-type="non_cash"
                                data-date="{{ $record['date']->format('Y-m-d') }}"
                                value="{{ number_format($record['non_cash'], 2, '.', '') }}"
                                step="0.01">
                        </td>
                        <td>{{ number_format($record['cash'] + $record['non_cash'], 2) }}</td>
                    </tr>
                    @endforeach
                    @else
                    @foreach($data as $record)
                    @php
                    $totalCash += $record->total_cash;
                    $totalNonCash += $record->total_non_cash;
                    @endphp
                    <tr>
                        <td>{{ $viewMode === 'year' ? Carbon\Carbon::create(null, $record->month, 1)->format('F') : $record->month }}</td>
                        <td>{{ number_format($record->total_cash, 2) }}</td>
                        <td>{{ number_format($record->total_non_cash, 2) }}</td>
                        <td>{{ number_format($record->total_cash + $record->total_non_cash, 2) }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr class="table-secondary fw-bold">
                        <td>Total</td>
                        <td>{{ number_format($totalCash, 2) }}</td>
                        <td>{{ number_format($totalNonCash, 2) }}</td>
                        <td>{{ number_format($totalCash + $totalNonCash, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


@push('scripts')
<script>
    $(document).ready(function() {
        let updateTimer;

        // Handle financial data input changes
        $('.financial-input').on('change', function() {
            clearTimeout(updateTimer);
            const input = $(this);
            const date = input.data('date');
            const cash = $(`input[data-type="cash"][data-date="${date}"]`).val() || 0;
            const nonCash = $(`input[data-type="non_cash"][data-date="${date}"]`).val() || 0;

            // Update total for the row
            const row = input.closest('tr');
            const totalCell = row.find('td:last');
            totalCell.text((parseFloat(cash) + parseFloat(nonCash)).toFixed(2));

            updateTimer = setTimeout(function() {
                $.ajax({
                    url: '{{ route("entrepreneurs.financial-data.update", $entrepreneur) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        date: date,
                        cash: cash,
                        non_cash: nonCash
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update totals
                            updateTotals();
                        } else {
                            alert('Failed to update record');
                        }
                    },
                    error: function() {
                        alert('Failed to update record');
                    }
                });
            }, 500);
        });

        // Function to update totals
        function updateTotals() {
            let totalCash = 0;
            let totalNonCash = 0;

            $('.financial-input[data-type="cash"]').each(function() {
                totalCash += parseFloat($(this).val()) || 0;
            });

            $('.financial-input[data-type="non_cash"]').each(function() {
                totalNonCash += parseFloat($(this).val()) || 0;
            });

            // Update footer totals
            $('tfoot td:nth-child(2)').text(totalCash.toFixed(2));
            $('tfoot td:nth-child(3)').text(totalNonCash.toFixed(2));
            $('tfoot td:nth-child(4)').text((totalCash + totalNonCash).toFixed(2));
        }

        // Handle period selection changes
        $('#month, #quarter, #year').on('change', function() {
            const viewMode = '{{ $viewMode }}';
            const year = $('#year').val();
            let url = '{{ route("entrepreneurs.financial-data", $entrepreneur) }}?view_mode=' + viewMode + '&year=' + year;

            if (viewMode === 'month') {
                url += '&month=' + $('#month').val();
            } else if (viewMode === 'quarter') {
                url += '&quarter=' + $('#quarter').val();
            }

            window.location.href = url;
        });
    });
</script>
<script>
$(document).ready(function() {
    // File import handling
    $('#importFile').change(function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: '{{ route("entrepreneurs.financial-data.import", $entrepreneur) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Import successful');
                    location.reload();
                } else {
                    alert('Import failed: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Import failed. Please check your file format.');
            }
        });
    });

    // Clear zero values on focus
    $('.financial-input').focus(function() {
        if ($(this).val() == '0.00') {
            $(this).val('');
        }
    });

    // Update year selector
    function updateYearSelector() {
        const currentYear = new Date().getFullYear();
        const $yearSelect = $('#year');
        const lastYear = parseInt($yearSelect.find('option:last').val());
        
        if (currentYear > lastYear) {
            $yearSelect.append(
                $('<option>', {
                    value: currentYear,
                    text: currentYear
                })
            );
        }
    }

    // Check for new year on page load
    updateYearSelector();
});
</script>
@endpush
@endsection