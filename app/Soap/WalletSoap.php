<?php

namespace App\Soap;

use App\Data\ResponseData;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WalletSoap
{

    /**
     * @param string $nroDocument
     * @param string $name
     * @param string $email
     * @param string $cellphone
     * @return ResponseData
     */
    public function registerCustomer(string $nroDocument, string $name, string $email, string $cellphone): ResponseData
    {
        $params = get_defined_vars();
        try {
            $validator = Validator::make($params, [
                'nroDocument' => 'required|unique:users',
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'cellphone' => 'required',
            ]);
            if ($validator->fails()) {
                return new ResponseData(false, '01', $validator->errors()->first(), []);
            }
            $data = $validator->validated();
            $data['password'] = Hash::make(Str::random(40));
            $user = User::create($data);

            return new ResponseData(true, '00', '', []);
        } catch (\Exception $e) {
            return new ResponseData(false, '01', $e->getMessage(), []);
        }
    }

    /**
     * otro ejemplo
     *
     * @param string $nroDocument
     * @param string $cellphone
     * @param float $amount
     * @return ResponseData
     */
    public function walletRecharge(string $nroDocument, string $cellphone, float $amount): ResponseData
    {
        $params = get_defined_vars();
        try {
            $validator = Validator::make($params, [
                'nroDocument' => 'required',
                'cellphone' => 'required',
                'amount' => 'required|numeric|between:0,9999999999.99'
            ]);
            if ($validator->fails()) {
                return new ResponseData(false, '01', $validator->errors()->first(), []);
            }

            $user = User::where('nroDocument', $nroDocument)->where('cellphone', $cellphone)->first();
            if (!$user) {
                return new ResponseData(false, '01', __('There is no user related to the data entered'), []);
            }
            $user->depositFloat($amount);

            return new ResponseData(true, '00', '', []);
        } catch (\Exception $e) {
            return new ResponseData(false, '01', $e->getMessage(), []);
        }
    }

    /**
     * @param string $orderId
     * @return ResponseData
     */
    public function pay(string $orderId): ResponseData
    {

        return new ResponseData(true, '00', __('mensaje del error'), ['name' => 'juan', 'email' => 'juan@test.com']);
    }

    /**
     * xxxxxxxxxxxxxx
     *
     * @param string $orderId
     * @param string $token
     * @return ResponseData
     */
    public function confirmPay(string $orderId, string $token): ResponseData
    {

        return new ResponseData(true, '00', __('mensaje del error'), ['aqui' => 'ddd']);
    }

    /**
     * xxxxxxxxxxxxxxxxxxxxx
     *
     * @param string $nroDocument
     * @param string $cellphone
     * @return ResponseData
     */
    public function checkBalance(string $nroDocument, string $cellphone): ResponseData
    {
        $params = get_defined_vars();
        try {
            $validator = Validator::make($params, [
                'nroDocument' => 'required',
                'cellphone' => 'required',
            ]);
            if ($validator->fails()) {
                return new ResponseData(false, '01', $validator->errors()->first(), []);
            }

            $user = User::where('nroDocument', $nroDocument)->where('cellphone', $cellphone)->first();
            if (!$user) {
                return new ResponseData(false, '01', __('There is no user related to the data entered'), []);
            }

            return new ResponseData(true, '00', '', ['balance' => $user->balanceFloat]);
        } catch (\Exception $e) {
            return new ResponseData(false, '01', $e->getMessage(), []);
        }
    }
}