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
        class="list-group-item list-group-item-action active">
        Загальна Інформація
    </a>
    <a href="{{ route('entrepreneurs.financial-data', $entrepreneur) }}" 
        class="list-group-item list-group-item-action">
        Книга обліку доходів
    </a>
    <a href="{{ route('entrepreneurs.tax-payments', $entrepreneur) }}" class="list-group-item list-group-item-action">
    Податкові платежі
    </a>
</div>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $entrepreneur->name }}</h1>
    <div>
        <a href="{{ route('entrepreneurs.edit', $entrepreneur) }}" class="btn btn-primary me-2">Редагувати</a>
        <form action="{{ route('entrepreneurs.destroy', $entrepreneur) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"
                onclick="return confirm('Ви впевнені, що хочете видалити цього підприємця?')">
                Видалити
            </button>
        </form>
    </div>
</div>

@if($settings->count() > 0)
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Інформація про податок</h5>
        <dl class="row">
            @foreach($settings as $setting)
                @php
                    $showSetting = false;
                    switch($entrepreneur->group) {
                        case '1':
                            $showSetting = in_array($setting->id, [4, 5, 2]);
                            break;
                        case '2':
                            $showSetting = in_array($setting->id, [1, 2, 5]);
                            break;
                        case '3':
                            $showSetting = in_array($setting->id, [6, 3]);
                            break;
                    }

                    // Define which settings should show грн or %
                    $suffix = '';
                    if(in_array($setting->id, [1, 2, 4, 5])) {
                        $suffix = ' грн';
                    } elseif(in_array($setting->id, [3, 6])) {
                        $suffix = ' %';
                    }
                @endphp
                
                @if($showSetting)
                    <dt class="col-sm-3">{{ $setting->name }}</dt>
                    <dd class="col-sm-9">{{ $setting->value }}{{ $suffix }}</dd>
                @endif
            @endforeach
        </dl>
    </div>
