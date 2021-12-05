<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';
require_once './cross/EstadoMesaEnum.php';
require_once './middlewares/AuthTokenMW.php';

class LoginController
{
    public function Login($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
    
      $usuariofromDb =  Usuario::obtenerUsuarioPorUsuarioYClave($usuario, $clave);
      if($usuariofromDb)
      {
        $datos = array('usuario' => $usuario, 'perfil' => $usuariofromDb->tipoUsuarioId, 'usuarioId' => $usuariofromDb->id);
        $request = $request->withAttribute('usuarioId', $usuariofromDb->id);
        // $empleadoId = $request->getAttribute('usuarioId');
        // var_dump($empleadoId);
        $token = AuthTokenMW::CrearToken($datos);
        $payload = json_encode(array('jwt' => $token));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      }
     
      
      $response->getBody()->write("No tiene permisos para ingresar.");
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}

