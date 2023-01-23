<?php
require 'config/database.php';


if(isset($_GET['id']))
{
    //fetch user from database
    $id = filter_var($_GET['id'],FILTER_SANITIZE_NUMBER_INT);


    //fetch user from database
    $query = "SELECT * FROM users WHERE id = $id";
    $result = mysqli_query($connection,$query);
    $user = mysqli_fetch_assoc($result);


    // make sure we got back only one user
    if(mysqli_num_rows($result) == 1)
    {
        $avatar_name = $user['avatar'];
        $avatar_path = '../images/'.$avatar_name;
        //delete the image if available
        if($avatar_path)
        {
            unlink($avatar_path);
        }
    }

    //for later
    //fetch thumbnails of users posts and delete them too
    $thumbnail_query = "SELECT thumbnail FROM posts WHERE author_id = $id";
    $thumbnauil_result = mysqli_query($connection,$query);
    if(mysqli_num_rows($thumbnauil_result) > 0)
    {
        while($thumbnail = mysqli_fetch_assoc($thumbnauil_result))
        {
            $thumbnail_path = '../images/'.$thumbnail['thumbnail'];
            //delete thumbnail from images folder if it exists
            if($thumbnail_path)
            {
                unlink($thumbnail_path);
            }
        }
    }









    //delete user from database
    $delete_user_query = "DELETE FROM users WHERE id = $id";
    $delete_user_result = mysqli_query($connection,$delete_user_query);
    if(mysqli_errno($connection))
    {
        $_SESSION['delete-user'] = "Couldn't delete user {$user['firstname']} {$user['lastname']}.";
    }
    else
    {
        $_SESSION['delete-user-success'] = "{$user['firstname']} {$user['lastname']} deleted successfully";
    }


}

header('location: '.ROOT_URL.'admin/manage-users.php');
die();