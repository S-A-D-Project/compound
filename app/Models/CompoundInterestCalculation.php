<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CompoundInterestCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'principal',
        'interest_rate',
        'time_period',
        'compounding_frequency',
        'result',
        'start_date'
    ];

    protected $casts = [
        'principal' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'time_period' => 'decimal:2',
        'result' => 'decimal:2',
        'start_date' => 'date'
    ];

    protected $dates = [
        'start_date',
        'created_at',
        'updated_at'
    ];

    protected function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value ? Carbon::parse($value) : Carbon::today();
    }

    public function getPaymentSchedule()
    {
        if (!$this->start_date) {
            $this->start_date = Carbon::today();
            $this->save();
        }

        $schedule = [];
        $startDate = $this->start_date;
        
        // Calculate the number of payments based on compounding frequency
        $paymentsPerYear = match($this->compounding_frequency) {
            'annually' => 1,
            'semi-annually' => 2,
            'quarterly' => 4,
            'monthly' => 12,
            'daily' => 365,
            default => 12
        };

        // Ensure we have at least one payment
        $totalPayments = max(1, $this->time_period * $paymentsPerYear);
        $paymentAmount = $this->result / $totalPayments;

        for ($i = 0; $i < $totalPayments; $i++) {
            // Calculate payment date based on frequency
            $paymentDate = match($this->compounding_frequency) {
                'annually' => $startDate->copy()->addYears($i),
                'semi-annually' => $startDate->copy()->addMonths($i * 6),
                'quarterly' => $startDate->copy()->addMonths($i * 3),
                'monthly' => $startDate->copy()->addMonths($i),
                'daily' => $startDate->copy()->addDays($i),
                default => $startDate->copy()->addMonths($i)
            };

            $remainingBalance = $this->result - ($paymentAmount * $i);
            
            $schedule[] = [
                'date' => $paymentDate->format('F j, Y'),
                'payment' => $paymentAmount,
                'remaining_balance' => max(0, $remainingBalance) // Ensure balance doesn't go negative
            ];
        }

        return $schedule;
    }
}
