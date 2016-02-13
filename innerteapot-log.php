<?php
/*
Plugin Name:       Inner Tea Pot Log
Plugin URI:        https://github.com/innerteapot/innerteapot-log
Description:       Keep a manual log of activities in a database table
Version:           0.1
Author:            Joel Sutton
Author URI:        http://innerteapot.com/
License:           GPL2
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/innerteapot/innerteapot-log
GitHub Branch:     master
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
        die;
}

function il_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'innerteapot_log';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time timestamp,
        note text NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    $wpdb->query($sql);
}
register_activation_hook(__FILE__, 'il_install');

function il_install_data() {
    global $wpdb;
    
    $welcome_note = 'Congratulations, you just completed the installation!';
    
    $table_name = $wpdb->prefix . 'innerteapot_log';
    
    $wpdb->insert( 
        $table_name, 
        array( 
            'note' => $welcome_note, 
        ) 
    );
}
register_activation_hook(__FILE__, 'il_install_data');

function il_menu()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'innerteapot_log';

    if (!empty($_POST) && 
        isset($_POST['Submit']) && 
        check_admin_referer('name_of_my_action', 'wpnf_ft')) 
    {
        if (!empty($_POST['note']))
        {
            $wpdb->insert( 
                $table_name, 
                array( 
                    'note' => $_POST['note']
                ), 
                array( 
                    '%s' 
                ) 
            );
        }
    }

    echo "<h1>Inner Tea Pot Log</h1>";

    $admin_log = $wpdb->get_results( 
        "
        SELECT * 
        FROM $table_name
        ORDER BY time DESC
        "
    );

    // input form
    //
    ?>
    <form name="il_form" method="post" action="">
    <?php wp_nonce_field('name_of_my_action', 'wpnf_ft'); ?>
    <textarea name="note" rows="4" cols="50"></textarea>
    <br />
    <input type="submit" class="button-primary" name="Submit" value="Add" />
    </form>
    <hr>
    <?php

    if ($admin_log)
    {
        foreach ($admin_log as $entry)
        {
            ?>
            <h2><?php echo $entry->time; ?></h2>
            <p><?php echo $entry->note; ?></p>
            <hr>
            <?php
        }   
    }
    else
    {
        echo "No entries found";
    }
}

function il_admin_actions()
{
    add_menu_page(
        "Inner Tea Pot Log", 
        "Inner Tea Pot Log", 
        1, 
        "innerteapot-log", 
        "il_menu"
    );
}
add_action('admin_menu', 'il_admin_actions');

?>
