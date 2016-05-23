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
		$mail = $this->createConnection();

		$CI =& get_instance();
		$CI->config->load('secret_config', TRUE);

		$mail->setFrom($CI->config->item('website_email_address', 'secret_config'), $CI->config->item('website_email_name', 'secret_config'));
		$mail->addAddress($recipient_email, $recipient_name);
		$website_replyto_address = $CI->config->item('website_replyto_address', 'secret_config');
		if ($website_replyto_address != '') {
			$mail->addReplyTo($website_replyto_address, $CI->config->item('website_replyto_name', 'secret_config'));
		}

		$mail->Subject = $subject;
		$mail->Body    = $body;

		if (!$mail->send()) {
			throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
		} else {
			return true;
		}
	}
}