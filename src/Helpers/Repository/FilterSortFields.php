<?php

namespace App\Helpers\Repository;

trait FilterSortFields{
    protected $filter;
    protected $sorter;
    protected $fields;
    
    /**
     * haz lo joins al primero nivel
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    abstract protected function doJoins(\Doctrine\ORM\QueryBuilder $queryBuilder);

    protected function filterSortFieldsFromRequest(){
        $this->filterFromRequest()
            ->sortFromRequest()
            ->fieldsFromRequest();

    }
    protected function filterSortFieldsQuery(\Doctrine\ORM\QueryBuilder $queryBuilder,$doJoins=true){
        if($doJoins)
            $queryBuilder=$this->doJoins($queryBuilder);

        $queryBuilder=$this->queryFilter($queryBuilder);
        $queryBuilder=$this->querySort($queryBuilder);
       // $queryBuilder=$this->queryFilterFields($queryBuilder);

        return $queryBuilder;
    }


    private function prepareFilters(){
        $filters=$this->filter;
        if(isset($this->filter["all"])){
            $filters=[];
            $filters["all"]=$this->filter["all"];
        }
        return $filters;

    }

    /**
     * Undocumented function
     *
     * @param array $filters
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return \Doctrine\ORM\Query\Expr\Andx|\Doctrine\ORM\Query\Expr\Orx
     */
    private function getExpressionFromFilter($filters,\Doctrine\ORM\QueryBuilder $queryBuilder){
        if(isset($filters["all"]))
            return $queryBuilder->expr()->orX();

        return $queryBuilder->expr()->andX();
    }

    private function newParameterFilter(&$exp,\Doctrine\ORM\QueryBuilder &$queryBuilder,$fieldName,$searchIn,$suffix=""){
        $paramName=":filter{$suffix}".str_replace(".","",$fieldName);
        
        $exp->add($queryBuilder->expr()->like($fieldName, "{$paramName}"));
        $queryBuilder->setParameter($paramName,"%{$searchIn}%");
        return true;
    }

    private function fieldExists($fieldName){
        
        if(!in_array($fieldName,$this->getAllFields()))
            return false;

        return true;
    }


    private function queryFilter(\Doctrine\ORM\QueryBuilder $queryBuilder){
        $this->filterFromRequest();
        $rootAlias=$queryBuilder->getRootAliases()[0];
        if($this->filter){  
            $filters=$this->prepareFilters();
            $exp=$this->getExpressionFromFilter($filters,$queryBuilder);

            $isExp=false;

            foreach($filters as $field=>$searchInArray){
                foreach($searchInArray as $key=>$searchIn){                    
                    if($field=="all"){
                        foreach($this->getAllFields() as $fieldName)  
                            $isExp=$this->newParameterFilter($exp, $queryBuilder,$fieldName,$searchIn,$key);
                    } else{
                        $fieldName=$this->getPropertyName($field,$rootAlias);
                        
                        
                        if(!$this->fieldExists($fieldName))
                            continue;

                        $isExp=$this->newParameterFilter($exp, $queryBuilder,$fieldName,$searchIn,$key);
                    } 
                }
            }

            if($isExp)
                $queryBuilder->andWhere($exp);
        }
        
        //ini_set('xdebug.var_display_max_data', -1);
        //var_dump($queryBuilder->getQuery()->getSQL());

        return $queryBuilder;

    }

    private function querySort(\Doctrine\ORM\QueryBuilder $queryBuilder){
        $this->sortFromRequest();
        $rootAlias=$queryBuilder->getRootAliases()[0];

        if($this->sorter){
            foreach($this->sorter as $field=>$direction){
                $fieldName=$this->getPropertyName($field,$rootAlias);
                if(!$this->fieldExists($fieldName))
                    continue;
               
                $queryBuilder->addOrderBy($fieldName,$direction);
            }
        }

        return $queryBuilder;

    }

    private function queryFilterFields(\Doctrine\ORM\QueryBuilder $queryBuilder){
        $this->fieldsFromRequest();
        $rootAlias=$queryBuilder->getRootAliases()[0];
        if($this->fields){
            $conta=0;
            foreach($this->fields as $field){
                $fieldName=$this->getPropertyName($field, $rootAlias);
              
                if(!$this->fieldExists($fieldName))
                    continue;

                
                $queryBuilder->addSelect($fieldName);
                if($conta++>1){
                    $queryBuilder->addSelect($fieldName);
                }else{
                    $queryBuilder->select($fieldName);
                }

                
            }
        }

        return $queryBuilder;


    }



    private function filterFromRequest(){
        if(!$this->filter && ($filters=$this->request->get("filtrar"))){
            $filters=array_filter($filters);
            foreach($filters as $filter){            
                $fieldSearchIn=explode("|",$filter);
                if(count($fieldSearchIn)==1){
                    $this->filter["all"][]=$filter;
                    continue;
                }
                
                
                $this->filter[$fieldSearchIn[0]][]=$fieldSearchIn[1];
            }
        }

        
        
        return $this;
    }

    private function sortFromRequest(){
        if(!$this->sorter && ($sorters=$this->request->get("ordenar"))){           

            $sorters=array_filter($sorters);
            foreach($sorters as $sorter){            
                $sortDirection=explode("|",$sorter);
                $direction="DESC";
                
                if(isset($sortDirection[1]) && in_array(strtoupper($sortDirection[1]),["DESC","ASC"]))
                    $direction=$sortDirection[1];
                
                $this->sorter[$sortDirection[0]]=strtoupper($direction);
            }
        }

        return $this;
    }

    private function fieldsFromRequest(){
        if(($fields=$this->request->get("campos"))){   
            $fields=explode(",",$fields);
            foreach($fields as $field){            
                if(!in_array($field,(array)$this->fields))
                    $this->fields[]=$field;
            }
        }
        return $this;
    }







}