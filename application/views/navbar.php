<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav navbar-right">
            <li><a href="<?php echo site_url('search'); ?>" title="Search followers"><i class="fa fa-search" aria-hidden="true"></i></a></li>
            <li><a href="<?php echo site_url('compose'); ?>" title="Compose a message to selected followers"><i class="fa fa-pencil" aria-hidden="true"></i></a></li>
            <?php if (isset($is_admin) && $is_admin) { ?>
            <li><a href="<?php echo site_url('promo'); ?>" title="Create a promotion"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></li>
            <?php } ?>
            <li><a href="<?php echo site_url('logout') ?>" title="Close your session"><i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</nav>