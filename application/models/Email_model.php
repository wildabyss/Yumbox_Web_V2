<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model
{
	// recipient_type
	const RECIPIENT_TO = 0;
	const RECIPIENT_CC = 1;
	const RECIPIENT_BCC = 2;

	public function addEmailToQueue($from_email, $from_name, $replyto_address, $replyto_name, array $recipients, array $cc, array $bcc, $subject, $body)
	{
		$this->db->trans_start();

		if (!$this->db->query('
			INSERT INTO mail_queue
				(from_address, from_name, replyto, replyto_name, subject, body, enqueue_date, sent_date, tries, try_date)
			VALUES
				(?, ?, ?, ?, ?, ?, NOW(), \'0000-00-00 00:00:00\', 0, \'0000-00-00 00:00:00\')', array(
				$from_email,
				$from_name,
				$replyto_address,
				$replyto_name,
				$subject,
				$body,
			))
		) {
			throw new Exception($this->db->error);
		}

		$mail_id = $this->db->insert_id();

		$mail_recipients = array();
		foreach ($recipients as $email => $name) {
			$mail_recipients[] = $mail_id;
			$mail_recipients[] = Email_model::RECIPIENT_TO;
			$mail_recipients[] = $email;
			$mail_recipients[] = $name;
		}
		foreach ($cc as $email => $name) {
			$mail_recipients[] = $mail_id;
			$mail_recipients[] = Email_model::RECIPIENT_CC;
			$mail_recipients[] = $email;
			$mail_recipients[] = $name;
		}
		foreach ($bcc as $email => $name) {
			$mail_recipients[] = $mail_id;
			$mail_recipients[] = Email_model::RECIPIENT_BCC;
			$mail_recipients[] = $email;
			$mail_recipients[] = $name;
		}

		if (!$this->db->query('
			INSERT INTO mail_recipient
				(mail_id, recipient_type, address, name)
			VALUES
				' . implode(', ', array_fill(0, count($recipients) + count($cc) + count($bcc), '(?, ?, ?, ?)')), $mail_recipients)
		) {
			throw new Exception($this->db->error);
		}

		$this->db->trans_complete();
	}

	public function getEmailFromQueue()
	{
		$query = $this->db->query('
			SELECT
				id AS mail_id, from_address, from_name, replyto, replyto_name, subject, body
			FROM mail_queue
			WHERE
				sent_date = \'0000-00-00 00:00:00\'
			ORDER BY try_date ASC, tries ASC, enqueue_date ASC
			LIMIT 1');
		$results = $query->result();

		if (count($results) == 0)
			return false;
		else {
			$mail = $results[0];
			$mail->recipients = array();
			$mail->cc = array();
			$mail->bcc = array();

			$query = $this->db->query('
				SELECT
					recipient_type, address, name
				FROM mail_recipient
				WHERE
					mail_id = ?', array($mail->mail_id));
			$results = $query->result();

			foreach ($results as $r) {
				switch ($r->recipient_type) {
					case Email_model::RECIPIENT_TO:
						$mail->recipients[$r->address] = $r->name;
						break;
					case Email_model::RECIPIENT_CC:
						$mail->cc[$r->address] = $r->name;
						break;
					case Email_model::RECIPIENT_BCC:
						$mail->bcc[$r->address] = $r->name;
						break;
				}
			}

			return $mail;
		}
	}

	/**
	 * Flag email as sent
	 * @return true on success
	 * @throw Exception
	 */
	public function flagEmailSent($mail_id)
	{
		if (!$this->db->query('
			DELETE FROM mail_queue
			WHERE
				id = ?', array($mail_id))
		) {
			throw new Exception($this->db->error);
		}

		return true;
	}

	/**
	 * Increment the tries column of mail_queue
	 * @return true on success
	 * @throw Exception
	 */
	public function incrementEmailTries($mail_id)
	{
		if (!$this->db->query('
			UPDATE mail_queue SET
				tries = tries + 1,
				try_date = NOW(),
			WHERE
				id = ?', array($mail_id))
		) {
			throw new Exception($this->db->error);
		}

		return true;
	}
}