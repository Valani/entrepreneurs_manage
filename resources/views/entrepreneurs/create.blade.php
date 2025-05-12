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
<h1>Create New Entrepreneur</h1>

<form action="{{ route('entrepreneurs.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror"
            id="name" name="name" value="{{ old('name') }}" required>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="ipn" class="form-label">IPN</label>
        <input type="text" class="form-control @error('ipn') is-invalid @enderror"
            id="ipn" name="ipn" value="{{ old('ipn') }}" required>
        @error('ipn')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="iban" class="form-label">IBAN</label>
        <input type="text" class="form-control @error('iban') is-invalid @enderror"
            id="iban" name="iban" value="{{ old('iban') }}" required>
        @error('iban')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="tax_office_name" class="form-label">Tax Office Name</label>
        <input type="text" class="form-control @error('tax_office_name') is-invalid @enderror"
            id="tax_office_name" name="tax_office_name" value="{{ old('tax_office_name') }}" required>
        @error('tax_office_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="group" class="form-label">Group</label>
        <select class="form-control @error('group') is-invalid @enderror"
            id="group" name="group" required>
            <option value="">Select Group</option>
            <option value="1" {{ (old('group', $entrepreneur->group ?? '') == '1') ? 'selected' : '' }}>Group 1</option>
            <option value="2" {{ (old('group', $entrepreneur->group ?? '') == '2') ? 'selected' : '' }}>Group 2</option>
            <option value="3" {{ (old('group', $entrepreneur->group ?? '') == '3') ? 'selected' : '' }}>Group 3</option>
        </select>
        @error('group')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">KVEDs</label>
        <div class="mb-3">
            <input type="text"
                class="form-control"
                id="kvedSearch"
                placeholder="Search KVEDs by number or name...">
        </div>

        <!-- Hidden container for maintaining selected KVEDs -->
        <div id="selectedKvedsContainer">
            @if(isset($entrepreneur))
            @foreach($entrepreneur->kveds as $kved)
            <input type="hidden"
                name="kveds[]"
                value="{{ $kved->id_kved }}"
                class="hidden-kved"
                data-kved-id="{{ $kved->id_kved }}">
            @endforeach
            @endif
        </div>

        <div id="kvedList" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
            @foreach($kveds as $kved)
            <div class="form-check">
                <input class="form-check-input kved-checkbox"
                    type="checkbox"
                    value="{{ $kved->id_kved }}"
                    id="kved{{ $kved->id_kved }}"
                    data-kved-id="{{ $kved->id_kved }}"
                    {{ isset($entrepreneur) && $entrepreneur->kveds->contains($kved->id_kved) ? 'checked' : '' }}>
                <label class="form-check-label" for="kved{{ $kved->id_kved }}">
                    {{ $kved->number }} - {{ $kved->name }}
                </label>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Keys Information</h5>

            <div class="mb-3">
                <h6>Private Key</h6>
                <div class="row">
                    <div class="col-md-6">
                        <label for="private_key_start" class="form-label">Start Date</label>
                        <input type="date" class="form-control @error('private_key_start') is-invalid @enderror"
                            id="private_key_start" name="private_key_start" value="{{ old('private_key_start') }}">
                        @error('private_key_start')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="private_key_end" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('private_key_end') is-invalid @enderror"
                            id="private_key_end" name="private_key_end" value="{{ old('private_key_end') }}">
                        @error('private_key_end')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <h6>ASC Key</h6>
                <div class="row">
                    <div class="col-md-6">
                        <label for="asc_key_start" class="form-label">Start Date</label>
                        <input type="date" class="form-control @error('asc_key_start') is-invalid @enderror"
                            id="asc_key_start" name="asc_key_start" value="{{ old('asc_key_start') }}">
                        @error('asc_key_start')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="asc_key_end" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('asc_key_end') is-invalid @enderror"
                            id="asc_key_end" name="asc_key_end" value="{{ old('asc_key_end') }}">
                        @error('asc_key_end')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary me-2">Create Entrepreneur</button>
        <a href="{{ route('entrepreneurs.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        let searchTimer;

        // Function to get currently selected KVED IDs from hidden inputs
        function getSelectedKvedIds() {
            return $('.hidden-kved').map(function() {
                return $(this).val();
            }).get();
        }

        // Function to render KVED list
        function renderKvedList(data) {
            let html = '';
            data.kveds.forEach(function(kved) {
                const isChecked = $('.hidden-kved[data-kved-id="' + kved.id_kved + '"]').length > 0 ? 'checked' : '';
                html += `
                <div class="form-check">
                    <input class="form-check-input kved-checkbox" 
                           type="checkbox" 
                           value="${kved.id_kved}" 
                           id="kved${kved.id_kved}"
                           data-kved-id="${kved.id_kved}"
                           ${isChecked}>
                    <label class="form-check-label" for="kved${kved.id_kved}">
                        ${kved.number} - ${kved.name}
                    </label>
                </div>
            `;
            });
            return html;
        }

        $('#kvedSearch').on('keyup', function() {
            clearTimeout(searchTimer);
            const query = $(this).val();
            const selectedKveds = getSelectedKvedIds();

            searchTimer = setTimeout(function() {
                $.ajax({
                    url: '/search/kveds',
                    method: 'GET',
                    data: {
                        query: query,
                        selected_kveds: selectedKveds
                    },
                    success: function(response) {
                        const html = renderKvedList(response);
                        $('#kvedList').html(html);
                    },
                    error: function() {
                        $('#kvedList').html('<div class="text-danger">Error loading KVEDs</div>');
                    }
                });
            }, 300);
        });

        // Handle checkbox changes
        $(document).on('change', '.kved-checkbox', function() {
            const kvedId = $(this).data('kved-id');

            if (this.checked) {
                // Add hidden input if it doesn't exist
                if ($('.hidden-kved[data-kved-id="' + kvedId + '"]').length === 0) {
                    $('#selectedKvedsContainer').append(
                        `<input type="hidden" name="kveds[]" value="${kvedId}" 
                     class="hidden-kved" data-kved-id="${kvedId}">`
                    );
                }
            } else {
                // Remove hidden input
                $('.hidden-kved[data-kved-id="' + kvedId + '"]').remove();
            }
        });
    });
</script>
@endpush