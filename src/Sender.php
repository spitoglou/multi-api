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
                $array2XML = new XmlArray();
                $array2XML->setArray($this->arrayToSend);
                $result = $array2XML->saveArray('results');
                break;
            default:
                $result = $this->arrayToTable($this->arrayToSend);
                break;
        }
        return response($result, $statusCode, $headers);
    }

    /**
     * array_to_table
     *
     * @param      $data
     * @param bool|array $args
     * @return string
     */
    private function arrayToTable($data, $args = false)
    {
        if (!is_array($args)) {
            $args = array();
        }

        $class = false;
        $column_widths = false;
        $custom_headers = false;
        $format_functions = false;
        $nowrap_head = false;
        $nowrap_body = false;
        $capitalize_headers = false;
        foreach ([
                     'class',
                     'column_widths',
                     'custom_headers',
                     'format_functions',
                     'nowrap_head',
                     'nowrap_body',
                     'capitalize_headers'
                 ] as $key) {
            if (array_key_exists($key, $args)) {
                $$key = $args[$key];
            } else {
                $$key = false;
            }
        }
        if ($class) {
            $class = ' class="' . $class . '"';
        } else {
            $class = '';
        }
        if (!is_array($column_widths)) {
            $column_widths = array();
        }

        //get rid of headers row, if it exists (headers should exist as keys)
        if (array_key_exists('headers', $data)) {
            unset($data['headers']);
        }

        $t = '<table' . $class . ' border="1">';
        $i = 0;
        foreach ($data as $row) {
            $i++;
            //display headers
            if ($i == 1) {
                foreach ($row as $key => $value) {
                    if (array_key_exists($key, $column_widths)) {
                        $style = ' style="width:' . $column_widths[$key] . 'px;"';
                    } else {
                        $style = '';
                    }
                    $t .= '<col' . $style . ' />';
                }
                $t .= '<thead><tr>';
                foreach ($row as $key => $value) {
                    if (is_array($custom_headers) &&
                        array_key_exists($key, $custom_headers) &&
                        ($custom_headers[$key])
                    ) {
                        $header = $custom_headers[$key];
                    } elseif ($capitalize_headers) {
                        $header = ucwords($key);
                    } else {
                        $header = $key;
                    }
                    if ($nowrap_head) {
                        $nowrap = ' nowrap';
                    } else {
                        $nowrap = '';
                    }
                    $t .= '<td' . $nowrap . '>' . $header . '</td>';
                }
                $t .= '</tr></thead>';
            }

            //display values
            if ($i == 1) {
                $t .= '<tbody>';
            }
            $t .= '<tr>';
            foreach ($row as $key => $value) {
                if (is_array($format_functions) &&
                    array_key_exists($key, $format_functions) &&
                    ($format_functions[$key])
                ) {
                    $function = $format_functions[$key];
                    if (!function_exists($function)) {
                        custom_die('Data format function does not exist: ' . htmlspecialchars($function));
                    }
                    $value = $function($value);
                }
                if ($nowrap_body) {
                    $nowrap = ' nowrap';
                } else {
                    $nowrap = '';
                }
                if (!is_array($value)) {
                    $t .= '<td' . $nowrap . '>' . $value . '</td>';
                } else {
                    $t .= '<td' . $nowrap . '>Array</td>';
                }

            }
            $t .= '</tr>';
        }
        $t .= '</tbody>';
        $t .= '</table>';
        return $t;
    }
}