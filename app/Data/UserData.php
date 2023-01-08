<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class UserData extends Data
{
    /**
     * @var string
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @param string $first_name
     * @param string $last_name
     */
    public function __construct(string $first_name, string $last_name)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }


}