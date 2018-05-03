<?php

namespace App\Http\Controllers;

use App\Components\Utilities\Responder;
use App\Components\Utilities\Transformer\TransformerHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Response success status with data.
     * @param Collection|Model|array|string|int $resource
     * @param string $message
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function response ($resource, $message = '')
    {
        // Check resource output empty.
        if (empty($resource) or (method_exists($resource, 'isEmpty') and $resource->isEmpty())) {
            return Responder::blank($message);
        }

        if (property_exists($this, 'transform') and $this->transform) {
            /**
             * Get transformer class for this controller.
             * @var Transformer $transformerClass
             */
            $transformerClass = TransformerHelper::getTransformerClass();

            // Transform resource if transformer class exists.
            if (class_exists($transformerClass)) {
                $resource = $transformerClass::transform($resource);
            }
        }

        // Response data with success status.
        return Responder::data($resource, $message);
    }
}
