<?php namespace Spitoglou\MultiApi;

use Illuminate\Http\Request;

class Sender
{
    protected $acceptHeader;
    protected $cache;
    protected $statusCode;
    /**
     * @var array
     */
    private $arrayToSend;

    /**
     * Sender constructor.
     *
     * @param Request $request
     * @param array $res
     * @param string $cache
     */
    public function __construct(array $res = [], $cache = '30')
    {
        $request = request();
        $this->acceptHeader = explode('/', $request->header('Accept'))[1];
        if (count($res) === 0) {
            abort(404, 'Empty Resultset');
        }
        $this->arrayToSend = $res;
        $this->cache = $cache;
    }

    public static function test()
    {
        return 'test';
    }

    public function sendError($statusCode)
    {
        \Log::error('API Error: ' . serialize($this->arrayToSend));
        return $this->finalSend($statusCode);
    }

    /**
     * finalSend
     *
     * @param int $statusCode
     * @return array|bool|string
     */
    public function finalSend($statusCode = 200)
    {
        if ($this->statusCode) {
            $statusCode = $this->statusCode;
        }
        $headers = [];
        $headers['X-Powered-By'] = 'Computer Solutions Web Services';
        $headers['Access-Control-Allow-Origin'] = '*';
        $headers['Access-Control-Expose-Headers'] = 'X-Number-Of-Results, X-Powered-By, X-Error-Description';
        $headers['Cache-Control'] = 'max-age=' . $this->cache . ',s-maxage=' . $this->cache . ' ,must-revalidate';
        switch ($this->acceptHeader) {
            case 'json':
                $result = $this->arrayToSend;
                break;
            case 'custom+xml':
                $headers['Content-Type'] = 'application/xml';
                $array2XML = new XmlArray($this->arrayToSend);
                $result = $array2XML->createXmlFromArray('results');
                break;
            default:
                $array2HTML = new HTMLArray($this->arrayToSend);
                $result = $array2HTML->arrayToTable($this->arrayToSend);
                break;
        }
        return response($result, $statusCode, $headers);
    }

}