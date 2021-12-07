<?php

use Psr\Http\Message\ServerRequestInterface as Request;
 use Psr\Http\Server\RequestHandlerInterface as Handler;
 use Slim\Psr7\Response as Response;

class LoggerMW
{

  public static function LoguearUsuario(Request $request,Handler $handler)
  {

    $response = new Response();
    $perfil = $request->getAttribute('perfil');
    $usuarioId = $request->getAttribute('usuarioId');
    $usuario = Usuario::obtenerUsuarioPorId($usuarioId);
    $fecha = DateHelper::DateAMD();

    $logUsuario = new LogUsuarios();
    $logUsuario->Mapper($usuario, $usuario->nombre, $usuario->apellido, $fecha, $perfil);
    $logUsuarioDb = new LogUsuarioDb();
    try
    {
        $logUsuarioDb->CrearLog($logUsuario);
    }
    catch(PDOException $e)
    {
      $response->getBody()->write(json_encode(array("message" => 'Algo  ocurrio  al intentar guardar el log')));
      return $response;
    }
    

     $response->getBody()
     ->write(json_encode(array("message" => 'Usuario loggeado  con exito')));
     return $response;  
   }



}