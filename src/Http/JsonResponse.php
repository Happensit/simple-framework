<?php

namespace Commty\Simple\Http;

/**
 * Class JsonResponse
 * @package commty\Http
 */
class JsonResponse extends Response
{
    /**
     * @var
     */
    protected $data;

    /**
     * JsonResponse constructor.
     * @param null $data
     * @param int $status
     * @param array $headers
     */
    public function __construct($data = null, $status = 200, $headers = [])
    {
        parent::__construct('', $status, $headers);

        if (is_null($data)) {
            $data = [];
        }

        $this->setJson($data);
        $this->setHeaders(['Content-Type', 'application/json']);
        $this->setContent($this->data);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setJson($data)
    {
        $this->data = json_encode(['status' => $this->getStatusCode()] + $data);

        return $this;
    }
}
