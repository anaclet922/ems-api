<?php

use App\Models\EmployeeModel;
use Config\Services;
use Firebase\JWT\JWT;



function getManagerFromToken()
{
    $headers = isset(apache_request_headers()['Authorization']) == TRUE ? apache_request_headers()['Authorization'] : getallheaders()['Authorization'];
    $encodedToken = explode(' ', $headers)[1];
    
    $key = Services::getSecretKey();
    $decodedToken = JWT::decode($encodedToken, $key, ['HS256']);
    $userModel = new EmployeeModel();
    return $userModel->findEmployeeByEmailAddress($decodedToken->email);
}
