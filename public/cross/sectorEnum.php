<?php
abstract class SectorEnum
{
    const tragosYvinosEntrada = 1;
    const cervezasPatioTrasero = 2;
    const cocina = 3;
    const candyBar = 4;

    public static function GetDescription($intValue){
        switch($intValue)
        {
            case 1:
                return "Entrada";
                break;
            case 2:
                return "Patio Trasero";
                break;
            case 3:
                return "Cocina";
                break;
            case 4:
                return "CandyBar";
                break;
            default:
            break;
        }
    }

    // public static function GetDescription($intValue){
    //     switch($intValue)
    //     {
    //         case 1:
    //             break;
    //         case 2:
    //             break;
    //         case 3:
    //             break;
    //         case 4:
    //             break;
    //         default:
    //         break;
    //     }
    // }
}

