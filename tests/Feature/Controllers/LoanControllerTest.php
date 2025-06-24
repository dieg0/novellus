<?php

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('loan creation', function () {
    $loanData = [
        'amount' => 1000,
        'interest_rate' => 5,
        'term' => 12,
        'name' => 1,
    ];

    $response = $this->postJson('/api/calculate', $loanData);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Loan created successfully',
            'data' => $loanData,
        ]);

    $this->assertDatabaseHas('loans', $loanData);
});
