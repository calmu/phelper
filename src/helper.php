<?php
/**
 * 一些通用函数库
 * @author CalvinHuang huang_calvin@163.com
 * @time 2021-04-07
 * 
 */

use Calphelper\Arr;

if( ! function_exists('dump')) {
	/**
	 * 格式化打印变量
	 * @param mixed ...$args
	 */
	function dump(...$args)
	{
		echo is_cli() ? PHP_EOL : '<pre>';
		foreach ($args as $arg) {
			var_dump($arg);
		}
		echo is_cli() ? PHP_EOL : '</pre>';
	}
}
if( ! function_exists('dd')) {
	/**
	 * 格式化打印变量并结束程序
	 * @param mixed ...$args
	 */
	function dd(...$args)
	{
		call_user_func_array('dump', $args);
		exit(0);
	}
}
if( ! function_exists('is_cli')) {
	/**
	 * 判断是否是命令行环境
	 * @return bool
	 */
	function is_cli()
	{
		return PHP_SAPI == 'cli';
	}
}
if( ! function_exists('view_type_list')) {
	/**
	 * 简易返回列表的值
	 * @param array $list
	 * @param $type
	 * @param bool $flag
	 * @return mixed|string
	 */
	function view_type_list(array $list, $type, $flag=FALSE)
	{
		$false = 'woshifalse';
		if(is_array($type)) list($type, $field) = $type;
		$value = $list[$type] ?? $false;
		isset($field) && $value = $list[$type][$field] ?? $false;
		if($value === $false) return $type;
		$flag && $value .= "({$type})";
		return $value;
	}
}
if( ! function_exists('view_hide_text')) {
	/**
	 * 简易返回列表的值
	 * @param array $list
	 * @param $type
	 * @param bool $flag
	 * @return mixed|string
	 */
	function view_hide_text($text, array $conf = [])
	{
		$str = '<div class="hide-text">'. $text .'</div>';
		return $str;
	}
}

if( ! function_exists('view_hide_text_func')) {
	/**
	 * 回调简易返回列表的值
	 * @param $func
	 * @param array $argsArr
	 * @param array $conf
	 * @return mixed|string
	 */
	function view_hide_text_func($func, array $argsArr, array $conf = [])
	{
		$str = call_user_func_array($func, $argsArr);
		return view_hide_text($str, $conf);
	}
}

if( ! function_exists('view_fill_array')) {
	/**
	 * 简易填充默认初始值数据
	 * @param $array_header
	 * @param $array_body
	 * @param int $default
	 */
	function view_fill_array($array_header, &$array_body, $default = 0)
	{
		foreach ($array_header as $ahk =>$ahv)
		{
			isset($array_body[$ahk]) || $array_body[$ahk] = $default;
		}
	}
}

if( ! function_exists('parse_date')) {
	/**
	 * 转换为时间对象，只能传入时间格式字符串，不能传入秒时间戳和毫秒时间戳
	 * @param string|int $dateStr 注意：如果传入带时区的ISO8601等格式的时间格式，那么参数的timezone将变成解析之后设置时区用
	 * @param string|DateTimeZone $timeZone 可以使用UTC、Asia/Shanghai、+08:00等格式解析不带时区时间格式字符串
	 * @return DateTime
	 * @throws Exception
	 */
	function parse_date($dateStr, $timeZone = null): DateTime
	{
		if ( ! $timeZone instanceof DateTimeZone) {
			$timeZone = empty($timeZone) ? null : new DateTimeZone($timeZone);
		}
		$dateTimeObj = new DateTime($dateStr, $timeZone);
		parse_timezone($timeZone, $dateTimeObj);
		return $dateTimeObj;
	}
}

if( ! function_exists('parse_time')) {
	/**
	 * 转换为时间对象, 只能传入秒时间戳和毫秒时间戳
	 * @param $timestamp
	 * @param bool $isMilli 是否是毫秒级别，默认true
	 * @param string|DateTimeZone $timeZone 可以使用UTC、Asia/Shanghai、+08:00等格式
	 * @return DateTime
	 * @throws Exception
	 */
	function parse_time($timestamp, bool $isMilli=true, $timeZone = null): DateTime
	{
		$timeZone = parse_timezone($timeZone);

		if( ! $isMilli) {
			$dateTimeObj = date_create_from_format('U', strval($timestamp));
		} else {
			$dateTimeObj = date_create_from_format('U.v', strval(strpos($timestamp, '.') !== false ? $timestamp : super_round($timestamp, 1000, 3)));
		}
		$dateTimeObj->setTimezone($timeZone);
		return $dateTimeObj;
	}
}

if( ! function_exists('iso8601_format')) {
	/**
	 * 将时间对象转为iso8601格式
	 * @param DateTime $dateTime
	 * @param bool $isMilli 是否是毫秒级别，默认true
	 * @param string|DateTimeZone $timeZone 可以使用UTC、Asia/Shanghai、+08:00等格式
	 * @return string
	 * @throws Exception
	 */
	function iso8601_format(DateTime $dateTime, bool $isMilli = true, $timeZone = ''): string
	{
		// 默认使用本地时区
		if ( ! empty($timeZone)) {
			parse_timezone($timeZone, $dateTime);
		}
		if($isMilli) {
			return time_format_RFC3339EXTEND($dateTime);
		}
		return $dateTime->format(DATE_ATOM);
	}
}

