<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ExchangeRateController extends Controller
{
    public function getExchangeRate(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        // Takvimi oluştur
        $calendar = $this->generateCalendar($year, $month);
        return view('exchange_rate_calendar', ['calendar' => $calendar]);
    }

    private function generateCalendar($year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $calendar = [];
        $week = [];
        $currentDayOfWeek = $startDate->dayOfWeekIso;

        if ($currentDayOfWeek > 1) {
            for ($i = 1; $i < $currentDayOfWeek; $i++) {
                $week[] = null;
            }
        }

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $isWeekend = $date->isWeekend();
            $week[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->day,
                'is_weekend' => $isWeekend,
                'exchange_rates' => $this->getExchangeRatesForDate($date->format('Y-m-d')),
            ];

            if ($date->dayOfWeekIso == 7) {
                $calendar[] = $week;
                $week = [];
            }
        }

        if (!empty($week)) {
            $calendar[] = $week;
        }

        return $calendar;
    }

    public function getExchangeRates(Request $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');

        // Tarih formatını kontrol et
        $formattedDates = $this->validateDates($startDate, $endDate);
        if ($formattedDates === false) {
            return response()->json(['error' => 'Geçersiz tarih biçimi.'], 400);
        }

        // Veritabanından veya API'den döviz kurlarını al
        $exchangeRates = $this->fetchExchangeRates($formattedDates['start'], $formattedDates['end']);
        return response()->json($exchangeRates, 200);
    }

    private function getExchangeRatesForDate($date)
    {
        $exchangeRate = ExchangeRate::where('date', $date)->first();

        if ($exchangeRate) {
            return $exchangeRate;
        }

        return null;
    }

    private function validateDates($startDate, $endDate)
    {
        $startDateObject = DateTime::createFromFormat('d-m-Y', $startDate);
        $endDateObject = DateTime::createFromFormat('d-m-Y', $endDate);

        if ($startDateObject && $endDateObject) {
            return [
                'start' => $startDateObject->format('Y-m-d'),
                'end' => $endDateObject->format('Y-m-d')
            ];
        }

        return false;
    }

    private function fetchExchangeRates($startDate, $endDate)
    {
        $exchangeRates = ExchangeRate::whereBetween('date', [$startDate, $endDate])->get();

        if ($exchangeRates->isNotEmpty()) {
            return $exchangeRates;
        }

        $url = $this->buildApiUrl($startDate, $endDate);
        $client = new Client();
        $apiKey = config('services.tcmb.api_key'); // API anahtarını env dosyasından al

        try {
            $response = $client->get($url, [
                'headers' => [
                    'key' => $apiKey,
                    'User-Agent' => 'tcmb-client',
                ],
            ]);

            return $this->parseApiResponse($response->getBody()->getContents());
        } catch (RequestException $e) {
            Log::error('TCMB API Hatası: ' . $e->getMessage());
            return response()->json(['error' => 'Veri alırken bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }

    private function buildApiUrl($startDate, $endDate)
    {
        $startDateObject = DateTime::createFromFormat('Y-m-d', $startDate);
        $endDateObject = DateTime::createFromFormat('Y-m-d', $endDate);

        // Hatalı tarih kontrolü
        if (!$startDateObject || !$endDateObject) {
            return response()->json(['error' => 'Geçersiz tarih biçimi.'], 400);
        }

        $startDateFormatted = $startDateObject->format('d-m-Y');
        $endDateFormatted = $endDateObject->format('d-m-Y');

        return 'https://evds2.tcmb.gov.tr/service/evds/series=' .
            'TP.DK.USD.S.YTL-TP.DK.USD.A.YTL-' .
            'TP.DK.EUR.S.YTL-TP.DK.EUR.A.YTL-' .
            '&startDate=' . $startDateFormatted . '&endDate=' . $endDateFormatted . '&type=xml';
    }

    private function parseApiResponse($responseBody)
    {
        $xmlDoc = new \DOMDocument();
        $xmlDoc->loadXML($responseBody);
        $items = $xmlDoc->getElementsByTagName('items');

        $newExchangeRates = [];

        foreach ($items as $item) {
            // Kayıtları al ve veritabanına ekle
            $this->processExchangeRateItem($item, $newExchangeRates);
        }

        return $newExchangeRates;
    }

    private function processExchangeRateItem($item, &$newExchangeRates)
    {
        $tarih = $item->getElementsByTagName('Tarih')->item(0)->nodeValue;
        $dateObject = DateTime::createFromFormat('d-m-Y', $tarih);

        if ($dateObject) {
            $formattedDate = $dateObject->format('Y-m-d');
            $existingRate = ExchangeRate::where('date', $formattedDate)->first();

            if (!$existingRate) {
                // Değerleri al, eğer yoksa null olarak ayarla
                $exchangeData = [
                    'date' => $formattedDate,
                    'usd_s' => $this->getNodeValue($item, 'TP_DK_USD_S_YTL'),
                    'usd_a' => $this->getNodeValue($item, 'TP_DK_USD_A_YTL'),
                    'eur_s' => $this->getNodeValue($item, 'TP_DK_EUR_S_YTL'),
                    'eur_a' => $this->getNodeValue($item, 'TP_DK_EUR_A_YTL'),
                ];

                ExchangeRate::create($exchangeData);
                $newExchangeRates[] = $exchangeData;
            }
        }
    }

    private function getNodeValue($item, $tagName)
    {
        $value = $item->getElementsByTagName($tagName)->item(0)->nodeValue;
        return $value !== '' ? $value : null; // Boş ise null döndür
    }


}
