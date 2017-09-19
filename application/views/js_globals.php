<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script>
    d_Session = <?php echo json_encode($session); ?>;
    AppDumbu.ajaxUrl = "<?php echo site_url(); ?>";
    AppDumbu.baseUrl = "<?php echo base_url(); ?>";
</script>
