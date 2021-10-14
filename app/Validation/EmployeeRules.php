<?php

namespace App\Validation;

use App\Models\EmployeeModel;
use Exception;

class EmployeeRules
{
    public function validateEmployee(string $str, string $fields, array $data): bool
    {
        try {
            $model = new EmployeeModel();
            $employee = $model->findEmployeeByEmailAddress($data['Email']);
            return strtolower(hash('sha512', $data['Password'])) == $employee['Password'];
        } catch (Exception $e) {
            return false;
        }
    }
    public function validatePhone($phone){

        $c = substr($phone, 0, 2);

        if($c == '78' || $c == '79' || $c == '72' || $c == '73'){
            return true;
        }else{
            return false;
        }
    }

    public function is_mature($date){
        $birthDate = explode("/", $date);
      //get age from date or birthdate
      $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
        ? ((date("Y") - $birthDate[2]) - 1)
        : (date("Y") - $birthDate[2]));

      if($age >= 18){
         return true;
      }else{
        return false;
      }
    }

}
