<?php
    // Örnek veriler
    $data = array(
    'wsUserName' => 'YKTEST',
    'wsPassword' => 'YK',
    'userLanguage' => 'TR',
    'ShippingOrderVO' => array(
    'cargoKey' => '12345678999',
    'invoiceKey' => '7896541236',
    'receiverCustName' => 'ALICI_AD_SOYAD',
    'receiverAddress' => 'ALICI_ADRESI',
    'cityName' => 'ALICI_SEHIR',
    'townName' => 'ALICI_ILCE',
    'receiverPhone1' => 'ALICI_TELEFON',
    'taxOfficeId' => '',
    'cargoCount' => '1',
    'specialField1' => '1$1340965#',
    'ttDocumentId' => '',
    'dcSelectedCredit' => '',
    'dcCreditRule' => '',
    'orgReceiverCustId' => '11988'
    )
    );

    // XML oluştur
    $xml = new SimpleXMLElement('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ship="http://testyurticikargo.com.tr/ShippingOrderDispatcherServices"></soapenv:Envelope>');
    $body = $xml->addChild('soapenv:Body');
    $shipment = $body->addChild('ship:createShipment');
    foreach($data as $key => $value) {
    if (is_array($value)) {
    $child = $shipment->addChild($key);
    foreach($value as $k => $v) {
    $child->addChild($k, $v);
    }
    } else {
    $shipment->addChild($key, $value);
    }
    }

    // cURL ile SOAP isteği gönder
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://testyurticikargo.com.tr/ShippingOrderDispatcherServices/createShipment");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml->asXML());
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Content-Length: " . strlen($xml->asXML()), "SOAPAction: \"http://testyurticikargo.com.tr/ShippingOrderDispatcherServices/createShipment\""));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if ($response === false) {
    echo "İstek başarısız oldu veya beklenmeyen bir hata oluştu.". curl_error($ch);
    } else {
    // Yanıtı işle
    $xml_response = simplexml_load_string($response);
    var_dump($xml_response);
    }

    curl_close($ch);
