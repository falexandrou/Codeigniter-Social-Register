<a href="javascript:auth_popup('<?php echo site_url('users/facebook') ?>', 500, 200)" title="connect with facebook">Connect with facebook</a> or
<?php if (!$twitter->logged_in()) { ?>
<a href="javascript:auth_popup('<?php echo site_url('users/twitter'); ?>', 810, 440)">Connect with twitter</a>
<?php } else { ?>    
<a href="<?php echo site_url('users/logout'); ?>">Logout</a>
<?php } ?>
<script type="text/javascript">
    function auth_popup(url, width, height){
	var left = (screen.width/2)-(width/2);
	var top = (screen.height/2)-(height/2);
	window.open (url,"auth","toolbar=no,location=no,directories=no,status=no,scrollbars=no,menubar=0,resizable=1,width="+width+",height="+height+',top='+top+',left='+left);
    }
</script>