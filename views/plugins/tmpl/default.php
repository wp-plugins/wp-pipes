<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: default.php 170 2014-01-26 06:34:40Z thongta $
 * @author               wppipes.com
 * @copyright            2014 wppipes.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

/*if ( isset ( $_FILES["file_zip"]["name"] ) ) {
	$filename = $_FILES["file_zip"]["name"];
	$source   = $_FILES["file_zip"]["tmp_name"];
	$type     = $_FILES["file_zip"]["type"];

	$name           = explode( ".", $filename );
	$accepted_types = array( 'application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed', 'application/octet-stream' );
	foreach ( $accepted_types as $mime_type ) {
		if ( $mime_type == $type ) {
			$okay = true;
			break;
		}
	}

	$continue = strtolower( $name[count( $name ) - 1] ) == 'zip' ? true : false;
	if ( ! $continue ) {
		$message = "The file you are trying to upload is not a .zip file. Please try again.";
	}

	$target_path = ABSPATH . 'wp-content' . DS . 'uploads' . DS . date( 'Y' ) . DS . date( 'm' ) . DS . $filename;

	if ( ! file_exists( ABSPATH . 'wp-content' . DS . 'uploads' . DS . date( 'Y' ) . DS . date( 'm' ) ) ) {
		mkdir( ABSPATH . 'wp-content' . DS . 'upload' . DS . date( 'Y' ) . DS . date( 'm' ), 0777, true );
	}

	if ( move_uploaded_file( $source, $target_path ) ) {
		$plugin   = explode( "_", $name[0] );
		$plg_type = explode( "-", $plugin[1] );
		$plg_type = $plg_type[1] . 's';
		$plg_name = $plugin[2];
		$des_path = dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . DS . 'plugins' . DS . $plg_type . DS . $plg_name;
		if ( ! file_exists( $des_path ) ) {
			mkdir( $des_path, 0777, true );
		}

		$zip = new ZipArchive();
		$x   = $zip->open( $target_path );
		if ( $x === true ) {
			$zip->extractTo( $des_path . DS );
			$zip->close();

			unlink( $target_path );
		}
		$message = "Your addon was uploaded and unpacked.";
	} else {
		$message = "There was a problem with the upload. Please try again.";
	}
}*/
?>
<!-- toolbar icon -->
<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br></div>
<!-- wrap -->
<div class="wrap nosubsub">
	<!-- toolbar -->
	<h2>
		<!-- toolbar title -->
		<?php
			echo __( 'Addons Manager' );
			echo ' <a class="add-new-h2" href="plugin-install.php">'. __( 'Add New', 'pipes' ) . '</a>';
		?>
		<!-- buttons -->
	</h2>

	<div id="col-container">
		<form id="items-filter" method="get">
			<?php
			$this->itemsTable->display();
			?>
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		</form>
		<!--<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap">
					<h3><?php /*echo __( 'Add New Addon' ); */?></h3>
					<?php
/*					if ( isset ( $message ) ) {
						echo "<p>$message</p>";
					}
					echo PIPES::show_message();
					*/?>
					<form method="post" enctype="multipart/form-data" class="wp-upload-form" action="">
						<label class="screen-reader-text" for="file_zip">Addon zip file</label>
						<input type="file" id="file_zip" name="file_zip" />
						<input type="submit" name="install-plugin-submit" id="install-plugin-submit" class="button" value="<?php /*echo __( 'Install Now' ); */?>" disabled="" />
					</form>
				</div>

			</div>
		</div>-->
	</div>
</div>