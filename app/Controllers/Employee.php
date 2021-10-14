<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class Employee extends BaseController
{
    /**
     * Get all Employees
     * @return Response
     */
    public function index() //get all employee
    {
        $model = new EmployeeModel();
        return $this->getResponse(
            [
                'message' => 'Employees retrieved successfully',
                'employees' => $model->findAll()
            ]
        );
    }

    /**
     * Create a new Employee
     */
    public function store()//store new employee
    {
       $rules = [
                'Name' => 'required',
                'NID' => 'required|max_length[16]|numeric',
                'Phone' => 'required|numeric|min_length[9]|max_length[9]|validatePhone[Phone]',
                'Email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[tbl_employees.Email]',
                'DateOfBirth' => 'required|valid_date[d/m/Y]|is_mature[DateOfBirth]'
            ];

        $input = $this->getRequestInput($this->request);

        $errors = [
            'Phone' => [
                'validatePhone' => 'The Phone field must start with 78, 79, 72 or 73'
            ],
            'DateOfBirth' => [
                'is_mature' => 'Employee must be above 18 years old or above.'
            ]
        ];

        if (!$this->validateRequest($input, $rules, $errors)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }

        $employeeEmail = $input['Email'];

        $input['ManagerId'] = getManagerFromToken()['Id'];

        $model = new EmployeeModel();

        $input['CODE'] = $model->generateEmployeeCode();
        
        $model->save($input);
        

        $employee = $model->where('Email', $employeeEmail)->first();

        return $this->getResponse(
            [
                'message' => 'Employee added successfully',
                'employee' => $employee
            ]
        );
    }

    public function sendEmail($input){
        $email = \Config\Services::email();

        $to = $input['Email'];

        $email->setTo($to);
        $email->setFrom('task@anaclet.online', 'Confirm Registration');

        $subject = 'Email verification';
        $href = base_url('verify-email/' . $verify['VerificationCode']);
        
        $message = '<h4>Welcome!</h4>';
        $message .= '<p>Hello ' . $input['Name'] . ', You have registered EMS as employee.</p>';
        $message .= 'Regards!';

        $email->setSubject($subject);
        $email->setMessage($message);

        $i = $email->send();
        if(!$i){
            return false;
        }else{
            return true;
        }
    }
    /**
     * Get a single employee by ID
     */
    public function show($id)//get employee
    {
        try {

            $model = new EmployeeModel();
            $employee = $model->findEmployeeById($id);

            return $this->getResponse(
                [
                    'message' => 'Employee retrieved successfully',
                    'employee' => $employee
                ]
            );

        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find employee for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)//update
    {
        try {

            $model = new EmployeeModel();
            $model->findEmployeeById($id);

            $input = $this->getRequestInput($this->request);

            if(isset($input['CODE'])){
                unset($input['CODE']);
            }

            if(isset($input['Position'])){
                unset($input['Position']);
            }
          
            if(isset($input['Id'])){
                unset($input['Id']);
            }

            if(isset($input['ManagerId'])){
                unset($input['ManagerId']);
            }

            $model->update($id, $input);
            $employee = $model->findEmployeeById($id);

            return $this->getResponse(
                [
                    'message' => 'Employee updated successfully',
                    'employee' => $employee
                ]
            );

        } catch (Exception $exception) {

            return $this->getResponse(
                [
                    'message' => $exception->getMessage()
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }

    public function destroy($id)//delete
    {
        try {

            $model = new EmployeeModel();
            $employee = $model->findEmployeeById($id);
            $model->delete($employee);

            return $this
                ->getResponse(
                    [
                        'message' => 'Employee deleted successfully',
                    ]
                );

        } catch (Exception $exception) {
            return $this->getResponse(
                [
                    'message' => $exception->getMessage()
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }

    public function updateStatus($id, $status){
         try {

            $model = new EmployeeModel();
            $model->findEmployeeById($id);

            $input = array(
                'Status' => 'ACTIVE'
            );
            $msg = 'Employee activated successfully';

            if($status == 'suspend'){
                $input['Status'] = 'INACTIVE';
                $msg = 'Employee suspended successfully';
            }


            $model->update($id, $input);
            $employee = $model->findEmployeeById($id);

            return $this->getResponse(
                [
                    'message' => $msg,
                    'employee' => $employee
                ]
            );

        } catch (Exception $exception) {

            return $this->getResponse(
                [
                    'message' => $exception->getMessage()
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }

    public function search(){
        $keyword = @$_GET['keyword'];

        $model = new EmployeeModel();

        $like = "";
        $position = isset($_GET['Position']) == TRUE ? $_GET['Position'] : '';
        $name = isset($_GET['Name']) == TRUE ? $_GET['Name'] : '';
        $email = isset($_GET['Email']) == TRUE ? $_GET['Email'] : ''; 
        $phone = isset($_GET['Phone']) == TRUE ? $_GET['Phone'] : ''; 
        $code = isset($_GET['Code']) == TRUE ? $_GET['Code'] : ''; 

        if($position != ''){
            $like .= "Position LIKE '%". $position ."%' OR ";
        }
        if($name != ''){
            $like .= "Name LIKE '%". $name ."%' OR ";
        }
        if($email != ''){
            $like .= "Email LIKE '%". $email ."%' OR ";
        }
        if($phone != ''){
            $like .= "Phone LIKE '%". $phone ."%' OR ";
        }
        if($code != ''){
            $like .= "CODE LIKE '%". $code ."%' OR ";
        }


        $like = chop($like, " OR "); 
         if($like == ''){
             return $this->getResponse(
                    [
                        'message' => 'Search results!!',
                        'Employees' => '[]'
                    ]
            );
         }

        $employees = $model->where("(". $like .")")->findAll();

        return $this->getResponse(
            [
                'message' => 'Search results!!',
                'Employees' => $employees
            ]
        );
    }

}
