<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as Response;
class Logger
{
    public static function LogOperacion(Request $request,Handler $handler)
    {
       
        if($request->getMethod() === 'GET')
        {
            echo 'es un get';
        }
        else
        {
            $params = $request->getParsedBody();

           if($params['nombre'] === 'Carla' && $params['perfil'] === 'Admin')
           {
                return $handler->handle($request);
           }
        }

        $response = new Response();
        $response->getBody()
        ->write(json_encode(array("message" => 'No tiene permisos')));
        return $response;
    }
}