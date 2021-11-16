<?php

class DetallePedido
{
    public $id;
    public $pedidoId;
    public $estadoId;
    public $empleadoId;
    public $productoId;
    public $cantidad;
    public $tiempoEstimado;
    public $tiempoInicial;
    public $tiempoFinal;

    public function crearPedidoDetalle()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO detallesPedidos 
                      (estadoId, pedidoId, empleadoId, tiempoEstimado, cantidad, productoId, tiempoInicial) 
            VALUES (:estadoId, :pedidoId, :empleadoId, :tiempoEstimado, :cantidad, :productoId, :tiempoInicial)");
            $consulta->bindValue(':estadoId', $this->estadoId, PDO::PARAM_INT);
            $consulta->bindValue(':pedidoId', $this->pedidoId, PDO::PARAM_INT);
            $consulta->bindValue(':empleadoId', $this->empleadoId, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_INT);
            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
            $consulta->bindValue(':productoId', $this->productoId, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoInicial', $this->tiempoInicial);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        
    }

    // public static function obtenerTodos()
    // {
    //     try{
    //         $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //         $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
    //         $consulta->execute();
    //         return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    //     }
    //     catch(PDOException $e)
    //     {
    //         throw $e;
    //     }
    // }
    
    public  function ToDetallePedido($pedidoId, $tiempoInicial, $estado, $productoId, $cantidad)
    {
            $this->productoId = $productoId;
            $this->cantidad = $cantidad;
            $this->estadoId = $estado;
            $this->tiempoInicial = $tiempoInicial;
            $this->pedidoId = $pedidoId;
    }

    // public function PedidoCompare($pedidoA, $pedidoB)
    // {
    //     return $pedidoA->codigo === $pedidoB->codigo;
    // }

    // public function SetCodigo(){
    //     $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    //     $this->codigo = substr(str_shuffle($permitted_chars), 0, 5);
    // }
}