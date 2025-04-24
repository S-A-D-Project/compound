<?php

namespace App\Http\Controllers;

use App\Models\CompoundInterestCalculation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CompoundInterestController extends Controller
{
    public function index()
    {
        $calculations = CompoundInterestCalculation::latest()->get();
        return view('compound-interest.index', compact('calculations'));
    }

    public function create()
    {
        return view('compound-interest.create');
    }

    public function store(Request $request)
    {
        // Debug log the raw request data
        \Log::info('Raw request data:', $request->all());

        // Validate raw input first
        $request->validate([
            'principal' => 'required|string',
            'interest_rate' => 'required|string',
            'time_period' => 'required|string',
            'compounding_frequency' => 'required|in:annually,semi-annually,quarterly,monthly,daily',
            'start_date' => 'nullable|date'
        ]);

        // Remove commas from numeric inputs
        $cleanedData = [
            'principal' => str_replace(',', '', $request->input('principal')),
            'interest_rate' => str_replace(',', '', $request->input('interest_rate')),
            'time_period' => str_replace(',', '', $request->input('time_period')),
            'compounding_frequency' => $request->input('compounding_frequency'),
            'start_date' => $request->input('start_date')
        ];

        // Debug log the cleaned data
        \Log::info('Cleaned data:', $cleanedData);

        // Validate numeric values after cleaning
        if (!is_numeric($cleanedData['principal']) || !is_numeric($cleanedData['interest_rate']) || !is_numeric($cleanedData['time_period'])) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Invalid numeric values provided.']);
        }

        // Convert to proper types
        $principal = (float)$cleanedData['principal'];
        $rate = (float)$cleanedData['interest_rate'];
        $time = (float)$cleanedData['time_period'];
        $frequency = $cleanedData['compounding_frequency'];

        if ($principal <= 0 || $rate < 0 || $time <= 0) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Principal and time period must be greater than 0, and interest rate must be non-negative.']);
        }

        $n = match($frequency) {
            'annually' => 1,
            'semi-annually' => 2,
            'quarterly' => 4,
            'monthly' => 12,
            'daily' => 365,
            default => 1
        };

        try {
            // Calculate compound interest
            $result = $principal * pow(1 + ($rate/100)/$n, $n * $time);

            // Debug log the calculation data
            \Log::info('Calculation data:', [
                'principal' => $principal,
                'rate' => $rate,
                'time' => $time,
                'frequency' => $frequency,
                'n' => $n,
                'result' => $result
            ]);

            // Create the calculation
            $calculation = new CompoundInterestCalculation();
            $calculation->principal = $principal;
            $calculation->interest_rate = $rate;
            $calculation->time_period = $time;
            $calculation->compounding_frequency = $frequency;
            $calculation->result = $result;
            $calculation->start_date = $cleanedData['start_date'] ? Carbon::parse($cleanedData['start_date']) : Carbon::today();
            $calculation->save();

            return redirect()->route('compound-interest.show', $calculation)
                ->with('success', 'Calculation created successfully!');

        } catch (\Exception $e) {
            \Log::error('Error in store method:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while processing your request. Please try again.']);
        }
    }

    public function show(CompoundInterestCalculation $calculation)
    {
        // Log the calculation data for debugging
        Log::info('Calculation data:', $calculation->toArray());

        // Load the calculation with its payment schedule
        $paymentSchedule = $calculation->getPaymentSchedule();
        
        // Calculate comparison data
        $frequencies = [
            'annually' => 1,
            'semi-annually' => 2,
            'quarterly' => 4,
            'monthly' => 12,
            'daily' => 365
        ];

        $comparisons = collect($frequencies)->map(function($n, $frequency) use ($calculation) {
            $result = $calculation->principal * pow(1 + ($calculation->interest_rate/100)/$n, $n * $calculation->time_period);
            $interest = $result - $calculation->principal;
            $difference = $result - $calculation->result;

            return [
                'frequency' => $frequency,
                'result' => $result,
                'interest' => $interest,
                'difference' => $difference
            ];
        });

        return view('compound-interest.show', compact('calculation', 'paymentSchedule', 'comparisons'));
    }
}
