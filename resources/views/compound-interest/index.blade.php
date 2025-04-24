@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Compound Interest Calculator</h2>
                    <a href="{{ route('compound-interest.create') }}" class="btn btn-primary">New Calculation</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($calculations->isEmpty())
                        <p>No calculations yet. Create your first calculation!</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Principal</th>
                                        <th>Interest Rate</th>
                                        <th>Time Period</th>
                                        <th>Frequency</th>
                                        <th>Result</th>
                                        <th>Start Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($calculations as $calculation)
                                        <tr>
                                            <td>₱{{ number_format($calculation->principal, 2) }}</td>
                                            <td>{{ $calculation->interest_rate }}%</td>
                                            <td>{{ $calculation->time_period }} years</td>
                                            <td>{{ ucfirst($calculation->compounding_frequency) }}</td>
                                            <td>₱{{ number_format($calculation->result, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($calculation->start_date)->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('compound-interest.show', $calculation) }}" class="btn btn-sm btn-info">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 