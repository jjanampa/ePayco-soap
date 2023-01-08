<?php

namespace App\Soap;

use App\Data\ResponseData;
use App\Models\User;
use App\Notifications\ConfirmPaymentNotification;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WalletSoap
{

    /**
     * register Customer
     *
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
     * Wallet Recharge
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
     * Pay
     *
     * @param string $nroDocument
     * @param string $cellphone
     * @param float $amount
     * @return ResponseData
     */
    public function pay(string $nroDocument, string $cellphone, float $amount): ResponseData
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
            $token = Str::random(6);
            $transaction = $user->withdrawFloat($amount, null, false);
            $transaction->token = $token;
            $transaction->save();

            $user->notify(new ConfirmPaymentNotification($transaction));

            return new ResponseData(true, '00', '', ['sessionId' => $transaction->uuid, 'token' => $token]);
        } catch (\Exception $e) {
            return new ResponseData(false, '01', $e->getMessage(), []);
        }
    }

    /**
     * Confirm Pay
     *
     * @param string $sessionId
     * @param string $token
     * @return ResponseData
     */
    public function confirmPay(string $sessionId, string $token): ResponseData
    {
        $params = get_defined_vars();
        try {
            $validator = Validator::make($params, [
                'sessionId' => 'required',
                'token' => 'required',
            ]);
            if ($validator->fails()) {
                return new ResponseData(false, '01', $validator->errors()->first(), []);
            }

            $transaction = Transaction::where('uuid', $sessionId)->where('token', $token)->first();
            if (!$transaction) {
                return new ResponseData(false, '01', __('There is no payment related to the data entered'), []);
            }

            $user = $transaction->payable;
            if (!$user) {
                return new ResponseData(false, '01', __('There is no user related to the data entered'), []);
            }

            $user->confirm($transaction);

            return new ResponseData(true, '00', '', ['sessionId' => $transaction->uuid]);
        } catch (\Exception $e) {
            return new ResponseData(false, '01', $e->getMessage(), []);
        }
    }

    /**
     * Check Balance
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