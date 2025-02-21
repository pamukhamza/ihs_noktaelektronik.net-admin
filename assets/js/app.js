function kartBinSorgulama(cardNumberSelector, infoContainerId) {
    // Kart bilgilerini ekranda göstermek için kullanılan yardımcı fonksiyon
    function appendInfo(text, infoContainer) {
        var p = document.createElement("p");
        p.textContent = text;
        infoContainer.appendChild(p);
    }

    // Kart bilgilerini göstereceğimiz container'ı seçiyoruz
    var infoContainer = document.getElementById(infoContainerId);

    // Kart numarasını almak için kullanıcının girdiği değeri alıyoruz
    var bin = $(cardNumberSelector).val().substr(0, 6);

    // Eğer BIN uzunluğu 6 ise AJAX isteği gönderiyoruz
    if (bin.length === 6) {
        $.ajax({
            url: 'functions/banka/bin_sorgula.php',
            method: 'POST',
            data: { bin: bin },
            success: function(response) {
                // Sunucudan gelen yanıtı virgülle ayırıyoruz
                var data = response.split(',');

                // Yanıt verilerini alıp temizliyoruz
                var bank = data[0].trim();
                var kamp = data[1].trim();
                var kartOrg = data[2].trim();

                // Kart bilgilerini ekranda güncelleyen fonksiyonu çağırıyoruz
                updateInfo(kamp, bank, kartOrg, infoContainer);
            },
            error: function(hata) {
                // AJAX isteği hata verirse kullanıcıya bilgi gösterilebilir
                console.log(hata);
                infoContainer.innerHTML = '<p>Bilgiler alınamadı. Lütfen tekrar deneyin.</p>';
            }
        });
    } else {
        // Eğer kart numarası 6 haneden küçükse, bilgileri temizliyoruz
        infoContainer.innerHTML = '';
    }

    // Kart bilgilerini ekranda göstermek için gerekli güncelleme fonksiyonu
    function updateInfo(kamp, bank, kartOrg, infoContainer) {
        // İlk olarak eski bilgileri temizliyoruz
        infoContainer.innerHTML = '';

        // Yeni bilgileri ekliyoruz
        appendInfo('Kampanya: ' + kamp, infoContainer);
        appendInfo('Banka: ' + bank, infoContainer);
        appendInfo('Kart Organizasyonu: ' + kartOrg, infoContainer);
    }
}
