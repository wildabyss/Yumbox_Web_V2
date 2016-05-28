<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mail_queue extends CI_Controller
{
    public function serve($count = -1)
    {
        if (php_sapi_name() !== 'cli') {
            die('This method can only be executed through CLI');
        }

        $this->load->library('mail_server');

        if ($count === -1) {
            $this->config->load('secret_config', TRUE);
            $count = (int)$this->config->item('queue_send_per_exe', 'secret_config');
        }

        for ($i=0; $i<$count; $i++) {
            $mail = $this->email_model->getEmailFromQueue();
            if ($mail === false) {
                break;
            }

            try {
                if ($this->mail_server->send(
                    $mail->from_address,
                    $mail->from_name,
                    $mail->replyto,
                    $mail->replyto_name,
                    $mail->recipients,
                    $mail->cc,
                    $mail->bcc,
                    $mail->subject,
                    $mail->body
                )
                ) {
                    $this->email_model->flagEmailSent($mail->mail_id);
                }
            }
            catch (\Exception $ex) {
                $this->email_model->incrementEmailTries($mail->mail_id);
            }
        }
    }
}