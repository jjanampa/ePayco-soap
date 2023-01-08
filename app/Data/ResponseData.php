<?php

namespace App\Data;

class ResponseData
{
    /**
     * @var bool
     */
    public $success;

    /**
     * @var string
     */
    public $cod_error;

    /**
     * @var string
     */
    public $message_error;

    /**
     * @var array
     */
    public $data;

    public function __construct(bool $success, string $cod_error, string $message_error, array $data)
    {

        $this->success = $success;
        $this->cod_error = $cod_error;
        $this->message_error = $message_error;
        $this->data = $data;
    }

}