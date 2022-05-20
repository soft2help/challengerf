<?php

namespace App\Helpers\Validator;

use ReflectionClass;
use App\Helpers\Exceptions\FormValidationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormValidator{

    private $errors=[];

    private $fieldErrors=[];

    private $validator;

    private $translator;

    private $translationDomain;

    public function __construct(ValidatorInterface $validator,TranslatorInterface $translator,$translationDomain='validators'){
        
        $this->validator=$validator;
        $this->translator=$translator;
        $this->translationDomain=$translationDomain;
    
    }
    
    
    public function hasErrors(){
        return !empty($this->errors) || !empty($this->fieldErrors);
    }


    public function getAllToResponse(){
        $errors=[];

        $errors["errors"]=$this->errors;

        foreach($this->fieldErrors as $field=>$fieldErrors){
            $errors["fieldErrors"][]=[
                                      "field"=>$field,
                                      "errors"=>$fieldErrors
                                    ];
        }
      

        return $errors;

    }

    public function trimParent($field){
        $childrens=explode(".",$field);
        if(count($childrens)>1){
            array_shift($childrens);
        }

        return implode(".",$childrens);


    }

    private function getShortName($object){
        $reflect = new ReflectionClass($object);
        return $reflect->getShortName();
    }

    public function throw(){ 
             
        if($this->hasErrors())
            throw new FormValidationException($this);

        return true;
    }

    public function translate(ConstraintViolation $error){
        $translatedMessage =$error->getMessage();
        if (null === $error->getPlural()) {
            $translatedMessage = $this->translator->trans(
                $error->getMessage(),
                $error->getParameters(),
                $this->translationDomain
            );
        }else{
            $translatedMessage = $this->translator->trans(
                $error->getMessage(),
                ['%count%' => $error->getPlural()] + $error->getParameters(),
                $this->translationDomain
            );
        }

        return $translatedMessage;
    }

    public function validate($entity,$constraints = null, $groups = null){
        $errors=$this->validator->validate($entity,$constraints, $groups);
        foreach($errors as $error){
            
            $field="{$this->getShortName($error->getRoot())}.{$error->getPropertyPath()}";

            $this->addFieldError($field,$this->translate($error));           
        }

        return $this;
    }

    /**
     * Añade un campo a la listada de errors, se puede meter un constraint para que pueda traducir el messaje y meter los paramentros, plurales etc..
     *
     * @param string $field
     * @param string $error
     * @param ConstraintViolation $contraintViolation
     * @return $this
     */
    public function addFieldError($field,$error,$contraintViolation=null){
        $field=$this->trimParent($field);
        if($contraintViolation)
            $error=$this->translate($contraintViolation);

        if(!isset($this->fieldErrors[$field]) || !in_array($error,$this->fieldErrors[$field]))
            $this->fieldErrors[$field][]=$error;

        return $this;
    }



    public function addError($error,$contraintViolation=null){
        if($contraintViolation)
            $error=$this->translate($contraintViolation);

        if(!in_array($error,$this->errors))
            $this->errors[]=$error;

        
        return $this;
    }

    /**
     * Get the value of errors
     */ 
    public function getErrors(){
        return $this->errors;
    }

    /**
     * Set the value of errors
     *
     * @return  self
     */ 
    public function setErrors($errors){
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get the value of fieldErrors
     */ 
    public function getFieldErrors(){
        return $this->fieldErrors;
    }

    /**
     * Set the value of fieldErrors
     *
     * @return  self
     */ 
    public function setFieldErrors($fieldErrors){
        $this->fieldErrors = $fieldErrors;

        return $this;
    }
}