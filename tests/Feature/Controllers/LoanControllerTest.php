<?php

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('loan calculation matches expected example', function () {
    $loanData = [
        'loan_amount' => 100000,
        'annual_interest_rate' => 0.09,
        'start_date' => '2025-06-01',
        'end_date' => '2025-11-30',
        'repayment_type' => 'repayment',
        'name' => 'Test Borrower',
    ];

    $response = $this->postJson('/api/loan/calculate', $loanData);

    $response->assertStatus(201);

    $responseData = $response->json();

    expect(round($responseData['summary']['total_interest'], 2))->toBe(2268.49);

    expect(round($responseData['summary']['monthly_payment'], 2))->toBe(16765.33);
});
