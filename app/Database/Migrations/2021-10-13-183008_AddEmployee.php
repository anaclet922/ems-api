<?php

use CodeIgniter\Database\Migration;

class AddEmployee extends Migration
{
    public function up()
    {
         $this->forge->addField([
            'Id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'Name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'NID' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'unique' => true
            ],
            'CODE' => [
                'type' => 'VARCHAR',
                'constraint' => '7',
                'null' => false,
                'unique' => true
            ],
            'Phone' => [
                'type' => 'VARCHAR',
                'constraint' => '14',
                'null' => false,
                'unique' => true
            ],
            'Email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'unique' => true
            ],
            'DateOfBirth' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false
            ],
            'Status' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => false,
                'default' => 'INACTIVE'
            ],
            'Position' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'Password' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false
            ],
            'ManagerId' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => true
            ],
            'Updated_At' => [
                'type' => 'datetime',
                'null' => true
            ],
        'CreateDate datetime default current_timestamp',
        ]);
        $this->forge->addPrimaryKey('Id');
        $this->forge->createTable('tbl_employees');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_employees');
    }
}

