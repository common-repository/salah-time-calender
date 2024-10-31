<?php

class Salah_Time {
	private $plugin_name;
	private $plugin_version;
	
	function __construct(){
		add_action( 'admin_menu', array($this,'edit_salah_time') );
	}

	public function edit_salah_time(){
        if ( current_user_can( 'edit_users' ) ){
		  add_submenu_page( 'options-general.php', 'Edit Salah Time', 'Edit Salah Time', 'edit_users', 'edit-salah-time', array( $this, 'manage_salah_time' ) );
        }
		
    }
	
	
	public function manage_salah_time(){
		global $wpdb;
		
		$json_path = $this->get_salah_json_path();
		
		$jsonMonth = file_get_contents($json_path);
		$jamat_data = json_decode($jsonMonth, true);
		$html = '';		
		$messages = array();
		$errors = array();
		
		$monthNames = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		
		if ( isset($_POST['month_submitted']) && $_POST['month-action'] != -1){
        	$month_name = sanitize_text_field($_POST['month-action']);
		}elseif(isset($_POST['date_submitted'])){
        	$month_name = sanitize_text_field($_POST['month']);
		}else{
         	$month_name = 'Jan';
		}
			
		if ( isset( $_POST['date_submitted'] ) && wp_verify_nonce( $_POST['date_wpnonce'], 'salah_edit_nonce' ) ) {
			$POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
			$ar_po = 0;
			foreach ( $jamat_data as $data ){
				
				$date_st = explode("-",$data['Date']);
				$date_name = $date_st[0];
				$date_no = $date_st[1];
				
				if($date_name == $month_name){
					
					$jamat_data[$ar_po]['Fajr'] = $POST[$data['Date'].'_Fajr'];
					$jamat_data[$ar_po]['Dhuhr'] = $POST[$data['Date'].'_Dhuhr'];
					$jamat_data[$ar_po]['Asr'] = $POST[$data['Date'].'_Asr'];
					$jamat_data[$ar_po]['Maghrib'] = $POST[$data['Date'].'_Maghrib'];
					$jamat_data[$ar_po]['Isha'] = $POST[$data['Date'].'_Isha'];
				}
				$ar_po++;
			}
			
			$newjsonMonth = json_encode($jamat_data);
			$upload_dir = wp_upload_dir();
			$blog_id = get_current_blog_id();
		
			$file_path = $upload_dir['path'].'/jamat-time-12hNS-'.$blog_id.'.json';
			$file_url = $upload_dir['url'].'/jamat-time-12hNS-'.$blog_id.'.json';
			update_option( 'jamat_time_json_path_'.$blog_id, $file_path );
			update_option( 'jamat_time_json_url_'.$blog_id, $file_url );
			
			file_put_contents($file_path, $newjsonMonth);
			
			$success =  esc_html__( 'Salah time updated successfully!' , 'salah-time-pro' );
			array_push($messages, $success);
		}
		
				
		?>
		<div class="wrap">
        <h1 class="wp-heading-inline"><?php echo esc_html__( 'Edit Salah Time' , 'salah-time-pro' ); ?></h1>
		<?php
		if( isset( $_POST['date_submitted'] ) && !empty($messages)): ?>
		<div id="message" class="updated notice is-dismissible">
		<?php
			foreach ( $messages as $message ) {
				echo '<p>'.$message.'</p>';
			}
		?>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo esc_html__( 'Dismiss this notice.' , 'salah-time' ); ?></span></button>
		</div>
		<?php
		elseif(isset($_POST['submitted'] ) && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ) && !empty($errors)): ?>
		<div id="message" class="error notice is-dismissible">
		<?php
			foreach ( $errors as $error ) {
				echo '<p>'.$error.'</p>';
			}
		?>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo esc_html__( 'Dismiss this notice.' , 'salah-time' ); ?></span></button>
		</div>
		<?php
		endif;
		?>
		<form method="post" id="posts-filter" action="">
		<table class="wp-list-table widefat fixed striped pages">
		<thead class="bdr-bottom">
			<tr>
				<th class="manage-column"><?php echo esc_html__( 'Date' , 'salah-time' ); ?></th>
				<th class="manage-column"><?php echo esc_html__( 'Fajr' , 'salah-time' ); ?></th>
				<th class="manage-column"><?php echo esc_html__( 'Dhuhr' , 'salah-time' ); ?></th>
				<th class="manage-column"><?php echo esc_html__( 'Asr' , 'salah-time' ); ?></th>
                <th class="manage-column"><?php echo esc_html__( 'Maghrib' , 'salah-time' ); ?></th>
                <th class="manage-column"><?php echo esc_html__( 'Isha' , 'salah-time' ); ?></th>
			</tr>
			
