<?php
require_once './models/Pedido.php';
require_once './cross/fileHelper.php';
require_once './interfaces/IApiUsable.php';
require_once './models/detallePedido.php';
require_once './cross/estadoPedidoDetalleEnum.php';
require_once './cross/estadoMesaEnum.php';
require_once './views/mozoPedidoView.php';
require_once './views/pedidoDashboardView.php';


class PedidoController extends Pedido implements IApiUsable
{
  public  $path = "cross/archivos/";
    public function CargarUno($request, $response, $args)
    {
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $empleadoId = $request->getAttribute('usuarioId');
        $parametros = $request->getParsedBody();
        $mesaId = $parametros['mesaId'];
        $nombreCliente = $parametros['nombreCliente'];
        $productos = $parametros['productos'];
        try
        {
          Mesa::ModificarEstado(EstadoMesaEnum::esperandoPedido, $mesaId);
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
          return $response;
        }
       
        $mensaje = "";
        foreach($productos as $producto)
        {
            $pedidoDetalle = new DetallePedido();

            $productoString = json_encode($producto);
            $decodedProducto = json_decode($productoString);
            $productoDb = Producto::obtenerProducto($decodedProducto->id);
            if($productoDb)
            {
              echo "probar esto en  controllerde crear pedido";
              $pedido->precioTotal = $pedido->precioTotal + $productoDb->precioUnitario;
              $pedidoDetalle->ToDetallePedido(intval($ultimoIdFromPedido) , $fecha, EstadoPedidoDetalleEnum::pendiente, intval($productoDb->id) , intval($decodedProducto->cantidad) );
              try{
                $pedidoDetalle->crearPedidoDetalle($pedidoDetalle);
                $mensaje = "Pedido generado con exito";
              }
              catch(PDOException $e){
                $error = json_encode(array("mensaje" => "Error al crear el detalle del pedido: ".$e->getMessage()));
                $response->getBody()->write($error);
                return $response;
              }
            }
            else{
              //no deberia llegar nunca aca ya que el front deberia encargarse de mostrar solo productos existentes
              $mensaje = "No se pudo generar los pedidos para los productos";
            }
            
        }

        $payload = json_encode(array("mensaje" => $mensaje));
              $response->getBody()->write($payload);
        return $response
                  ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $empleadoId = $request->getAttribute('usuarioId');
        $parametros = $request->getParsedBody();
        $pedidoId = $parametros['pedidoId'];
        $productos = $parametros['productos'];
      
        // // try
        // // {
        // //   Mesa::ModificarEstado(EstadoMesaEnum::esperandoPedido, $mesaId);
        // // }
        // // catch(PDOException $e){
        // //   $error = json_encode(array("mensaje" => "Error al modificar el estado de la mesa: ".$e->getMessage()));
        // //   $response->getBody()->write($error);
        // // }

        // $fecha = date("Y/m/d h:i:sa");
        // try
        // {
        //   $ultimoIdFromPedido = $pedido->crearPedido();
        // }
        // catch(PDOException $e){
        //   $error = json_encode(array("mensaje" => "Error al crear el pedido: ".$e->getMessage()));
        //   $response->getBody()->write($error);
        // }
       
        // foreach($productos as $producto)
        // {
        //     $pedidoDetalle = new DetallePedido();

        //     $productoString = json_encode($producto);
        //     $decodedProducto = json_decode($productoString);
        //     $productoDb = Producto::obtenerProducto($decodedProducto->id);
        //     $pedidoDetalle->ToDetallePedido(intval($ultimoIdFromPedido) , $fecha, EstadoPedidoEnum::pendiente, intval($productoDb->id) , intval($decodedProducto->cantidad) );
        //     try{
        //       $pedidoDetalle->crearPedidoDetalle($pedidoDetalle);
        //       $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
        //       $response->getBody()->write($payload);
        //       return $response
        //           ->withHeader('Content-Type', 'application/json');
        //     }
        //     catch(PDOException $e){
        //       $error = json_encode(array("mensaje" => "Error al crear el detalle del pedido: ".$e->getMessage()));
        //       $response->getBody()->write($error);
        //     }
        // }
    }

