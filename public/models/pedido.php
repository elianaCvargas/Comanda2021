<?php

class Pedido
{
    public $id;
    public $codigo;
    public $mesaId;
    public $clienteId;
    public $foto;
    public $precioTotal;
    public $fecha;
    public $empleadoId;

    public function crearPedido()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, mesaId, clienteId, empleadoId, fecha, precioTotal, foto) 
            VALUES (:codigo, :mesaId, :clienteId, :empleadoId, :fecha, :precioTotal, :foto)");
            $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':mesaId', $this->mesaId, PDO::PARAM_INT);
            $consulta->bindValue(':clienteId', $this->clienteId, PDO::PARAM_INT);
            $consulta->bindValue(':empleadoId', $this->empleadoId, PDO::PARAM_INT);
            $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_INT);
            $consulta->bindValue(':precioTotal', $this->precioTotal);
            $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
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
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    
    public  function ToPedido($codigo, $mesaId, $clienteId, $empleadoId, $foto)
    {
        $this->codigo = $codigo;
        $this->mesaId = $mesaId;
        $this->clienteId = $clienteId;
        $this->empleadoId = $empleadoId;
        $this->foto = $foto;
    }

    public function PedidoCompare($productoA, $productoB)
    {
        return $productoA->codigo === $productoB->codigo;
    }
}