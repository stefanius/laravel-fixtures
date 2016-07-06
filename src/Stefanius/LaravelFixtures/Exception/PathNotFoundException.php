<?php

namespace Stefanius\LaravelFixtures\Exception;

use Exception;

class PathNotFoundException extends \Exception
{
    protected $message = 'The path / dir "%s" does not exists.';

    /**
     * PathNotFoundException constructor.
     *
     * @param string    $filename
     * @param Exception $previous
     */
    public function __construct($filename, \Exception $previous = null)
    {
        parent::__construct(sprintf($this->message, $filename), 9001, $previous);
    }
}
