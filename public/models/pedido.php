<?php
require_once './views/pedidoDetalleCSV.php';
require_once './cross/dateHelper.php';

class Pedido
{
    public $id;
    public $codigo;
    public $mesaId;
    public $nombreCliente;
    public $foto;
    public $precioTotal;
    public $fecha;
    public $empleadoId;


    public function crearPedido()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, mesaId, nombreCliente, empleadoId, fecha) 
            VALUES (:codigo, :mesaId, :nombreCliente, :empleadoId, :fecha)");
            $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':mesaId', $this->mesaId, PDO::PARAM_INT);
            $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
            $consulta->bindValue(':empleadoId', $this->empleadoId, PDO::PARAM_INT);
            $consulta->bindValue(':fecha', $this->fecha);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        
    }


    public static function obtenerTodos()
    {
        $hoy = DateHelper::DateAMD();
        // var_dump($hoy);
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT p.id, p.codigo as codigoPedido, m.codigo as codigoMesa, u.nombre, u.apellido, m.estado, p.empleadoId as mozoId, p.fecha   FROM pedidos p
            INNER JOIN usuarios u on u.id = p.empleadoId
            INNER JOIN mesas m on m.id = p.mesaId
            WHERE p.fecha =:hoy && m.estado != 4
            ORDER BY p.id, m.estado, p.fecha");

            $consulta->bindValue(':hoy', $hoy."%");

            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoDashboardView');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    // public static function obtenerTodos()
    // {
    //     $hoy = DateHelper::DateAMD();
    //     try{
    //         $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //         $consulta = $objAccesoDatos->prepararConsulta(
    //         "SELECT p.id, p.codigo as codigoPedido, m.codigo as codigoMesa, m.estado, pro.sectorId, pro.descripcion, p.empleadoId as mozoId, dp.empleadoId as empleadoId, dp.estadoId, dp.tiempoEstimado, dp.tiempoInicial, p.fecha   FROM pedidos p
    //         INNER JOIN usuarios u on u.id = p.empleadoId
    //         INNER JOIN detallesPedidos dp on dp.PedidoId = p.id
    //         INNER JOIN mesas m on m.id = p.mesaId
    //         INNER JOIN productos pro on pro.id = dp.productoid
    //         WHERE fecha =:hoy
    //         ORDER BY p.id, pro.sectorId, fecha");

    //         $consulta->bindValue(':hoy', $hoy."%");

    //         $consulta->execute();
    //         return $consulta->fetchAll(PDO::FETCH_CLASS, 'SocioPedidoView');
    //     }
    //     catch(PDOException $e)
    //     {
    //         throw $e;
    //     }
    // }

    public static function ObtenerPorSector($sectorId)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT p.id, p.codigo as codigoPedido, m.codigo as codigoMesa, u.usuario as emailMozo, dp.estadoId, dp.tiempoEstimado, dp.tiempoInicial, p.fecha   FROM pedidos p
            INNER JOIN usuarios u on u.id = p.empleadoId
            INNER JOIN detallesPedidos dp on dp.PedidoId = p.id
            INNER JOIN mesas m on m.id = p.mesaId
            INNER JOIN productos pro on pro.id = dp.productoid
            WHERE pro.sectorId = :sectorId
            WHERE fecha =:hoy
            ORDER BY dp.estadoId");
            $consulta->bindValue(':sectorId', $sectorId, PDO::PARAM_INT);
            $consulta->bindValue(':hoy', DateHelper::DateAMD()."%");


            $consulta->execute();
            return $consulta->fetchAll();
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerPorEmpleadoId($empleadoId)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT p.id, p.codigo as codigoPedido, m.codigo as codigoMesa, p.nombreCliente, pro.descripcion, dp.estadoId, m.estado, dp.tiempoEstimado, dp.tiempoInicial, p.fecha   FROM pedidos p
            INNER JOIN usuarios u on u.id = p.empleadoId
            INNER JOIN detallesPedidos dp on dp.PedidoId = p.id
            INNER JOIN mesas m on m.id = p.mesaId
           
            INNER JOIN productos pro on pro.id = dp.productoid
            WHERE p.empleadoId = :empleadoId && fecha =:hoy && (dp.estadoId != 4 || dp.estadoId != 5)
            order by p.id, dp.estadoId, pro.sectorId
           ");
            $consulta->bindValue(':empleadoId', intval($empleadoId), PDO::PARAM_INT);
            $consulta->bindValue(':hoy', DateHelper::DateAMD()."%");


            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MozoPedidoView');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    
    public  function ToPedido($mesaId, $empleadoId, $nombreCliente, $fecha)
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $codigo = substr(str_shuffle($permitted_chars), 0, 5);
        $this->codigo = $codigo;
        $this->mesaId = $mesaId;
        $this->empleadoId = $empleadoId;
        $this->nombreCliente = $nombreCliente;
        $this->fecha = $fecha;
    }

    public function PedidoCompare($pedidoA, $pedidoB)
    {
        return $pedidoA->codigo === $pedidoB->codigo;
    }

    public function SetCodigo(){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $this->codigo = substr(str_shuffle($permitted_chars), 0, 5);
    }

    public static function obtenerPedidoPorCodigo($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function obtenerPedido($pedidoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE pedidoId = :pedidoId");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function AgregarFoto($foto, $id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET foto = :foto  WHERE id = :id");
        $consulta->bindValue(':foto', $foto, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerTodosDetalle()
    {

        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT p.id, p.codigo as codigoPedido, m.codigo as codigoMesa, c.nombre, c.apellido, c.email as emailCliente, u.usuario as emailMozo, p.precioTotal, p.fecha   FROM pedidos p
            INNER JOIN usuarios u on u.id = p.empleadoId
            INNER JOIN clientes c on c.id = p.clienteId
            INNER JOIN mesas m on m.id = p.mesaId
            ORDER BY fecha");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoDetalleCSV');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    
    public static function CargarPedidosCSV()
    {
       $listaPedidos =  Pedido::obtenerTodosDetalle();
       
       return $listaPedidos;

    }

    public static function BucarPedidoParaRecibo($pedidoId)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta
            ("SELECT *   FROM pedidos p
            INNER JOIN usuarios u on u.id = p.empleadoId
            INNER JOIN clientes c on c.id = p.clienteId
            INNER JOIN mesas m on m.id = p.mesaId
            INNER JOIN detallesPedidos dp on dp.pedidoId = p.id
            WHERE p.id = :pedidoId");
            $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_INT);

            $consulta->execute();
            return $consulta->$consulta->fetchObject();
        }
        catch(PDOException $e)
        {
            throw $e;
        }

    }
}