if ( ! function_exists('time_format_RFC3339EXTEND')) {
	/**
	 * 将时间对象转为DATE_RFC3339_EXTENDED格式
	 * @param DateTime $dateTime 时间对象
	 * @param string|DateTimeZone $timeZone 可以使用UTC、Asia/Shanghai、+08:00等格式
	 * @return string
	 * @throws Exception
	 */
	function time_format_RFC3339EXTEND(DateTime $dateTime,  $timeZone = ''): string
	{
		if ( ! empty($timeZone)) {
			parse_timezone($timeZone, $dateTime);
		}
		return $dateTime->format(DATE_RFC3339_EXTENDED);
	}
}

if( ! function_exists('parse_timezone')) {
	/**
	 * 解析时区
	 * @param string|DateTimeZone $timeZone 可以使用UTC、Asia/Shanghai、+08:00等格式
	 * @param DateTime|null $dateTime
	 * @return DateTimeZone
	 * @throws Exception
	 */
	function parse_timezone($timeZone = null, DateTime $dateTime = null): DateTimeZone
	{
		if ( ! $timeZone instanceof DateTimeZone) {
			$timeZone = empty($timeZone) ? new DateTimeZone(date_default_timezone_get()) : new DateTimeZone($timeZone);
		}
		if ($dateTime instanceof DateTime) {
			$dateTime->setTimezone($timeZone);
		}
		return $timeZone;
	}
}

if( ! function_exists('super_time')) {
	/**
	 * 智能时间转换,如果传入带有时区的
	 * @param string|int $time 注意：如果传入带时区的ISO8601等格式的时间格式，那么参数的timezone将变成解析之后设置时区用
	 * @param string|DateTimeZone $timeZone 可以使用UTC、Asia/Shanghai、+08:00等格式解析不带时区时间格式字符串
	 * @param bool $isMilli 是否是毫秒级别，默认true
	 * @return DateTime
	 * @throws Exception
	 */
	function super_time($time, $timeZone = null, bool $isMilli=true): DateTime
	{
		if ( ! is_numeric($time)) {
			$dateTimeObj = parse_date($time, $timeZone);
		} else {
			$dateTimeObj = parse_time($time, $isMilli, $timeZone);
		}
		return $dateTimeObj;
	}
}

if ( ! function_exists('super_round')) {
	/**
	 * ++++++++++++++++
	 *  获得除数round
	 * ++++++++++++++++
	 *
	 * @param $divisor
	 * @param null $dividend
	 * @param int $precision
	 * @param int $person
	 * @param bool $filling
	 * @return float
	 *
	 */
	function super_round($divisor, $dividend = null, int $precision = 0, int $person = 1, bool $filling = true)
	{
		if ($divisor != 0 && $dividend != 0) {
			$res = $divisor / $dividend;
		} else {
			$res = 0;
		}
		if ($person != 1) {
			$res *= $person;
		}
		$res = round($res, $precision);
		if ($filling && $precision) {
			$res .= '';
			$pointPos = strpos($res, '.');
			if ($pointPos !== false) {
				$subStr = substr($res, $pointPos + 1);
				$subStrLen = strlen($subStr);
				$precision > $subStrLen && $res .= str_repeat('0', $precision - $subStrLen);
			} else {
				$res .= '.' . str_repeat('0', $precision);
			}
		}
		return $res;
	}
}

if ( ! function_exists('sample_rand_str')) {
	/**
	 * 简单的获取随机字符
	 * @param $lenth 随机字符长度
	 * @param $char_str 随机字符集
	 */
	function sample_rand_str($lenth = 4, $char_str = NULL)
	{
		$rand_str = '';
		if (is_null($char_str)) {
			$char_str = '';
			$ascii_arr = ['number' => [48,57], 'lower' => [97,122], 'upper' => [65,90]];
			foreach ($ascii_arr as $item) {
				for ($i = $item[0]; $i < $item[1]; ++$i) { 
					$char_str .= chr($i);
				}
			}
		}
		for ($j = 0; $j < $lenth; ++$j) { 
			$rand_str .= $char_str[mt_rand(0, strlen($char_str) - 1)];
		}
		return $rand_str;
	}
}
if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     */
    function value($value)
    {
        return $value instanceof \Closure ? $value() : $value;
    }
}
if (! function_exists('data_fill')) {
    /**
     * Fill in data where it's missing.
     *
     * @param mixed $target
     * @param array|string $key
     * @param mixed $value
     */
    function data_fill(&$target, $key, $value)
    {
        return data_set($target, $key, $value, false);
    }
}
if (! function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param null|array|int|string $key
     * @param null|mixed $default
     * @param mixed $target
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', is_int($key) ? (string) $key : $key);
        while (! is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (! is_array($target)) {
                    return value($default);
                }
                $result = [];
                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }
                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }
            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }
        return $target;
    }
}
if (! function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param mixed $target
     * @param array|string $key
     * @param bool $overwrite
     * @param mixed $value
     */
    function data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);
        if (($segment = array_shift($segments)) === '*') {
            if (! Arr::accessible($target)) {
                $target = [];
            }
            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments) {
                if (! Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || ! Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (! isset($target->{$segment})) {
                    $target->{$segment} = [];
                }
                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || ! isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];
            if ($segments) {
                $target[$segment] = [];
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }
        return $target;
    }
}