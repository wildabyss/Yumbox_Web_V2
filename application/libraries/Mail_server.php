<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mail_server {
	/**
	 * Establish PHPMailer connection and return the PHPMailer object
	 * with the correct settings
	 */
	protected function createConnection() {
		$mail = new PHPMailer();

		$CI =& get_instance();
		$CI->config->load('config', TRUE);

		// Enable verbose debug output
		//$mail->SMTPDebug = 3;

		// Set mailer to use SMTP
		$mail->isSMTP();

		// Specify main and backup SMTP servers
		$mail->Host = $CI->config->item('smtp_host');

		// TCP port to connect to (587)
		$mail->Port = $CI->config->item('smtp_port');

		// Enable SMTP authentication, 0 / 1
		if ($CI->config->item('smtp_authentication') == '1') {
			$mail->SMTPAuth = true;
		}

		// SMTP username
		$mail->Username = $CI->config->item('smtp_username');

		// SMTP password
		$mail->Password = $CI->config->item('smtp_password');

		// Enable TLS encryption (optional), `` / `ssl` / `tls` are accepted
		$smtp_security = $CI->config->item('smtp_security');
		if ($smtp_security != '') {
			$mail->SMTPSecure = $smtp_security;
		}

		// Set email format to HTML
		$mail->isHTML(true);

		return $mail;
	}

	/**
	 * Send an email with the specified recipient and body
	 * Depending on the configuration, immediately send or add to mail queue
	 */
	public function sendFromWebsite($recipient_email, $recipient_name, $subject, $body) {
		$CI =& get_instance();
		$CI->config->load('config', TRUE);

		$from_email = $CI->config->item('website_email_address');
		$from_name = $CI->config->item('website_email_name');
		$replyto_address = $CI->config->item('website_replyto_address');
		$replyto_name = $CI->config->item('website_replyto_name');

		if ($CI->config->item('queue_mail') === true) {
			// Queue the mail to the mail server
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
		} else {
			// Immediate send
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

	/**
	 * Immediately send an email with the specified recipient and body
	 * To be used from the queue or sendFromWebsite method
	 * @param $from_email
	 * @param $from_name
	 * @param $replyto_address
	 * @param $replyto_name
	 * @param array $recipients
	 * @param array $cc
	 * @param array $bcc
	 * @param $subject
	 * @param $body
	 * @throws Exception
	 * @throws phpmailerException
	 * @return true on success, error on failure
	 */
	public function send($from_email, $from_name, $replyto_address, $replyto_name, array $recipients, array $cc, array $bcc, $subject, $body)
	{
		$mail = $this->createConnection();

		$CI =& get_instance();
		$CI->config->load('config', TRUE);

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
		$mail->Body	= $body;

		if (!$mail->send()) {
			return $mail->ErrorInfo;
		} else {
			return true;
		}
	}
}