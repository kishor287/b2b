<?php

namespace Panel\Server\Classes\Config;

require "../var.php";
require_once APP_PATH . "plugins/PHPMailer/src/Exception.php";
require_once APP_PATH . "plugins/PHPMailer/src/PHPMailer.php";
require_once APP_PATH . "plugins/PHPMailer/src/SMTP.php";
require_once APP_PATH . "function.php";

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class SendMail
{
    private $email_host;
    private $email_username;
    private $email_password;
    private $Website;

    public string|array $to;
    public string $subject;
    public array|string $attachment;
    public string $content;

    public string|array $cc = [];
    public function __construct()
    {
        // Initialize your email settings
        $this->email_host = 'webmail.innerxcrm.com';
        $this->email_username = 'no-reply@innerxcrm.com';
        $this->email_password = 'no-reply@77';
        $this->Website = 'innerxcrm.com';
    }

    public function setTo(array|string $email)
    {
        $this->to = $email;
    }

    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    public function setAttachment(string|array $attachment)
    {
        $this->attachment = $attachment;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }
    public function sendMail(): array|string|int
    {
        if (is_array($this->to)) {
            $response = [];
            foreach ($this->to as $to) {
                $response[] = $this->mailTo($to);
            }
            logMessage('Sending Mail:'. json_encode($response));
            return $response;
        } else {
            return $this->mailTo($this->to);
        }
    }

    public function setCC(array|string $cc)
    {
        if (is_array($cc)) {
            foreach ($cc as $value) {
                $this->cc[] = $value;
            }
        } else {
            $this->cc = $cc;
        }
    }

    private function mailTo($to): array|string|int
    {
        $mail = new PHPMailer(true); // 'true' enables exceptions
        try {
            $mail->IsSMTP();
            $mail->Host = $this->email_host;
            $mail->SMTPAuth = true;
            $mail->Port = 25;
            $mail->Username = $this->email_username;
            $mail->Password = $this->email_password;
            $mail->From = $this->email_username;
            $mail->FromName = $this->Website;
            $mail->AddAddress($to);

            if (!empty($this->attachment)) {
                $mail->AddAttachment($this->attachment);
            }
            if (!empty($this->cc) && !is_array($this->cc)) {
                $mail->addCC($this->cc);
            } else if (!empty($this->cc) && is_array($this->cc)) {
                foreach ($this->cc as $val) {
                    $mail->addCC($val);
                }
            }
            $mail->IsHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body = $this->content;
            $response = $mail->Send();
            error_log('mail response:' . $response);
            return $response;
        } catch (Exception $e) {
            error_log('mail response:' . $mail->ErrorInfo);
            return "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
