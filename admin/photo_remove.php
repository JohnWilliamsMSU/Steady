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
    $report = new report($dbo);

    $photoId = 0;
    $photoInfo = array();

    if (isset($_GET['id'])) {

        $photoId = isset($_GET['id']) ? $_GET['id'] : 0;
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : '';
        $fromUserId = isset($_GET['fromUserId']) ? $_GET['fromUserId'] : 0;

        $photoId = helper::clearInt($photoId);
        $fromUserId = helper::clearInt($fromUserId);

        $photos = new photos($dbo);
        $photos->setRequestFrom($fromUserId);

        $photos->remove($photoId);

        $report->removePhotoReports($photoId);

    } else {

        header("Location: /admin/main.php");
        exit;
    }
