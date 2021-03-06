<?php

class HightFrequencyCryptoTrading {

    use HightFrequencyCrypto;

    CONST CONF = 'all.json';
    CONST GET_PROTO_FUNC_SUFFIX = 'GetProto';
    CONST HTML_HISTORY = '<ul>{items}</ul>';
    CONST HTML_HISTORY_TAG = '{items}';
    CONST HTML_DATE_FORMAT = 'd/m/Y H:m:s';
    CONST HTML_HIDE = array('classeName', 'classeType', 'classParent', 'id', 'historyAuditCreate',
        'historyAauditAdded', 'historyAuditRemoved', 'historyRole', 'historyStatus', 'historyAccount',
        'words', 'crypted', 'connected', 'keyUser', 'token', 'tokenAccess', 'login', 'accesstoken',
        'keyService');

    public static $resources;
    public static $histories;
    public static $params;
    public static $response;
    public static $html;
    public static $refs;

    public static function  build()
    {

        $input = json_decode(file_get_contents(self::CONF));
        self::$params = $input->params;
        self::$resources = $input->resources;
        self::$histories = $input->histories;
        self::$response = $input->response;
        self::$refs = self::$response->result->data->refs;
        self::$refs->currency->list = json_decode(file_get_contents(self::$currenciesUrl));
    }

    public static function typeNameToType($type) {

        if(substr($type, -1) === 'y') {

            $type = substr($type, 0, -1).'ie';
        }
        return $type.'s';
    }

    public static function itemGetProto($item) {

        $obj = new stdClass();

        if(isset($item->classeType) === true) {

            $type = self::typeNameToType($item->classeType);
            $clName = $item->classeName;

            if(isset(self::$refs->$clName) === true) {

                foreach(self::$refs->$clName->list as $clInstence){

                    if($clInstence->id === $item->id){

                        foreach ($clInstence as $k => $v) $item->$k = $v;
                    }
                }
            }
        }
        if(isset($item->classeType) === true) {

            $type = self::typeNameToType($item->classeType);
            $clName = $item->classeName;
            $cl = self::$$type->$clName;
            $clParentName = $cl->classParent;
            $typeParent = $type;

            if($clParentName !== false) {

                $clParent = self::$$typeParent->$clParentName;

                foreach($clParent as $k => $v) $obj->$k = $v;
            }
            foreach($cl as $k => $v) $obj->$k = $v;
        }
        foreach($item as $k => $v) $obj->$k = $v;

        foreach($obj as $k => $v) {

            if(isset($v->classeName) === true) $obj->$k = self::itemGetProto($v);
        }
        return $obj;
    }

    public static function html($item, $date = '', $name = '', $items = '') {

        if(is_object($item) === false && is_array($item) === false) {

            if(in_array($item, self::HTML_HIDE) !== false) return null;

            return $item;
        }
        if(isset($item->classeName) === true &&
            in_array($item->classeName, self::HTML_HIDE) !== false) return null;

        if(isset($item->date) === true && $item->date !== 0) $date = date(self::HTML_DATE_FORMAT, $item->date);
        if(isset($item->name) === true) $name = '<strong>'.$item->name.'</strong>';

        if(is_array($item) === true) {

            foreach($item as $k => $v) $items .= '<li>'.self::html($v).'</li>'."\n";

            return '<ul>'.$items.'</ul>'."\n";
        }
        $html = '<p>'.$name.' '.$date.'</p>'."\n";

        foreach($item as $k => $v) {

            if (is_null($v) === true || empty($v) === true) continue;

            if ($k === 'name' || $k === 'date') continue;

            if (in_array($k, self::HTML_HIDE) !== false) continue;

            $v = self::html($v) . "\n";

            if (isset($v->name) === true || isset($v->date)) {

                $items .= '<li>'.$v.'</li>'."\n";
            }
            elseif(is_numeric($k) === false) {

                $items .= '<li>'.$k.': '.$v.'</li>'."\n";
            }
            else {

                $items .= '<li>'.$v.'</li>'."\n";
            }
        }
        return $html.'<ul>'.$items.'</ul>'."\n";
    }

    public static function load($html = self::HTML_HISTORY, $itemsTag = self::HTML_HISTORY_TAG, $items = '') {

        HightFrequencyCryptoTrading::build();

        $account = self::$response->result->data->context->account;
        $account = self::itemGetProto($account);

        $items .= self::html($account);

        $historySaving = self::$response->result->data->context->historySaving->list;
        $historySaving = self::itemGetProto($historySaving);


        foreach($historySaving->list as $k => $saving){

            $url = str_replace('{id}', $saving->amount->currency->id, self::CONF_CURRENCY_UPDATE_URL);
            $url = str_replace('{symbol}', $saving->amountStart->currency->symbol, $url);
            $update = json_decode(file_get_contents($url));

            // $historySaving->list[$k]->amountUpdate = $saving->amount;
            // $historySaving->list[$k]->amountUpdate->currency = $update;
        }
        $items .= self::html($historySaving);

        self::$html = str_replace($itemsTag, $items, $html);

        return true;
    }
}