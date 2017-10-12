<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="nav navbar-header hidden-xs hidden-sm">
            <a class="navbar-brand" href="#"><?php echo isset($username) ? $username : ''; ?></a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="<?php echo site_url('search'); ?>" title="Search followers"><i class="fa fa-search" aria-hidden="true"></i></a></li>
            <li><a href="<?php echo site_url('compose'); ?>" title="Compose a message to selected followers"><i class="fa fa-pencil" aria-hidden="true"></i></a></li>
            <?php if (isset($is_admin) && $is_admin) { ?>
            <li><a href="<?php echo site_url('promo'); ?>" title="Promotion" class="dropdown-toggle" data-toggle="dropdown" role="button"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo site_url('promo'); ?>">Create promotion</a></li>
                    <li><a href="<?php echo site_url('promo/browse'); ?>">Browse promotions</a></li>
                </ul>
            </li>
            <?php } ?>
            <li><a href="<?php echo site_url('logout') ?>" title="Close your session"><i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
        </ul>
    </div>
</nav>