    public function TomarPedidoDetalle($request, $response, $args)
    {
        $empleadoId = $request->getAttribute('usuarioId');
        $parametros = $request->getParsedBody();
        $detallePedidoId = $parametros['detallePedidoId'];
        $estadoId = $parametros['estadoId'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        try{
          DetallePedido::IniciarPreparacion($estadoId, $detallePedidoId, $empleadoId, $tiempoEstimado);
         }
         catch(Exception $e){
           $error = json_encode(array("mensaje" => "Error al Actualizar el pedido: ".$e->getMessage()));
           $response->getBody()->write($error);
         }

         $payload = json_encode(array("mensaje" => "Pedido tomado."));
         $response->getBody()->write($payload);
         return $response
           ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarEstadoPedidoDetalle($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $detallePedidoId = $parametros['detallePedidoId'];
        $estadoId = $parametros['estadoId'];
        try{
          DetallePedido::ModificarEstado(EstadoPedidoDetalleEnum::listoParaServir, $detallePedidoId);
         }
         catch(Exception $e){
           $error = json_encode(array("mensaje" => "Error al Actualizar el pedido: ".$e->getMessage()));
           $response->getBody()->write($error);
         }

         $payload = json_encode(array("mensaje" => "Pedido actualizado."));
         $response->getBody()->write($payload);
         return $response
           ->withHeader('Content-Type', 'application/json');
    }

    public function CancelarPedidoDetalle($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $detallePedidoId = $parametros['detallePedidoId'];
        try{
          DetallePedido::ModificarEstado(EstadoPedidoDetalleEnum::cancelada, $detallePedidoId);
         }
         catch(Exception $e){
           $error = json_encode(array("mensaje" => "Error al Actualizar el pedido: ".$e->getMessage()));
           $response->getBody()->write($error);
         }

         $payload = json_encode(array("mensaje" => "Pedido actualizado."));
         $response->getBody()->write($payload);
         return $response
           ->withHeader('Content-Type', 'application/json');
    }

    public function EntregarPedidoDetalle($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $detallePedidoId = $parametros['detallePedidoId'];
        $estadoId = $parametros['estadoId'];
        try{
          DetallePedido::EntregarPedido($estadoId, $detallePedidoId);
         }
         catch(Exception $e){
           $error = json_encode(array("mensaje" => "Error al Actualizar el pedido: ".$e->getMessage()));
           $response->getBody()->write($error);
         }

         $payload = json_encode(array("mensaje" => "Pedido entregado."));
         $response->getBody()->write($payload);
         return $response
           ->withHeader('Content-Type', 'application/json');
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
          return $response;
        }
    }

    public function TraerTodos($request, $response, $args)
    {
      $empleadoId = $request->getAttribute('usuarioId');
      $perfil = $request->getAttribute('perfil');
      $lista = [];
      switch($perfil)
      {
        case PerfilUsuarioEnum::socio:
        case PerfilUsuarioEnum::mozo:
          try{
            $lista = Pedido::obtenerTodos();
            if($lista)
            {
              foreach($lista as $pedido)
              {
                $listaDetalle = DetallePedido::ObtenerFullDataPedidosDetalle($pedido->id);

                if($listaDetalle)
                {
                  foreach($listaDetalle as $detalle)
                  {
                    if($detalle->empleadoId != null)
                    {
                      $usuario = Usuario::obtenerUsuarioPorId($detalle->empleadoId);
                      $listaDetalle->empleadoNombre = $usuario->apellido.", ".$usuario->nombre;
                    }

                    $detalle->sector = SectorEnum::GetDescription($detalle->sectorId);
                    $detalle->estadoPedido = EstadoPedidoDetalleEnum::GetDescription($detalle->estadoId);
                  }
                  $pedido->productos= $listaDetalle;
                }
              } 
              
            }
           
            $payload = json_encode(array("lista Pedidos socio" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer Los pedidos: ".$e->getMessage()));
              $response->getBody()->write($error);
          }

          break;
     
        case PerfilUsuarioEnum::cocinero:
          $lista = $this->DevolverListaDetallesPedidosPorSector(SectorEnum::cocina);

          break;
        case PerfilUsuarioEnum::bartender:
          $lista = $this->DevolverListaDetallesPedidosPorSector(SectorEnum::tragosYvinosEntrada);

          break;

        case PerfilUsuarioEnum::cervecero:
          $lista = $this->DevolverListaDetallesPedidosPorSector(SectorEnum::cervezasPatioTrasero);
          break;

      }

      $payload = json_encode(array("lista Pedidos" => $lista));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {

    }

    public function TraerUno($request, $response, $args)
    {

    }

    public function DownloadCSV($request, $response, $args)
    {
      $filename = "pedidos-lista_".date('Y-m-d');
      $extension = ".csv"; 
      $listaPedidos = Pedido::CargarPedidosCSV();
      $arrayPedidos = [];
      $header = array('ID', 'COD PEDIDO', 'COD MESA', 'CLIENTE', 'EMAIL', 'MOZO', 'PRECIO TOTAL', 'FECHA'); 
      foreach($listaPedidos as $pedido)
      {   $apellido = ($pedido->apellido && $pedido->apellido != null)? $pedido->apellido : "-";
          $nombre = ($pedido->nombre && $pedido->nombre != null)? $pedido->nombre : "-";
          $pedido->cliente = ($nombre != "-" && $apellido != "-")? $apellido.", ".$nombre: "-";
          $lineData = array($pedido->id, $pedido->codigoPedido,  $pedido->codigoMesa, $pedido->cliente ,
           $pedido->emailCliente, $pedido->emailMozo, $pedido->precioTotal, strval($pedido->fecha)); 
          array_push($arrayPedidos, $lineData);
      }

      File::ArchivarSVC($header, $arrayPedidos, $this->path, $filename, $extension);
      $mensaje = json_encode(array("mensaje" => "Descarga exitosa."));
          $response->getBody()->write($mensaje);
          return $response;
    }

    public function DownloadPdf($request, $response, $args)
    {
      $parametros = $request->getQueryParams();
      $pedidoId = $parametros['pedidoId'];
      $recibo = Pedido::BucarPedidoParaRecibo($pedidoId);
      var_dump($recibo);
      $mensaje = json_encode(array("mensaje" => "Descarga exitosa."));
          $response->getBody()->write($mensaje);
          return $response;  
    }

    private function DevolverListaDetallesPedidosPorSector($sectorId)
    {
      $lista = DetallePedido::ObtenerPedidosDetallePorSector($sectorId);
      if($lista)
      {
        foreach($lista as $detalle)
        {
          $detalle->estadoDetalle = EstadoPedidoDetalleEnum::GetDescription($detalle->estadoId);
        }
      }

      return $lista;
    }

    //  public function PdfAlquileres($request, $response, $args)
    // {
    //   $parametros = $request->getQueryParams();
    //    $desde = $parametros['desde'];
    //    $hasta = $parametros['hasta'];

    //   $alquileres = Alquiler::TraerEntreFechas($desde, $hasta);
    //   if($alquileres)
    //   { 
    //     //convierto los datos en lista de string
    //     $datos = [];
    //     foreach($alquileres as $alquiler)
    //     {
    //       $nombreFinalUsuario = $alquiler->usuario;
    //       $val = "Cliente: ".$nombreFinalUsuario."  -  Estilo: ".$alquiler->estilo."  -  Cantidad dias: ".$alquiler->cantidadDias."\n";
    //       array_push($datos, $val);
    //     }


    //     $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false, true);
    //     $pdf->addPage();
    //     $pdf->write(0, 'Alquieleres'."\n");
    //     for ($i = 1; $i < count($datos) + 1; $i++)
    //     {
    //         $pdf->write($i, $datos[$i]);
            
    //     }
    //     // Render and return pdf content as string
    //     $content = $pdf->output('doc.pdf', 'S');

    //     $response->getBody()->write($content);

    //     $response = $response
    //         ->withHeader('Content-Type', 'application/pdf')
    //         ->withHeader('Content-Disposition', 'attachment; filename="filename.pdf"');

    //     return $response;
    //   }

    
}

