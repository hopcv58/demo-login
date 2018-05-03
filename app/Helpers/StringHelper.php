<?php

if (! function_exists('e')) {
    /**
     * Alias for htmlentities.
     * @param string|int|bool|null $value
     * @return string
     */
    function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

/**
 * ################### URL FUNCTIONS ###########################
 */

/** Build url string from array data;
 * @param array $urlData
 * @return string
 */
function url_build($urlData)
{
    $url = '';
    if (isset($urlData['host'])) {
        $url .= $urlData['scheme'] . '://';
        if (isset($urlData['user'])) {
            $url .= $urlData['user'];
            if (isset($urlData['pass'])) {
                $url .= ':' . $urlData['pass'];
            }
            $url .= '@';
        }
        $url .= $urlData['host'];
        if (isset($urlData['port'])) {
            $url .= ':' . $urlData['port'];
        }
    }
    $url .= $urlData['path'];
    if (isset($urlData['query'])) {
        $url .= '?' . $urlData['query'];
    }
    if (isset($urlData['fragment'])) {
        $url .= '#' . $urlData['fragment'];
    }

    return $url;
}


/**
 * Add or update a param to url.
 * @param $url
 * @param array|string $paramName
 * @param string|int $paramValue
 * @return string
 */
function url_param($url, $paramName, $paramValue = '')
{
    $urlData = parse_url($url);

    if (!isset($urlData['query'])) {
        $urlData['query'] = '';
    }

    $params = array();
    parse_str($urlData['query'], $params);
    if (is_array($paramName)) {
        $params = $paramName;
    } else {
        $params[$paramName] = $paramValue;
    }
    $urlData['query'] = http_build_query($params);

    return url_build($urlData);
}

/**
 * ################### STRING FUNCTIONS ###########################
 */

/**
 * 長い文字列を、指定した長さにトリムして返す。
 * 文字列が引数で指定した長さよりも長ければ、末尾に ... を付加する。.
 * @param string $str トリム対象の長い文字列
 * @param int $length 文字数
 * @param string $suffix
 * @return string sub string
 */
function mb_subplain($str, $length, $suffix = '...')
{
    $result = strip_tags($str);

    if ($length < mb_strlen($result)) {
        $result = mb_substr($result, 0, $length) . $suffix;
    }

    return $result;
}

/**
 * Esc vietnamese
 * @param string $str
 * @return string
 */
function esc_vi($str)
{
    $unicode = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D' => 'Đ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    ];

    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }

    $str = preg_replace('/[^\d\w-]/', '-', $str);;

    return $str;
}

/**
 * Convert string to seo url string
 * @param tring $str
 * @return string
 */
function str_seo($str)
{
    $str = esc_vi($str);
    return strtolower(str_replace(' ', '-', $str));
}

/**
 * ################### DATE FUNCTIONS ###########################
 */

/**
 * Format date by format string.
 * @param string $date
 * @param string $format
 * @return string
 */
function str_time ($date, $format = 'Y-m-d H:i:s')
{
    return date($format, strtotime($date));
}

/**
 * Remove all while space in string.
 * @param string $string
 * @return string
 */
function str_esc_space ($string)
{
    return preg_replace('/\s+/', '', $string);
}

/**
 *  Check string target is begin with match.
 * @param string $needle
 * @param string $haystack
 * @return bool
 */
function begin_with($needle, $haystack)
{
    return (substr($haystack, 0, strlen($needle)) === $needle);
}

/**
 *  Check string target is end with match.
 * @param string $needle
 * @param string $haystack
 * @return bool
 */
function end_with($needle, $haystack)
{
    $length = strlen($needle);

    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

/**
 * String binding params.
 * @param string $content
 * @param array $replaces
 * @param string $delimiter
 * @return string
 */
function str_bind ($content, array $replaces = [], $delimiter = ':')
{
    if (empty($replaces)) {
        return $content;
    }

    // Soft array by length DESC → Replace greater string first.
    $results = [];

    foreach ($replaces as $key => $value) {
        $results[$key] = mb_strlen($key) * -1;
    }

    asort($results, SORT_REGULAR);

    foreach (array_keys($results) as $key) {
        $results[$key] = $replaces[$key];
    }
    // End soft.

    // Replace.
    foreach ($results as $key => $value) {
        $content = str_replace(
            [$delimiter.$key, $delimiter.strtolower($key), $delimiter.strtoupper($key), $delimiter.ucfirst($key)],
            [$value, strtolower($value), strtoupper($value), ucfirst($value)],
            $content
        );
    }

    return $content;
}

/**
 * @param string $sql
 * @param array $bindings
 * @return string
 */
function sql_bind ($sql, array $bindings)
{
    // Bind by param name.
    {
        // Convert values of bindings to string or int.
        foreach ($bindings as &$value) {
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');

            } elseif ($value === false) {
                $value = 0;
            }
        }

        $sql = str_bind($sql, $bindings, ':');
    }

    // Bind by char `?`.
    {
        $needle = '?';

        foreach ($bindings as $replace) {
            $pos = strpos($sql, $needle);

            if ($pos !== false) {
                if (gettype($replace) === "string") {
                    $replace = ' "'.addslashes($replace).'" ';

                }

                $sql = substr_replace($sql, $replace, $pos, strlen($needle));
            }
        }
    }

    return $sql;
};