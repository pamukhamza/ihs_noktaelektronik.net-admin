<?php

namespace Gosas\Core\Settings;

require_once('core/enums/RequestMode.php');

use Gosas\Core\Enums\RequestMode;

class PosSettings
{
    public $requestUrl;
    public $requestMode;

    public $version = "512";
    public $provUserId = "PROVAUT";
    public $provUserId3DS = "NOKTA ELEKTRONİK";
    public $provUserPassword = "Dell28736.";
    public $userId = "PROVAUT";
    public $terminalId = "10273343";
    public $merchantId = "2381180";

    public $emailAddress = "eticaret@garanti.com.tr";
    public $ipAddress = "213.14.137.98";

    public $storeKey = "414b494e534f4654343434343038304b4f4e594134323432";
    public $threeDPaymentResultUrl = "https://www.noktaelektronik.net/admin/functions/banka/garanti/success.php";
    public $threeDPaymentErrorUrl = "https://www.noktaelektronik.net/admin/functions/banka/garanti/error.php";

    public function __construct($mode)
    {
        $this->requestMode = $mode;
    }

    public function GetRequestUrl()
    {
        if ($this->requestMode === RequestMode::Test) {
            $this->requestUrl = "https://sanalposprovtest.garanti.com.tr/VPServlet";
        } else {
            $this->requestUrl = "https://sanalposprov.garanti.com.tr/VPServlet";
        }

        return $this->requestUrl;
    }
}
