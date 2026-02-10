<?php

require_once './Database.php';

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
        $stmt = $this->db->prepare('insert into users (name, email, contact, sexe, eglise, leader_nom, leader_contact, paiement, timestamp, created_at, updated_at) values (:name, :email, :contact, :sexe, :eglise, :leader_nom, :leader_contact, :paiement, :timestamp, :created_at, :updated_at)');
        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'contact' => $data['contact'],
            'sexe' => $data['sexe'],
            'eglise' => $data['eglise'],
            'leader_nom' => $data['leader_nom'],
            'leader_contact' => $data['leader_contact'],
            'paiement' => $data['paiement'],
            'timestamp' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function sendMail(string $to, string $subject, string $body): bool
    {
        $apiKey = getenv('RESEND_API_KEY');

        if (!$apiKey) {
            error_log("RESEND_API_KEY manquante");
            return false;
        }

        $payload = [
            "from" => getenv('MAIL_FROM_NAME') . " <" . getenv('MAIL_FROM') . ">",
            "to" => [$to],
            "subject" => $subject,
            "html" => $body,
        ];

        $ch = curl_init("https://api.resend.com/emails");

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$apiKey}",
                "Content-Type: application/json",
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log("Erreur cURL Resend : " . curl_error($ch));
            curl_close($ch);
            return false;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        error_log("Erreur Resend ({$httpCode}) : " . $response);
        return false;
    }


    public function template(array $userData, string $eventName = "Merci de votre inscription") {
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($userData['email']);
        
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
        <meta charset='UTF-8'>
        <title>Votre Ticket - {$eventName}</title>
        </head>
        <body style='margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;'>

        <table width='100%' cellpadding='0' cellspacing='0'>
        <tr>
        <td align='center' style='padding:40px 20px;'>

        <!-- Ticket Container -->
        <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);'>

        <!-- Header avec dégradé -->
        <tr>
        <td style='background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);padding:30px;text-align:center;'>
        <h1 style='margin:0;color:#ffffff;font-size:28px;font-weight:bold;'>🎉 TICKET CONFIRMÉ</h1>
        <p style='margin:10px 0 0 0;color:#e0e7ff;font-size:16px;'>{$eventName}</p>
        </td>
        </tr>

        <!-- Bande de statut -->
        <tr>
        <td style='background:#10b981;padding:12px;text-align:center;'>
        <span style='color:#ffffff;font-weight:bold;font-size:14px;'>✓ INSCRIPTION VALIDÉE</span>
        </td>
        </tr>

        <!-- Informations du participant -->
        <tr>
        <td style='padding:30px;'>
        
        <div style='text-align:center;margin-bottom:25px;'>
        <h2 style='margin:0 0 5px 0;color:#111827;font-size:24px;'>{$userData['name']}</h2>
        <p style='margin:0;color:#6b7280;font-size:14px;'>{$userData['email']}</p>
        </div>

        <!-- QR Code -->
        <div style='text-align:center;margin:25px 0;'>
        <img src='{$qrCodeUrl}' alt='QR Code' style='border:3px solid #e5e7eb;border-radius:8px;padding:10px;background:#ffffff;'>
        <p style='margin:10px 0 0 0;color:#6b7280;font-size:12px;'>Présentez ce QR code à l'entrée</p>
        </div>

        <!-- Détails en colonnes -->
        <table width='100%' cellpadding='0' cellspacing='0' style='margin-top:30px;'>
        <tr>
        <td width='50%' style='padding:15px;background:#f9fafb;border-radius:8px;'>
        <p style='margin:0;color:#6b7280;font-size:12px;font-weight:bold;'>📞 CONTACT</p>
        <p style='margin:5px 0 0 0;color:#111827;font-size:14px;'>{$userData['contact']}</p>
        </td>
        <td width='10'></td>
        <td width='50%' style='padding:15px;background:#f9fafb;border-radius:8px;'>
        <p style='margin:0;color:#6b7280;font-size:12px;font-weight:bold;'>👤 SEXE</p>
        <p style='margin:5px 0 0 0;color:#111827;font-size:14px;'>" . strtoupper($userData['sexe']) . "</p>
        </td>
        </tr>
        </table>

        <table width='100%' cellpadding='0' cellspacing='0' style='margin-top:15px;'>
        <tr>
        <td width='50%' style='padding:15px;background:#f9fafb;border-radius:8px;'>
        <p style='margin:0;color:#6b7280;font-size:12px;font-weight:bold;'>⛪ ÉGLISE</p>
        <p style='margin:5px 0 0 0;color:#111827;font-size:14px;'>{$userData['eglise']}</p>
        </td>
        <td width='10'></td>
        <td width='50%' style='padding:15px;background:#f9fafb;border-radius:8px;'>
        <p style='margin:0;color:#6b7280;font-size:12px;font-weight:bold;'>💳 PAIEMENT</p>
        <p style='margin:5px 0 0 0;color:#111827;font-size:14px;font-weight:bold;color:#10b981;'>" . strtoupper($userData['paiement']) . "</p>
        </td>
        </tr>
        </table>

        <!-- Informations Leader -->
        <div style='margin-top:25px;padding:20px;background:#fef3c7;border-left:4px solid #f59e0b;border-radius:8px;'>
        <p style='margin:0;color:#92400e;font-size:14px;font-weight:bold;'>👥 Référent Spirituel</p>
        <p style='margin:8px 0 0 0;color:#78350f;font-size:14px;'>
        <strong>{$userData['leader_nom']}</strong><br>
        📞 {$userData['leader_contact']}
        </p>
        </div>

        <!-- Instructions importantes -->
        <div style='margin-top:30px;padding:20px;background:#eff6ff;border-radius:8px;'>
        <p style='margin:0;color:#1e40af;font-size:14px;font-weight:bold;'>ℹ️ Instructions importantes</p>
        <ul style='margin:10px 0 0 0;padding-left:20px;color:#1e3a8a;font-size:13px;line-height:1.8;'>
        <li>Présentez-vous 30 minutes avant le début</li>
        <li>Apportez une pièce d'identité</li>
        <li>Conservez ce ticket sur votre téléphone</li>
        <li>En cas de problème, contactez votre leader</li>
        </ul>
        </div>

        </td>
        </tr>

        <!-- Footer -->
        <tr>
        <td style='background:#111827;color:#9ca3af;padding:25px;text-align:center;font-size:12px;'>
        <p style='margin:0 0 10px 0;'>© " . date('Y') . " - Tous droits réservés</p>
        <p style='margin:0;color:#6b7280;'>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
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
