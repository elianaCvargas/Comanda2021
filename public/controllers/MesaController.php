<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';
require_once './cross/EstadoMesaEnum.php';
// require_once './cross/productoTipoEnum.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $codigo = $parametros['codigo'];
        $numeroMesa = $parametros['numeroMesa'];

        $mesa = new Mesa();
        $mesa->ToMesa($codigo, intval($numeroMesa), EstadoMesaEnum::disponible);
        try
        {
          $listaMesas = $mesa->obtenerTodos();
          if($listaMesas != null && count($listaMesas) > 0)
          {
             
              foreach($listaMesas as $mesaDb)
              {
                 $mesaComparison = $mesa->MesaCompare($mesa, $mesaDb);
                  if($mesaComparison)
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

        $mesa->crearMesa();
        $payload = json_encode(array("mensaje" => "Mesa creado con exito"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos($request, $response, $args)
    {
      try{
        $lista = Mesa::obtenerTodos();
        if(count($lista) > 0)
        {
            foreach($lista as $mesa)
            {
                $mesa->estadoDescripion = EstadoMesaEnum::GetDescription($mesa->estado);
            }
        }

        $payload = json_encode(array("lista Mesas" => $lista));
        $response->getBody()->write($payload);
      }
      catch(PDOException $e)
      {
          $error = json_encode(array("mensaje" => "Error al traer Las mesas: ".$e->getMessage()));
          $response->getBody()->write($error);
      }  
        
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $pedidoId = $parametros['pedidoId'];
      $mesaId = $parametros['mesaId'];
      $estadoId = $parametros['estadoId'];
      $detallesPedidoDb = DetallePedido::ObtenerFullDataPedidosDetalle($pedidoId);
      if($detallesPedidoDb && $estadoId == EstadoMesaEnum::comiendo)
      {
        foreach($detallesPedidoDb as $detalle)
        {
            if($detalle->estadoId != EstadoPedidoDetalleEnum::entregado)
            {
              $error = json_encode(array("mensaje" => "El pedido ".$detalle->descripcion." aun no fué entregado. No puede modificar el estado a Comiendo"));
              $response->getBody()->write($error);
              return $response;
            }
        }
      }

      try{
        Mesa::ModificarEstado($estadoId, $mesaId);
        $payload = json_encode(array("lista Mesas" => "Mesa actualizada."));
        $response->getBody()->write($payload);
      }
      catch(PDOException $e)
      {
          $error = json_encode(array("mensaje" => "Error al traer Las mesas: ".$e->getMessage()));
          $response->getBody()->write($error);
      }  
        
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {

    }

    public function TraerUno($request, $response, $args)
    {
      
    }

}

