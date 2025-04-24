<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Compound Interest Calculator') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 1rem;
        }
        .comparison-table th {
            background-color: #f8f9fa;
        }
        .input-group-text {
            min-width: 120px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Compound Interest Calculator') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('compound-interest.create') }}">New Calculation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('compound-interest.index') }}">History</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Real-time calculation and validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (form) {
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.addEventListener('input', validateInput);
                });
            }
        });

        function validateInput(e) {
            const input = e.target;
            const value = parseFloat(input.value);
            const feedback = input.nextElementSibling;
            
            if (input.hasAttribute('required') && !value) {
                input.classList.add('is-invalid');
                if (feedback) feedback.textContent = 'This field is required';
                return;
            }

            if (input.type === 'number') {
                if (value < 0) {
                    input.classList.add('is-invalid');
                    if (feedback) feedback.textContent = 'Value must be positive';
                    return;
                }
                if (input.id === 'interest_rate' && value > 100) {
                    input.classList.add('is-invalid');
                    if (feedback) feedback.textContent = 'Interest rate cannot exceed 100%';
                    return;
                }
            }

            input.classList.remove('is-invalid');
            if (feedback) feedback.textContent = '';
        }
    </script>
    @yield('scripts')
</body>
</html> 