<?php
namespace App\Helpers\Repository;

use App\Helpers\Paginator;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;


class RepositoryHelper extends EntityRepository {
    use FilterSortFields;
   

    private $request;
    private $paginaActual;
    private $numItemsPorPagina;
    private $paramsItemsPorPagina;

    private $allFields=[];
    private $assocFields=[];

    protected function getPropertyName($name,$alias='o'): string{
        
        if (false === strpos($name, '.')) {         
            return "{$alias}.{$name}";
        }

        return $name;
    }

    public function newQb(){
        return $this->getEntityManager()->createQueryBuilder();
    }

    /**
     * devuelve el query builder con el identificador
     *
     * @param string $identificador
     * @return QueryBuilder
     */
    public function getQueryBuilder($alias='o'):QueryBuilder{        
       return $this->createQueryBuilder($alias);    
    }

    private function getFieldsEntity($alias='o'){
        
        if(empty($this->allFields)){
            $fieldsEntity=$this->getClassMetadata()->getFieldNames();

            
            foreach($this->getClassMetadata()->getAssociationMappings() as $relations){
                $this->assocs[$relations["fieldName"]]=$this
                                                        ->getEntityManager()
                                                        ->getMetadataFactory()
                                                        ->getMetadataFor($relations["targetEntity"])
                                                        ->getFieldNames();
            }

          
            

            foreach($fieldsEntity as $field){
                $this->allFields[]=$this->getPropertyName($field,$alias);
            }


            foreach($this->assocs as $targetEntity=>$fields){ 
                foreach($fields as $field){
                    $this->allFields[]=$this->getPropertyName($field,$targetEntity); 
                }
            }
          
        }
        return $this->allFields;
    }

    

    protected function doJoins($queryBuilder){
        $rootAlias=$queryBuilder->getRootAliases()[0];
        $this->getFieldsEntity($rootAlias);       
        
        foreach($this->assocs as $targetEntity=>$fields)            
            $queryBuilder->leftJoin($this->getPropertyName($targetEntity,$rootAlias),$targetEntity);
        
        return $queryBuilder;

    }


    private function getParametrosItemsPorPagina(){
        try{
            return $this->paramsItemsPorPagina;
        }catch(\Exception $ex){
            return ["default"=>10,"max"=>100];
        }
        

    }

    public function startPaginator($qb,$defaultItemsPorPagina=null,$maxItemsPorPagina=null,$paginaActual=null):Paginator{
        $itemsPorPaginaParams=$this->getParametrosItemsPorPagina();


        
        if(!$defaultItemsPorPagina)
            $defaultItemsPorPagina=$itemsPorPaginaParams["default"];
        
        if(!$maxItemsPorPagina)
            $maxItemsPorPagina=$itemsPorPaginaParams["max"];
        


        $numItemsPorPagina=$this->request->query->getInt('numItemsPorPagina',$defaultItemsPorPagina);

        $numItemsPorPagina=min($numItemsPorPagina,$maxItemsPorPagina);

        if(!$paginaActual)
            $paginaActual=$this->request->query->getInt('pagina', 1);

        $this->setNumItemsPorPagina($numItemsPorPagina)->setPaginaActual($paginaActual);
        

        return new Paginator($qb,$this->paginaActual,$this->numItemsPorPagina);
        

    }

    public function setRequest(Request $request){
        $this->request=$request;
        
        return $this;
    }


    
    /**
     * Set the value of paginaActual
     *
     * @return  self
     */ 
    public function setPaginaActual($paginaActual){
        $this->paginaActual = $paginaActual;

        return $this;
    }

   

 

    /**
     * Set the value of numItemsPorPagina
     *
     * @return  self
     */ 
    public function setNumItemsPorPagina($numItemsPorPagina){
        $this->numItemsPorPagina = $numItemsPorPagina;

        return $this;
    }

    /**
     * Set the value of paramsItemsPorPagina
     *
     * @return  self
     */ 
    public function setParamsItemsPorPagina($paramsItemsPorPagina){
        $this->paramsItemsPorPagina = $paramsItemsPorPagina;

        return $this;
    }

    

    /**
     * Get the value of assocFields
     */ 
    public function getAssocFields(){
        $this->getFieldsEntity();
        return $this->assocFields;
    }

   

    /**
     * Get the value of allFields
     */ 
    public function getAllFields(){
        $this->getFieldsEntity();
        return $this->allFields;
    }
}