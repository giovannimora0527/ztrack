<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmailHelper
 *
 * @author francisco
 */
class EmailHelper {

    private $phpMailer;

    public function __construct($mailer, $mailerName, $receiver, $receiverName, $subject, $body) {
        $this->phpMailer = new PHPMailer();        
        $this->phpMailer->Mailer='smtp';
        $this->setMailer($mailer, $mailerName); //El que envia el msj
        $this->addReceiver($receiver, $receiverName); //El que recibe el msj
        $this->setSubject($subject);//asunto
        $this->setBody($body);//cuerpo del correo
        $this->PluginDir =' ..\cambari\laravel\vendor\phpmailer\phpmailer\class.smtp.php';
    }

    /**
     * Adds a new attachment to the email
     * 
     * @param string $uri : file's path
     * @return boolean
     */
    public function addAttachment($uri) {
        return $this->phpMailer->addAttachment($uri);
    }

    /**
     * Sets the mailer
     * 
     * @param string $email : new mailer's email
     * @param type $name : new mailer's name
     * @return boolean
     */
    public function setMailer($email, $name) {
        return $this->phpMailer->setFrom($email, $name);
    }

    /**
     * Adds a receiver to the email
     * 
     * @param string $email : new receiver's email
     * @param type $name : receiver's name
     * @return boolean
     */
    public function addReceiver($email, $name) {        
        $this->phpMailer->clearAddresses();        
        return $this->phpMailer->addAddress($email, $name);
    }

    /**
     * Sets emails's subject
     * 
     * @param string $subject : new email's subject
     */
    public function setSubject($subject) {
        $this->phpMailer->Subject = $subject;
    }

    /**
     * Sets email's body
     * 
     * @param type $body
     * @return type
     */
    public function setBody($body) {
        return $this->phpMailer->MsgHTML($body);
    }

    /**
     * Sends the email
     * 
     * @return boolean
     */
    public function send() {         
        return $this->phpMailer->send();      
    }
   
    
    public function getError(){        
       return $this->phpMailer->ErrorInfo;    
    }
   

}
