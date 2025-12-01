<?php

// Global mail headers for use in functions
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: MUNext <noreply@munext.com>" . "\r\n";

$siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

// Make it available via $GLOBALS (and as a reference)
$GLOBALS['headers'] = &$headers;
$GLOBALS['siteUrl'] = &$siteUrl;






// Send welcome email
function sendUnsubcriptionEmail($email){

    global $headers;
    global $siteUrl;
    $subject = "Welcome to MUNext Newsletter!";

    $welcomeMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #862633 0%, #6b1e28 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; }
            .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to MUNext!</h1>
            </div>
            <div class='content'>
                <h2>You're all set!</h2>
                <p>Your subscription to MUNext newsletter has been confirmed. You'll now receive:</p>
                <ul>
    <li>Latest job postings from top employers</li>
    <li>Career development tips</li>
    <li>Exclusive opportunities for MUN students</li>
    <li>Campus recruitment updates</li>
                </ul>
                <p><strong>Stay connected and never miss an opportunity!</strong></p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " MUNext - Memorial University Job Board</p>
                <p><a href='$siteUrl/newsletter-unsubscribe.php?email=" . urlencode($email) . "' style='color: #fff;'>Unsubscribe</a></p>
            </div>
        </div>
    </body>
    </html>
    ";
            
     // $headers = "MIME-Version: 1.0" . "\r\n";
     // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
     // $headers .= "From: MUNext <noreply@munext.com>" . "\r\n";
            
    return mail($email, $subject, $welcomeMessage, $headers);
}



















// Function to send confirmation email
function sendSubscriptionEmail($email, $token) {
    // Use the global headers defined at the top of the file
    global $headers;

    $siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $confirmLink = $siteUrl . "/newsletter-confirm.php?token=" . $token;
    
    $subject = "Confirm Your MUNext Newsletter Subscription";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #862633 0%, #6b1e28 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; }
            .button { display: inline-block; padding: 15px 30px; background: #862633; color: white; text-decoration: none; border-radius: 25px; font-weight: 600; margin: 20px 0; }
            .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 10px 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to MUNext Newsletter!</h1>
            </div>
            <div class='content'>
                <h2>Thank you for subscribing!</h2>
                <p>You're one step away from receiving the latest job opportunities from Memorial University's premier job board.</p>
                <p>Please confirm your subscription by clicking the button below:</p>
                <p style='text-align: center;'>
                    <a href='{$confirmLink}' class='button'>Confirm Subscription</a>
                </p>
                <p>If the button doesn't work, copy and paste this link into your browser:</p>
                <p style='word-break: break-all; color: #862633;'>{$confirmLink}</p>
                <p><strong>What you'll receive:</strong></p>
                <ul>
                    <li>New job postings tailored for MUN students and alumni</li>
                    <li>Career tips and advice</li>
                    <li>Exclusive employer opportunities</li>
                    <li>Campus recruitment events</li>
                </ul>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " MUNext - Memorial University Job Board</p>
                <p>If you didn't subscribe to this newsletter, please ignore this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // $headers = "MIME-Version: 1.0" . "\r\n";
    // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // $headers .= "From: MUNext <noreply@munext.com>" . "\r\n";
    
    return mail($email, $subject, $message, $headers);
}