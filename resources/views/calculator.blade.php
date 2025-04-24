<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compound Interest Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .currency-input {
            position: relative;
        }
        .currency-input::before {
            content: "₱";
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #4a5568;
        }
        .currency-input input {
            padding-left: 25px;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-center mb-6">Compound Interest Calculator</h1>
            
            <form id="calculatorForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Principal Amount (₱)</label>
                        <div class="currency-input">
                            <input type="number" id="principal" name="principal" class="w-full px-3 py-2 border rounded-md" min="0" step="0.01" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Interest Rate (%)</label>
                        <input type="number" id="rate" name="rate" class="w-full px-3 py-2 border rounded-md" min="0" step="0.01" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Years</label>
                        <input type="number" id="years" name="years" class="w-full px-3 py-2 border rounded-md" min="0" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Months</label>
                        <input type="number" id="months" name="months" class="w-full px-3 py-2 border rounded-md" min="0" max="11" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Compounding Frequency</label>
                        <select id="frequency" name="frequency" class="w-full px-3 py-2 border rounded-md">
                            <option value="1">Annually</option>
                            <option value="2">Semi-Annually</option>
                            <option value="4">Quarterly</option>
                            <option value="12">Monthly</option>
                            <option value="365">Daily</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-center space-x-4 mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Calculate</button>
                    <button type="reset" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Reset</button>
                </div>
            </form>

            <div id="results" class="mt-8 hidden">
                <h2 class="text-xl font-semibold mb-4">Results</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="text-sm text-gray-600">Final Amount</p>
                        <p id="finalAmount" class="text-2xl font-bold text-green-600">₱0.00</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="text-sm text-gray-600">Total Interest</p>
                        <p id="totalInterest" class="text-2xl font-bold text-blue-600">₱0.00</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">Year-by-Year Breakdown</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border">Year</th>
                                    <th class="px-4 py-2 border">Interest</th>
                                    <th class="px-4 py-2 border">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody id="breakdownTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($calculations->count() > 0)
            <div class="mt-8">
                <h2 class="text-xl font-semibold mb-4">Recent Calculations</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border">Date</th>
                                <th class="px-4 py-2 border">Principal</th>
                                <th class="px-4 py-2 border">Rate</th>
                                <th class="px-4 py-2 border">Time</th>
                                <th class="px-4 py-2 border">Final Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculations as $calculation)
                            <tr>
                                <td class="px-4 py-2 border">{{ $calculation->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-2 border">₱{{ number_format($calculation->principal, 2) }}</td>
                                <td class="px-4 py-2 border">{{ $calculation->interest_rate }}%</td>
                                <td class="px-4 py-2 border">{{ $calculation->years }}y {{ $calculation->months }}m</td>
                                <td class="px-4 py-2 border">₱{{ number_format($calculation->final_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('calculatorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/calculate', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const calculation = data.calculation;
                    document.getElementById('finalAmount').textContent = `₱${calculation.final_amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    document.getElementById('totalInterest').textContent = `₱${calculation.total_interest.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    
                    // Generate year-by-year breakdown
                    const breakdownTable = document.getElementById('breakdownTable');
                    breakdownTable.innerHTML = '';
                    
                    let currentAmount = calculation.principal;
                    const totalYears = calculation.years + (calculation.months / 12);
                    const n = calculation.compounding_frequency;
                    const r = calculation.interest_rate / 100;
                    
                    for (let year = 1; year <= Math.ceil(totalYears); year++) {
                        const yearAmount = calculation.principal * Math.pow(1 + (r/n), n*Math.min(year, totalYears));
                        const yearInterest = yearAmount - currentAmount;
                        currentAmount = yearAmount;
                        
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-4 py-2 border">${year}</td>
                            <td class="px-4 py-2 border">₱${yearInterest.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                            <td class="px-4 py-2 border">₱${yearAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        `;
                        breakdownTable.appendChild(row);
                    }
                    
                    document.getElementById('results').classList.remove('hidden');
                    // Reload the page to show the new calculation in history
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while calculating. Please try again.');
            });
        });

        document.getElementById('calculatorForm').addEventListener('reset', function() {
            document.getElementById('results').classList.add('hidden');
        });
    </script>
</body>
</html>