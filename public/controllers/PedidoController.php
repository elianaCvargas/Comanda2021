<?php
require_once './models/Pedido.php';
require_once './cross/fileHelper.php';
require_once './interfaces/IApiUsable.php';
require_once './models/detallePedido.php';
require_once './cross/estadoPedidoEnum.php';
require_once './cross/estadoMesaEnum.php';


class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $empleadoId = $request->getAttribute('usuarioId');
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $parametros = $request->getParsedBody();
        $mesaId = $parametros['mesaId'];
        $nombreCliente = $parametros['nombreCliente'];
        $productos = $parametros['productos'];

        try
        {
          Mesa::ModificarEstado(EstadoMesaEnum::asignada, $mesaId);
        }
        catch(PDOException $e){
          $error = json_encode(array("mensaje" => "Error al modificar el estado de la mesa: ".$e->getMessage()));
          $response->getBody()->write($error);
        }

        $pedido = new Pedido();
        $fecha = date("Y/m/d h:i:sa");
        $pedido->ToPedido(intval($mesaId), intval($empleadoId), $nombreCliente, $fecha);
        try
        {
          $ultimoIdFromPedido = $pedido->crearPedido();
        }
        catch(PDOException $e){
          $error = json_encode(array("mensaje" => "Error al crear el pedido: ".$e->getMessage()));
          $response->getBody()->write($error);
        }
       
        foreach($productos as $producto)
        {
            $pedidoDetalle = new DetallePedido();

            $productoString = json_encode($producto);
            $decodedProducto = json_decode($productoString);
            $productoDb = Producto::obtenerProducto($decodedProducto->id);
            $pedidoDetalle->ToDetallePedido(intval($ultimoIdFromPedido) , $fecha, EstadoPedidoEnum::pendiente, intval($productoDb->id) , intval($decodedProducto->cantidad) );
            try{
              $pedidoDetalle->crearPedidoDetalle($pedidoDetalle);
              $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
              $response->getBody()->write($payload);
              return $response
                  ->withHeader('Content-Type', 'application/json');
            }
            catch(PDOException $e){
              $error = json_encode(array("mensaje" => "Error al crear el detalle del pedido: ".$e->getMessage()));
              $response->getBody()->write($error);
            }
        }
    }

    public function CargarFoto($request, $response, $args)
    {
      date_default_timezone_set("America/Argentina/Buenos_Aires");
      $parametros = $request->getParsedBody();
       $foto = $_FILES['foto'];
      //  $nombreCliente = $parametros['nombreCliente'];
       $pedidoId = $parametros['pedidoId'];
       $fecha = $fecha = date("Y/m/d");
       $pedido = Pedido::obtenerPedido($pedidoId);
       $pathFoto = "";
        try{
         $pathFoto = File::GuardarImagen($foto, $pedido->nombreCliente,  $pedidoId, $fecha);
        }
        catch(Exception $e){
          $error = json_encode(array("mensaje" => "Error al guardar la imagen en el folder: ".$e->getMessage()));
          $response->getBody()->write($error);
        }

        try
        {
            Pedido::AgregarFoto($pathFoto, $pedidoId);
        }
        catch(Exception $e){
          $error = json_encode(array("mensaje" => "Error al guardar la imagen en la DB: ".$e->getMessage()));
          $response->getBody()->write($error);
        }
    }

    public function TraerTodos($request, $response, $args)
    {
      try{
        $lista = Pedido::obtenerTodos();


        $payload = json_encode(array("lista Pedidos" => $lista));
        $response->getBody()->write($payload);
      }
      catch(PDOException $e)
      {
          $error = json_encode(array("mensaje" => "Error al traer Los pedidos: ".$e->getMessage()));
          $response->getBody()->write($error);
      }

      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {

    }

    public function BorrarUno($request, $response, $args)
    {

    }

    public function TraerUno($request, $response, $args)
    {

    }

}

