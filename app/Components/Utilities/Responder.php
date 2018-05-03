<?php
/**
 * Created by ASUS.
 * Date: 7/5/2017
 * Time: 2:14 PM
 */
namespace App\Components\Utilities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Request;
use App;

class Responder
{
    /**
     * Response Service Unavailable.
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function unauthorized ($message = '', array $header = [])
    {
        return self::response([], 401, 'unauthorized', $message, $header);
    }

    /**
     * Response Service Unavailable.
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function timeout ($message = '', array $header = [])
    {
        return self::response([], 408, 'request_timeout', $message, $header);
    }

    /**
     * Response Service Unavailable.
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function methodFalse ($message = '', array $header = [])
    {
        return self::response([], 405, 'method_not_allowed', $message, $header);
    }

    /**
     * Response Service Unavailable.
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function forbidden ($message = '', array $header = [])
    {
        return self::response([], 403, 'forbidden', $message, $header);
    }

    /**
     * Response Service Unavailable.
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function unavailable ($message = '', array $header = [])
    {
        return self::response([], 503, 'service_unavailable', $message, $header);
    }

    /**
     * Response Internal Server Error.
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function error ($message = '', array $header = [])
    {
        return self::response([], 500, 'internal_server_error', $message, $header);
    }

    /**
     * Response 404 not found.
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function notFound ($message = '', array $header = [])
    {
        return self::response([], 404, 'not_found', $message, $header);
    }


    /**
     * Response invalid.
     * @param array $errors
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function invalid (array $errors, $message = '', array $header = [])
    {
        return self::response($errors, 422, 'invalid', $message, $header);
    }

    /**
     * Response data.
     * @param array|object $data
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function data ($data, $message = '', array $header = [])
    {
        return self::response($data, 200, 'ok', $message, $header);
    }

    /**
     * Response empty.
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function blank ($message = '', array $header = [])
    {
        return self::response([], 200, 'no_content', $message, $header);
    }

    /**
     * Auto content type response by Accept header request.
     * @param array|object $data
     * @param int $status
     * @param string $code
     * @param string $message
     * @param array $header
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function response ($data = [], $status = 200, $code = 'ok', $message = '', array $header = [])
    {
        // Cast object to array.
        if ($data instanceof Collection or $data instanceof Model) {
            $data =  $data->toArray();
        }

        // Format body response.
        $content = static::body($data, $status, $code, $message);

        // Add more header response info.
        $header = static::header($header);

        // Response as access defined.
        foreach (Request::getAcceptableContentTypes() as $type) {
            switch ($type) {
                case 'application/xml':
                case 'text/xml':
                case 'xml':
                    return response()->xml($content, $status, $header);
                    break;

                case 'application/json':
                case 'application/x-javascript':
                case 'text/json':
                case 'text/x-json':
                case 'text/javascript':
                case 'text/x-javascript':
                case 'json':
                    return response()->json($content, $status, $header);
                    break;
            }
        }

        return response()->json($content, $status, $header);
    }

    /**
     * Format body response.
     * @param mixed $data
     * @param int $status
     * @param string $code
     * @param string $message
     * @return array
     */
    private static function body ($data = [], $status = 200, $code = 'ok', $message = '')
    {
        return [
            'meta' => [
                'status' => $status,
                'code' => $code,
                'message' => $message,
            ],
            'data' => $data
        ];
    }

    /**
     * Update header before response.
     * @param $header
     * @return array
     */
    private static function header ($header)
    {
        // TODO: updating...
        $header['Content-Language'] = App::getLocale();
        $header['X-Powered-By'] = 'Golftect Gdo';
        $header['X-Version'] = '1';

        return $header;
    }
}