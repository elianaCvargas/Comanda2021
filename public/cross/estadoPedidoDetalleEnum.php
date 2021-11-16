<?php
abstract class EstadoPedidoEnum
{
    const pendiente = 1;
    const Preparando = 2;
    const listo = 3;

       public static function GetDescription($intValue){
        switch($intValue)
        {
            case 1:
                return "pendiente";
                break;
            case 2:
                return "Preparando";
                break;
            case 3:
                return "listo";
                break;
        }
    }
}

