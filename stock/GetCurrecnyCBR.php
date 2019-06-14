<?php
    class GetCurrencyCBR{
        public $ValuteUSD=0;
        public $ValuteEUR=0;
        function __construct()
        {
            $doc=simplexml_load_file("http://www.cbr.ru/scripts/XML_daily.asp");
            foreach ($doc->Valute as $ValuteOne) {
                switch ($ValuteOne->CharCode) {
                    case "USD":
                        $this->ValuteUSD = floatval(str_replace(",",".",$ValuteOne->Value))+3;
                        break;
                    case "EUR":
                        $this->ValuteEUR = floatval(str_replace(",",".",$ValuteOne->Value))+3;
                        break;
                };
            }
        }
    }
?>