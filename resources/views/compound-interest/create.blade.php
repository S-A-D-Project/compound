@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h2 class="mb-0 fs-4">New Compound Interest Calculation</h2>
                    <a href="{{ route('compound-interest.index') }}" class="btn btn-light">Back to List</a>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('compound-interest.store') }}" id="calculationForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="principal" class="form-label fw-bold">Principal Amount</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light">₱</span>
                                        <input type="text" class="form-control form-control-lg @error('principal') is-invalid @enderror" 
                                               id="principal" name="principal" value="{{ old('principal') }}" 
                                               placeholder="Enter amount" required>
                                        @error('principal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="interest_rate" class="form-label fw-bold">Interest Rate</label>
                                    <div class="input-group input-group-lg">
                                        <input type="text" class="form-control form-control-lg @error('interest_rate') is-invalid @enderror" 
                                               id="interest_rate" name="interest_rate" value="{{ old('interest_rate') }}" 
                                               placeholder="Enter rate" required>
                                        <span class="input-group-text bg-light">%</span>
                                        @error('interest_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="time_period" class="form-label fw-bold">Time Period</label>
                                    <div class="input-group input-group-lg">
                                        <input type="text" class="form-control form-control-lg @error('time_period') is-invalid @enderror" 
                                               id="time_period" name="time_period" value="{{ old('time_period') }}" 
                                               placeholder="Enter years" required>
                                        <span class="input-group-text bg-light">years</span>
                                        @error('time_period')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="compounding_frequency" class="form-label fw-bold">Compounding Frequency</label>
                                    <select class="form-select form-select-lg @error('compounding_frequency') is-invalid @enderror" 
                                            id="compounding_frequency" name="compounding_frequency" required>
                                        <option value="annually" {{ old('compounding_frequency') == 'annually' ? 'selected' : '' }}>Annually</option>
                                        <option value="semi-annually" {{ old('compounding_frequency') == 'semi-annually' ? 'selected' : '' }}>Semi-annually</option>
                                        <option value="quarterly" {{ old('compounding_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="monthly" {{ old('compounding_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="daily" {{ old('compounding_frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    </select>
                                    @error('compounding_frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="start_date" class="form-label fw-bold">Start Date</label>
                                    <input type="date" class="form-control form-control-lg @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}">
                                    <div class="form-text text-muted">Leave empty to use today's date</div>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Preview Results</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded">
                                                    <div class="text-muted mb-1">Final Amount</div>
                                                    <div class="h4 mb-0" id="previewResult">₱0.00</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded">
                                                    <div class="text-muted mb-1">Interest Earned</div>
                                                    <div class="h4 mb-0" id="previewInterest">₱0.00</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chart-container" style="height: 300px;">
                                            <canvas id="previewChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                Calculate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
.input-group-text {
    border: 1px solid #ced4da;
}
.chart-container {
    position: relative;
    margin: auto;
}
.card {
    border: none;
    border-radius: 0.5rem;
}
.card-header {
    border-top-left-radius: 0.5rem !important;
    border-top-right-radius: 0.5rem !important;
}
</style>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('calculationForm');
    const inputs = form.querySelectorAll('input, select');
    const previewResult = document.getElementById('previewResult');
    const previewInterest = document.getElementById('previewInterest');
    let chart = null;

    // Format number with commas
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Remove commas and convert to number
    function parseNumber(str) {
        return parseFloat(str.replace(/,/g, '')) || 0;
    }

    // Format input value with commas
    function formatInput(input) {
        const value = input.value.replace(/,/g, '');
        if (!isNaN(value) && value !== '') {
            input.value = formatNumber(value);
        }
    }

    // Add event listeners for number formatting
    document.getElementById('principal').addEventListener('input', function() {
        formatInput(this);
    });

    document.getElementById('interest_rate').addEventListener('input', function() {
        formatInput(this);
    });

    document.getElementById('time_period').addEventListener('input', function() {
        formatInput(this);
    });

    function calculatePreview() {
        const principal = parseNumber(document.getElementById('principal').value);
        const rate = parseNumber(document.getElementById('interest_rate').value);
        const years = parseNumber(document.getElementById('time_period').value);
        const frequency = document.getElementById('compounding_frequency').value;
        
        if (!principal || !rate || !years || !frequency) return;

        const n = {
            'annually': 1,
            'semi-annually': 2,
            'quarterly': 4,
            'monthly': 12,
            'daily': 365
        }[frequency] || 1;

        const result = principal * Math.pow(1 + (rate/100)/n, n * years);
        const interest = result - principal;

        previewResult.textContent = '₱' + formatNumber(result.toFixed(2));
        previewInterest.textContent = '₱' + formatNumber(interest.toFixed(2));

        updateChart(principal, rate, years, n);
    }

    function updateChart(principal, rate, years, n) {
        const ctx = document.getElementById('previewChart').getContext('2d');
        const labels = Array.from({length: Math.ceil(years) + 1}, (_, i) => i);
        const data = labels.map(year => {
            return principal * Math.pow(1 + (rate/100)/n, n * year);
        });

        if (chart) {
            chart.destroy();
        }

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Investment Growth',
                    data: data,
                    borderColor: '#0d6efd',
                    tension: 0.3,
                    fill: true,
                    backgroundColor: 'rgba(13, 110, 253, 0.1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + formatNumber(value.toFixed(2));
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + formatNumber(context.parsed.y.toFixed(2));
                            }
                        }
                    }
                }
            }
        });
    }

    // Add event listeners for all form inputs
    inputs.forEach(input => {
        input.addEventListener('change', calculatePreview);
        input.addEventListener('input', calculatePreview);
    });

    // Initial calculation if form is pre-filled
    calculatePreview();
});
</script>
@endsection 