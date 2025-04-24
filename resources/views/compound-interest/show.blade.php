@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h2 class="mb-0 fs-4">Calculation Details</h2>
                    <div>
                        <a href="{{ route('compound-interest.index') }}" class="btn btn-light me-2">Back to List</a>
                        <button onclick="window.print()" class="btn btn-light">
                            <i class="bi bi-printer"></i> Print/Export
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0 fs-5">Calculation Parameters</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th class="ps-0">Principal Amount:</th>
                                            <td class="text-end pe-0">₱{{ number_format($calculation->principal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Interest Rate:</th>
                                            <td class="text-end pe-0">{{ number_format($calculation->interest_rate, 2) }}%</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Time Period:</th>
                                            <td class="text-end pe-0">{{ number_format($calculation->time_period, 2) }} years</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Compounding Frequency:</th>
                                            <td class="text-end pe-0">{{ ucfirst($calculation->compounding_frequency) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0">Start Date:</th>
                                            <td class="text-end pe-0">{{ $calculation->start_date ? $calculation->start_date->format('F j, Y') : 'Not set' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0 fs-5">Results</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <div class="text-muted mb-1">Final Amount</div>
                                                <div class="h4 mb-0">₱{{ number_format($calculation->result, 2) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded">
                                                <div class="text-muted mb-1">Interest Earned</div>
                                                <div class="h4 mb-0">₱{{ number_format($calculation->result - $calculation->principal, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h4 class="mb-0 fs-5">Payment Schedule</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Payment Date</th>
                                            <th class="text-end">Payment Amount</th>
                                            <th class="text-end">Remaining Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paymentSchedule as $payment)
                                            <tr>
                                                <td>{{ $payment['date'] }}</td>
                                                <td class="text-end">₱{{ number_format($payment['payment'], 2) }}</td>
                                                <td class="text-end">₱{{ number_format($payment['remaining_balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0 fs-5">Growth Over Time</h4>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="growthChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h4 class="mb-0 fs-5">Compare with Different Frequencies</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Frequency</th>
                                            <th class="text-end">Final Amount</th>
                                            <th class="text-end">Interest Earned</th>
                                            <th class="text-end">Difference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($comparisons as $comparison)
                                            <tr class="{{ $comparison['frequency'] === $calculation->compounding_frequency ? 'table-primary' : '' }}">
                                                <td>{{ ucfirst($comparison['frequency']) }}</td>
                                                <td class="text-end">₱{{ number_format($comparison['result'], 2) }}</td>
                                                <td class="text-end">₱{{ number_format($comparison['interest'], 2) }}</td>
                                                <td class="text-end {{ $comparison['difference'] > 0 ? 'text-success' : ($comparison['difference'] < 0 ? 'text-danger' : '') }}">
                                                    {{ $comparison['difference'] > 0 ? '+' : '' }}₱{{ number_format($comparison['difference'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="text-muted">
                        <small>Calculation performed on {{ $calculation->created_at ? $calculation->created_at->format('F j, Y g:i A') : 'Not available' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 0.5rem;
}
.card-header {
    border-top-left-radius: 0.5rem !important;
    border-top-right-radius: 0.5rem !important;
}
.table > :not(caption) > * > * {
    padding: 1rem;
}
@media print {
    .btn { display: none; }
    .card { box-shadow: none !important; }
    .bg-primary { background-color: #fff !important; color: #000 !important; }
    .bg-light { background-color: #fff !important; }
}
</style>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('growthChart').getContext('2d');
    const principal = {{ $calculation->principal }};
    const rate = {{ $calculation->interest_rate }};
    const years = {{ $calculation->time_period }};
    const frequency = {{ 
        match($calculation->compounding_frequency) {
            'annually' => 1,
            'semi-annually' => 2,
            'quarterly' => 4,
            'monthly' => 12,
            'daily' => 365,
            default => 1
        }
    }};

    const labels = Array.from({length: Math.ceil(years) + 1}, (_, i) => i);
    const data = labels.map(year => {
        return principal * Math.pow(1 + (rate/100)/frequency, frequency * year);
    });

    new Chart(ctx, {
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
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection 