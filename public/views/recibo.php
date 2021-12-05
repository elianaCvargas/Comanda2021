<?php

class Recibo
{
    public $id;
    public $codigoPedido;
    public $codigoMesa;
    public $cliente;
    public $emailCliente;
    public $emailMozo;
    public $precioTotal;
    public $fecha;
    public $nombre;
    public $apellido;
    public $productos = [];
    
    
    public  function ToPedidoView($codigoPedido, $tiempoInicial, $estado, $productoId, $cantidad)
    {
            $this->productoId = $productoId;
            $this->cantidad = $cantidad;
            $this->estadoId = $estado;
            $this->tiempoInicial = $tiempoInicial;
            $this->$codigoPedido = $codigoPedido;
    }


}