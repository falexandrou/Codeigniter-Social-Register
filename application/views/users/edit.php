<form action="<?php echo current_url() ?>" method="post">
    <p>
	<label>User id</label>
	<input type="text" name="id" disabled="true" value="<?php echo $user->id ?>" />
    </p>
    <p>
	<label>Real Name</label>
	<input type="text" name="real_name" value="<?php echo $user->real_name ?>" />
    </p>
    <p>
	<label>Email</label>
	<input type="text" name="email" value="<?php echo $user->image ?>" />
    </p>
    <input type="submit" name="submit" value="SAVE" />
</form>