</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Інформація</h5>
        <dl class="row">
            <dt class="col-sm-3">ІПН</dt>
            <dd class="col-sm-9">{{ $entrepreneur->ipn }}</dd>

            <dt class="col-sm-3">IBAN</dt>
            <dd class="col-sm-9">{{ $entrepreneur->iban }}</dd>

            <dt class="col-sm-3">Податкова</dt>
            <dd class="col-sm-9">{{ $entrepreneur->tax_office_name }}</dd>

            <dt class="col-sm-3">Група</dt>
            <dd class="col-sm-9">{{ $entrepreneur->group }}</dd>
        </dl>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Ключі</h5>
        <dl class="row">
            @php
            $privateKey = $entrepreneur->keys->where('type', 'private')->first();
            $ascKey = $entrepreneur->keys->where('type', 'asc')->first();
            $oneMonthFromNow = \Carbon\Carbon::now()->addMonth();
            
            function formatKeyInfo($key) {
                if (!$key) return 'Немає даних';
                
                $now = \Carbon\Carbon::now();
                $dateStart = $key->date_start->format('d.m.Y');
                $dateEnd = $key->date_end->format('d.m.Y');
                
                if ($now > $key->date_end) {
                    return "{$dateStart} - {$dateEnd} (Ключ прострочено)";
                }
                
                $daysLeft = round($now->diffInDays($key->date_end));
                return "{$dateStart} - {$dateEnd} ({$daysLeft} днів залишилось)";
            }
            @endphp

            <dt class="col-sm-3">Ключ Приват банк:</dt>
            <dd class="col-sm-9">
                <span class="{{ $privateKey && $privateKey->date_end > $oneMonthFromNow ? 'text-success' : 'text-danger' }}">
                    {{ formatKeyInfo($privateKey) }}
                </span>
            </dd>

            <dt class="col-sm-3">Ключ АСЦК:</dt>
            <dd class="col-sm-9">
                <span class="{{ $ascKey && $ascKey->date_end > $oneMonthFromNow ? 'text-success' : 'text-danger' }}">
                    {{ formatKeyInfo($ascKey) }}
                </span>
            </dd>
        </dl>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Кведи:</h5>
        @if($entrepreneur->kveds->count() > 0)
        <ul class="list-group list-group-flush">
            @foreach($entrepreneur->kveds as $kved)
            <li class="list-group-item">
                {{ $kved->number }} - {{ $kved->name }}
            </li>
            @endforeach
        </ul>
        @else
        <p class="text-muted">Не вибарно кведи</p>
        @endif
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Звітність</h5>
            <div class="d-flex align-items-center">
                <label for="reportYear" class="me-2">Рік:</label>
                <select id="reportYear" class="form-select" style="width: auto;">
                    @for($year = 2020; $year <= date('Y'); $year++)
                        <option value="{{ $year }}" {{ $year == request('year', date('Y')) ? 'selected' : '' }}>
                        {{ $year }}
                        </option>
                        @endfor
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Звіт</th>
                        <th>1 квартал</th>
                        <th>2 квартал</th>
                        <th>3 квартал</th>
                        <th>4 квартал</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                    <tr>
                        <td>{{ $report->name }}</td>
                        @for($quarter = 1; $quarter <= 4; $quarter++)
                            @php
                            $reportStatus=$entrepreneur->reportEntrepreneurs()
                            ->where('id_report', $report->id_report)
                            ->where('quarter', $quarter)
                            ->where('year', request('year', date('Y')))
                            ->first();
                            @endphp
                            <td>
                                <div class="form-check">
                                    @php
                                    $reportStatus = $entrepreneur->reportEntrepreneurs()
                                    ->where('id_report', $report->id_report)
                                    ->where('quarter', $quarter)
                                    ->where('year', request('year', date('Y')))
                                    ->first();

                                    // Debug output
                                    \Log::info('Checkbox data:', [
                                    'entrepreneur_id' => $entrepreneur->id_entrepreneurs,
                                    'report_id' => $report->id_report,
                                    'quarter' => $quarter,
                                    'year' => request('year', date('Y')),
                                    'status' => $reportStatus ? 'found' : 'not found'
                                    ]);
                                    @endphp
                                    <input type="checkbox"
                                        class="form-check-input report-status"
                                        data-entrepreneur="{{ $entrepreneur->id_entrepreneurs }}"
                                        data-report="{{ $report->id_report }}"
                                        data-quarter="{{ $quarter }}"
                                        data-year="{{ request('year', date('Y')) }}"
                                        {{ $reportStatus && $reportStatus->done ? 'checked' : '' }}
                                        style="cursor: pointer;">
                                </div>
                            </td>
                            @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@push('scripts')
<script type="text/javascript">
    console.log('Script starting to load');
    
    $(function() {
        console.log('jQuery DOM ready fired');
        console.log('Checkbox elements found:', $('.report-status').length);
        console.log('Year selector found:', $('#reportYear').length);
        
        $(document).on('change', '.report-status', function() {
            console.log('Checkbox clicked');
            
            const checkbox = $(this);
            const originalState = checkbox.prop('checked');
            
            checkbox.prop('disabled', true);
            
            // Convert checkbox state to number (0 or 1) for proper boolean handling
            const done = checkbox.is(':checked') ? 1 : 0;
            console.log('Checkbox state:', done);
            
            const data = {
                _token: '{{ csrf_token() }}',
                entrepreneur_id: parseInt(checkbox.data('entrepreneur')),
                report_id: parseInt(checkbox.data('report')),
                quarter: parseInt(checkbox.data('quarter')),
                year: parseInt(checkbox.data('year')),
                done: done // sending as 1 or 0
            };
            
            console.log('Sending data:', data);
            
            $.ajax({
                url: '{{ route("entrepreneurs.update-report") }}',
                method: 'POST',
                data: data,
                dataType: 'json',
                contentType: 'application/x-www-form-urlencoded',
                success: function(response) {
                    console.log('Success response:', response);
                    checkbox.prop('disabled', false);
                    if (!response.success) {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        alert('Помилка оновлення статусу звіту: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    checkbox.prop('disabled', false);
                    checkbox.prop('checked', originalState);
                    alert('Помилка оновлення статусу звіту. Будь ласка, спробуйте знову.');
                }
            });
        });
        
        $('#reportYear').on('change', function() {
            window.location.href = '{{ route("entrepreneurs.show", $entrepreneur) }}?year=' + $(this).val();
        });
    });
</script>
@endpush
@endsection