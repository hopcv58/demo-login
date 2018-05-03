<?php
/**
 * Created by ASUS.
 * Date: 7/18/2017
 * Time: 8:50 AM
 */

namespace App\Components\Utilities\Transformer;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AbstractTransformer
 * @package App\Components\Utilities\Transformer
 *      Ex Using: ClientTransformer::transform($resource); // Auto
 *                ClientTransformer::transformIndex($resource); // Not auto
 *                ClientTransformer::index($resource); // Not auto
 */
abstract class AbstractTransformer
{
    const PREFIX_METHOD = 'transform';

    /**
     * AbstractTransformer constructor.
     */
    private final function __construct()
    {
        // Disable create new Transform by command new.
    }

    /**
     * Transform a resource.
     * @param Collection|Model|array|string|integer $resource
     * @param string $method
     * @param bool $methodWarning
     * @return Collection|Model|array|string|integer|mixed
     * @throws \BadMethodCallException
     */
    public static function transform ($resource, $method = '', $methodWarning = false)
    {
        if (empty($resource)) {
            return $resource;
        }

        $transform = new static();

        if (empty($method)) {
            $method = TransformerHelper::getTransformerMethod();
        }

        if (method_exists($transform, $method)) {
            $resource = $transform->doTransform($resource, $method);
        } else {
            if ($methodWarning) {
                throw new \BadMethodCallException('Method '. $method .' not exists in class '. static::class .'.');
            }
        }

        return $resource;
    }

    /**
     * Transform a resource by magic method.
     * @param string $name
     * @param array $arguments
     * @return array|Model|Collection|int|mixed|string
     */
    public static function __callStatic($name, $arguments)
    {
        if (begin_with(static::PREFIX_METHOD, $name)) {
            $method = substr($name, strlen(static::PREFIX_METHOD));

            $name = lcfirst($method);
        }

        return self::transform(reset($arguments), $name, true);
    }

    /**
     * Convert resource to transformed.
     * @param Collection|Model|array|string|integer $resource
     * @param string $method
     * @return Collection|Model|array|string|integer|mixed
     */
    private function doTransform ($resource, $method)
    {
        // Type list item.
        if (
            $resource instanceof Collection
            or is_array_result($resource)
            or (is_object($resource) and is_array_result((array) $resource))
        ) {
            $output = [];

            foreach ($resource as $key => $item) {
                $output[$key] = $this->transformItem($item, $method, $key);
            }

            return $output;
        }

        // Type an item: Model, array, string, integer
        return $this->transformItem($resource, $method);
    }

    /**
     * Do transform.
     * @param Collection|Model|array|string|integer $resource
     * @param string $method
     * @param string|int $key
     * @return Collection|Model|array|string|integer|mixed
     */
    private function transformItem ($resource, $method, &$key = null)
    {
        return $this->{$method}($resource, $key);
    }
}