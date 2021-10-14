<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class EmployeeModel extends Model
{
    protected $table = 'tbl_employees';
    protected $allowedFields = [
        'Id',
        'Name',
        'NID',
        'CODE',
        'Phone',
        'Email',
        'DateOfBirth',
        'Status',
        'Position',
        'Password',
        'ManagerId',
        'Updated_At',
        'CreateDate'
    ];
    protected $primaryKey = 'Id';
    protected $updatedField = 'Updated_At';

    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    protected function beforeInsert(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    protected function beforeUpdate(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    private function getUpdatedDataWithHashedPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $plaintextPassword = $data['data']['password'];
            $data['data']['password'] = $this->hashPassword($plaintextPassword);
        }
        return $data;
    }

    private function hashPassword(string $plaintextPassword): string
    {
        return strtolower(hash('sha512', $plaintextPassword));
    }
                                      
    public function findEmployeeByEmailAddress(string $emailAddress)
    {
        $employee = $this
            ->asArray()
            ->where(['Email' => $emailAddress])
            ->first();

        if (!$employee) 
            throw new Exception('Employee does not exist for specified email address');

        return $employee;
    }
    public function generateEmployeeCode()
    {
        $n = random_int(0000, 9999);
        $code = 'EMP' . str_pad($n, 4, '0', STR_PAD_LEFT);

        $employee = $this
            ->asArray()
            ->where(['CODE' => $code])
            ->first();
        if(!$employee){
            return $code;
        }else{
            $this->generateEmployeeCode();
        }
    }
    public function findEmployeeById($id)
    {
        $employee = $this
            ->asArray()
            ->where(['Id' => $id])
            ->first();

        if (!$employee) throw new Exception('Could not find employee for specified ID');

        return $employee;
    }
}
