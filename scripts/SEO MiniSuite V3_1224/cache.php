<?php

$str = str_replace("\\","",$str);
$node = json_decode($str);
$image_url = $node->image->url;

//    $image_url = $node->image->url;//This is the sanitized image url.
$image = pathinfo($image_url);//Extracting information into array.
$image_name = $image['basename'];
$upload_dir = wp_upload_dir();
$image_data = file_get_contents($image_url);
$unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
$filename = basename($unique_file_name);
$postarr = array(
    'post_title' => $node->title,
    'post_content' => $content,
    'post_type' => 'post',//or whatever is your post type slug.
    'post_status' => 'publish',
    'meta_input' => array(
//If you have any meta data, that will go here.
    ),
    'post_category' => (array)$node->wp_category
);
$insert_id = wp_insert_post($postarr, true);
//    print_r($insert_id);
if (!is_wp_error($insert_id)) {
    if ($image != '') {
// Check folder permission and define file location
        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }
// Create the image  file on the server
        file_put_contents($file, $image_data);
// Check image file type
        $wp_filetype = wp_check_filetype($filename, null);
// Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit',
        );
// Create the attachment
        $attach_id = wp_insert_attachment($attachment, $file, $insert_id);
// Include image.php
        require_once ABSPATH . 'wp-admin/includes/image.php';
// Define attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
// Assign metadata to attachment
        wp_update_attachment_metadata($attach_id, $attach_data);
// And finally assign featured image to post
        $thumbnail = set_post_thumbnail($insert_id, $attach_id);
    }
    $post = get_post($insert_id);
//        print_r($post);
//        exit();
    wp_send_json(($post));
}