<?php

namespace Phalconify\Application\Rest\Controllers;

use Phalconify\Application\Rest\Collections\Base as BaseCollection;
use Phalconify\Application\Rest\Http\Response\Json as JsonResponse;

/**
 * Abstract implementation of a base controller.
 */
abstract class Base extends \Phalcon\Mvc\Controller
{
    /**
     * Query loaded from the request.
     *
     * @var array
     */
    protected $query;

    /**
     * Initializes the controller.
     */
    public function onConstruct()
    {
        $request = $this->getDI()->get('phalconify-request');
        $this->query = $request->getQueryData();
    }

    /**
     * Executes the save action over a document.
     *
     * @param BaseCollection $document
     *
     * @return string
     *                JSON response
     */
    protected function save(BaseCollection $document)
    {
        $request = $this->getDI()->get('phalconify-request');
        $document->loadFromBody($request);
        if (!$document->save()) {
            /* Get invalid fields */
            $invalidFields = [];
            foreach ($document->getMessages() as $msg) {
                $invalid = [
                    'field' => $msg->getField(),
                    'message' => $msg->getMessage(),
                ];
                array_push($invalidFields, $invalid);
            }

            $payload = ['invalidFields' => $invalidFields];

            return JsonResponse::error($document->getMessagesAsArray(), $payload);
        }

        return JsonResponse::success([], $document);
    }

    /**
     * Executes the remove action over a document.
     *
     * @param BaseCollection $document
     *
     * @return string
     *                JSON response
     */
    protected function remove(BaseCollection $document)
    {
        if (!$document->delete()) {
            return JsonResponse::error($document->getMessagesAsArray());
        }

        return JsonResponse::success([]);
    }
}
