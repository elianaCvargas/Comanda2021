<?php

class PedidoView
{
    public $codigoPedido;
    public $codigoMesa;
    public $nombreMozo;
    public $productoDescripcion;
    public $cantidad;
    public $tiempoEstimado;
    public $tiempoReal;
    public $estadoPedido;
    public $estadoMesa;
    
    public  function ToPedidoView($codigoPedido, $tiempoInicial, $estado, $productoId, $cantidad)
    {
            $this->productoId = $productoId;
            $this->cantidad = $cantidad;
            $this->estadoId = $estado;
            $this->tiempoInicial = $tiempoInicial;
            $this->$codigoPedido = $codigoPedido;
    }


}