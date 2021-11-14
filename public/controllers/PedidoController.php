<?php
require_once './models/Pedido.php';
require_once './cross/fileHelper.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $empleadoId = $request->getAttribute('usuarioId');
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $parametros = $request->getParsedBody();
        $mesaId = $parametros['mesaId'];
        $nombreCliente = $parametros['clienteNombre'];
      //el mozo entra por token
        // $empleadoId = $parametros['empleadoId'];
        $productoId = $parametros['productoId'];
        $foto = $_FILES['foto'];
       
        $pedido = new Pedido();
        $fecha = date("Y/m/d");
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $codigo = substr(str_shuffle($permitted_chars), 0, 5);
          // $empleado = Usuario::obtenerUsuario($empleadoId);

        $producto = Producto::obtenerProducto($productoId);
        try{
         $fotoPath = File::GuardarImagen($foto, $nombreCliente,  $codigo, $fecha);
        }
        catch(Exception $e){
          $error = json_encode(array("mensaje" => "Error al guardar la imagen: ".$e->getMessage()));
          $response->getBody()->write($error);
        }
        
        $pedido->ToPedido(intval($mesaId), intval($empleadoId), $fotoPath, intval($productoId), $fecha);
          
        // try
        // {
        //   $listaPedidos = $pedido->obtenerTodos();
        //   if($listaPedidos != null && count($listaPedidos) > 0)
        //   {
             
        //       foreach($listaPedidos as $pedidoDb)
        //       {
        //          $pedidoComparison = $pedido->PedidoCompare($pedido, $pedidoDb);
        //           if($pedidoComparison)
        //           {
        //             $payload = json_encode(array("mensaje" => "La mesa ya existe"));
        //             $response->getBody()->write($payload);
        //             return $response->withHeader('Content-Type', 'application/json');
        //           }
        //       }
        //   }
        // }
        // catch(PDOException $e){
        //   $error = json_encode(array("mensaje" => "Error al crear el usuario: ".$e->getMessage()));
        //   $response->getBody()->write($error);
        // }

        $pedido->crearPedido();
        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos($request, $response, $args)
    {
      try{
        $lista = Pedido::obtenerTodos();
        // if(count($lista) > 0)
        // {
        //     foreach($lista as $mesa)
        //     {
        //         $mesa->estadoDescripion = EstadoMesaEnum::GetDescription($mesa->estado);
        //     }
        // }

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

