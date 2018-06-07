<?php

namespace Phalconify\Application\Rest\Controllers;

use Phalconify\Application\Rest\Http\Response\Json as JsonResponse;

/**
 * Implements the base for a CRUD controller.
 * - Create
 * - Read
 * - Update
 * - Delete.
 *
 * With all the endpoints necessary to perform these taks automatically.
 *
 * @author armonb
 */
abstract class CRUD extends Base
{
    /**
     * Returns the collection to use in this CRUD controller.
     *
     * @return string
     */
    abstract protected function getCollection();

    /**
     * Get all records based on query data.
     *
     * Request Method: GET
     */
    public function getAll()
    {
        echo 'getll'; die;
        $collection = $this->getCollection();
        $records = $collection::fetch($this->query);
        $totalRecords = $collection::total($this->query);

        $payload = [
            'totalRecords' => $totalRecords,
            'pageLimit' => $collection::getLimit($this->query),
            'records' => $records,
        ];

        return JsonResponse::success([], $payload);
    }

    /**
     * Get one record based on id.
     *
     * Request Method: GET
     */
    public function get($documentId)
    {
        $collection = $this->getCollection();
        $record = $collection::findById($documentId);
        if ($record) {
            return JsonResponse::success([], $record);
        }

        return JsonResponse::error([JsonResponse::ERROR_RECORD_NOT_FOUND]);
    }

    /**
     * Create a record.
     *
     * Request Method: POST
     */
    public function create()
    {
        $collection = $this->getCollection();
        if (is_string($collection) === true) {
            $document = new $collection();
        } else {
            $document = $collection;
        }

        return $this->save($document);
    }

    /**
     * Updates a record based on id.
     *
     * Request Method: PUT
     */
    public function update($documentId)
    {
        $collection = $this->getCollection();
        $document = $collection::findById($documentId);
        if ($document) {
            return $this->save($document);
        }

        return JsonResponse::error([JsonResponse::ERROR_RECORD_NOT_FOUND]);
    }

    /**
     * Deletes a record based on the id.
     *
     * Request Method: DELETE
     */
    public function delete($documentId)
    {
        $collection = $this->getCollection();
        $document = $collection::findById($documentId);

        return $this->remove($document);
    }
}
