<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<TITLE>Отправить ошибку</TITLE>
<style type="text/css">
body {
margin: 23px 28px 0 28px;
font-size:14px;
font-family:Helvetica, Sans-serif, Arial;
line-height:2em;
}
form {margin: 0;}
.text {
font-weight: bold;
font-size:12px;
color:#777;
}
.copyright {
font-size:11px;
color:#777;
}
.mclose a{
font:bold 16px Verdana;
color:#7f7f7f;
position:absolute;
right:12px;
top:9px;
display:block;
text-decoration:none;
}
.mclose a:hover{
color: #000;
}
#m{
border: 1px solid silver;
padding: 3px;
width: 294px;
border-radius:4px;
font-size: 13px;
background-color: #fff;
}
#m strong{
color:red;
}
</style>

<script language="JavaScript"> 
var p=top;
function readtxt()
{ if(p!=null)document.forms.mistake.url.value=p.loc
 if(p!=null)document.forms.mistake.mis.value=p.mis
}
function hide()
{ var win=p.document.getElementById('mistake');
win.parentNode.removeChild(win);
}
</script>

<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// If necessary, modify the path in the require statement below to refer to the
// location of your Composer autoload.php file.
require_once dirname(__DIR__).'/globals.php';
require_once dirname(__DIR__). '/vendor/autoload.php';

if (isset($_POST['submit'])) { 

// Replace sender@example.com with your "From" address.
// This address must be verified with Amazon SES.
$sender = 'poetrychinese@gmail.com';
$senderName = 'chinese-poetry.ru errors checking service';

// Replace recipient@example.com with a "To" address. If your account
// is still in the sandbox, this address must be verified.
$recipient = 'poetrychinese@gmail.com';
$recipient2 = 'alex.druk@gmail.com';

// Replace smtp_username with your Amazon SES SMTP user name.
$usernameSmtp = UserConfig::$usernameSmtp;

// Replace smtp_password with your Amazon SES SMTP password.
$passwordSmtp = UserConfig::$passwordSmtp;

// Specify a configuration set. If you do not want to use a configuration
// set, comment or remove the next line.
$configurationSet = 'config_set';

// If you're using Amazon SES in a region other than US West (Oregon),
// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
// endpoint in the appropriate region.
$host = 'email-smtp.us-west-2.amazonaws.com';
$port = 587;

// The subject line of the email
$subject = 'Error on chinese_poetry.ru';

$ip = getenv('REMOTE_ADDR');
$url = (trim($_POST['url']));
$mis =  (trim($_POST['mis']));
$comment =  substr(htmlspecialchars(trim($_POST['comment'])), 0, 100000);
if (empty($comment)) {$coment = '';}
$records = check_mistakes($mis, $ip, $url);
$send_mail = true;
if (count($records) > 0) {
    $send_mail = false;
}
// The plain-text body of the email
$bodyText =  "Error on chinese_poetry.ru: on".$url.' errror: '.$mis;
mistakes_insert_record($mis, $ip, $url, $comment);
// The HTML-formatted body of the email
$bodyHtml = '
 <html>
<head>
<title>Ошибка на сайте</title>
</head>
<body>
<strong>Адрес страницы:</strong> <a href="'.$url.'">'.$url.'</a>
<br/>
<strong>Ошибка:</strong> '.$mis.'
<br/>
<strong>Комментарий:</strong> '.$comment.'
<br/>                               
<strong>IP:</strong> '.$ip.'
</body>
</html>
';

$mail = new PHPMailer(true);

try {
    // Specify the SMTP settings.
    $mail->isSMTP();
    $mail->setFrom($sender, $senderName);
    $mail->Username   = $usernameSmtp;
    $mail->Password   = $passwordSmtp;
    $mail->Host       = $host;
    $mail->Port       = $port;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'tls';
    $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

    // Specify the message recipients.
    $mail->addAddress($recipient);
    $mail->addCC($recipient2);
     // You can also add CC, BCC, and additional To recipients here.

    // Specify the content of the message.
    $mail->isHTML(true);
    $mail->Subject    = $subject;
    $mail->Body       = $bodyHtml;
    $mail->AltBody    = $bodyText;
    if ($send_mail) {
        $mail->Send();
    }
    $sent =  "Your email sent!";
} catch (phpmailerException $e) {
    echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
} catch (Exception $e) {
    echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
}
echo '<div class="mclose"><a href="javascript:void(0)" onclick="hide()" title="Закрыть">&times;</a></div><br><center>'.$sent.'<br>Спасибо!<br>Ваше сообщение отправлено.<br><br><input onclick="hide()" type="button" value="Закрыть окно" id="close" name="close"><br><br><center>'; 
exit();
}  
?>

</head>
<body onload=readtxt()>
<div class="mclose"><a href="javascript:void(0)" onclick="hide()" title="Закрыть">&times;</a></div>
<span class="text">
Адрес страницы :
 </span>
<br /> 
<form name="mistake" action="" method=post>
<input id="m" type="text" name="url" size="35" readonly="readonly">
              <span class="text">
              Ошибка :
              </span><br />
              <div id="m" style="line-height:normal;height: 75px;width: 287px;">
<script language="JavaScript">
	document.write(p.mis); 
</script>
              </div>
              <input type="hidden" id="m" rows="5" name="mis" readonly="readonly"></textarea>
              <span class="text">
              Комментарий :
              </span>
              <br /> 
              <textarea id="m" rows="5" name="comment" cols="30"></textarea> 
              <div style="margin-top: 7px"><input type="submit" value="Отправить" name="submit">
              <input onclick="hide()" type="button" value="Отмена" id="close" name="close"> 
              </div>
</form> 
</body>
</html>
