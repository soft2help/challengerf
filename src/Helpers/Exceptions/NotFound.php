<?php
namespace App\Helpers\Exceptions;

use App\Helpers\Validator\FormValidator;
use Exception;

class NotFound extends Exception{

    public function __construct($message=null,$code=404, Exception $previous = null){        
        parent::__construct($message, $code, $previous);
    }

}
