<?php

class HightFrequencyCryptoScheduled {

    use HightFrequencyCrypto;

    CONST SCHEDULED_FREQUENCY = 60*5;

    public static $resources;
    public static $histories;
    public static $params;
    public static $response;
    public static $html;
    public static $refs;

    public function __construct() {

        foreach(self::$currenciesBase as $currencyBase) {

            $url = str_replace('{symbol}', $currencyBase, self::$currenciesUrl);
            $currencies = file_get_contents($url);
            $dir = self::$currenciesHistoDir.$currencyBase.'/';

            if(is_dir($dir) === false) mkdir($dir, 0777);

            file_put_contents($dir.self::$currenciesHistoLastExt, $currencies);
            file_put_contents($dir.time().self::$currenciesHistoExt, $currencies);
        }
    }

    public static function run() {

        set_time_limit(0);

        new HightFrequencyCryptoScheduled();
        $t = time();

        while(true){

            if(time() < ($t + self::SCHEDULED_FREQUENCY)) sleep(self::SCHEDULED_FREQUENCY);

            new HightFrequencyCryptoScheduled();

            $t = time();
        }
    }
}
