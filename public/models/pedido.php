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
    public $productoId;

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
    
    public  function ToPedido($mesaId, $empleadoId, $foto, $productoId, $fecha)
    {
        $this->codigo = random_bytes(5);
        $this->mesaId = $mesaId;
        $this->empleadoId = $empleadoId;
        $this->foto = $foto;
        $this->productoId = $productoId;
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
}