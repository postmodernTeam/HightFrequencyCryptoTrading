<?phpclass HightFrequencyCryptoTrading {    CONST CONF = 'all.json';    CONST GET_PROTO_FUNC_SUFFIX = 'GetProto';    CONST HTML_HISTORY = '<ul>{items}</ul>';    CONST HTML_HISTORY_TAG = '{items}';    CONST HTML_DATE_FORMAT = 'd/m/Y H:m:s';    CONST HTML_HIDE = array('classeName', 'classeType', 'classParent', 'id');    public static $resources;    public static $histories;    public static $params;    public static $response;    public static $html;    public function __construct()    {        $input = json_decode(file_get_contents(self::CONF));        self::$params = $input->params;        self::$resources = $input->resources;        self::$histories = $input->histories;        self::$response = $input->response;    }    public static function itemGetProto($item){        $obj = new stdClass();        $type = $item->classeType;        $func = strtolower($type).self::GET_PROTO_FUNC_SUFFIX;        $clName = $item->classeName;        $cl = self::$item->$clName;        $clParentName = $cl->classParent;        if($clParentName !== false) {            $clParent = self::$item->$clParentName;            foreach($clParent as $k => $v) {                $obj->$k = $v;            }        }        foreach($cl as $k => $v) {            $obj->$k = $v;        }        foreach($obj as $k => $v) {            if(isset($v->classeName) === true){                $obj->$k = self::itemGetProto($v);            }        }        return $obj;    }    public static function html($item, $html = '', $date = '', $items = '') {        if(is_object($item) === false && is_array($item) === false) {            if(in_array($item, self::HTML_HIDE) !== false){                return null;            }            return $item;        }        if(is_array($item) === false) {            foreach($item as $k => $v){                if(is_null($v) === true || empty($v) === true){                    continue;                }                $v = html($v)."\n";                $items .= '<li>'.$k.': '.$v.'</li>'."\n";            }            return '<ul>'.$items.'</ul>'."\n";        }        if(isset($item->date) === true) {            $date = date(self::HTML_DATE_FORMAT, $item->date);        }        $html .= '<p><strong>'.$item->name.'</strong> '.$date.'</p>'."\n";        foreach($item as $k => $v) {            if(is_null($v) === true || empty($v) === true){                continue;            }            $v = html($v)."\n";            $items .= '<li>'.$k.': '.$v.'</li>'."\n";        }        $html .= '<ul>'.$items.'</ul>'."\n";        return $html;    }    public static function load($html = self::HTML_HISTORY, $itemsTag = self::HTML_HISTORY_TAG, $items = '') {        new HightFrequencyCryptoTrading();        $account = self::$response->result->data->context->account;        $historySaving = self::$response->result->data->context->historySaving->list;        $account = self::itemGetProto($account);        $historySaving = self::itemGetProto($historySaving);        $items .= self::html($account);        $items .= self::html($historySaving);        self::$html = str_replace(self::$itemsTag, $items, $html);        return true;    }}HightFrequencyCryptoTrading::load();?><!doctype html><html lang="en"><head>    <meta charset="utf-8">    <title>Hight Frequency Crypto Trading</title>    <meta name="description" content="Hight Frequency Crypto Trading">    <meta name="author" content="Nicolas Cantu"></head><body><h1>Hight Frequency Crypto Trading</h1><?php echo HightFrequencyCryptoTrading::$html; ?></body></html>