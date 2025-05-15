<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$mail = new PHPMailer(true);

try {

    //Server settings
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                           // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'dome.zurlo@gmail.com';                             // SMTP username
    $mail->Password   = 'eawh hrrf zend cqrs';                             // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

     //Recipients - main edits
    $mail->setFrom('suitegrottadellaluna@gmail.com', 'Suite Grotta della luna');             // Email Address and Name FROM
    $mail->addAddress('suitegrottadellaluna@gmail.com', '');                  // Email Address and Name TO - Name is optional
    $mail->addReplyTo('noreply@domain.com', 'Suite Grotta della luna');       // Email Address and Name NOREPLY
    $mail->isHTML(true);                                                       
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->Subject = 'Suite Grotta della luna';                                // Email Subject       

    // Email verification, do not edit
    function isEmail($email_contact ) {
        return(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/",$email_contact));
    }

   // Form fields
    $name_contact     = $_POST['name_contact'];
    $lastname_contact     = $_POST['lastname_contact'];
    $email_contact    = $_POST['email_contact'];
    $phone_contact    = $_POST['phone_contact'];
    $message_contact = $_POST['message_contact'];
    $verify_contact   = $_POST['verify_contact'];
    $lang_selected = isset($_POST['lang_selected']) ? $_POST['lang_selected'] : 'Italian';

    // Multilingual for error and success messages
    $messages = [
        'English' => [
            'name_required' => 'You must enter your Name.',
            'lastname_required' => 'Please enter your Last Name.',
            'email_required' => 'Please enter a valid email address.',
            'email_invalid' => 'You have entered an invalid e-mail address.',
            'phone_required' => 'Please enter a valid phone number.',
            'phone_invalid' => 'Phone number can only contain numbers.',
            'message_required' => 'Please enter your message.',
            'verify_required' => 'Please enter the verification number.',
            'verify_invalid' => 'The verification number you entered is incorrect.',
            'success_title' => 'Thank you!',
            'success_text' => 'Request successfully sent!',
        ],
        'Italian' => [
            'name_required' => 'Devi inserire il tuo nome.',
            'lastname_required' => 'Per favore inserisci il tuo cognome.',
            'email_required' => 'Per favore inserisci un indirizzo email valido.',
            'email_invalid' => 'Hai inserito un indirizzo email non valido.',
            'phone_required' => 'Per favore inserisci un numero di telefono valido.',
            'phone_invalid' => 'Il numero di telefono può contenere solo numeri.',
            'message_required' => 'Per favore inserisci il tuo messaggio.',
            'verify_required' => 'Per favore inserisci il numero di verifica.',
            'verify_invalid' => 'Il numero di verifica inserito non è corretto.',
            'success_title' => 'Grazie!',
            'success_text' => 'Richiesta inviata con successo!',
        ]
    ];
    $msg = $messages[$lang_selected];

    if(trim($name_contact) == '') {
        echo '<div class="error_message">' . $msg['name_required'] . '</div>';
        exit();
    } else if(trim($lastname_contact) == '') {
        echo '<div class="error_message">' . $msg['lastname_required'] . '</div>';
        exit();
    } else if(trim($email_contact) == '') {
        echo '<div class="error_message">' . $msg['email_required'] . '</div>';
        exit();
    } else if(!isEmail($email_contact)) {
        echo '<div class="error_message">' . $msg['email_invalid'] . '</div>';
        exit();
    } else if(trim($phone_contact) == '') {
        echo '<div class="error_message">' . $msg['phone_required'] . '</div>';
        exit();
    } else if(!is_numeric($phone_contact)) {
        echo '<div class="error_message">' . $msg['phone_invalid'] . '</div>';
        exit();
    } else if(trim($message_contact) == '') {
        echo '<div class="error_message">' . $msg['message_required'] . '</div>';
        exit();
    } else if(!isset($verify_contact) || trim($verify_contact) == '') {
        echo '<div class="error_message">' . $msg['verify_required'] . '</div>';
        exit();
    } else if(trim($verify_contact) != '4') {
        echo '<div class="error_message">' . $msg['verify_invalid'] . '</div>';
        exit();
    }                               
            
    // Get the email's html content
    $email_html = file_get_contents('template-email.html');

   // Setup html content for owner (always italian)
    $owner_title = "Messaggio da Suite Grotta della luna";
    $e_content = "Sei stato contattato da <strong>$name_contact $lastname_contact</strong> con il seguente messaggio:<br><br>$message_contact<br><br>Puoi contattare $name_contact via email all'indirizzo $email_contact o al telefono $phone_contact";
    $body = str_replace(array('message_title','message'),array($owner_title, $e_content),$email_html);
    $mail->MsgHTML($body);

    $mail->CharSet = 'UTF-8'; //Force UTF for special characters

    $mail->send();

    // Confirmation/autoreply email sent to the user who filled the form
    $mail->ClearAddresses();
    $mail->isSMTP();
    $mail->addAddress($_POST['email_contact']); // Email address entered on form
    $mail->isHTML(true);
    $mail->Subject    = ($lang_selected == 'English') ? 'Confirmation info request for Suite grotta della luna' : 'Conferma richiesta info per Suite grotta della luna'; // Custom subject
    
    // Get the email's html content
    $email_html_confirm = file_get_contents('confirmation.html');

    // Setup html content for user
    if ($lang_selected == 'English') {
        $confirm_title = "Thank you for your time!";
        $confirm_content = "Thank you for contacting us, we will reply as soon as possible!";
    } else {
        $confirm_title = "Grazie per il tuo tempo!";
        $confirm_content = "Grazie per averci contattato, ti risponderemo al più presto!";
    }
    $body = str_replace(
        array('message_title','message'),
        array($confirm_title, $confirm_content),
        $email_html_confirm
    );
    $mail->MsgHTML($body);

    $mail->CharSet = 'UTF-8'; //Force UTF for special characters

    $mail->Send();

    // Success message
    echo '<div id="success_page">
            <div class="icon icon--order-success svg">
                 <svg xmlns="http://www.w3.org/2000/svg" width="72px" height="72px">
                  <g fill="none" stroke="#8EC343" stroke-width="2">
                     <circle cx="36" cy="36" r="35" style="stroke-dasharray:240px, 240px; stroke-dashoffset: 480px;"></circle>
                     <path d="M17.417,37.778l9.93,9.909l25.444-25.393" style="stroke-dasharray:50px, 50px; stroke-dashoffset: 0px;"></path>
                  </g>
                 </svg>
             </div>
            <h5>' . $msg['success_title'] . '<span>' . $msg['success_text'] . '</span></h5>
        </div>';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }  
?>
