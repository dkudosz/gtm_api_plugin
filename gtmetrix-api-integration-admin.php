<?php

/*** Analyse and Generate Report ***/
function gtm_analyse() {
	require_once("api/Services_WTF.php");

	$credentials = gtm_get_credentials();
	$url = sanitize_text_field($_POST['gtm_url']);
	$timestamp = date('j/n/Y G:i:s', time());
	
	$test = new Services_WTF_Test($credentials[0]->api_email, $credentials[0]->api_key);
	
	echo "Testing $url\n";
	
	$testid = $test->test(
		array(
			'url' => $url
		)
	);
	
	if($testid) {
		echo "Test started with $testid\n";
	} else {
		die("Test failed: " . $test->error() . "\n");
	}
	
	echo "Waiting for test to finish\n";
	$test->get_results();
	
	if ($test->error()) {
		die($test->error());
	}
	
	$testid = $test->get_test_id();
	echo "Test completed succesfully with ID $testid\n";
	$results = $test->results();
	
	//Create PDF report
	require('fpdf/fpdf.php');
	
	$logo = get_theme_mod( 'custom_logo' );
	$image = wp_get_attachment_image_src( $logo , 'full' );
	
	$upload_dir   = wp_upload_dir();
	$filename = $upload_dir['basedir'].'/pdf-reports/'.$testid.'.pdf'; //save custom report in uploads/pdf-reports/ folder
	
	$pdf = new FPDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$pdf->SetTitle('title');
	$pdf->Image($image[0],10,10,90,0,'PNG');
	
	$pdf->SetFontSize(20);
	$pdf->Text(112,20,'Pagespeed:');
	
	$pdf->SetFontSize(20);
	$pdf->Text(112,50,'YSlow:');
	
	echo '<pre>';
	print_r($results);
	echo '</pre>';
	
	$pagespeed = $results['pagespeed_score'];
	$y_slow = $results['yslow_score'];
	
	if($pagespeed > 50 && $pagespeed < 80) {
		$pdf->SetTextColor(245,161,29);
	}
	if($pagespeed == 50 || $pagespeed < 50) {
		$pdf->SetTextColor(235,57,59);
	}
	if($pagespeed > 80) {
		$pdf->SetTextColor(113,187,48);
	}
	
	$pdf->SetFontSize(90);
	$pdf->Text(158,36,$pagespeed);
	
	if($y_slow > 50 && $y_slow < 80) {
		$pdf->SetTextColor(245,161,29);
	}
	if($y_slow == 50 || $y_slow < 50) {
		$pdf->SetTextColor(235,57,59);
	}
	if($y_slow > 80) {
		$pdf->SetTextColor(113,187,48);
	}
	
	$pdf->SetFontSize(90);
	$pdf->Text(158,65,$y_slow);
	
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetFontSize(20);
	$pdf->Text(10,60,'Performance Report');
	
	$pdf->SetFontSize(14);
	$pdf->Text(10,70,'Website: '.$_POST['gtm_url']);
	$pdf->Text(10,77,'Date and time: '.$timestamp);
	
	$pdf->SetFontSize(17);
	$pdf->Text(10,90,'Details: ');
	
	$fully_loaded_time = $results['fully_loaded_time'] / 1000;
	$fully_loaded_time = number_format((float)$fully_loaded_time, 1, '.', '');
	$page_size = ($results['page_bytes'] + $results['html_bytes']) / 1000000;
	$page_size = number_format((float)$page_size, 2, '.', '');
	
	$pdf->SetFontSize(14);
	$pdf->Text(10,97,'Fully Loaded Time: '.$fully_loaded_time.'s');
	$pdf->Text(10,104,'Total Page Size: '.$page_size.'MB');
	$pdf->Text(10,111,'Requests: '.$results['page_elements']);
	
	
	
	$pdf->Output($filename, 'F');
	/**/
	
	echo "\nResources\n";
	$resources = $test->resources();
	foreach ($resources as $resource => $url) {
		echo "  Resource: $resource $url\n";
	}
	
	echo "Loading test id $testid\n";
	$test->load($testid);
	
	echo "Deleting test id $testid\n";
	$result = $test->delete();
	if (! $result) { 
		die("error deleting test: " . $test->error()); 
	}
	
	echo "\nLocations GTmetrix can test from:\n";
	$locations = $test->locations();
	// Returns an array of associative arrays:
	foreach ($locations as $location) {
		echo "GTmetrix can run tests from: " . $location["name"] . " using id: " . $location["id"] . " default (" . $location["default"] . ")\n";
	}
}
/*** END - Analyse and Generate Report ***/

