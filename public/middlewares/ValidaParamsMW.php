<?php

use Psr\Http\Message\ServerRequestInterface as Request;
 use Psr\Http\Server\RequestHandlerInterface as Handler;
 use Slim\Psr7\Response as Response;

class ValidaParamsMW
{
    public static function VerificarParamsLogin(Request $request,Handler $handler)
    {
        $params = $request->getParsedBody();
        
        if($params === null || $params['usuario'] === null || $params['perfil'] === null || $params['clave'] === null 
        || $params['usuario'] === "" || $params['perfil'] === "" || $params['clave'] === "")
        {
              $response = new Response();
                $response->getBody()
                ->write(json_encode(array("message" => 'Faltan definir parametros')));
                return $response;  
        }
        
        if(ValidaParamsMW::ExisteUsuarioYClave($params['usuario'], $params['clave']))
        {
            return $handler->handle($request);
        }
        
        $response = new Response();
                $response->getBody()
                ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
                return $response;  
         
    }


    public static function ExisteUsuarioYClave($usuario, $clave)
    {
      return  $usuario = Usuario::obtenerUsuarioPorUsuarioYClave($usuario, $clave);
    }
}