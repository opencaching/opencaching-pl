<?
function run_in_bg($Command, $Priority = 0)
{
if($Priority)
$PID = shell_exec("nohup nice -n $Priority $Command 2> /dev/null & echo $!");
else
$PID = shell_exec("nohup $Command 2> /dev/null & echo $!");
return($PID);
}

function is_running($PID)
{
exec("ps $PID", $ProcessState);
return(count($ProcessState) >= 2);
}

function wait_for_pid($pid)
{
while(is_running($pid)) usleep(100000);
}

function encrypt($text, $key)
{
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv));
}

function decrypt($text, $key)
{
if(!$text)
return "";
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($text), MCRYPT_MODE_ECB, $iv), "\0");
}

function validate_msg($cookietext)
{
if(!ereg("[0-9]+ This is a secret message", $cookietext))
return false;

$num=0;
sscanf($cookietext, "%d", $num);
return $num;
}

?>