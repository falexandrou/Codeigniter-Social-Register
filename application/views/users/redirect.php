<script type="text/javascript">
   window.onload = function (){
       window.opener.location = '<?php echo site_url('users/register') ?>/<?php echo $method; ?>/';
       self.close();
   }
</script>