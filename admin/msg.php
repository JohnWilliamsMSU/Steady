<?php

    /*!
     * ifsoft.co.uk engine v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    include_once($_SERVER['DOCUMENT_ROOT']."/core/init.inc.php");

    if (!admin::isSession()) {

        header("Location: /admin/login.php");
    }

    $stats = new stats($dbo);
    $admin = new admin($dbo);

    $msgId = 0;
    $msgInfo = array();

    if (isset($_GET['id'])) {

        $msgId = isset($_GET['id']) ? $_GET['id'] : 0;
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        $msgId = helper::clearInt($msgId);

        if ($accessToken === admin::getAccessToken() && !APP_DEMO) {

            $messages = new messages($dbo);
            $messages->remove($msgId);
        }
    }