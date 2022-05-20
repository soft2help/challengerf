<?php
namespace App\Helpers\Normalizer;
use Swagger\Annotations as SWG;

trait StaticProperties{

  /**
   * Undocumented function
   *
   * @return void
   */
  public function getStaticProperties(){
      $properties=array_keys(get_object_vars($this));
      $staticProperties=[];
      
      foreach($properties as $propertie){
        if(substr_compare($propertie, "SId", -strlen("SId")) === 0)
          $staticProperties[]=$propertie;
      }
  
      return $staticProperties;
    }
}