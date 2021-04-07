<?php
/**
 * 一些通用函数库
 * @author CalvinHuang huang_calvin@163.com
 * @time 2021-04-07
 * 
 */
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
if( ! function_exists('super_time')) {
	/**
	 * 智能时间转换
	 * @param $time
	 * @param string $timeZone
	 * @return DateTime
	 * @throws Exception
	 */
	function super_time($time, $timeZone = 'UTC')
	{
		$dateZone = new DateTimeZone($timeZone);
		if (!is_numeric($time)) {
			$timeObj = new DateTime($time, $dateZone);
		} else {
			$timeObj = new DateTime('now', $dateZone);
			$timeObj->setTimestamp($time);
		}
		return $timeObj;
	}
}