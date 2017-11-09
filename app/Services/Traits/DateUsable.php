<?php

namespace App\Services\Traits;

trait DateUsable
{

    protected $date;
    protected $time;
    protected $date_object;

    public function isDate($date_buf) {
        try {
            $this->setDate($date_buf);
        } catch (\Exception $exc) {
            return false;
        }
        return true;
    }

    public function setDate($date_time_buf) {
        if (
                empty($date_time_buf) ||
                $date_time_buf === '0000-00-00' ||
                $date_time_buf === '00000000'
        )
        {
            $this->date_object = null;
            return $this;
        }

        $array    = $this->parseDateTime($date_time_buf);
        $date_buf = $array['date'];
        $time_buf = $array['time'];

        if (strpos($date_buf, '-') !== false || strpos($date_buf, '/') !== false)
        {
            $date_array = $this->setYearMonthDateIncludeHyphoneOrSlash($date_buf);
        }
        else
        {
            $date_array = $this->setYearMonthDate($date_buf);
        }

        if (!checkdate($date_array['month'], $date_array['day'], $date_array['year']))
        {
            throw new \Exception("日付型以外のものが指定されました。（引数：{$date_buf}）");
        }

        $time_array        = $this->setTime($time_buf);
        $d                 = "{$date_array['year']}-{$date_array['month']}-{$date_array['day']}";
        $t                 = "{$time_array['hour']}:{$time_array['min']}:{$time_array['sec']}";
        $this->date_object = new \DateTime("{$d} {$t}");
        return $this;
//        return new \DateTime("{$d} {$t}");
    }

    public function getDate() {
        return $this->date_object;
    }

    private function parseDateTime($date_time_buf) {
        $date = null;
        $time = null;
        if (strpos($date_time_buf, ' ') !== false)
        {
            $tmp = explode(' ', $date_time_buf);
            if (count($tmp) !== 2)
            {
                throw new \Exception("日付型以外のものが指定されました。（引数：{$date_time_buf}）");
            }
            return [
                'date' => $tmp[0],
                'time' => $tmp[1],
            ];
        }
        else
        {
            return [
                'date' => $date_time_buf,
                'time' => null,
            ];
        }
    }

    public function setTime($time_buf) {
        $time_array = $this->setHourTimeMinute($time_buf);
        return $time_array;
    }

    private function setYearMonthDateIncludeHyphoneOrSlash($date_buf) {
        if (strpos($date_buf, '-') !== false)
        {
            $date_array = explode('-', $date_buf);
        }
        elseif ((strpos($date_buf, '/') !== false))
        {
            $date_array = explode('/', $date_buf);
        }
        else
        {
            throw new \Exception("文字列が日付型ではないようです。（引数：{$date_buf}）");
        }

        if (count($date_array) !== 3)
        {
            $date_array[] = 1;
        }
        if (count($date_array) !== 3)
        {
            throw new \Exception("想定外のエラーが発生しました。（引数：{$date_buf}）");
        }

        foreach ($date_array as $d) {
            if (!is_numeric($d))
            {
                throw new \Exception("文字列が数字ではないようです。（引数：{$date_buf}）");
            }
        }
        $return_array = [
            'year'  => (int) $date_array[0],
            'month' => (int) $date_array[1],
            'day'   => (int) $date_array[2],
        ];
        return $return_array;
    }

    private function setYearMonthDate($date_buf) {
        if (!is_numeric($date_buf))
        {
            throw new \Exception("文字列が日付型ではないようです。（引数：{$date_buf}）");
        }

        if (mb_strlen($date_buf) === 6)
        {
            $date_buf .= '01';
        }

        if (mb_strlen($date_buf) !== 8)
        {
            throw new \Exception("文字数が日付型と一致しませんでした。（引数：{$date_buf}）");
        }

        $date_array = [
            'year'  => (int) mb_substr($date_buf, 0, 4),
            'month' => (int) mb_substr($date_buf, 4, 2),
            'day'   => (int) mb_substr($date_buf, 6, 2),
        ];
        return $date_array;
    }

    private function setHourTimeMinute($time_buf) {
        $time_array = [];
        if (empty($time_buf))
        {
            return ['hour' => 0, 'min' => 0, 'sec' => 0,];
        }

        if (strpos($time_buf, ':'))
        {
            $time_array = explode(':', $time_buf);
        }
        else
        {
            throw new \Exception("時刻型以外のものが指定されました。（引数：{$time_buf}）");
        }

        if (count($time_array) > 3)
        {
            throw new \Exception("配列が多すぎます。（引数：{$time_buf}）");
        }

        foreach ($time_array as $t) {
            if (!is_numeric($t))
            {
                throw new \Exception("時刻型でないようです。（引数：{$time_buf}）");
            }
        }

        $return_array['hour'] = (isset($time_array[0])) ? (int) $time_array[0] : 0;
        $return_array['min']  = (isset($time_array[1])) ? (int) $time_array[1] : 0;
        $return_array['sec']  = (isset($time_array[2])) ? (int) $time_array[2] : 0;

        return $return_array;
    }

}
