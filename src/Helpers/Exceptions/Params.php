<?php
namespace App\Helpers\Exceptions;

use App\Helpers\Validator\FormValidator;
use Exception;

class Params extends Exception{

    public function __construct($message=null,$code=400, Exception $previous = null){        
        parent::__construct($message, $code, $previous);
    }

}
