<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управління підприємцями</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container-fluid p-0">
        <!-- Primary Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h5 class="mb-3">Управління підприємцями</h5>
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search" class="form-control" placeholder="Пошук підприємців...">
                </div>
                <div>
                    <a href="{{ route('entrepreneurs.keys-overview') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-key me-1"></i>Огляд
                    </a>
                </div>
            </div>

            <div class="scroll-area">
                <div id="entrepreneurs-list">
                    @yield('sidebar')
                </div>
            </div>

            <div class="action-buttons">
                <a href="{{ route('entrepreneurs.create') }}" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-plus me-2"></i>Додати
                </a>
                <a href="{{ route('settings.index') }}" class="btn btn-light w-100">
                    <i class="fas fa-cog me-2"></i>Налаштування
                </a>
            </div>
        </div>

        <!-- Secondary Navigation -->
        <div class="secondary-nav">
            @yield('secondary-nav')
        </div>

        <!-- Main Content -->
        <div class="main-content">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @yield('content')
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let searchTimer;

            // Improved search functionality with debouncing


            $('#search').on('keyup', function() {
                clearTimeout(searchTimer);
                const query = $(this).val();

                searchTimer = setTimeout(function() {
                    $.ajax({
                        url: '{{ route("entrepreneurs.search") }}',
                        method: 'GET',
                        data: {
                            query: query
                        },
                        success: function(data) {
                            let html = '';
                            data.forEach(function(entrepreneur) {
                                html += `
                        <a href="/entrepreneurs/${entrepreneur.id_entrepreneurs}"
                           class="entrepreneur-item">
                            <i class="fas fa-user-tie"></i>
                            ${entrepreneur.name}
                        </a>
                    `;
                            });
                            $('#entrepreneurs-list').html(html);
                        },
                        error: function(xhr) {
                            console.error('Помилка пошуку:', xhr);
                            $('#entrepreneurs-list').html('<div class="text-muted">Помилка завантаження результатів</div>');
                        }
                    });
                }, 300);
            });

            // Responsive sidebar toggles
            $('.sidebar-toggle').click(function() {
                $('.sidebar').toggleClass('active');
            });

            // Handle responsive behavior
            function handleResponsive() {
                if (window.innerWidth <= 768) {
                    $('.sidebar').removeClass('active');
                    $('.secondary-nav').removeClass('active');
                }
            }

            window.addEventListener('resize', handleResponsive);
        });
    </script>
    @stack('scripts')
</body>

</html>