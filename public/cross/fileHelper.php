<?php
class File
{   
    static function ReadFileTXT($root, $modo)
    {
        $file = fopen($root, $modo);
         $lineas = [];

        if(!$file)
        {
            echo "Error, no se encuentra el archivo";
        }
        else
        {
            while(!feof($file))
            {
                $retorno = fgets($file);

                $lineas = json_decode($retorno);
            }
          
        }

        fclose($file);
        return $lineas;
    }

    static function ArchivarSVC($data, $root, $modo)
    {
        $arraydata = [[$data->_color, $data->_marca, $data->_precio, $data->_fecha]];
        $file = fopen($root, $modo);

        if(!$file)
        {
            echo "error";
        }
        else
        {
            foreach($arraydata as $row)
            {
                fputcsv($file,  $row, ";");
            }

            echo "se guardo el elemento: con formato CSV<br/>";
        }

        fclose($file);
    }


    static function ArchivarTXT($arrayObj, $obj, $root, $modo)
    {
        // $arrayObj = [$obj];
         $file = fopen($root, $modo);

        if(!$file)
        {
            echo "error";
        }
        else
        {
            file_put_contents($root, json_encode($arrayObj));
           // echo "se guardo el elemento: <br/>". $obj->ToJson($obj);
           echo "modificar esto al  guardar";
           return true;
        }

        fclose($file);
    }

    public static function GuardarImagen($imagen, $nombreCliente, $pedido, $fecha)
    {
        $userName = explode("@", $nombreCliente);
       
        if(strlen($imagen["name"] > 0))
        {
            $extension = pathinfo($imagen["name"], PATHINFO_EXTENSION);

            if(File::VerificarExtension($extension))
            {
                $fechaStr = str_replace('/', '-', $fecha);

                $imagen["name"] = $nombreCliente."-".$pedido."-".$fechaStr.".".$extension;
                $moveTo = $_SERVER['DOCUMENT_ROOT']."\cross\imagenesDeLaVenta\\";
                move_uploaded_file($imagen["tmp_name"], $moveTo.$imagen["name"]);
                $fullPath =  $moveTo.$imagen["name"].$imagen["tmp_name"];
                return  $fullPath;
            }
            else { echo "No es un una imagen"; }
        }  
        else { echo "No se ingresó una imagen"; }           
    }

    private static function VerificarExtension($name)
    {
        switch($name)
        {
            case "jfif":
            case "jpg":
            case "gif":
                return true;
                break;
            default:
                return false;
            break;
        }
    }


}


?>