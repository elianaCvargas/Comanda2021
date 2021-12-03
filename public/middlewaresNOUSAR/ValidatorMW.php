<?php

use Psr\Http\Message\ServerRequestInterface as Request;
 use Psr\Http\Server\RequestHandlerInterface as Handler;
 use Slim\Psr7\Response as Response;

class ValidatorMW
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
        
        $usuario = ValidatorMW::ExisteUsuarioYClave($params['usuario'], $params['clave']);
        if($usuario)
        {
          $request = $request->withAttribute('usuarioId', $usuario->id);
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

    public static function CheckPerfilSocio(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil == PerfilUsuarioEnum::socio)
      {
        return $handler->handle($request);

      }else {
        $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
        return $response;
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckPerfilMozo(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil == PerfilUsuarioEnum::mozo)
      {
        return $handler->handle($request);

      }else {
        $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
        return $response;
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckPerfilMozoYCliente(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil == PerfilUsuarioEnum::mozo || $perfil == PerfilUsuarioEnum::cliente)
      {
        return $handler->handle($request);

      }else {
        $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
        return $response;
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckEmpleados(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil =! PerfilUsuarioEnum::cliente)
      {
          $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }else {
         return $handler->handle($request);
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }
}