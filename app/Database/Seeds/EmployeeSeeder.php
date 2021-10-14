<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;
use App\Models\EmployeeModel;

class EmployeeSeeder extends Seeder
{
    private $positions = ['MANAGER', 'DEVELOPER', 'DESIGNER', 'TESTER', 'DEVOPS'];
    private $status = ['ACTIVE', 'INACTIVE'];

    public function run()
    {
        $active_manager = [
            'Name' => 'Anaclet Ahishakiye',
            'NID' => '9999999999999999',
            'CODE' => 'EMP0001',
            'Phone' => '+250 784354460',
            'Email' => 'a.anaclet920@gmail.com',
            'DateOfBirth' => '31/08/1996',
            'Status' => 'ACTIVE',
            'Position' => 'MANAGER',
            'Password' => strtolower(hash('sha512', 'password'))
        ];
        $this->db->table('tbl_employees')->insert($active_manager);

        for ($i = 0; $i < 10; $i++) {
            $this->db->table('tbl_employees')->insert($this->generateClient());
        }
    }

    private function generateClient(): array
    {
        $faker = Factory::create();
        $model = new EmployeeModel();

        return [
            'Name' => $faker->name(),
            'NID' => $faker->numberBetween(1000000000000000, 9999999999999999),
            'CODE' => $model->generateEmployeeCode(),
            // 'Phone' => $faker->e164PhoneNumber,
            'Phone' => $faker->numerify('+250 #########'),
            'Email' => $faker->email,
            'DateOfBirth' => $faker->dateTimeBetween('1900-01-01', '2002-12-31')->format('d/m/Y'),
            'Status' => $this->status[random_int(0,1)],
            'Position' => $this->positions[random_int(0,1)],
            'Password' => strtolower(hash('sha512', 'password')),
            'ManagerId' => 1
        ];
    }
}
