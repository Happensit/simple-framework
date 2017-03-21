<?php

namespace Commty\Simple\Exception;

/**
 * Interface ApiExceptionInterface
 * @package commty\Exception
 */
interface ApiExceptionInterface
{
    /**
     * @return integer
     */
    public function getStatusCode();
}
