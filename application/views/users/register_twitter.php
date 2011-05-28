<form action="<?php echo current_url() ?>" method="post">
    <p>
	<label>Twitter user id</label>
	<input type="text" name="tw_id" value="<?php echo $user->id_str ?>" />
    </p>
    <p>
	<label>Real Name</label>
	<input type="text" name="real_name" value="<?php echo $user->name ?>" />
    </p>
    <p>
	<label>Twitter Handle</label>
	<input type="text" name="handle" value="<?php echo $user->screen_name ?>" />
    </p>
    <p>
	<label>Bio</label>
	<textarea name="bio"><?php echo $user->description ?></textarea>
    </p>
    <p>
	<label>Image</label>
	<input type="text" name="profile_image_url" value="<?php echo $user->profile_image_url ?>" />
	<img src="<?php echo $user->profile_image_url ?>" width="50" />
    </p>
    <input type="submit" name="submit" value="SAVE" />
</form>