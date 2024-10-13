$(document).ready(function() {
    let selectedDate = '';

    // Tab tıklama olayı (Yıl ve Ay)
    $('#monthTab .nav-link').on('click', function() {
        $('#monthTab .nav-link').removeClass('active');
        $(this).addClass('active');
    });

    // TD'ye tıklama olayı
    $('.calendar-table td').on('click', function(event) {
        event.preventDefault();
        selectedDate = $(this).data('date');
        const isWeekend = $(this).data('is-weekend');

        $('.calendar-table td').removeClass('highlighted-date');
        $(this).addClass('highlighted-date');

        if (isWeekend) {
            Swal.fire({
                title: 'Hata!',
                text: 'Hafta sonuna ait kur bilgilerini veremiyoruz.',
                icon: 'error',
                confirmButtonText: 'Tamam'
            });
        }
    });

    // Listele butonuna tıklama
    $('#list-button').on('click', function() {
        if (selectedDate) {
            const isWeekend = $('.highlighted-date').data('is-weekend');
            if (!isWeekend) {
                const selectedYear = getActiveYear();
                const selectedMonth = getActiveMonth();
                const gun = selectedDate.split('-')[2];
                const fullDate = `${gun}-${selectedMonth}-${selectedYear}`;

                getExchangeRates(fullDate);
            } else {
                Swal.fire({
                    title: 'Hata!',
                    text: 'Hafta sonuna ait kur bilgilerini veremiyoruz.',
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        } else {
            Swal.fire({
                title: 'Hata!',
                text: 'Lütfen bir tarih seçin.',
                icon: 'error',
                confirmButtonText: 'Tamam'
            });
        }
    });

    function getActiveYear() {
        return $('#yearTab .nav-link.active').attr('id').substring(1, 5);
    }

    function getActiveMonth() {
        const activeMonthIndex = parseInt($('#monthTab .nav-link.active').attr('id').substring(0,1), 10);
        return String(activeMonthIndex + 1).padStart(2, '0');
    }

    function getExchangeRates(date) {
        // Tarih formatını d-m-Y olarak güncelle
        const parts = date.split('-');

        // parts dizisini kontrol et
        if (parts.length !== 3) {
            console.error('Tarih formatı geçerli değil:', date);
            Swal.fire({
                title: 'Hata!',
                text: 'Tarih formatı geçerli değil.',
                icon: 'error',
                confirmButtonText: 'Tamam'
            });
            return;
        }

        const formattedDate = `${parts[0]}-${parts[1]}-${parts[2]}`; // gün-ay-yıl
        console.log('Dönüştürülen tarih:', formattedDate);

        // API'den veri al
        $.ajax({
            url: `/exchange-rates?startDate=${formattedDate}&endDate=${formattedDate}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (Array.isArray(response) && response.length > 0) {
                    // Veriler varsa, bunları modalda göster
                    showExchangeRatesInModal(response);
                } else {
                    Swal.fire({
                        title: 'Hata!',
                        text: 'Seçilen tarih için veri bulunamadı.',
                        icon: 'error',
                        confirmButtonText: 'Tamam'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Hata!',
                    text: 'Seçilen tarih için veri okunamıyor.',
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    }



    function showExchangeRatesInModal(rates) {
        let rows = '';
        rates.forEach(function(rate) {
            rows += `
                <tr>
                    <td>${rate.date}</td>
                    <td>${rate.usd_s}</td>
                    <td>${rate.usd_a}</td>
                    <td>${rate.eur_s}</td>
                    <td>${rate.eur_a}</td>
                </tr>
            `;
        });

        $('#exchangeRateTableBody').html(rows);
        $('#exchangeRateModal').modal('show');
    }
});
