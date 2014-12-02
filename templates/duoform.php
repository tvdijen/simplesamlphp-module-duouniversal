<?php
/*
 * Using Duo's Web SDK
 */
$attributes = $this->data['attributes'];
include('duo_web.php');

/*
 * This is something uniquely generated by you for your application
 * and is not shared with Duo.
 */
define('AKEY',$this->data['akey']);

/*
 * IKEY, SKEY, and HOST should come from the Duo Security admin dashboard
 * on the integrations page.
 */
define('IKEY',$this->data['ikey']);
define('SKEY',$this->data['skey']);
define('HOST',$this->data['host']);

$this->includeAtTemplateBase('includes/header.php');

echo "<h1>Duo Security</h1>";


/*
 * Once secondary auth has completed you may log in the user
 */
if(isset($_POST['sig_response'])){ //verify sig response and log in user
	//make sure that verifyResponse does not return NULL
	//if it is NOT NULL then it will return a username
	//you can then set any cookies/session data for that username
	//and complete the login process
	$resp = Duo::verifyResponse(IKEY, SKEY, AKEY, $_POST['sig_response']);
	if($resp != NULL){
		//password protected content would go here
		SimpleSAML_Auth_ProcessingChain::resumeProcessing($this->data['state']);
	}
}


/*
 * verify username and password
 * if the user and pass are good, then generate a sig_request and
 * load up the Duo iframe for secondary authentication
 */
if(isset($attributes['username'])){
		$username = $attributes['username'][0];
		//generate sig request and then load up Duo javascript and iframe
		$sig_request = Duo::signRequest(IKEY, SKEY, AKEY, $username);

?>
		<script src="Duo-Web-v1.bundled.min.js"></script>
		<?php
			foreach ($this->data['yesData'] as $name => $value) {
 	   			echo '<input type="hidden" id="' . htmlspecialchars($name) .
                                 '" name="' . htmlspecialchars($name) .
       				 '" value="' . htmlspecialchars($value) . '" />';
			}  
		?>
		<input type="hidden" id="duo_host" value="<?php echo HOST ; ?>">
		<input type="hidden" id="duo_sig_request" value="<?php echo $sig_request; ?>">
		<script src="Duo-Init.js"></script>

		<iframe id="duo_iframe" width="620" height="500" frameborder="0" allowtransparency="true" style="background: transparent;"></iframe>
<?php

}

$this->includeAtTemplateBase('includes/footer.php');
?>