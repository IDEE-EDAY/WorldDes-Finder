<?php
/**********************************************************************************************************************************
*
* Update notice
* 
* Author: Webbu Design
*
***********************************************************************************************************************************/
add_action('admin_notices', 'pointfinder_v1588_admin_notice');

function pointfinder_v1588_admin_notice() {
	global $current_user ;
    $user_id = $current_user->ID;

	if ( ! get_user_meta($user_id, 'pointfinder_v1588_admin_notice') ) {
        echo '<div class="updated"><p>'; 
        echo '<h3>Point Finder v1.5.9 Update Notice</h3>';

        echo '<ul>';
        	echo '<li><strong>New option added : </strong>';
			printf('<strong>Featured Image on Gallery</strong> <a href="%1$s">PF Settings > Options Panel > Item Detail Page</a>',admin_url( 'admin.php?page=_pointfinderoptions'));echo '<br/>';
            echo '</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Hide featured item problem on grid listing</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Hide featured item problem on grid listing</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Custom field problem – Only show in backend feature.</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Reported Translation keyword problems.</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Upload page Image limit Problem.</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'PFcheck_postmeta_exist() function problem.</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Item Detail Page – Last Tab Repeat Problem</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Auto Login disabled for register user action.</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Checkbox & Selectbox field not saving backend.</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Ajax field auto complete enable/disable problem.</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'Custom Tab hide on empty problem.</li>';

            echo '<li><strong>Fixed : </strong>';
            echo 'TGM Plugin Class for security reasons.</li>';

            echo '<li><strong>Added : </strong>';
            echo '"PLEASE SELECT" OPTION in custom dropdown field.</li>';

            echo '<li><strong>Updated : </strong>';
            echo 'Visual Composer / Templatera / Slider Revolution is updated. (Please re install these plugins.)</li>';

        echo '</ul>';



        echo '<p>';
            printf('<a href="%1$s" target="_blank">Click here to view old change log.</a>','http://support.webbudesign.com/forums/topic/changelog/');echo '<br/>';
        echo '</p>';echo '<br/>';

        
        
        printf(__('<a href="%1$s">Dismiss</a>'), '?pointfinderv1588_nag_ignore=0');
        echo "</p></div>";
	}
}

add_action('admin_init', 'pointfinderv1588_nag_ignore');

function pointfinderv1588_nag_ignore() {
	    global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['pointfinderv1588_nag_ignore']) && '0' == $_GET['pointfinderv1588_nag_ignore'] ) {
             update_user_meta($user_id, 'pointfinder_v1588_admin_notice', 'true', true);
	    }
}

add_action('admin_init', 'pointfinderv1588_nag_enable');

function pointfinderv1588_nag_enable() {
        global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['pointfinderv1588_nag_enable']) && '0' == $_GET['pointfinderv1588_nag_enable'] ) {
             delete_user_meta($user_id, 'pointfinder_v1588_admin_notice');
        }
}

