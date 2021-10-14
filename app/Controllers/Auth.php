<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\VerificationModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use ReflectionException;

class Auth extends BaseController
{
     public function register()
    {
        try{
            $rules = [
                'Name' => 'required',
                'NID' => 'required|max_length[16]|numeric',
                'Phone' => 'required|numeric|min_length[9]|max_length[9]|validatePhone[Phone]',
                'Email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[tbl_employees.Email]',
                'DateOfBirth' => 'required|valid_date[d/m/Y]',
                'Password' => 'required|min_length[8]|max_length[255]',
            ];

            $input = $this->getRequestInput($this->request);

            $errors = [
                'Phone' => [
                    'validatePhone' => 'The Phone field must start with 78, 79, 72 or 73'
                ]
            ];

            if (!$this->validateRequest($input, $rules, $errors)) {
                return $this
                    ->getResponse(
                        $this->validator->getErrors(),
                        ResponseInterface::HTTP_BAD_REQUEST
                    );
            }

            $employeeModel = new EmployeeModel();
            $input['Phone'] = '+250 ' . $input['Phone'];
            $input['CODE'] = $employeeModel->generateEmployeeCode();
            $input['Position'] = 'MANAGER';
             if(isset($input['CreateDate'])){
                unset($input['CreateDate']);
             }

            $employeeModel->save($input);

            $verify = array(
                'Email' => $input['Email'],
                'VerificationCode' => $this->generateRandomString(8)
            );

            $verifyModel = new VerificationModel();
            $verifyModel->save($verify);


            $email = \Config\Services::email();

            $to = $input['Email'];

            $email->setTo($to);
            $email->setFrom('task@anaclet.online', 'Confirm Registration');

            $subject = 'Email verification';
            $href = base_url('verify-email/' . $verify['VerificationCode']);
            
            $message = '<h4>Welcome!</h4>';
            $message .= '<p>Hello ' . $input['Name'] . ', Please click the below link to verify your e-mail.</p>';
            $message .= '<a href="' . $href . '">Verify</a>';

            $email->setSubject($subject);
            $email->setMessage($message);

            $i = $email->send();
            if(!$i){
                echo 'Invalid smtp configuration';die();
            }
            $reg = $employeeModel->findEmployeeByEmailAddress($input['Email']);

            return $this
                    ->getResponse(
                        [
                            'Message' => 'Sign up successfully',
                            'Employee' => $reg
                        ]
                    );
        } catch (Exception $exception) {
            return $this
                ->getResponse(
                    [
                        'error' => $exception->getMessage(),
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
    }
    public function login()
    {
        $rules = [
            'Email' => 'required|min_length[6]|max_length[50]|valid_email',
            'Password' => 'required|min_length[8]|max_length[255]|validateEmployee[Email, Password]'
        ];

        $errors = [
            'Password' => [
                'validateEmployee' => 'Invalid login credentials provided'
            ]
        ];

        $input = $this->getRequestInput($this->request);


        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
       return $this->getJWTForEmployee($input['Email']);

       
    }

    private function getJWTForEmployee(
        string $emailAddress,
        int $responseCode = ResponseInterface::HTTP_OK
    )
    {
        try {
            $model = new EmployeeModel();
            $employee = $model->findEmployeeByEmailAddress($emailAddress);
            unset($employee['Password']);

            helper('jwt');

            return $this
                ->getResponse(
                    [
                        'Message' => 'Employee authenticated successfully',
                        'Employee' => $employee,
                        'access_token' => getSignedJWTForUser($emailAddress)
                    ]
                );
        } catch (Exception $exception) {
            return $this
                ->getResponse(
                    [
                        'error' => $exception->getMessage(),
                    ],
                    $responseCode
                );
        }
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $model = new VerificationModel();
        $code = $model
            ->asArray()
            ->where(['VerificationCode' => $randomString])
            ->first();
        if (!$code){
            return $randomString;
        }else{
            $this->generateRandomString(8);
        }
    }

    public function verify($code){
        try {
            $model = new EmployeeModel();
            $verModel = new VerificationModel();

            $codeRecord = $verModel
                ->asArray()
                ->where(['VerificationCode' => $code])
                ->first();

            if(!$codeRecord) throw new Exception('Invalid Verification url');

            $verModel->update($codeRecord['Id'], ['Status' => 'VERIFIED']);

            $employee = $model
                ->asArray()
                ->where(['Email' => $codeRecord['Email']])
                ->first();
            $model->update($employee['Id'], ['Status' => 'ACTIVE']);

            return $this
                ->getResponse(
                    [
                        'Message' => 'Account verified, you can now login'
                    ]
                );
        } catch (Exception $exception) {
            return $this
                ->getResponse(
                    [
                        'error' => $exception->getMessage(),
                    ],
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
    }
}
