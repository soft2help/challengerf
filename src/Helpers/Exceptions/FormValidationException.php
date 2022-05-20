<?php
namespace App\Helpers\Exceptions;

use App\Helpers\Validator\FormValidator;
use Exception;

class FormValidationException extends Exception{
    public $formValidator;

    public function __construct(FormValidator $formValidator,$message=null,$code=400, Exception $previous = null){
        $this->formValidator=$formValidator;
        parent::__construct($message, $code, $previous);
    }








}
