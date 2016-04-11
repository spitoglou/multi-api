<?php namespace Spitoglou\MultiApi;

class HTMLArray extends TransformArray
{
    public function arrayToTable($data, $class = '')
    {

        $flat = false;
        $t = '<table' . $class . ' border="1">';
        $i = 0;

        foreach ($data as $row) {
            if (!is_array($row)) {
                $flat = true;
                break;
            }
            $i++;
            //display headers
            if ($i === 1) {
                $t .= '<thead><tr>';
                foreach ($row as $key => $value) {

                    $header = ucwords($key);
                    $t .= "<th>{$header}</th>";
                }
                $t .= '</tr></thead>';
            }

            //display values
            if ($i === 1) {
                $t .= '<tbody>';
            }
            $t .= '<tr>';
            foreach ($row as $key => $value) {

                if (!is_array($value)) {
                    $t .= "<td>{$value}</td>";
                } else {
                    $t .= '<td>Array</td>';
                }

            }
            $t .= '</tr>';
        }
        if ($flat) {
            $t .= '<thead><tr>';
            foreach (array_keys($data) as $header) {
                $t .= "<th>{$header}</th>";
            }
            $t .= '</tr></thead>';
            $t .= '<tbody><tr>';
            foreach (array_values($data) as $value) {
                if (is_array($value)) {
                    $t .= '<td>Array</td>';
                } else {
                    $t .= "<td>{$value}</td>";
                }
            }
            $t .= '</tr>';
        }
        $t .= '</tbody>';
        $t .= '</table>';
        return $t;
    }
}