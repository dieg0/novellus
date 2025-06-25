<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoanRequest $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        //
    }

    /**
     * Calculate loan payment schedule and summary
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'loan_amount' => 'required|integer|min:1',
            'annual_interest_rate' => 'required|numeric|min:0|max:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'repayment_type' => 'required|string|in:repayment,interest-only,interest-retained',
            'name' => 'required|string|max:255',
        ]);

        $loanAmount = $validated['loan_amount'];
        $annualRate = $validated['annual_interest_rate'];
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $repaymentType = $validated['repayment_type'];
        $name = $validated['name'];

        $summary = $this->calculateLoanSummary(
            $loanAmount,
            $annualRate,
            $startDate,
            $endDate,
            $repaymentType
        );

        return response()->json([
            'summary' => $summary,
            'borrower_name' => $name,
        ], 201);
    }

    /**
     * Calculate loan summary with daily interest calculation
     */
    private function calculateLoanSummary(int $loanAmount, float $annualRate, Carbon $startDate, Carbon $endDate, string $repaymentType)
    {
        $dailyRate = $annualRate / 365;
        switch ($repaymentType) {
            case 'repayment':
                return $this->calculateRepaymentLoan($loanAmount, $startDate, $endDate, $dailyRate);
            default:
                throw new \InvalidArgumentException('Invalid repayment type');
        }
    }

    /**
     * Calculate repayment loan
     * This method does not include standard amortising payments.
     */
    private function calculateRepaymentLoan(int $loanAmount, Carbon $startDate, Carbon $endDate, float $dailyRate)
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $dailyPrincipalPayment = $loanAmount / $totalDays;

        $balance = $loanAmount;
        $totalInterest = 0;
        $currentDate = $startDate->copy();
        $dailyPayments = [];

        for ($day = 1; $day <= $totalDays; $day++) {
            $dailyInterest = $balance * $dailyRate;
            $totalDailyPayment = $dailyPrincipalPayment + $dailyInterest;
            $dailyPayments[] = [
                'day' => $day,
                'date' => $currentDate->format('Y-m-d'),
                'balance_start' => $balance,
                'daily_interest' => $dailyInterest,
                'principal_payment' => $dailyPrincipalPayment,
                'total_payment' => $totalDailyPayment,
            ];
            $totalInterest += $dailyInterest;
            $balance -= $dailyPrincipalPayment;
            $currentDate->addDay();
        }

        $totalMonths = 0;
        $currentMonth = $startDate->copy();
        while ($currentMonth->lessThanOrEqualTo($endDate)) {
            $totalMonths++;
            $currentMonth->addMonth();
        }
        $averageMonthlyPayment = ($totalInterest + $loanAmount) / $totalMonths;

        return [
            'monthly_payment' => $averageMonthlyPayment,
            'total_interest' => $totalInterest,
            'total_paid' => $loanAmount + $totalInterest,
            'final_payment' => $endDate->format('Y m d'),
            'daily_principal_payment' => $dailyPrincipalPayment,
            'total_days' => $totalDays,
            'daily_payments' => $dailyPayments,
        ];
    }
}
