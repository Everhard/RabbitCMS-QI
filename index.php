<?php
if ($_SERVER['SCRIPT_NAME'] == "/index2.php") {
    if (is_dir("RabbitCMS-master")) {
        if (exec("mv RabbitCMS-master/RabbitCMS/* . -f") && exec("mv RabbitCMS-master/RabbitCMS/.htaccess . -f")) {
            if (exec("rm RabbitCMS-master index2.php -rf")) {
                $success = true;
            }
        } else $error_message = "Move files error!";
    }
}

if (isset($_POST['install'])) {
    // Install CMS:
    $curl = new HTTPClient("https://github.com/Everhard/RabbitCMS/archive/master.zip");
    $file = $curl->do_request();
    if ($file) {
        if (file_put_contents("rabbitcms.zip", $file)) {
            if (exec("unzip rabbitcms.zip")) {
                unlink("rabbitcms.zip");
                rename("index.php", "index2.php");
                header("Location: index2.php");
                exit;
            } else $error_message = "Unzip file error!";
        } else $error_message = "Write file on disk error!";
    } else $error_message = "Download file error!";
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quick Install for RabbitCMS</title>
  </head>
  <body>
	<h1>Quick install for RabbitCMS</h1>
        <?php if (isset($error_message)) echo "<p>$error_message</p>"; ?>
        <?php if (isset($success)) echo "<p>RabbitCMS installed successfully!</p>"; else { ?>
	<form method="post">
		<input type="submit" name="install" value="Install" />
	</form>
        <?php } ?>
  </body>
</html>
<?php
class HTTPClient {
	/*
		HTTPClient (cURL) Settings:
		*. CURLOPT_URL:              The URL to fetch.
		*. CURLOPT_USERAGENT:        The contents of the "User-Agent" header to be used in a HTTP request.
		*. CURLOPT_HEADER:           TRUE to include the header in the output.
		*. CURLOPT_TIMEOUT:          The maximum number of seconds to allow cURL functions to execute.
		*. CURLOPT_CONNECTTIMEOUT:   The number of seconds to wait while trying to connect.
		*. CURLOPT_POST:             TRUE to do a regular HTTP POST.
		*. CURLOPT_POSTFIELDS:       The full data to post in a HTTP "POST" operation.
		*. CURLOPT_COOKIEFILE:       The name of the file containing the cookie data.
		*. CURLOPT_RETURNTRANSFER:   TRUE to return the transfer as a string instead of outputting it out directly.
		*. CURLOPT_FOLLOWLOCATION:   TRUE to follow any "Location: " header that the server sends as part of the HTTP header.
		
		Docs on PHP.net:
		http://www.php.net/manual/ru/function.curl-setopt.php
	*/
	
	// Default parameters:
	const DEFAULT_TIMEOUT = 120;
	const DEFAULT_CONNCECT_TIMEOUT = 120;
	const DEFAULT_USER_AGENT = "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:30.0) Gecko/20100101 Firefox/30.0";

	public function __construct($url = NULL) {

		// Initialization cURL:
		$this->curl = curl_init($url);
		
		// Default cURL settings:
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->curl, CURLOPT_HEADER, FALSE);
		curl_setopt($this->curl, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, self::DEFAULT_CONNCECT_TIMEOUT);
		curl_setopt($this->curl, CURLOPT_USERAGENT, self::DEFAULT_USER_AGENT);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, '');
	}

	public function do_request() {
		$result = curl_exec($this->curl);
		if (!$result) {
			$this->curl_errors[] = curl_error($this->curl)." (".curl_errno($this->curl).").";
			return false;
		}
		return $result;
	}

	public function set_url($url) {
		$this->url = $url;
		curl_setopt($this->curl, CURLOPT_URL, $this->url); 
	}
	
	public function set_user_agent($user_agent) {
		$this->user_agent = $user_agent;
		curl_setopt($this->curl, CURLOPT_USERAGENT, $this->user_agent);
	}
	
	public function set_post_method($post_data) {
		curl_setopt($this->curl, CURLOPT_POST, TRUE);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post_data);
	}
	
	public function set_get_method() {
		curl_setopt($this->curl, CURLOPT_HTTPGET, TRUE);
	}
	
	public function close_client() {
		curl_close($this->curl);
	}
	
	private $url;
	private $user_agent;
	private $curl;
	private $curl_errors = array();
}
?>
