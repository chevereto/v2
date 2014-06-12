<?php if(!defined('access') or !access) die('This file cannot be directly accessed.'); ?>
<?php include_theme_header(); ?>
    <div id="content" class="pages page_contact">
    	<h1>Contact us</h1>
        <p>This is an example page for your chevereto site. You can edit this file located in <code>/content/themes/Peafowl/pages/contact.php</code></p>
        <p>As you may see, this example is quite different from others because it features a simple contact form using PHPMailer.</p>
        <p>Please notice that  you need to tweak the code on <code>contact.php</code> before this works.</p>
		
        <?php				
        	if ($_POST['name'] and $_POST['email'] and $_POST['message']) {
				$result  = true;
				$name    = $_POST['name'];
				$email   = trim($_POST['email']);
				$message = $_POST['message'];
				
				$to = 'inbox@chevereto.com';
				$from = 'inbox@chevereto.com';
				$subject = 'Contact form example';
			
				$mailbody .= "Name: ".$name."\n";
				$mailbody .= "E-mail: ".$email."\n\n";
				
				$mailbody .= "Message: ".$message."\n\n";
				
				$mailbody .= "--"."\n\n";
				
				$mailbody .= "Sent from: ".__CHV_BASE_URL__."\n";
				 $mailbody .= "IP: ".$_SERVER['REMOTE_ADDR']."\n"; 
				$mailbody .= "Browser: ".getenv("HTTP_USER_AGENT")."\n\n";
				
				$use_phpmailer = false; // true: uses php mailer | false: uses the default mail() function
				
				// We are going to check the fields...
				if(check_email_address($email) and check_value($name) and check_value($message)) {
					// Waht are we using?
					if($use_phpmailer) {
						// Hail PHPMailer!
						require_once __CHV_PATH_CLASSES__ . 'class.phpmailer.php';
						$mail = new phpmailer();
						$mail->Subject = $subject;
						$mail->Body = $mailbody;
						$mail->From = $from;		
						$mail->FromName = $name.' (contact form)';
						$mail->AddAddress($to);						
						$mail->AddReplyTo($email); // indicates ReplyTo headers	
						$mail->Mailer = "mail"; // mail|smtp|sendmail						
						//$mail->Host = ''; // SMTP host
						//$mail->SMTPAuth = true; // Turn on SMTP auth (use when SMTP needs to indicate the user:password
						//$mail->SMTPSecure = 'ssl'; // Values: ssl|tls
						//$mail->Username = ''; // For SMTPAuth
						//$mail->Password = ''; // For SMTPAuth
						//$mail->SMTPDebug = 1; // Problems sending with SMTP? Uncomment this line to get the debug.
						$mail->Timeout = 30;
						$success = $mail->Send();
					} else {
						$success = mail($to, $subject, $mailbody, 'From: '.$email); // $to, $subject, $mailbody
					}
					// Succes true...
					if($success) {
						$output = 'Form submitted, we will contact you soon.';
						$contact_class = 'contact-ok';
					} else { // Oh no... errors.
						$output = 'There was an error sending your request. Please try again later.';
						$contact_class = 'contact-error';
					}
				} else { // Invalid values...
					$output = 'Please fill correct all the form fields.';
					$contact_class = 'contact-error';
				}
        	}
		?>
        <?php if ($result) : ?>
        <div class="contact-result <?php echo $contact_class; ?>"><?php echo $output; ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        	<div><label for="name">Name:</label> <input type="text" name="name" id="name" /></div>
            <div><label for="email">E-mail:</label> <input type="text" name="email" id="email" /></div>
            <div><label for="message">Message:</label> <textarea name="message" id="message" ></textarea></div>
            <div><input type="submit" value="Send form" class="send-form"/></div>
        </form>
    </div>

<?php include_theme_footer(); ?>