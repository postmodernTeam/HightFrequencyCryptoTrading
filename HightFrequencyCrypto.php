<?php

trait HightFrequencyCrypto {

    public static $currenciesHistoDir = 'history/';
    public static $currenciesHistoLastExt = 'last.json';
    public static $currenciesHistoExt = '.json';
    public static $currenciesBase = array('AUD', 'BRL', 'CAD', 'CHF', 'CNY', 'EUR', 'GBP', 'HKD', 'IDR', 'INR', 'JPY', 'KRW', 'MXN', 'RUB');
    public static $currenciesUrl = 'https://api.coinmarketcap.com/v1/ticker/?convert={symbol}';
    public static $currenciesUrlUpdate = 'https://api.coinmarketcap.com/v1/ticker/{id}/?convert={symbol}';
}