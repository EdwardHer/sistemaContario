<?php
require __DIR__ . '\..\..\autoload.php'; //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
/*
	Este ejemplo imprime un
	ticket de venta desde una impresora térmica
*/


/*
    Aquí, en lugar de "POS" (que es el nombre de mi impresora)
	escribe el nombre de la tuya. Recuerda que debes compartirla
	desde el panel de control
*/


function imprrimirTicket($nombre,$direccion,$giro,$telefono,$correlativo,$infoPresentacion, $cantidades,$nomProd,$importe,$precioVenta,$exento,$total,$p_iva,$p_grab,$p_exenta,$p_tec){
$nombre_impresora = "TICKET"; 
$connector = new WindowsPrintConnector($nombre_impresora);
$printer = new Printer($connector);
# Vamos a alinear al centro lo próximo que imprimamos
$printer->setJustification(Printer::JUSTIFY_CENTER);

try{
//	$logo = EscposImage::load("geek.png", false);
  //  $printer->bitImage($logo);
}catch(Exception $e){/*No hacemos nada si hay error*/}

/*
	Ahora vamos a imprimir un encabezado
*/

$printer->text("\n". $nombre . "\n");
$printer->text("Direccion: ". $direccion. "\n");
$printer->text("Giro: ".$giro . "\n");
$printer->text("Tel: ". $telefono . "\n");
#La fecha también
date_default_timezone_set("America/El_Salvador");
$printer->text("FECHA DE RESOLUCION: ".date("Y-m-d H:i:s") . "\n");
$printer->text("Nro. Ticket: ".$correlativo."\n");

$printer->text("-----------------------------" . "\n");
$printer->setJustification(Printer::JUSTIFY_LEFT);
$printer->text("CANT  DESCRIPCION PRECIO  TOTAL.\n");
$printer->text("-----------------------------"."\n");

/*
	Ahora vamos a imprimir los
	productos en un foreach
*/	$printer->setJustification(Printer::JUSTIFY_LEFT);
    for ($i=0; $i < count($nomProd); $i++) {
        $infoPre = explode('*',$infoPresentacion[$i]);
        if($exento[$i] == 1){
            $printer->text($cantidades[$i]."  ". $nomProd[$i] ."  ". $precioVenta[$i]." E\n");

        }else{
            $printer->text($cantidades[$i]."  ". $nomProd[$i] ."  ". $precioVenta[$i]." G\n");
        }
    }

    $printer->text("-----------------------------"."\n");

    $printer->text("E*Exenta    G*Gravada\n");
    $printer->text("SUB TOTAL $:           ". $total ."\n");
    $printer->text("EXENTO    $:           ". $p_exenta ."\n");
    $printer->text("GRAVADA   $:           ". ($p_grab+$p_iva)."\n");
    $printer->text("CESC      $:           ". $p_exenta ."\n");
    $printer->text("TOTAL A PAGAR $:       ". $total ."\n");
  //  $printer->text("E*Exenta    G*Gravada\n");
	/*Alinear a la izquierda para la cantidad y el nombre*/
/*	$printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Producto Galletas\n");
    $printer->text( "2  pieza    10.00 20.00   \n");
    $printer->text("Sabrtitas \n");
    $printer->text( "3  pieza    10.00 30.00   \n");
    $printer->text("Doritos \n");
    $printer->text( "5  pieza    10.00 50.00   \n");*/
/*
	Terminamos de imprimir
	los productos, ahora va el total
*/



/*
	Podemos poner también un pie de página
*/
$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer->text("¡GRACIAS POR SU COMPRA!\n");



/*Alimentamos el papel 3 veces*/
$printer->feed(3);

/*
	Cortamos el papel. Si nuestra impresora
	no tiene soporte para ello, no generará
	ningún error
*/
$printer->cut();

/*
	Por medio de la impresora mandamos un pulso.
	Esto es útil cuando la tenemos conectada
	por ejemplo a un cajón
*/
$printer->pulse();

/*
	Para imprimir realmente, tenemos que "cerrar"
	la conexión con la impresora. Recuerda incluir esto al final de todos los archivos
*/
$printer->close();

}
?>