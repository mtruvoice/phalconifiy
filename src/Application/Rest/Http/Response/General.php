<?php

namespace Phalconify\Application\Rest\Http\Response;

abstract class General extends \Phalcon\Http\Response
{
    /**
     * General parameters of the response.
     */
    const ERROR = 'error';
    const SUCCESS = 'success';

    /**
     * Default response codes
     */
    const CODE_OK = 200;
    const CODE_NOT_FOUND = 404;
    const CODE_DENIED = 403;
    const CODE_UNAUTHORIZED = 401;
    const CODE_BAD_REQUEST = 400;
    const CODE_SERVER_ERROR = 500;

    /**
     * Default error messages.
     */
    const ERROR_INIT = 'There was an error initialising.';
    const ERROR_MISSING_FIELDS = 'The following fields were missing from your request: ';
    const ERROR_NOT_CREATED = 'The record was not created. The following errors occured: ';
    const ERROR_NO_RESULTS = 'No results found.';
    const ERROR_RECORD_NOT_FOUND = 'Record not found.';
    const ERROR_DATA_MISSING = 'There was missing data in the request.';
    const ERROR_PERMISSION_DENIED = 'Permission denied.';
    const ERROR_UNAUTHORIZED = 'Authorization required';

    /**
     * Status of the response.
     *
     * @var string
     */
    protected $status;

    /**
     * {@inheritdoc}
     */
    public function __construct($content = null, $code = null, $status = null)
    {
        parent::__construct($content, $code, $status);
        $this->initialise();
    }

    /**
     * Initialises the default behaviour of all the responses.
     */
    public function initialise()
    {
        $config = $this->getDI()->getShared('phalconify-config');
        $openCors = true;
        if (isset($config->environment)) {
            if (isset($config->environment->cors)) {
                if (isset($config->environment->cors->allowOrigin)) {
                    $openCors = false;
                    if (is_array($config->environment->cors->allowOrigin) || is_object($config->environment->cors->allowOrigin)) {

                        $allowedOrigins = json_decode(json_encode($config->environment->cors->allowOrigin), true);
                        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

                        if (in_array($origin, $allowedOrigins)) {
                            $this->setHeader('Access-Control-Allow-Origin', $origin);
                        } else {
                            $this->setHeader('Access-Control-Allow-Origin', $allowedOrigins[0]);
                        }

                    } else {
                        $this->setHeader('Access-Control-Allow-Origin', $config->environment->cors->allowOrigin ?? '*');
                    }
                }
            }
        }

        if ($openCors) {
            $this->setHeader('Access-Control-Allow-Origin', $config->environment->cors->allowOrigin ?? '*');
        }

        $this->setHeader('Access-Control-Request-Method', $config->environment->cors->requestMethods ?? 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        $this->setHeader('Access-Control-Allow-Methods', $config->environment->cors->allowMethods ?? 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        $this->setHeader('Access-Control-Allow-Headers', $config->environment->cors->allowHeaders ?? 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
        $this->setHeader('Access-Control-Allow-Credentials', $config->environment->cors->allowCredentials ?? '');
    }

    /**
     * Checks if this response is a success.
     *
     * @return bool
     *              True if it's a success, false otherwise
     */
    public function isSuccess()
    {
        return $this->status == self::SUCCESS;
    }

    /**
     * Checks if this response is an error.
     *
     * @return bool
     *              True if it's an error, false otherwise
     */
    public function isError()
    {
        return !$this->isSuccess();
    }

    /**
     * Sets the status of the response.
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}
