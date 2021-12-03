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

    public static function GetEnumerator($descripcion){
        $descSinEspacios = str_replace( ' ', '', strtolower($descripcion));

        switch($descSinEspacios)
        {
            case "entrada":
                return 1;
                break;
            case "patiotrasero":
                return 2;
                break;
            case "cocina":
                return 3;
                break;
            case "candyBar":
                return 4;
                break;
           
            default:
            return "-";
            
            break;
        }
    }
}

