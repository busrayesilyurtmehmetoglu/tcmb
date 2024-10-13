<h4>Case;</h4>
<ul>
    <li>TCMB kurallarını gösteren bir ekran tasarlanacak.</li>
     <li>Ekranda olması gerekenler, tarih seçimi ve listele butonu.</li>
     <li>Ekranın işlevi, seçilen tarihteki kurları TCMB’nin sayfasından alıp ekrana listelemek olacak.</li>
     <li>Ekranın çalışma mantığı; <br/>
    <ul>
        <li>Seçilen tarihteki veriler TCMB sayfasından alınacak,</li>
        <li>Alınan veriler veri tabanına yazılacak,</li>
        <li>Ekrana yansıtma veri tabanından olacak,</li>
    </ul>
</li>
    <li>Eğer veriler daha önceden TCMB sayfasından alınıp, veri tabanına kaydedilmişse, sorgulama yapıldığında servis TCMB servislerini çağırmayacak ve veri tabanından veriyi ekrana getirecek. Eğer daha evvelinden veri tabanında kayıt yoksa servis, TCMB servislerini çağırarak veriyi alacak ve veri tabanına yazarak, ekranda gösterimini yapacak,
Eğer seçilen tarih hafta sonu ise “hafta sonuna ait kur bilgilerini veremiyoruz” diye uyarı verecek. Seçilen tarih aralığına hafta sonu da dahilse, sadece hafta içine ait günlerin bilgisini alacak ve uyarı vermeyecek.</li>
</ul>
<p>Bu case study için gerekli teknolojiler, <br/>

Veri tabanı için MSSQL ve/veya MySQL kullanılmalı,
Front End yazılımı için PHP Laravel ile kodlama yapılmalı,
Back End de çağırılacak servis, API veya WS olmalı,
Ekranın responsive olması gerekli,
Unit testleri için test case.leri yazılmalı.</p>



 


 



<h4>Bilgilendirme</h4>

<ul>
    <li>.env dosyasına eklenmelidir. 
    <br/>
        <ul>
            <li>TCMB_API_KEY=c1u4qTEGkD</li>
        </ul>
    </li>
    <li>
        Database migrate ile oluşturulabilir.
        <br/>
        <ul>
            <li> php artisan migrate</li>
        </ul>
    </li>
    <li>
        Database unit test yapılabilir.
        <br/>
        <ul>
            <li> php artisan test</li>
        </ul>
    </li>
</ul>
