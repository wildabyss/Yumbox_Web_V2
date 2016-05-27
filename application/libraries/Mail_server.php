<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail_server {
	private function createConnection() {
		$mail = new PHPMailer();

		$CI =& get_instance();
		$CI->config->load('secret_config', TRUE);

		// Enable verbose debug output
		//$mail->SMTPDebug = 3;

		// Set mailer to use SMTP
		$mail->isSMTP();

		// Specify main and backup SMTP servers
		$mail->Host = $CI->config->item('smtp_host', 'secret_config');

		// TCP port to connect to (587)
		$mail->Port = $CI->config->item('smtp_port', 'secret_config');

		// Enable SMTP authentication, 0 / 1
		if ($CI->config->item('smtp_authentication', 'secret_config') == '1') {
			$mail->SMTPAuth = true;
		}

		// SMTP username
		$mail->Username = $CI->config->item('smtp_username', 'secret_config');

		// SMTP password
		$mail->Password = $CI->config->item('smtp_password', 'secret_config');

		// Enable TLS encryption (optional), `` / `ssl` / `tls` are accepted
		$smtp_security = $CI->config->item('smtp_security', 'secret_config');
		if ($smtp_security != '') {
			$mail->SMTPSecure = $smtp_security;
		}

		// Set email format to HTML
		$mail->isHTML(true);

		return $mail;
	}

	public function sendFromWebsite($recipient_email, $recipient_name, $subject, $body) {
		$CI =& get_instance();
		$CI->config->load('secret_config', TRUE);

        $from_email = $CI->config->item('website_email_address', 'secret_config');
        $from_name = $CI->config->item('website_email_name', 'secret_config');
        $replyto_address = $CI->config->item('website_replyto_address', 'secret_config');
        $replyto_name = $CI->config->item('website_replyto_name', 'secret_config');

        if ($CI->config->item('queue_mail', 'secret_config') == '1') {
            $CI->email_model->addEmailToQueue(
                $from_email,
                $from_name,
                $replyto_address,
                $replyto_name,
                array(
                    $recipient_email => $recipient_name,
                ),
                array(),
                array(),
                $subject,
                $body
            );
        }
        else {
            return $this->send(
                $from_email,
                $from_name,
                $replyto_address,
                $replyto_name,
                array(
                    $recipient_email => $recipient_name,
                ),
                array(),
                array(),
                $subject,
                $body
            );
        }
	}

    public function send($from_email, $from_name, $replyto_address, $replyto_name, array $recipients, array $cc, array $bcc, $subject, $body)
    {
        $mail = $this->createConnection();

        $CI =& get_instance();
        $CI->config->load('secret_config', TRUE);

        $mail->setFrom($from_email, $from_name);

        if (count($recipients) == 0) {
            throw new \Exception('Can not send an email without any recipient');
        }

        foreach ($recipients as $email => $name) {
            $mail->addAddress($email, $name);
        }

        foreach ($cc as $email => $name) {
            $mail->addCC($email, $name);
        }

        foreach ($bcc as $email => $name) {
            $mail->addBCC($email, $name);
        }

        if ($replyto_address != '') {
            $mail->addReplyTo($replyto_address, $replyto_name);
        }

        $mail->Subject = $subject;
        $mail->Body    = $body;

        if (!$mail->send()) {
            throw new \Exception('Mailer Error: ' . $mail->ErrorInfo);
        } else {
            return true;
        }
    }
}