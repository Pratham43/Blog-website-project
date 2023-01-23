<?php 
require 'config/database.php';
require 'config/constants.php';
if(isset($_POST['submit']))
{
    var_dump($_POST);
    $id = filter_var($_POST['id'],FILTER_SANITIZE_NUMBER_INT);
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $title = filter_var($_POST['title'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $body = filter_var($_POST['body'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $category_id = filter_var($_POST['category'],FILTER_SANITIZE_NUMBER_INT);
    $is_featured = filter_var($_POST['is_featured'],FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    //set is_featured to 0 if it was unchecked
    $is_featured = $is_featured == 1 ?:0;


    //check and validate input values
    if(!$title)
    {
        $_SESSION['edit-post'] = "Couldn't update post. Invalid form data on edit post page.1";
    }
    elseif(!$category_id)
    {
        $_SESSION['edit-post'] = "Couldn't update post. Invalid form data on edit post page.2";
    }
    elseif(!$body)
    {
        $_SESSION['edit-post'] = "Couldn't update post. Invalid form data on edit post page.3";
    }
    else
    {
        if($thumbnail['name'])
        {
            $previous_thumbnail_path = '../images/'.$previous_thumbnail_name;
            if($previous_thumbnail_path)
            {
                unlink($previous_thumbnail_path);
            }

            //WORK ON NEW THUMBNAIL
            //RENAME IMAGE
            $time = time();
            $thumbnail_name = $time.$thumbnail['name'];
            $thumbnail_tmp_name = $thumbnail['tmp_name'];
            $thumbnail_destination_path = '../images/'.$thumbnail_name;

            //make sure file is an image
            $allowed_files = ['png','jpg','jpeg'];
            $extension = explode('.',$thumbnail_name);
            $extension = end($extension);
            if(in_array($extension,$allowed_files))
            {
                //make sure avatar is not too large
                if($thumbnail['size'] < 2000000)
                {
                    //upload avatar
                    move_uploaded_file($thumbnail_tmp_name,$thumbnail_destination_path);
                }
                else
                {
                    $_SESSION['edit-post'] = "Couldn't update post. Thumbnail size too big. Size should be less than 2MB.4";
                }
            }
            else
            {
                $_SESSION['edit-post'] = "Couldn't update post. Thumbnail should be png,jpg or jpeg.5";
            }
        }
    }


    if($_SESSION['edit-post'])
    {
        //redirect to manage from page if from was invalid
        header('location: '.ROOT_URL.'admin/');
        die();
    }
    else
    {
        //set featured name if a new one was uploaded, else keep old thumbnail name
        if($is_featured == 1)
        {
            $zero_all_is_featured_query = "UPDATE posts SET is_featured = 0";
            $zero_all_is_featured_result = mysqli_query($connection,$zero_all_is_featured_query);
        }
        $thumbnail_to_insert = $thumbnail_name ?? $previous_thumbnail_name;
        $query = "UPDATE posts SET title='$title', body='$body',thumbnail='$thumbnail_to_insert',category_id = $category_id, is_featured = $is_featured WHERE id= $id LIMIT 1";
        $result = mysqli_query($connection , $query);
    }

    if(!mysqli_errno($connection))
    {
        $_SESSION['edit-post-success'] = "Post updated successfully";
    }

}
header('location: '.ROOT_URL.'admin/');
die();