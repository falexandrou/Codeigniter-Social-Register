<p>Welcome <?php echo $user->real_name ?></p>
<p>You are a member since <?php echo $user->date_added ?></p>
<p>You prefer <?php echo $user->login_type ?> for your login</p>
<p>You <?php echo $user->active == 1 ? 'not' : '' ?> banned </p>
<?php if (empty($user->image)): ?>
<p>You have not set an avatar</p>
<?php else: ?>
<p>Your avatar is <img src="<?php echo $user->image ?>" width="50" /></p>
<?php endif;?>
<p>Bio: <?php echo $user->bio ?></p>
<p>Your username/handle is: <?php echo $user->bio ?></p>
<p>Here is a link to your public profile: <?php echo $user->profile_url ?></p>
<p>You may as well <a href="<?php echo site_url('users/logout') ?>">Logout</a></p>
