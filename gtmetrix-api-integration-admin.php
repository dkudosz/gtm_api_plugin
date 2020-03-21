<?php
/*** Update API key ***/
function update_key() {
	
}
/*** END - Update API key ***/

/*** Shortcode ***/
add_shortcode('gtm-gen', 'gtm_gen');
function gtm_gen() {
	?>
	<form action="" method="post" class="gtm_analyse_form">
		<input value="" type="url" name="gtm_url" required />
		<input value="Analyse" name="analyse" type="submit" />
	</form>
	<?php
}
/*** END Shortcode ***/

/*** DB ***/
function gtm_install_data() {
	global $wpdb;
	
	$api_key = $_POST['gtm_credentials'];
	
	$table_name = $wpdb->prefix . 'gtm_settings';
	
	$delete = $wpdb->query("TRUNCATE TABLE $table_name");
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'api_key' => $api_key, 
		) 
	);
}

function update_api_key() {
	echo 'no';
	if($_POST['save'] && $_POST['gtm_credentials']) {
		echo 'yes';
	}
}

/*** END - DB ***/

/*** Plugin Contants ***/
function gtm_admin_menu_contents() {
	?>
	<h2>GTmetrix API</h2>
	<p>Welcome to the GTmetrix plugin. Here you can change few settings about the plugin.</p>
	<p>Use this <input value="<?php echo '[gtm-gen]'; ?>" size="10" disabled /> shortcode on any page you want to display the generator.</p>
	
	<?php
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
	?>
	
	<p>GTmetrix API Key:</p>
	
	<form action="" method="post" class="gtm_credentials" >
		<input value="" type="text" name="gtm_credentials" size="40" required />
		<p>You can find you API key <a href="//gtmetrix.com/api/" target="_blank">HERE</a></p>
		<input value="Save" name="save" type="submit" />
	</form>	
	
	<p>This plugin was developed by <a href="//yupscode.com" target="_blank">Damian Kudosz</a> as part of range of free plugins from <a href="//yupscode.com" target="_blank">yupsCode.com</a>.
	
	<?php
	update_api_key();
}
/*** END Plugin Contants ***/