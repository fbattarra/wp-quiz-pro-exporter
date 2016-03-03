<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.battarra.it
 * @since      1.0.0
 *
 * @package    Wp_Pro_Quiz_Export
 * @subpackage Wp_Pro_Quiz_Export/admin/partials
 */
global $wpdb;
$arQuizzes = $wpdb->get_results('SELECT id AS id, CONCAT("[", id, "] ", name) AS name FROM cibq_wp_pro_quiz_master ORDER BY name ASC;', ARRAY_A);
?>
<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <p>Export a CSV file containing all the user-related data for a given quiz.</p>
    <p>&nbsp;</p>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <fieldset>
            <legend class="screen-reader-text"><span>Choose the quiz</span></legend>
            <label for="<?php echo $this->plugin_name; ?>-quiz">
                <select id="<?php echo $this->plugin_name; ?>-quiz" name="<?php echo $this->plugin_name; ?>-quiz">
                    <option value="0">Select a WP-Pro-Quiz&hellip;</option>
                    <?php
                    if (!empty($arQuizzes)) {
                        foreach ($arQuizzes as $quiz) {
                            ?>
                            <option value="<?php echo $quiz['id']; ?>"><?php echo $quiz['name']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>&nbsp;<input class="button-primary" type="submit" id="btCSV" value="&#8680; CSV"/>
                <input type="hidden" name="action" value="csv-export"/>
                <?php wp_nonce_field('csv-export', 'checksum'); ?>
            </label>
        </fieldset>
    </form>
</div>
