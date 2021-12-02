<?php

class Pedido
{
    public $id;
    public $codigo;
    public $mesaId;
    public $nombreCliente;
    public $foto;
    public $precioTotal;
    public $fecha;
    public $empleadoId;

    public function crearPedido()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, mesaId, nombreCliente, empleadoId, fecha) 
            VALUES (:codigo, :mesaId, :nombreCliente, :empleadoId, :fecha)");
            $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':mesaId', $this->mesaId, PDO::PARAM_INT);
            $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
            $consulta->bindValue(':empleadoId', $this->empleadoId, PDO::PARAM_INT);
            $consulta->bindValue(':fecha', $this->fecha);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        
    }

    public static function obtenerTodos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function obtenerTodosPorSector($sector)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    
    public  function ToPedido($mesaId, $empleadoId, $nombreCliente, $fecha)
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $codigo = substr(str_shuffle($permitted_chars), 0, 5);
        $this->codigo = $codigo;
        $this->mesaId = $mesaId;
        $this->empleadoId = $empleadoId;
        $this->nombreCliente = $nombreCliente;
        $this->fecha = $fecha;
    }

    public function PedidoCompare($pedidoA, $pedidoB)
    {
        return $pedidoA->codigo === $pedidoB->codigo;
    }

    public function SetCodigo(){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $this->codigo = substr(str_shuffle($permitted_chars), 0, 5);
    }

    public static function obtenerPedidoPorCodigo($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedido($pedidoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE pedidoId = :pedidoId");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function AgregarFoto($foto, $id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET foto = :foto  WHERE id = :id");
        $consulta->bindValue(':foto', $foto, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
}