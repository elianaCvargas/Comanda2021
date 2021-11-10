<?php

class Mesa
{
    public $id;
    public $codigo;
    public $numeroMesa;
    public $estado;

    public function crearMesa()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo, numeroMesa, estado) VALUES (:codigo, :numeroMesa, :estado)");
            $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':numeroMesa', $this->numeroMesa, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
            // echo ''Error: '' .$e->getMessage() . ''<br/> '';
        }
        
    }

    public static function obtenerTodos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    
    public  function ToMesa($codigo, $numeroMesa, $estado)
    {
        $this->codigo = $codigo;
        $this->numeroMesa = $numeroMesa;
        $this->estado = $estado;
    }

    public function MesaCompare($productoA, $productoB)
    {
        return $productoA->codigo === $productoB->codigo;
    }
}