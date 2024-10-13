<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Döviz Kuru Tarih Seçimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="col-12">
        <div class="calendar-container p-4">
            <h2 class="calendar-header">TCMB Döviz Kuru Tarih Seçimi</h2>

            <!-- Yıl Seçimi Tab Bar -->
            <ul class="nav nav-tabs" id="yearTab" role="tablist">
                @foreach (range(2024, 2020) as $year)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="y{{ $year }}-tab" data-bs-toggle="tab" data-bs-target="#y{{ $year }}" type="button" role="tab" aria-controls="y{{ $year }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $year }}</button>
                    </li>
                @endforeach
            </ul>

            <!-- Ay Seçimi Tab Bar -->
            <ul class="nav nav-tabs mt-3" id="monthTab" role="tablist">
                @foreach (['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'] as $index => $month)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ strtolower(substr($index, 0, 3)) }}-tab" data-bs-toggle="tab" data-bs-target="#{{ strtolower(substr($month, 0, 3)) }}" type="button" role="tab" aria-controls="{{ strtolower(substr($month, 0, 3)) }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} <br/> <small>{{ $month }}</small>
                        </button>
                    </li>
                @endforeach
            </ul>

            <!-- Takvim -->
            <div class="tab-content mt-3">
                <div class="table-responsive">
                    <table class="calendar-table table table-bordered table-hover table-striped">
                        <thead class="table-light">
                        <tr>
                            <th>Pzt</th>
                            <th>Sal</th>
                            <th>Çar</th>
                            <th>Per</th>
                            <th>Cum</th>
                            <th>Cts</th>
                            <th>Paz</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Tarihler -->
                        @foreach ($calendar as $week)
                            <tr>
                                @foreach ($week as $day)
                                    @if ($day)
                                        <td class="{{ $day['is_weekend'] ? 'highlighted-date' : '' }}" data-date="{{ $day['date'] }}" data-is-weekend="{{ $day['is_weekend'] }}">
                                            <a href="#" class="day">
                                                {{ $day['day'] }}
                                            </a>
                                        </td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-primary btn-lg" id="list-button">Listele</button>
            </div>
        </div>
    </div>
</div>

<!-- Döviz Kurları Modalı -->
<div class="modal fade" id="exchangeRateModal" tabindex="-1" aria-labelledby="exchangeRateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exchangeRateModalLabel">Döviz Kurları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>USD Satış</th>
                        <th>USD Alış</th>
                        <th>EUR Satış</th>
                        <th>EUR Alış</th>
                    </tr>
                    </thead>
                    <tbody id="exchangeRateTableBody">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- JavaScript Kütüphaneleri -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="{{asset('js/app.js')}}"></script>
</body>
</html>
