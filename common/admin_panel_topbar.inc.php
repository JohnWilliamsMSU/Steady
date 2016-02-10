<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */


    if (!admin::isSession()) {

        ?>

            <div id="page_topbar">

                <div class="topbar">
                    <div class="content">
                        <a href="/" class="logo"></a>

                        <div style="float: right">
                            <a href="/admin/login.php" class="topbar_item">Log in</a>
                        </div>
                    </div>
                </div>

            </div>
        <?php

    } else {

        ?>

            <div id="page_topbar">

                <div class="topbar">
                    <div class="content">
                        <a href="/admin/main.php" class="logo"></a>

                        <div style="float: right">
                            <a href="/admin/main.php" class="topbar_item">General</a>
                            <a href="/admin/users.php" class="topbar_item">Users</a>
                            <a href="/admin/messages_stream.php" class="topbar_item">Messages Stream</a>
                            <a href="/admin/gifts.php" class="topbar_item">Gifts</a>
                            <a href="/admin/reports.php" class="topbar_item">Reports</a>
                            <a href="/admin/photo_reports.php" class="topbar_item">Photo Reports</a>
                            <a href="/admin/admob.php" class="topbar_item">AdMob</a>
                            <a href="/admin/gcm.php" class="topbar_item">GCM</a>
                            <a href="/admin/support.php" class="topbar_item">Support</a>
                            <a href="/admin/settings.php" class="topbar_item">Settings</a>
                            <a href="/admin/logout.php/?access_token=<?php echo admin::getAccessToken(); ?>&continue=/" class="topbar_item">Logout</a>
                        </div>
                    </div>
                </div>

            </div>
        <?php
    }
?>