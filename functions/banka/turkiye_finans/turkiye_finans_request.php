<?php
session_start();
session_regenerate_id(true);
include('../../db.php');
if(isset($_POST["cariOdeme"])){
    $verimiz = [
        "cardHolder" => $_POST['cardName'],
        "cardNo" => $_POST['cardNumber'],
        "yantoplam" => $_POST["toplam"],
        "banka_id" => $_POST["banka_id"],
        "hesap" => $_POST["hesap"],
        "taksit" => $_POST["odemetaksit"],
        "uye_id" => $_POST["uye_id"],
        "lang" => $_POST["lang"]
    ];
    $verimizB64 = base64_encode(json_encode($verimiz));
    $odemetutar = $_POST['odemetutar'];
    $odemetutar = str_replace(',', '', $odemetutar);
    // Separate last two digits with a dot
    $odemetutar = substr_replace($odemetutar, '.', -2, 0);

    $orgClientId  = "280624575";
    $orgAmount = $odemetutar;
    $orgOkUrl =  "https://www.noktaelektronik.net/php/sip_olustur?cariveriFinans=" . $verimizB64;
    $orgFailUrl = "https://www.noktaelektronik.net/cariodeme?lang=tr";
    $orgTransactionType = "Auth";
    $orgInstallment = $_POST['odemetaksit'];
    $orgRnd =  microtime();
    $orgCallbackUrl = "https://www.noktaelektronik.com.tr/php/bank/turkiye_finans/callback.php";
    $orgCurrency = "949";
    ?>
    <form id="cariOdemeForm" method="post" action="https://www.noktaelektronik.net/admin/php/bank/turkiye_finans/GenericVer3RequestHashHandler.php">
        <input type="hidden" name="Ecom_Payment_Card_ExpDate_Month" value="<?php echo $_POST['expMonth'] ?>">
        <input type="hidden" name="Ecom_Payment_Card_ExpDate_Year" value="<?php echo $_POST['expYear'] ?>">
        <input type="hidden" name="cv2" value="<?php echo $_POST['cvCode'] ?>">
        <input type="hidden" name="pan" value="<?php echo $_POST['cardNumber'] ?>">
        <input type="hidden" name="name" value="noktaadmin">
        <input type="hidden" name="password" value="HLAD95796637">
        <input type="hidden" name="clientid" value="<?php echo $orgClientId ?>">
        <input type="hidden" name="amount" value="<?php echo $orgAmount ?>">
        <input type="hidden" name="okurl" value="<?php echo $orgOkUrl ?>">
        <input type="hidden" name="failUrl" value="<?php echo $orgFailUrl ?>">
        <input type="hidden" name="TranType" value="<?php echo $orgTransactionType ?>">
        <input type="hidden" name="Instalment" value="<?php echo $orgInstallment ?>">
        <input type="hidden" name="callbackUrl" value="<?php echo $orgCallbackUrl ?>">
        <input type="hidden" name="currency" value="<?php echo $orgCurrency ?>">
        <input type="hidden" name="rnd" value="<?php echo $orgRnd ?>">
        <input type="hidden" name="storetype" value="3d">
        <input type="hidden" name="hashAlgorithm" value="ver3">
        <input type="hidden" name="lang" value="tr">
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("cariOdemeForm").submit();
        });
    </script>
    <?php 
} elseif(isset($_POST["adminCariOdeme"])){
    $verimiz = [
        "cardHolder" => $_POST['cardName'],
        "cardNo" => $_POST['cardNumber'],
        "yantoplam" => $_POST["toplam"],
        "banka_id" => $_POST["banka_id"],
        "hesap" => $_POST["hesap"],
        "taksit" => $_POST["odemetaksit"],
        "uye_id" => $_POST["uye_id"],
        "lang" => $_POST["lang"],
    ];
    $verimizB64 = base64_encode(json_encode($verimiz));

    $odemetutar = $_POST['odemetutar'];

    $orgClientId  =   "280624575";
    $orgAmount = $odemetutar;
    $orgOkUrl =  "https://www.noktaelektronik.net/admin/functions/banka/manuelodeme?cariveriFinans=" . $verimizB64;
    $orgFailUrl = "https://www.noktaelektronik.net/admin/pages/b2b/b2b-sanalpos?w=noktab2b";
    $orgTransactionType = "Auth";
    $orgInstallment = $_POST['odemetaksit'];
    $orgRnd =  microtime();
    $orgCallbackUrl = "https://www.noktaelektronik.net/admin/functions/banka/turkiye_finans/callback.php";
    $orgCurrency = "949";
    ?>
    <form id="cariOdemeForm" method="post" action="https://www.noktaelektronik.net/admin/functions/banka/turkiye_finans/GenericVer3RequestHashHandler.php">
        <input type="hidden" name="Ecom_Payment_Card_ExpDate_Month" value="<?php echo $_POST['expMonth'] ?>">
        <input type="hidden" name="Ecom_Payment_Card_ExpDate_Year" value="<?php echo $_POST['expYear'] ?>">
        <input type="hidden" name="cv2" value="<?php echo $_POST['cvCode'] ?>">
        <input type="hidden" name="pan" value="<?php echo $_POST['cardNumber'] ?>">
        <input type="hidden" name="name" value="noktaadmin">
        <input type="hidden" name="password" value="HLAD95796637">
        <input type="hidden" name="clientid" value="<?php echo $orgClientId ?>">
        <input type="hidden" name="amount" value="<?php echo $orgAmount ?>">
        <input type="hidden" name="okurl" value="<?php echo $orgOkUrl ?>">
        <input type="hidden" name="failUrl" value="<?php echo $orgFailUrl ?>">
        <input type="hidden" name="TranType" value="<?php echo $orgTransactionType ?>">
        <input type="hidden" name="Instalment" value="<?php echo $orgInstallment ?>">
        <input type="hidden" name="callbackUrl" value="<?php echo $orgCallbackUrl ?>">
        <input type="hidden" name="currency" value="<?php echo $orgCurrency ?>">
        <input type="hidden" name="rnd" value="<?php echo $orgRnd ?>">
        <input type="hidden" name="storetype" value="3d">
        <input type="hidden" name="hashAlgorithm" value="ver3">
        <input type="hidden" name="lang" value="tr">
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("cariOdemeForm").submit();
        });
    </script>
    <?php 
}