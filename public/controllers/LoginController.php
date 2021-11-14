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
        $perfil = $parametros['perfil'];

        $datos = array('usuario' => $usuario, 'perfil' => $perfil);

        $token = AuthTokenMW::CrearToken($datos);
        $payload = json_encode(array('jwt' => $token));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    //verificar token, verificar parametros, validar mozo

}

