<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class VerificationModel extends Model
{
    protected $table = 'tbl_verification_codes';
    protected $allowedFields = [
        'Id',
        'Email',
        'VerificationCode',
        'Status',
        'Updated_At',
        'CreateDate'
    ];
    protected $updatedField = 'Updated_At';
    
    protected $primaryKey = 'Id';

}
