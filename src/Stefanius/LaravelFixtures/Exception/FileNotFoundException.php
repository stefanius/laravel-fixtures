<?php

namespace Stefanius\LaravelFixtures\Exception;

use Exception;

class FileNotFoundException extends \Exception
{
    protected $message = 'The file "%s" does not exists.';

    public function __construct($filename, \Exception $previous = null)
    {
        parent::__construct(sprintf($this->message, $filename), 9001, $previous);
    }
}