/*** Shortcode ***/
add_shortcode('gtm-gen', 'gtm_gen');
function gtm_gen() {
	ob_start();
	?>
	<form action="" method="post" class="gtm_analyse_form">
		<input value="" type="url" name="gtm_url" required />
		<input value="Analyse" name="analyse" type="submit" />
	</form>
	<?php
	
	if($_POST['analyse'] && $_POST['gtm_url']) {
		gtm_analyse();
	}
	
	//demo code
	require('fpdf/fpdf.php');
	
	$logo = get_theme_mod( 'custom_logo' );
	$image = wp_get_attachment_image_src( $logo , 'full' );
	
	$upload_dir   = wp_upload_dir();
	$filename = $upload_dir['basedir'].'/pdf-reports/test.pdf';
	
	$pdf = new FPDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$pdf->SetTitle('title');
	$pdf->Image($image[0],10,10,90,0,'PNG');
	
	$pdf->SetFontSize(20);
	$pdf->Text(112,20,'Pagespeed:');
	
	$pdf->SetFontSize(20);
	$pdf->Text(112,50,'YSlow:');
	
	$pagespeed = 90;
	$y_slow = 60;
	
	if($pagespeed > 50 && $pagespeed < 80) {
		$pdf->SetTextColor(245,161,29);
	}
	if($pagespeed == 50 || $pagespeed < 50) {
		$pdf->SetTextColor(235,57,59);
	}
	if($pagespeed > 80) {
		$pdf->SetTextColor(113,187,48);
	}
	
	$pdf->SetFontSize(90);
	$pdf->Text(158,36,$pagespeed);
	
	if($y_slow > 50 && $y_slow < 80) {
		$pdf->SetTextColor(245,161,29);
	}
	if($y_slow == 50 || $y_slow < 50) {
		$pdf->SetTextColor(235,57,59);
	}
	if($y_slow > 80) {
		$pdf->SetTextColor(113,187,48);
	}
	
	$pdf->SetFontSize(90);
	$pdf->Text(158,65,$y_slow);
	
	$pdf->SetTextColor(0,0,0);
	
	$pdf->SetFontSize(20);
	$pdf->Text(10,60,'Performance Report');
	
	$pdf->SetFontSize(14);
	$pdf->Text(10,70,'Website: ');
	$pdf->Text(10,77,'Date: ');
	
	$pdf->SetFontSize(17);
	$pdf->Text(10,90,'Details: ');
	
	$pdf->SetFontSize(14);
	$pdf->Text(10,97,'Fully Loaded Time: ');
	$pdf->Text(10,104,'Total Page Size: ');
	$pdf->Text(10,111,'Requests: ');
	
	
	
	$pdf->Output($filename, 'F');
	
	//demo code
	
	return ob_get_clean();
}
/*** END Shortcode ***/

/*** DB ***/
function gtm_get_credentials() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'gtm_settings';
	$credentials = $wpdb->get_results( "SELECT * FROM $table_name" );
	
	if(!empty($credentials)) {
		return $credentials;
	}
}

function update_api_key() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'gtm_settings';
	$api_key = sanitize_text_field($_POST['gtm_credentials']);
	$api_email = sanitize_text_field($_POST['gtm_email']);
	
	$delete = $wpdb->query("TRUNCATE TABLE $table_name");
	
	$wpdb->insert( 
		$table_name, 
			array( 
				'api_email' => $api_email,
				'api_key' => $api_key,
			) 
	);
}

/*** END - DB ***/

/*** Plugin Contants ***/
function gtm_admin_menu_contents() {
	?>
	<h2>GTmetrix API</h2>
	<p>Welcome to the GTmetrix plugin. Here you can change few settings about the plugin.</p>
	<p>Use this <input style="text-align: center;" value="<?php echo '[gtm-gen]'; ?>" size="10" disabled /> shortcode on any page you want to display the generator.</p>
	
	<p>GTmetrix API credentials:</p>
	
	<?php
	$credentials = gtm_get_credentials();
	?>
	
	<form action="" method="post" class="gtm_credentials" >
		<input value="<?php echo $credentials[0]->api_email; ?>" type="email" name="gtm_email" placeholder="GTmetrix Email" required />
		<input value="<?php echo $credentials[0]->api_key; ?>" type="text" name="gtm_credentials" placeholder="API Key" size="40" required />
		<p>You can find your API key <a href="//gtmetrix.com/api/" target="_blank">HERE</a></p>
		<input value="Save" name="save" type="submit" />
	</form>
	
	<p>NOTE: The the amount of credits depends on the type of account you have with GTmetrix. If your site provides many reports everyday you will need to purchase GTmetrix subscripsion. You can purchase GTmetrix PRO <a href="https://gtmetrix.com/pro/" target="_blank"> HERE</a>.</p>
	
	<p>This plugin was developed by <a href="//yupscode.com" target="_blank">Damian Kudosz</a> as part of range of free plugins from <a href="//yupscode.com" target="_blank">yupsCode.com</a>.
	
	<?php
	//Update Credentials
	if($_POST['save'] && $_POST['gtm_credentials'] && $_POST['gtm_email']) {
		update_api_key();
	}
}
/*** END Plugin Contants ***/




?>