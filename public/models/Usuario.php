<?php

class Usuario
{
    public $id;
    public $usuario;
    public $nombre;
    public $apellido;
    public $clave;
    public $tipoUsuarioId;

    public function crearUsuario()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, nombre, apellido, tipoUsuarioId) VALUES (:usuario, :clave, :nombre, :apellido, :tipoUsuarioId)");
            $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
            $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':tipoUsuarioId', $this->tipoUsuarioId, PDO::PARAM_INT);
            $consulta->bindValue(':clave', $claveHash);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
            // echo ''Error: '' .$e->getMessage() . ''<br/> '';
        }
        
    }

    public static function obtenerTodos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function modificarUsuario($nombre, $id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
        $consulta->bindValue(':usuario', $nombre, PDO::PARAM_STR);
        // $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    public static function obtenerUsuarioPorUsuarioYClave($usuario, $clave)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE usuario = :usuario and clave = :clave");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUsuarioPorEmail($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public  function ToUsuario($nombre, $apellido, $usuario, $clave, $tipo)
    {
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->clave = $clave;
        $this->tipoUsuarioId = $tipo;
    }

    public function UsuarioCompare($usuarioA, $usuarioB)
    {
        return $usuarioA === $usuarioB;
    }
}