<?php
abstract class ProductoTipoEnum
{
    const bebidasAlcoholicas = 1;
    const bebidasSinAlcohol = 2;
    const snacks = 3;
    const dulces = 4;
    const comida = 5;
    // etc.

       public static function GetDescription($intValue){
        switch($intValue)
        {
            case 1:
                return "Bebidas alcoholicas";
                break;
            case 2:
                return "Bebidas sin alcohol";
                break;
            case 3:
                return "Snacks";
                break;
            case 4:
                return "Dulces";
                break;
            default:
            return "Comida";
            
            break;
        }
    }
}

