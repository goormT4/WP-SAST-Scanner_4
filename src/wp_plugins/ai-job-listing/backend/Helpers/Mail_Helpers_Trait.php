<?php
namespace Axilweb\AiJobListing\Helpers; 
use PHPMailer\PHPMailer\PHPMailer;
use WP_Error;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Mail_Helpers_Trait
{
 
    /**
     * Get the PHPMailer instance.
     *
     * @return PHPMailer\PHPMailer\PHPMailer The PHPMailer instance.
     *
     */
    public static function getPHPMailer()
    {
        global $phpmailer;

        // Ensure PHPMailer instance is available
        if (!($phpmailer instanceof PHPMailer\PHPMailer\PHPMailer)) {
            // Initialize PHPMailer if not already set
            $phpmailer = new PHPMailer\PHPMailer\PHPMailer(true);

            // Set email validator
            $phpmailer::$validator = static function ($email) {
                return (bool) is_email($email);
            };
        }

        return $phpmailer;
    }

    /**
     * Sends an email to the specified recipient using SMTP or the default WordPress mail function.
     *
     * This function checks whether SMTP settings are configured and attempts to send the email
     * via SMTP if the settings are available. If SMTP is not configured, it falls back to the default
     * WordPress `wp_mail()` function to send the email.
     *
     * @param string $to The recipient email address.
     * @param string $subject The subject of the email.
     * @param string $message The body content of the email.
     * @param string $headers Optional headers for the email, such as from email address and content type.
     * 
     * @return bool|WP_Error Returns `true` on success, `false` on failure, or a `WP_Error` object if an error occurs.
     */
    public static function emailSend($to, $subject, $message, $headers = '')
    { 
       
     
        return self::sendEmailViaWpMail($to, $subject, $message, $headers);
        
    }

    /**
     * Sends an email using the WordPress `wp_mail` function.
     *
     * This function uses WordPress' built-in `wp_mail` function to send an email to the specified recipient.
     * It is used when SMTP settings are not configured.
     *
     * @param string $to The recipient email address.
     * @param string $subject The subject of the email.
     * @param string $message The body content of the email.
     * @param string $headers Optional headers for the email, such as from email address and content type.
     * 
     * @return bool Returns `true` if the email was successfully sent, `false` otherwise.
     */
    public static function sendEmailViaWpMail($to, $subject, $message, $headers = '')
    {
          
        return $sent = wp_mail($to, $subject, $message, $headers);
    }


    /**
     * Send email using PHPMailer with SMTP.
     *
     * @param string $to      The recipient's email address.
     * @param string $subject The subject of the email.
     * @param string $message The body of the email.
     * @param string $headers The headers (optional).
     *
     * @return bool True if the email was sent successfully, false otherwise.
     */
    public static function sendEmailViaSmtp($to, $subject, $message, $headers = '') {
        // Get the PHPMailer instance
        $phpmailer = self::getPHPMailer();
        $admin_email_smtp = Helpers::getSmtpSettings('smtp');

        $smtp_host = $admin_email_smtp['smtp_host']; // Example of getting the SMTP host from the settings
        $smtp_username = $admin_email_smtp['smtp_username'];
        $smtp_password = $admin_email_smtp['smtp_password'];
 
        try {
            // SMTP Configuration
            $phpmailer->isSMTP();
            $phpmailer->Host       = $smtp_host;      // Your SMTP host
            $phpmailer->SMTPAuth   = true;
            $phpmailer->Username   = $smtp_username ;  // SMTP username
            $phpmailer->Password   = $smtp_password ;  // SMTP password
            $phpmailer->SMTPSecure = "tls"; // TLS Encryption
            $phpmailer->Port       = 587;                         // SMTP Port

            // Email Details
            $phpmailer->setFrom('no-reply@axilweb.com', 'Your Site Name'); // Replace with your sender info
            $phpmailer->addAddress($to);    // Recipient
            $phpmailer->Subject = $subject;
            $phpmailer->Body    = $message;

            $phpmailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            if (!empty($headers)) {
                $phpmailer->addCustomHeader($headers); // Adding headers if available
            }

            // Send email
            return $phpmailer->send();
        } catch (Exception $e) {
            // Log the error if email sending fails
            // error_log('PHPMailer SMTP error: ' . $phpmailer->ErrorInfo);
            return false;
        }
    }

}