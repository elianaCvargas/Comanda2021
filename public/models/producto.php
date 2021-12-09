<?php

class Producto
{
    public $id;
    public $descripcion;
    public $precio;
    public $sectorId;

    public function crearProducto()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (descripcion, sectorId, precio) VALUES (:descripcion, :sectorId, :precio)");
            $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
            $consulta->bindValue(':sectorId', $this->sectorId, PDO::PARAM_INT);
            $consulta->bindValue(':precio', $this->precio);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
            // echo ''Error: '' .$e->getMessage() . ''<br/> '';
        }
        
    }

    public function Modificar($producto)
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("UPDATE productos SET descripcion = :descripcion, sectorId = :sectorId, precio = :precio  WHERE id = :id");
            $consulta->bindValue(':descripcion', $producto->descripcion, PDO::PARAM_STR);
            $consulta->bindValue(':sectorId', $producto->sectorId, PDO::PARAM_INT);
            $consulta->bindValue(':precio', $producto->precio);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
            // echo ''Error: '' .$e->getMessage() . ''<br/> '';
        }
        
    }

    public static function ObtenerTodos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function obtenerProducto($productoId)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE id = :productoId");
            $consulta->bindValue(':productoId', $productoId, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Producto');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }


    public  function ToProducto($descripcion, $precio, $sectorId)
    {
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->sectorId = $sectorId;
    }

    public function ProductoCompare($productoA, $productoB)
    {
        return $productoA->descripcion === $productoB->descripcion 
        && $productoA->sectorId == $productoB->sectorId;
    }
}