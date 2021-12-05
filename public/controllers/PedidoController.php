<?php
require_once './models/Pedido.php';
require_once './cross/fileHelper.php';
require_once './interfaces/IApiUsable.php';
require_once './models/detallePedido.php';
require_once './cross/estadoPedidoDetalleEnum.php';
require_once './cross/estadoMesaEnum.php';
require_once './views/mozoPedidoView.php';
require_once './views/socioPedidoView.php';


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
        // var_dump($empleadoId);
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
          return $response;
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
            }
            catch(PDOException $e){
              $error = json_encode(array("mensaje" => "Error al crear el detalle del pedido: ".$e->getMessage()));
              $response->getBody()->write($error);
            }
        }

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));
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

    //vista para el socio
    public function TraerTodos($request, $response, $args)
    {
      $empleadoId = $request->getAttribute('usuarioId');
      $perfil = $request->getAttribute('perfil');

    // public $empleado;
    // public $mozo;
      switch($perfil)
      {
        case PerfilUsuarioEnum::socio:
          try{
            $lista = Pedido::obtenerTodos();
            $listaPedidos = [];
            if($lista)
            {
              foreach($lista as $pedido)
              {
          // p.empleadoId as mozoId, dp.empleadoId, dp.estadoId, dp.tiempoEstimado, dp.tiempoInicial, p.fecha   FROM pedidos p

                $pedidoSocio = new SocioPedidoView();
                $pedidoSocio->codigoMesa = $pedido->codigoMesa;
                $pedidoSocio->codigoPedido = $pedido->codigoPedido;
                $pedidoSocio->sector = SectorEnum::GetDescription($pedido->sectorId);
                $pedidoSocio->descripcion = $pedido->descripcion;
                $pedidoSocio->estadoPedido = EstadoPedidoDetalleEnum::GetDescription($pedido->estadoId);
                $pedidoSocio->estadoMesa = EstadoMesaEnum::GetDescription($pedido->estado);
                // $pedidoSocio->empleado = Usuario::obtenerUsuario($pedido->empleadoId);
                // $pedidoSocio->mozo = Usuario::obtenerUsuario($pedido->mozoId);
                array_push($listaPedidos, $pedidoSocio);
               
              }
            }
           
            $payload = json_encode(array("lista Pedidos" => $listaPedidos));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer Los pedidos: ".$e->getMessage()));
              $response->getBody()->write($error);
          }

          break;
        case PerfilUsuarioEnum::mozo:
          try{
            $lista = Pedido::ObtenerPorEmpleadoId($empleadoId);
            $listaMozo = [];
            if($lista)
            {
              foreach($lista as $pedido)
              {
                  $item = new MozoPedidoView();
                  $item->codigoMesa = $pedido->codigoMesa;
                  $item->codigoPedido = $pedido->codigoPedido;
                  $item->nombreCliente = $pedido->nombreCliente;
                  $item->descripcion = $pedido->descripcion;
                  $item->estadoPedido = EstadoPedidoDetalleEnum::GetDescription($pedido->estadoId);
                  $item->estadoMesa = EstadoMesaEnum::GetDescription($pedido->estado);

                 array_push($listaMozo, $item);
              }
            }
            $payload = json_encode(array("lista Pedidos" => $listaMozo));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer Los pedidos: ".$e->getMessage()));
              $response->getBody()->write($error);
          }
          break;
        case PerfilUsuarioEnum::cocinero:
          try{
            $lista = Pedido::ObtenerPorSector(SectorEnum::cocina);
            $payload = json_encode(array("lista Pedidos" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer Los pedidos: ".$e->getMessage()));
              $response->getBody()->write($error);
          }
          break;
        case PerfilUsuarioEnum::bartender:
          break;
        case PerfilUsuarioEnum::cervecero:
          break;

      }

    

      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    //vista por sector
    public function TraerPorSector($request, $response, $args)
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

