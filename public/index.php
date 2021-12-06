<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/LoginController.php';
require_once './controllers/PedidoController.php';
require_once './db/AccesoDatos.php';
require_once './middlewares/ValidatorMW.php';
require_once './middlewares/AuthTokenMW.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
// $app->setBasePath('/public');
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("hola alumnos de los lunes!");
    return $response;
});

// peticiones
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\ValidatorMW::class . ':CheckPerfilSocio')->add(\AuthTokenMW::class . ':AutenticarUsuario');
    $group->put('[/]', \UsuarioController::class . ':ModificarUno');
    // $group->post('/{usuario}', \UsuarioController::class . ':BorrarUsuario');
  });

  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->post('[/]', \ProductoController::class . ':CargarUno');
    $group->get('[/]', \ProductoController::class . ':TraerTodos');

    $group->post('/carga-csv', \ProductoController::class . ':LoadCSV');

  });

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->post('[/]', \MesaController::class . ':CargarUno');
    $group->get('[/]', \MesaController::class . ':TraerTodos');
  });

  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':TraerTodos')
      ->add(\ValidatorMW::class . ':CheckEmpleados')->add(\AuthTokenMW::class . ':AutenticarUsuario');
      
    $group->post('/fotos', \PedidoController::class . ':CargarFoto')->add(\ValidatorMW::class . ':CheckPerfilMozoYCliente')->add(\AuthTokenMW::class . ':AutenticarUsuario');

    $group->post('[/]', \PedidoController::class . ':CargarUno')
      ->add(\ValidatorMW::class . ':CheckPerfilMozo')->add(\AuthTokenMW::class . ':AutenticarUsuario');

    $group->get('/descarga-csv', \PedidoController::class . ':DownloadCSV');
    $group->get('/recibo-pdf', \PedidoController::class . ':DownloadPdf');
    
    $group->post('/tomar-pedido', \PedidoController::class . ':TomarPedidoDetalle')
      ->add(\ValidatorMW::class . ':CheckEmpleadosParaTomarPedido')->add(\AuthTokenMW::class . ':AutenticarUsuario');
    $group->post('/modificar-pedido', \PedidoController::class . ':ModificarEstadoPedidoDetalle')
      ->add(\ValidatorMW::class . ':CheckEmpleadosParaTomarPedido')->add(\AuthTokenMW::class . ':AutenticarUsuario');

    //entregar y  cancelar solo lo puede hacer el  mozo
    $group->post('/entregar-pedido', \PedidoController::class . ':EntregarPedidoDetalle')
      ->add(\ValidatorMW::class . ':CheckPerfilMozo')->add(\AuthTokenMW::class . ':AutenticarUsuario');
    $group->post('/cancelar-pedido', \PedidoController::class . ':EntregarPedidoDetalle')
      ->add(\ValidatorMW::class . ':CheckPerfilMozo')->add(\AuthTokenMW::class . ':AutenticarUsuario');
  });

  $app->group('/credenciales', function (RouteCollectorProxy $group) {
    //conviene hacer un middle que valide los datos de usuarios y devuelva un token 
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  });
  //->add(\MiClase::class . ':Login')

  $app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', \LoginController::class . ':Login');
  });
// Run app
$app->run();

