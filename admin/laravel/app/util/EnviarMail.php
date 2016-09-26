<?php
/**
 * Created by PhpStorm.
 * User: devphp3
 * Date: 27/11/14
 * Time: 02:50 PM
 */

class EnviarMail{


    public function enviar_mail($ext, $informacion, $empresa, $nom_archivo){



        $fecha_actual = date("Y-m-d H:i:s");
        $nombreFullArchivo = $empresa->nombre."-".$nom_archivo."-".$fecha_actual;

        $doc_root = $_SERVER["DOCUMENT_ROOT"];
        $enlace = "reportes/" . $nombreFullArchivo.$ext;
        $url_full = $doc_root . "/cambari/". $enlace;

        if(strcasecmp ($ext , ".txt" ) == 0){

            $fp=fopen($url_full,"x");
            fwrite($fp,$informacion);
            fclose($fp) ;

            $rta = $this->sendMail( $empresa->persona_contacto, $url_full );
            $datos = array("fecha" => $fecha_actual, "url_archivo" => $enlace, "mensaje_mail" => $rta);
            return $datos;
        }
        else if(strcasecmp ($ext , ".xls" ) == 0){


            $informacion->getProperties()->setCreator("kubesoft Generator") // Nombre del autor
            ->setTitle($nombreFullArchivo) // Titulo
            ->setSubject("Reporte Recaudos ".$empresa->nombre) //Asunto
            ->setDescription("Reporte de Recaudos ".$empresa->nombre) //Descripción
            ->setCategory("Reporte excel"); //Categorias

            // Se activa la hoja para que sea la que se muestre cuando el archivo se abre
            $informacion->setActiveSheetIndex(0);

            $objWriter = PHPExcel_IOFactory::createWriter($informacion, 'Excel5');
            $objWriter->save($doc_root . "/multipagos/". $enlace);

            $rta = $this->sendMail( $empresa->persona_contacto, $url_full );
            $datos = array("fecha" => $fecha_actual, "url_archivo" => $enlace, "mensaje_mail" => $rta);
            return $datos;

        }

    }

    public function sendMail( $persona_contacto, $url_full){

        $mymail = new PHPMailer();
        $body='<h1>Reporte Periodico</h1><strong>Reporte periodico de los recaudos efectuados</strong>';

        $mymail->setFrom('contacto@kubesoft.com', 'Kubesoft');//remitente del mensaje

        foreach ($persona_contacto as $contacto) {

            $mymail->addAddress($contacto->email, $contacto->nombre);//destinatario

        }
        $mymail->Subject = 'Reporte Periodico Recaudos';
        $mymail->addAttachment( $url_full );
        $mymail->MsgHTML( $body );

        if(!$mymail->Send()) {
            return("Error al enviar el correo: " . $mymail->ErrorInfo);
        } else {
            return("Correo enviado!!");
        }

    }
}
