<?php

class Producto
{
    public $id;
    public $descripcion;
    public $precioUnitario;
    public $sectorId;

    public function crearProducto()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (descripcion, sectorId, precioUnitario) VALUES (:descripcion, :sectorId, :precioUnitario)");
            $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
            $consulta->bindValue(':sectorId', $this->sectorId, PDO::PARAM_INT);
            $consulta->bindValue(':precioUnitario', $this->precioUnitario);
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

    // public static function modificarUsuario($nombre, $id)
    // {
    //     $objAccesoDato = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
    //     $consulta->bindValue(':usuario', $nombre, PDO::PARAM_STR);
    //     // $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
    //     $consulta->bindValue(':id', $id, PDO::PARAM_INT);
    //     $consulta->execute();
    // }

    // public static function borrarUsuario($usuario)
    // {
    //     $objAccesoDato = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
    //     $fecha = new DateTime(date("d-m-Y"));
    //     $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
    //     $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
    //     $consulta->execute();
    // }

    public  function ToProducto($descripcion, $precioUnitario, $sectorId)
    {
        $this->descripcion = $descripcion;
        $this->precioUnitario = $precioUnitario;
        $this->sectorId = $sectorId;
    }

    public function ProductoCompare($productoA, $productoB)
    {
        return $productoA->descripcion === $productoB->descripcion 
        && $productoA->sectorId == $productoB->sectorId;
    }
}