<?
	function sendEmail($email, $subject, $message, $no_reply = false, $att_name = false, $att_str = false)
	{
		require_once 'PHPMailerAutoload.php';
		
		global $ms, $gsValues;
		
		$signature = "\r\n\r\n".$gsValues['EMAIL_SIGNATURE'];
		$message .= $signature;
		$message = str_replace("\\n", "\n", $message); 
		
		$mail = new PHPMailer();
		
		if ($gsValues['EMAIL_SMTP'] == 'true')
		{
			$mail->IsSMTP(); // telling to use SMTP
			$mail->Host       = $gsValues['EMAIL_SMTP_HOST'];
			$mail->Port       = $gsValues['EMAIL_SMTP_PORT'];
			$mail->SMTPDebug  = 0;
			$mail->SMTPAuth   = $gsValues['EMAIL_SMTP_AUTH'];
			$mail->SMTPSecure = $gsValues['EMAIL_SMTP_SECURE'];
			$mail->Username   = $gsValues['EMAIL_SMTP_USERNAME'];
			$mail->Password   = $gsValues['EMAIL_SMTP_PASSWORD'];
		}
		
		$email_from = $gsValues['EMAIL'];
		
		if ($no_reply != false)
		{
			if ($gsValues['EMAIL_NO_REPLY'] != '')
			{
				$email_from = $gsValues['EMAIL_NO_REPLY'];
			}	
		}
		
		$mail->From = $email_from;
		$mail->FromName = $gsValues['NAME'];		
		$mail->SetFrom($email_from, $gsValues['NAME']);
		$mail->AddReplyTo($email_from, $gsValues['NAME']);
		
		// multiple emails
		$email = explode(",", $email);
		for ($i = 0; $i < count($email); ++$i)
		{
			if ($i > 4)
			{
				break;
			}
			
			$email[$i] = trim($email[$i]);
			$mail->AddAddress($email[$i], '');
		}
		
		if ($att_name != false)
		{
			$mail->AddStringAttachment($att_str,$att_name);
		}
		
		$mail->Subject = $subject;
		$mail->Body = $message;
		
		if(!$mail->Send())
		{
			return false;
		}
		else
		{
			return true;
		}
	}
?>