<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $mesaId = $parametros['mesaId'];
        $clienteId = $parametros['clienteId'];
        $empleadoId = $parametros['empleadoId'];
        // $precioTotal = $parametros['precioTotal'];
        $foto = $parametros['foto'];
        $fotoPath = "path";
        $pedido = new Pedido();
        $pedido->ToPedido($codigo, intval($mesaId), intval($clienteId), intval($empleadoId), $fotoPath);
        $pedido->fecha = date("Y/m/d");
        try
        {
          $listaPedidos = $pedido->obtenerTodos();
          if($listaPedidos != null && count($listaPedidos) > 0)
          {
             
              foreach($listaPedidos as $pedidoDb)
              {
                 $pedidoComparison = $pedido->PedidoCompare($pedido, $pedidoDb);
                  if($pedidoComparison)
                  {
                    $payload = json_encode(array("mensaje" => "La mesa ya existe"));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                  }
              }
          }
        }
        catch(PDOException $e){
          $error = json_encode(array("mensaje" => "Error al crear el usuario: ".$e->getMessage()));
          $response->getBody()->write($error);
        }

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

