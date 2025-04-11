<?php
include "../../mail/mail_gonder.php";
$host = "noktanetdb.cbuq6a2265j6.eu-central-1.rds.amazonaws.com";
$username = "nokta";
$password = "Dell28736.!";
$database = "noktanetdb";

try {
    // Establish PDO connection
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set UTF-8 charset
    $db->exec("set names utf8");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$q = $db->prepare("SELECT * FROM b2b_siparisler WHERE durum IN (3, 4)");
$q->execute();
$siparisler = $q->fetchAll(PDO::FETCH_ASSOC);

foreach ($siparisler as $siparis) {
    $barkodlar = explode(',', $siparis['barkod']);

    foreach ($barkodlar as $barkod) {
        $kargoAnahtarlari = $barkod;
        $requestXml = '
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://yurticikargo.com.tr/ShippingOrderDispatcherServices">
            <soapenv:Header/>
            <soapenv:Body>
                <ship:queryShipment>
                    <wsUserName>1104N187205434G</wsUserName><wsPassword>i101h487Ww128g24</wsPassword><wsLanguage>TR</wsLanguage>
                    <keys>'.$kargoAnahtarlari.'</keys>
                    <keyType>0</keyType>
                    <addHistoricalData>false</addHistoricalData>
                    <onlyTracking>false</onlyTracking>
                </ship:queryShipment >
            </soapenv:Body>
        </soapenv:Envelope>
        ';

        $ch = curl_init();
        $url = "https://ws.yurticikargo.com/KOPSWebServices/ShippingOrderDispatcherServices?wsdl";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: text/xml",
            "SOAPAction: createShipment"
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        $xml = simplexml_load_string($response);

        if ($xml !== false) {
            $xml->registerXPathNamespace('ns1', 'http://yurticikargo.com.tr/ShippingOrderDispatcherServices');

            $queryShipmentResponse = $xml->xpath('//ns1:queryShipmentResponse');
            if (!empty($queryShipmentResponse)) {
                $ShippingDeliveryVO = $queryShipmentResponse[0]->ShippingDeliveryVO;
                $shippingDeliveryDetailVO = $ShippingDeliveryVO->shippingDeliveryDetailVO;

                if (isset($shippingDeliveryDetailVO)) {
                    if (isset($shippingDeliveryDetailVO->operationCode)) {
                        $operationCode = (int) $shippingDeliveryDetailVO->operationCode;
                        $operationMessage = (string) $shippingDeliveryDetailVO->operationMessage;

                        if ($operationCode == 1) {
                            if($siparis['durum'] == 3) {
                                // Update the status to 4
                                $docId = (string)$shippingDeliveryDetailVO->shippingDeliveryItemDetailVO->docId;
                                echo 'Kargolandı :' . $docId . '<br>';
                                $updateQuery = $db->prepare("UPDATE b2b_siparisler SET durum = 4, kargo_durumu = :kargo, kargo_no = :kargo_no WHERE barkod = :barkod");
                                $updateQuery->bindParam(':kargo_no', $docId);
                                $siparis_no = $siparis['siparis_no'];
                                $uye = $siparis['teslimat_ad']. ' ' . $siparis['teslimat_soyad'];
                                $siparis_tarihi = $siparis['tarih'];
                                $uye_email = $siparis['uye_email'];
                                $mail_icerik = siparisKargolandi($uye, $siparis_no, $siparis_tarihi, $docId, 'Yurtiçi Kargo');
                                mailGonder($uye_email, 'Siparişiniz Kargolandı', $mail_icerik, 'Nokta Elektronik');
                            }
                        } elseif ($operationCode == 5) {
                            if($siparis['durum'] == 4) {
                                // Update the status to 5
                                $updateQuery = $db->prepare("UPDATE b2b_siparisler SET durum = 5, kargo_durumu = :kargo WHERE barkod = :barkod");
                                $siparis_no = $siparis['siparis_no'];
                                $sip_id = $siparis['id'];
                                $uye_email = $siparis['uye_email'];
                                $kargo_no = $siparis['kargo_no'];
                                $uye = $siparis['teslimat_ad']. ' ' . $siparis['teslimat_soyad'];
                                $siparis_tarihi = $siparis['tarih'];
                                $mail_icerik = siparisTeslimEdildi($uye, $siparis_no, $sip_id, $siparis_tarihi);
                                mailGonder($uye_email, 'Siparişiniz Teslim Edildi', $mail_icerik, 'Nokta Elektronik');
                                echo 'Kargo Teslim Edildi :' . $kargo_no . '<br>';
                            }
                        } else {
                            $updateQuery = $db->prepare("UPDATE b2b_siparisler SET kargo_durumu = :kargo WHERE barkod = :barkod");
                        }
                        // Check if $updateQuery is defined before using it
                        if (isset($updateQuery)) {
                            // Bind the barkod parameter and execute the query
                            $updateQuery->bindValue(':barkod', $kargoAnahtarlari);
                            $updateQuery->bindValue(':kargo', $operationMessage);
                            $updateQuery->execute();
                            //var_dump($updateQuery);
                        } else {
                            // Debug: Output the value of $shippingDeliveryDetailVO and other relevant variables to see if they contain the expected data
                            //var_dump($shippingDeliveryDetailVO);
                            //var_dump($operationCode);
                            //var_dump($operationMessage);

                        }
                    } else {
                        echo "operationCode öğesi eksik.";
                    }
                } else {
                    echo "shippingDeliveryDetailVO öğesi eksik.";
                }

            } else {
                echo "queryShipmentResponse öğesi eksik.";
            }
        } else {
            echo "XML yanıtı geçerli değil.";
        }

        curl_close($ch);
    }
}
?>
