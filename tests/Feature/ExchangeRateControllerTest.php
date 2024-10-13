<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ExchangeRate;
use Illuminate\Foundation\Testing\WithFaker;

class ExchangeRateControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_exchange_rates_for_a_valid_date()
    {
        $exchangeRate = ExchangeRate::create([
            'date' => '2024-10-12',
            'usd_s' => '18.00',
            'usd_a' => '18.10',
            'eur_s' => '19.00',
            'eur_a' => '19.10',
        ]);

        $response = $this->get('/exchange-rates?startDate=12-10-2024&endDate=12-10-2024');

        $response->assertStatus(200);

        // JSON içinde belirli bir parça var mı kontrol et
        $response->assertJson([
            [
                'date' => '2024-10-12',
                'usd_s' => '18.00',
                'usd_a' => '18.10',
            ]
        ]);
    }

    /** @test */
    public function it_returns_error_for_invalid_date_format()
    {
        $response = $this->get('/exchange-rates?startDate=invalid-date&endDate=invalid-date');

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid date format provided.']);
    }




}
