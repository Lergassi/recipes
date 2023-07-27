<?php

namespace App\Controllers\SandboxControllers;

use App\Services\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ValidationSandboxController extends AbstractSandboxController
{
    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->_getStarted();

        return $response;
    }

    private function _getStarted()
    {
        $validation = new Validator();

        $array = [
            'id' => 42,
        ];

        $invalidateKeys = [
            -1,
            0,
            1,
            '-1',
            '0',
            '1',
            ' ',
            'key',
        ];

//        dump([
//            $validation->validateRequiredKey($array, 'id'),
//        ]);
//
//        dump([
//            $validation->validateRequiredKey($array, -1) === false,
//            $validation->validateRequiredKey($array, 0) === false,
//            $validation->validateRequiredKey($array, 1) === false,
//            $validation->validateRequiredKey($array, '-1') === false,
//            $validation->validateRequiredKey($array, '0') === false,
//            $validation->validateRequiredKey($array, '1') === false,
//            $validation->validateRequiredKey($array, ' ') === false,
//            $validation->validateRequiredKey($array, 'key') === false,
//        ]);

        dump([
            $validation->validateRequiredKeys($array, [
                'id',
            ]),
        ]);

        $invalidateResult = [];
        $invalidateResult[] = $validation->validateRequiredKeys($array, []) === false;
        $invalidateResult[] = $validation->validateRequiredKeys([], []) === false;
        $invalidateResult[] = $validation->validateRequiredKeys([], $invalidateKeys) === false;
        foreach ($invalidateKeys as $invalidateKey) {
            $invalidateResult[] = $validation->validateRequiredKeys($array, [
                $invalidateKey,
            ]) === false;
        }
        dump($invalidateResult);
    }
}