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

    static function ArchivarSVC($header, $lista, $root, $filename, $extension)
    {
        if(count($lista) > 0)
        {
            $delimiter = ","; 
            $filename = "pedidos-lista_" . date('Y-m-d') . ".csv"; 
            // Create a file pointer 
            $f = fopen($root.$filename, 'w');  
             
            // Set column headers 
            fputcsv($f, $header, $delimiter); 
            for($i = 0; $i < count($lista); $i++)
            {
                fputcsv($f, $lista[$i], $delimiter); 
            }
          
            fseek($f, 0); 
             
            // Set headers to download file rather than displayed 
            header('Content-Type: text/csv'); 
            header('Content-Disposition: attachment; filename="' . $filename . '";'); 
             
            //output all remaining data on a file pointer 
            // fpassthru($f); 
        }
       
    }

    static function ReadFileCSV($root)
    {
        $file = fopen($root, 'r');
        $lineas = [];

        if(!$file)
        {
            echo "Error";
        }
        else
        {
            // while(!feof($file))
            // {
            //     $retorno = fgetcsv($file);
            //     // var_dump($retorno);
            //     array_push($lineas, $retorno);
            //     return $lineas;
            // }
            // read each line in CSV file at a time
            while (($row = fgetcsv($file)) !== false) {
                $lineas[] = $row;
            }
        }

        fclose($file);
        return $lineas;
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