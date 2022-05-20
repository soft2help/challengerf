<?php
namespace App\Helpers;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class Paginator{
    private $paginaActual;
    private $numItemsPorPagina;
    private $items;
    private $total;
    private $numPaginas;
    private $qb;

    public function __construct($qb,$paginaActual=1,$numItemsPorPagina=10){
        $this->paginaActual=$paginaActual;
        $this->numItemsPorPagina=$numItemsPorPagina;
        $this->qb=$qb;
      
        $adapter = new DoctrineORMAdapter($this->qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->numItemsPorPagina);
        $pagerfanta->setCurrentPage($this->paginaActual);

        $this->total=$pagerfanta->getNbResults();
        $this->numPaginas=ceil($this->total/$this->numItemsPorPagina);
        $items = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $items[] = $result;
        }
        $this->items=$items;

    }


    public function toSerialize(){
        return [
            "paginaActual"=>$this->paginaActual,
            "numItemsPorPagina"=>$this->numItemsPorPagina,
            "total"=>$this->total,
            "numPaginas"=>$this->numPaginas,
            "items"=>$this->items
        ];

    }



    /**
     * Get the value of paginaActual
     */ 
    public function getPaginaActual(){
        return $this->paginaActual;
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
     * Get the value of numItemsPorPagina
     */ 
    public function getNumItemsPorPagina(){
        return $this->numItemsPorPagina;
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
     * Get the value of items
     */ 
    public function getItems(){
        return $this->items;
    }

    /**
     * Set the value of items
     *
     * @return  self
     */ 
    public function setItems($items){
        $this->items = $items;

        return $this;
    }

   
   

    /**
     * Get the value of total
     */ 
    public function getTotal(){
        return $this->total;
    }

    /**
     * Set the value of total
     *
     * @return  self
     */ 
    public function setTotal($total){
        $this->total = $total;

        return $this;
    }

    /**
     * Get the value of numPaginas
     */ 
    public function getNumPaginas(){
        return $this->numPaginas;
    }

    /**
     * Set the value of numPaginas
     *
     * @return  self
     */ 
    public function setNumPaginas($numPaginas){
        $this->numPaginas = $numPaginas;

        return $this;
    }

    /**
     * Get the value of qb
     */ 
    public function getQb(){
        return $this->qb;
    }

    /**
     * Set the value of qb
     *
     * @return  self
     */ 
    public function setQb($qb){
        $this->qb = $qb;

        return $this;
    }
}