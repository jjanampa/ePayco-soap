<?php

namespace App\Http\Controllers;

use App\Data\ResponseData;
use App\Data\UserData;
use App\Soap\WalletSoap;

class WalletController extends \KDuma\SoapServer\AbstractSoapServerController
{
    protected function getService(): string
    {
        return WalletSoap::class;
    }

    protected function getEndpoint(): string
    {
        return route('my_soap_server');
    }

    protected function getWsdlUri(): string
    {
        return route('my_soap_server.wsdl');
    }

    protected function getClassmap(): array
    {
        return [
            'UserData' => UserData::class,
            'ResponseData' => ResponseData::class,
        ];
    }
}