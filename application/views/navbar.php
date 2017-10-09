<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav navbar-right">
            <li><a href="<?php echo site_url('search'); ?>"><i class="fa fa-search" aria-hidden="true"></i></a></li>
            <?php if (isset($is_admin) && $is_admin) { ?>
            <li><a href="<?php echo site_url('promo'); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></li>
            <?php } ?>
            <li><a href="<?php echo site_url('compose'); ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a></li>
            <li><a href="<?php echo site_url('logout') ?>"><i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</nav>