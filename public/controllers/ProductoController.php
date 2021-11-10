<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
require_once './cross/sectorEnum.php';
require_once './cross/productoTipoEnum.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $descripcion = $parametros['descripcion'];
        $tipoProductoId = $parametros['tipoProductoId'];
        $sectorId = $parametros['sectorId'];

        $prod = new Producto();
        $prod->ToProducto($descripcion, intval($tipoProductoId), intval($sectorId));
        try
        {
          $listaProductos = $prod->obtenerTodos();
          if($listaProductos != null && count($listaProductos) > 0)
          {
             
              foreach($listaProductos as $prodDb)
              {
                 $prodComparison = $prod->ProductoCompare($prod, $prodDb);
                  if($prodComparison)
                  {
                    $payload = json_encode(array("mensaje" => "El producto ya existe"));
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

        $prod->crearProducto();
        $payload = json_encode(array("mensaje" => "Producto creado con exito"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos($request, $response, $args)
    {
      try{
        $lista = Producto::obtenerTodos();
        if(count($lista) > 0)
        {
            foreach($lista as $producto)
            {
                $producto->sectorDescripion = SectorEnum::GetDescription($producto->sectorId);
                $producto->tipoProductoDescripion = ProductoTipoEnum::GetDescription($producto->tipoProductoId);
            }
        }

        $payload = json_encode(array("listaUsuario" => $lista));
        $response->getBody()->write($payload);
      }
      catch(PDOException $e)
      {
          $error = json_encode(array("mensaje" => "Error al traer los productos: ".$e->getMessage()));
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

