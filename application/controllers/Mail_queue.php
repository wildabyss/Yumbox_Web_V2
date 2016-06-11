<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mail_queue extends CI_Controller
{
	/**
	 * Send emails from the mailing queue
	 * @param int $count Specifies the max number of emails to be released from
	 * the queue. If -1, use the default number from config
	 */
	public function serve($count = -1)
	{
		// only execute through CLI
		if (php_sapi_name() !== 'cli') {
			show_404();
		}

		$this->load->library('mail_server');

		// if specified as -1, load the default release number
		if ($count === -1) {
			$this->config->load('config', TRUE);
			$count = (int)$this->config->item('queue_send_per_exe');
		}

		for ($i=0; $i<$count; $i++) {
			// get the highest priority email from the queue
			$mail = $this->email_model->getEmailFromQueue();
			if ($mail === false) {
				break;
			}

			// immediately send the email
			$res = $this->mail_server->send(
				$mail->from_address,
				$mail->from_name,
				$mail->replyto,
				$mail->replyto_name,
				$mail->recipients,
				$mail->cc,
				$mail->bcc,
				$mail->subject,
				$mail->body
			);
			if ($res === true){
				$this->email_model->flagEmailSent($mail->mail_id);
			} else {
				$this->email_model->incrementEmailTries($mail->mail_id);
			}
		}
	}
}