		</thead>
		        
        <?php if ( !empty($jamat_data)) : ?>
		<tbody id="the-list">
		<?php 
		
		
		foreach ( $jamat_data as $data ):
			
			$date_st = explode("-",$data['Date']);
			$date_name = $date_st[0];
			$date_no = $date_st[1];
			
			if($date_name == $month_name):
		?>
			
			<tr>
            	<td class="iedit author-self level-0 hentry"><?php echo esc_attr($data['Date']); ?></td>
				<td class="iedit author-self level-0 hentry"><input type="text" name="<?php echo esc_attr($data['Date']).'_'.'Fajr'; ?>" value="<?php echo esc_attr($data['Fajr']); ?>"></td>
				<td class="iedit author-self level-0 hentry"><input type="text" name="<?php echo esc_attr($data['Date']).'_'.'Dhuhr'; ?>" value="<?php echo esc_attr($data['Dhuhr']); ?>"></td>
				<td class="iedit author-self level-0 hentry"><input type="text" name="<?php echo esc_attr($data['Date']).'_'.'Asr'; ?>" value="<?php echo esc_attr($data['Asr']); ?>"></td>
				<td class="iedit author-self level-0 hentry"><input type="text" name="<?php echo esc_attr($data['Date']).'_'.'Maghrib'; ?>" value="<?php echo esc_attr($data['Maghrib']); ?>"></td>
                <td class="iedit author-self level-0 hentry"><input type="text" name="<?php echo esc_attr($data['Date']).'_'.'Isha'; ?>" value="<?php echo esc_attr($data['Isha']); ?>"></td>
			</tr>
			<?php endif; ?> 
		<?php endforeach; ?>
        
		<?php endif; ?> 
		</tbody>
		</table>
		<p>
		<div class="alignright actions bulkactions">
		<input type="submit" id="request-action" name="date_submitted" class="button action" tabindex="3" value="Save Changes" />	
		<input type="hidden" name="month" id="month" value="<?php echo esc_attr($month_name); ?>"/> 
		<?php wp_nonce_field( 'salah_edit_nonce', 'date_wpnonce' ); ?>
		</form>
		</div>
		<div class="alignleft actions bulkactions">
		<form method="post" id="month-filter" action="">
		 <label for="bulk-action-selector-bottom" class="screen-reader-text">Select Month</label>
		 <select name="month-action" id="bulk-action-selector-bottom">
			<option value="-1">--Select--</option>
			<option value="Jan" class="hide-if-no-js">January</option>
			<option value="Feb">February</option>
			<option value="Mar">March</option>
			<option value="Apr">April</option>
			<option value="May">May</option>
			<option value="Jun">June</option>
			<option value="Jul">July</option>
			<option value="Aug">August</option>
			<option value="Sep">September</option>
			<option value="Oct">October</option>
			<option value="Nov">November</option>
			<option value="Dec">December</option>
		</select>
		<input type="submit" id="request-action" name="month_submitted" class="button action btn-primary" tabindex="3" value="Select" />	
		<?php wp_nonce_field( 'month_nonce', 'month_wpnonce' ); ?>
		</form>
		</div>
		</p>
		<?php
    }
	
	public function cm_plugin_info(){	 
		$plugin_data = get_plugin_data( ISP_PATH.'cm.php', false, false );
		$this->plugin_name = $plugin_data['Name'];
		$this->plugin_version = $plugin_data['Version'];
	}
	
	static public function get_salah_json_path(){
		
		$or_json = SALAH_PATH . 'assets/js/jamat-time-12hns.json';
		$blog_id = get_current_blog_id();
		$json_option = "jamat_time_json_path_".$blog_id;
		
		$blog_json	 = get_option( $json_option );
		
		if(!empty($blog_json)){
			return $blog_json;
		}else{
			return $or_json;
		}
	}
	static public function get_salah_json_url(){
		
		$or_json = SALAH_URL . 'assets/js/jamat-time-12hns.json';
		$blog_id = get_current_blog_id();
		$json_option = "jamat_time_json_url_".$blog_id;
		
		$blog_json	 = get_option( $json_option );
		
		if(!empty($blog_json)){
			return $blog_json;
		}else{
			return $or_json;
		}
	}

}

new Salah_Time();

?>