<?php 
//NOTE: Facebook returns arrays while twitter returns objects
?>
<form action="<?php echo current_url() ?>" method="post">
    <p>
	<label>Facebook id</label>
	<input type="text" name="fb_id" value="<?php echo $user['id'] ?>" />
    </p>
    <p>
	<label>Real Name</label>
	<input type="text" name="real_name" value="<?php echo $user['name'] ?>" />
    </p>
    <p>
	<label>Email</label>
	<input type="text" name="email" value="<?php echo $user['email'] ?>" />
    </p>
    <p>
	<label>Facebook Handle</label>
	<input type="text" name="handle" value="<?php echo $user['username'] ?>" />
    </p>
    <p>
	<label>Profile URL</label>
	<input type="text" name="profile_url" value="<?php echo $user['link'] ?>" />
    </p>
    <p>
	<label>Bio</label>
	<textarea name="bio"><?php echo $user['bio'] ?></textarea>
    </p>
    <p>
	<label>Image</label>
	<input type="text" name="profile_image_url" value="<?php echo $image ?>" />
	<img src="<?php echo $image ?>" width="50" />
    </p>
    <input type="submit" name="submit" value="SAVE" />
</form>