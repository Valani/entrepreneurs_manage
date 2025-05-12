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
        class="list-group-item list-group-item-action">
        Книга обліку доходів
    </a>
    <a href="{{ route('entrepreneurs.tax-payments', $entrepreneur) }}"
        class="list-group-item list-group-item-action active">
        Податкові платежі
    </a>
</div>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <h2 class="mb-0">Tax Payments - {{ $entrepreneur->name }}</h2>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-primary px-4" id="addPayment" style="white-space: nowrap;">
                    <i class="fas fa-plus me-2"></i>
                    <span>Створити</span>
                </button>
                <label for="year" class="form-label mb-0">Year:</label>
                <select class="form-select" id="year" style="width: 120px;">
                    @foreach(range(now()->year - 5, now()->year) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="accordion" id="paymentsAccordion">
            @foreach($payments as $month => $monthPayments)
            @php
            $monthName = \Carbon\Carbon::createFromFormat('m', $month)->format('F');
            $isCurrentMonth = $month == now()->format('m') && $year == now()->year;
            @endphp
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $month }}">
                    <button class="accordion-button {{ !$isCurrentMonth ? 'collapsed' : '' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#month{{ $month }}"
                        aria-expanded="{{ $isCurrentMonth ? 'true' : 'false' }}"
                        aria-controls="month{{ $month }}">
                        <div class="d-flex justify-content-between w-100 me-3">
                            <span>{{ $monthName }}</span>
                            <span class="badge bg-primary">{{ number_format($monthlyTotals[$month], 2) }} UAH</span>
                        </div>
                    </button>
                </h2>
                <div id="month{{ $month }}"
                    class="accordion-collapse collapse {{ $isCurrentMonth ? 'show' : '' }}"
                    data-bs-parent="#paymentsAccordion"
                    aria-labelledby="heading{{ $month }}">
                    <div class="accordion-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 15%">Дата</th>
                                        <th style="width: 20%">Сума</th>
                                        <th style="width: 55%">Опис</th>
                                        <th style="width: 10%" class="text-end">Видалити</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthPayments as $payment)
                                    <tr data-payment-id="{{ $payment->id }}">
                                        <td class="editable" data-field="date">
                                            <span class="display-value">{{ $payment->date->format('Y-m-d') }}</span>
                                            <input type="date" class="form-control edit-input d-none"
                                                value="{{ $payment->date->format('Y-m-d') }}">
                                        </td>
                                        <td class="editable" data-field="amount">
                                            <span class="display-value">{{ number_format($payment->amount, 2) }} UAH</span>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control edit-input d-none"
                                                value="{{ number_format($payment->amount, 2, '.', '') }}">
                                        </td>
                                        <td class="editable" data-field="description">
                                            <span class="display-value">{{ $payment->description }}</span>
                                            <input type="text" class="form-control edit-input d-none"
                                                value="{{ $payment->description }}">
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-outline-danger delete-payment">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: inherit;
        box-shadow: none;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0, 0, 0, 0.125);
    }

    .editable {
        padding: 1rem 1.25rem !important;
    }

    .editable:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .table> :not(caption)>*>* {
        padding: 1rem 1.25rem;
        vertical-align: middle;
    }

    .form-control,
    .form-select {
        border-radius: 4px;
    }

    .btn-primary {
        border-radius: 4px;
    }

    .edit-input {
        padding: 0.5rem 0.75rem;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        // Year selection change
        $('#year').change(function() {
            window.location.href = `{{ route("entrepreneurs.tax-payments", $entrepreneur) }}?year=${$(this).val()}`;
        });

        // Search functionality with debouncing
        let searchTimer;
        $('#search').on('keyup', function() {
            clearTimeout(searchTimer);
            const query = $(this).val();

            searchTimer = setTimeout(function() {
                window.location.href = `{{ route("entrepreneurs.tax-payments", $entrepreneur) }}?year={{ $year }}&search=${query}`;
            }, 500);
        });

        // Update the tax payment JavaScript code in tax-payments.blade.php
        $('#addPayment').click(function() {
            const today = new Date().toISOString().split('T')[0];

            // Create form data
            const formData = {
                _token: '{{ csrf_token() }}',
                date: today,
                amount: 0, // Set default amount
                description: '' // Set empty description
            };

            $.ajax({
                url: '{{ route("entrepreneurs.tax-payments.store", $entrepreneur) }}',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Reload the page to show the new payment
                        location.reload();
                    } else {
                        alert('Failed to create payment: ' + response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Handle validation errors
                        const errors = xhr.responseJSON.errors;
                        let errorMessage = 'Validation failed:\n';
                        Object.keys(errors).forEach(key => {
                            errorMessage += `${errors[key].join('\n')}\n`;
                        });
                        alert(errorMessage);
                    } else {
                        alert('Failed to create payment. Please try again.');
                    }
                }
            });
        });

        $('.editable').click(function() {
            const td = $(this);
            const displayValue = td.find('.display-value');
            const input = td.find('.edit-input');

            displayValue.addClass('d-none');
            input.removeClass('d-none').focus();

            // Only clear amount if it's zero
            if (td.data('field') === 'amount') {
                const currentAmount = parseFloat(input.val());
                if (currentAmount === 0) {
                    input.val('');
                }
            }
        });

        $('.edit-input').blur(function() {
            const input = $(this);
            const td = input.closest('td');
            const displayValue = td.find('.display-value');
            const paymentId = td.closest('tr').data('payment-id');
            const field = td.data('field');
            let value = input.val();

            // If amount is empty, set it to 0
            if (field === 'amount' && value === '') {
                value = '0';
            }

            $.ajax({
                url: `/tax-payments/${paymentId}`,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    [field]: value
                },
                success: function(response) {
                    if (response.success) {
                        if (field === 'amount') {
                            displayValue.text(response.formatted_amount + ' UAH');
                        } else if (field === 'date') {
                            displayValue.text(response.formatted_date);
                        } else {
                            displayValue.text(value);
                        }

                        input.addClass('d-none');
                        displayValue.removeClass('d-none');

                        if (field === 'date') {
                            location.reload();
                        }
                    }
                },
                error: function(xhr) {
                    alert('Failed to update. Please try again.');
                    input.val(displayValue.text());
                    input.addClass('d-none');
                    displayValue.removeClass('d-none');
                }
            });
        });

        // Delete payment
        $('.delete-payment').click(function() {
            if (!confirm('Are you sure you want to delete this payment?')) {
                return;
            }

            const tr = $(this).closest('tr');
            const paymentId = tr.data('payment-id');

            $.ajax({
                url: '/tax-payments/' + paymentId,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        tr.fadeOut(function() {
                            tr.remove();
                            location.reload();
                        });
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection