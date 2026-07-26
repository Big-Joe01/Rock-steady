<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private PHPMailer $mailer;
    private string $templateDir;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->templateDir = RESOURCES_PATH . '/views/emails/';
        
        $this->configure();
    }

    private function configure(): void
    {
        if (APP_ENV === 'local') {
            $this->mailer->isSMTP();
            $this->mailer->Host = SMTP_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = SMTP_USERNAME;
            $this->mailer->Password = SMTP_PASSWORD;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = SMTP_PORT;
        } else {
            $this->mailer->isMail();
        }
        
        $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    public function to(string $email, string $name = ''): self
    {
        $this->mailer->addAddress($email, $name);
        return $this;
    }

    public function replyTo(string $email, string $name = ''): self
    {
        $this->mailer->addReplyTo($email, $name);
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    public function body(string $html): self
    {
        $this->mailer->Body = $html;
        $this->mailer->AltBody = strip_tags($html);
        return $this;
    }

    public function template(string $template, array $data = []): self
    {
        $templateFile = $this->templateDir . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Email template not found: {$template}");
        }
        
        extract($data);
        
        ob_start();
        include $templateFile;
        $html = ob_get_clean();
        
        return $this->body($html);
    }

    public function attach(string $path, string $name = ''): self
    {
        $this->mailer->addAttachment($path, $name);
        return $this;
    }

    public function send(): bool
    {
        try {
            $this->mailer->send();
            $this->reset();
            return true;
        } catch (Exception $e) {
            Logger::error('Mail sending failed: ' . $this->mailer->ErrorInfo);
            $this->reset();
            return false;
        }
    }

    private function reset(): void
    {
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();
        $this->mailer->clearReplyTos();
    }

    public static function sendEmail(string $to, string $subject, string $body, array $options = []): bool
    {
        $mail = new self();
        
        if (isset($options['reply_to'])) {
            $mail->replyTo($options['reply_to']);
        }
        
        return $mail->to($to, $options['name'] ?? '')
            ->subject($subject)
            ->body($body)
            ->send();
    }

    public static function sendTemplate(string $to, string $subject, string $template, array $data = [], array $options = []): bool
    {
        $mail = new self();
        
        if (isset($options['reply_to'])) {
            $mail->replyTo($options['reply_to']);
        }
        
        return $mail->to($to, $options['name'] ?? '')
            ->subject($subject)
            ->template($template, $data)
            ->send();
    }
}
