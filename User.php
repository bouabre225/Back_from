<?php

require_once './Database.php';

// Configuration for mail sending
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

class User {
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    /*public function findByEmail(string $email) {
        $stmt = $this->db->prepare('select * from users where email = :email');
        $stmt->execute([
            'email' => $email
        ]);

        return $stmt->fetch();
    }*/

    public function create(array $data) {
        $stmt = $this->db->prepare('insert into users (name, email, password, contact, sexe, eglise, leader_nom, leader_contact, paiement) values (:name, :email, :password, :contact, :sexe, :eglise, :leader_nom, :leader_contact, :paiement)');
        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'contact' => $data['contact'],
            'sexe' => $data['sexe'],
            'eglise' => $data['eglise'],
            'leader_nom' => $data['leader_nom'],
            'leader_contact' => $data['leader_contact'],
            'paiement' => $data['paiement'],
        ]);
    }

    public function sendMail(string $to, string $subject, string $body) {

        $config = require __DIR__ . '/config/mail.php';
        $mail = new PHPMailer(true);
        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['port'];
            // Destinataires
            $mail->setFrom($mail->Username, 'Confirmation');
            $mail->addAddress($to);
            // Contenu du mail
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi du mail: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function template(string $titre, string $message, ?string $ctaUrl = null) {
        $cta = '';
    if ($ctaUrl) {
        $cta = "
            <tr>
            <td align='center' style='padding: 30px;'>
            <a href='{$ctaUrl}'
            style='background:#4f46e5;color:#ffffff;
            padding:14px 24px;
            text-decoration:none;
            border-radius:6px;
            display:inline-block;
            font-weight:bold;'>
            Voir le détail
            </a>
            </td>
            </tr>";
                }

                return "
            <!DOCTYPE html>
            <html lang='fr'>
            <head>
            <meta charset='UTF-8'>
            <title>{$titre}</title>
            </head>
            <body style='margin:0;padding:0;background:#f3f4f6;'>

            <table width='100%' cellpadding='0' cellspacing='0'>
            <tr>
            <td align='center'>

            <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;margin:40px 0;border-radius:8px;overflow:hidden;'>

            <!-- Header -->
            <tr>
            <td style='background:#111827;color:#ffffff;padding:24px;text-align:center;'>
            <h1 style='margin:0;font-size:22px;'>Mon Application</h1>
            </td>
            </tr>

            <!-- Content -->
            <tr>
            <td style='padding:32px;color:#111827;font-family:Arial,sans-serif;font-size:16px;line-height:1.6;'>
            <h2 style='margin-top:0;color:#111827;'>{$titre}</h2>
            <p>{$message}</p>
            </td>
            </tr>

            {$cta}

            <!-- Footer -->
            <tr>
            <td style='background:#f9fafb;color:#6b7280;
            padding:20px;text-align:center;font-size:12px;'>
            © " . date('Y') . " Mon Application - Email automatique
            </td>
            </tr>

            </table>

            </td>
            </tr>
            </table>

            </body>
            </html>
            ";
    }
}
