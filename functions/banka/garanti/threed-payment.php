<?php 

use Gosas\Core\Entity\ThreeDPayment;
use Gosas\Core\GarantiPaymentProcess;
use Gosas\Core\Settings\PosSettings;
use Gosas\Core\Enums\RequestMode;

require_once('core/settings/PosSettings.php');
require_once('core/enums/RequestMode.php');
require_once('core/entity/ThreeDPayment.php');
require_once('core/GarantiPaymentProcess.php');

$settings = new PosSettings(RequestMode::Prod);
$paymentProcess = new GarantiPaymentProcess();
$params = new ThreeDPayment();
$paymentProcess->PrepareOrder();
$paymentProcess->PrepareCustomer();

$params = $paymentProcess->PrepareThreeDPayment($paymentProcess->request->order->orderId, 100, 949, 1, 'sales');
?>

<div class="card">
    <div class="card-body">
        <form id="payment-form" method="post" role="form" action="https://sanalposprov.garanti.com.tr/servlet/gt3dengine">
            <input type="hidden" name="mode" id="mode" value="PROD" />
            <input type="hidden" name="apiversion" id="apiversion" value="512" />
            <input type="hidden" name="terminalprovuserid" id="terminalprovuserid" value="<?php print $settings->provUserId ?>" />
            <input type="hidden" name="terminaluserid" id="terminaluserid" value="<?php print $settings->provUserId3DS ?>" />
            <input type="hidden" name="terminalmerchantid" id="terminalmerchantid" value="<?php print $settings->merchantId ?>" />
            <input type="hidden" name="txntype" id="txntype" value="<?php print $params->type ?>" />
            <input type="hidden" name="txncurrencycode" id="txncurrencycode" value="<?php print $params->currency ?>" />
            <input type="hidden" name="txninstallmentcount" id="txninstallmentcount" value="1" />
            <input type="hidden" name="txnamount" id="txnamount" value="100" />
            <input type="hidden" name="orderid" id="orderid" value="<?php print $paymentProcess->request->order->orderId ?>" />
            <input type="hidden" name="terminalid" id="terminalid" value="<?php print $settings->terminalId ?>" />
            <input type="hidden" name="successurl" id="successurl" value="<?php print $params->successUrl ?>" />
            <input type="hidden" name="errorurl" id="errorurl" value="<?php print $params->errorUrl ?>" />
            <input type="hidden" name="customeremailaddress" id="customeremailaddress" value="<?php print $paymentProcess->request->customer->emailAddress ?>" />
            <input type="hidden" name="customeripaddress" id="customeripaddress" value="<?php print $paymentProcess->request->customer->ipAddress ?>" />
            <input type="hidden" name="companyname" id="companyname" Value="NOKTA ELEKTRONİK" />
            <input type="hidden" name="lang" id="lang" Value="tr" />
            <input type="hidden" name="txntimestamp" id="txntimestamp" value="<?php print date("h:i:sa") ?>" />
            <input type="hidden" name="refreshtime" id="refreshtime" value="1" />
            <input type="hidden" name="secure3dhash" id="secure3dhash" value="<?php print $params->hashedData ?>" />
            <input type="hidden" name="secure3dsecuritylevel" id="secure3dsecuritylevel" value="3D_PAY" />

            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <div class="card-wrapper">3D'li Ödeme Ekranı</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Ad Soyad</label>
                                <input class="form-control" name="cardholdername" value="Test User" />
                            </div>
                            <div class="form-group">
                                <label>Kart Numarası</label>
                                <input class="form-control" id="cardnumber" name="cardnumber" value="5406697543211173" />
                            </div>
                            <div class="form-group">
                                <label>Son Kullanma (Ay)</label>
                                <input class="form-control" id="cardexpiredatemonth" name="cardexpiredatemonth" value="03" />
                            </div>
                            <div class="form-group">
                                <label>Son Kullanma (Yıl)</label>
                                <input class="form-control" id="cardexpiredateyear" name="cardexpiredateyear" value="23" />
                            </div>
                            <div class="form-group">
                                <label>CVV2</label>
                                <input class="form-control" name="cardcvv2" value="465" />
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">Ödemeyi Tamamla</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
