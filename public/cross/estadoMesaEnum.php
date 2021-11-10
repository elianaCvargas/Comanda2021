<?php
abstract class EstadoMesaEnum
{

    const esperandoPedido = 1;
    const comiendo = 2;
    const pagando = 3;
    const cerrada = 4;
    // etc.

       public static function GetDescription($intValue){
        switch($intValue)
        {
            case 1:
                return "Esperando pedido";
                break;
            case 2:
                return "Comiendo";
                break;
            case 3:
                return "Pagando";
                break;
            case 4:
                return "Cerrado";
                break;
        }
    }
}

