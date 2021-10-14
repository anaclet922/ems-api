<?php

use CodeIgniter\Database\Migration;

class AddVerify extends Migration
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
            'Email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'unique' => true
            ],
            'VerificationCode' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'Status' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'default' => 'NOT_VERIFIED'
            ],
            'Updated_At' => [
                'type' => 'datetime',
                'null' => true
            ],
        'CreateDate datetime default current_timestamp',
        ]);
        $this->forge->addPrimaryKey('Id');
        $this->forge->createTable('tbl_verification_codes');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_verification_codes');
    }
}

