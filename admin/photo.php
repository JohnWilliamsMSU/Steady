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

    $photoId = 0;
    $photoInfo = array();

    if (isset($_GET['id'])) {

        $photoId = isset($_GET['id']) ? $_GET['id'] : 0;
        $accessToken = isset($_GET['access_token']) ? $_GET['access_token'] : 0;
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        $photoId = helper::clearInt($photoId);

        $photos = new photos($dbo);
        $photoInfo = $photos->info($photoId);

        if ($photoInfo['error'] === true) {

            header("Location: /admin/main.php");
            exit;
        }

        if ($photoInfo['removeAt'] != 0) {

            header("Location: /admin/photo_reports.php");
            exit;
        }

    } else {

        header("Location: /admin/main.php");
        exit;
    }

    $page_id = "photo";

    $error = false;
    $error_message = '';

    $css_files = array("admin.css");
    $page_title = "Photo info";

    include_once($_SERVER['DOCUMENT_ROOT']."/common/header.inc.php");

?>

<body class="bg_gray">

    <div id="page_wrap">

    <?php

        include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_topbar.inc.php");
    ?>

    <div id="page_layout">

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_banner.inc.php");
        ?>

        <div id="page_body">
            <div id="wrap3">
                <div id="wrap2">
                    <div id="wrap1">

                        <div id="content">

                            <div class="header">
                                <div class="title">
                                    <span>Photo info</span>
                                </div>
                            </div>

                            <?php

                                if ($photoInfo['removeAt'] > 0) {

                                    ?>

                                    <?php

                                } else {

                                    draw($photoInfo, $helper);
                                }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php

            include_once($_SERVER['DOCUMENT_ROOT']."/common/admin_panel_footer.inc.php");
        ?>

        <script type="text/javascript">

            window.Photo || (window.Photo = {});

            Photo.remove = function (offset, fromUserId, accessToken) {

                $.ajax({
                    type: 'GET',
                    url: '/admin/photo_remove.php/?id=' + offset + '&fromUserId=' + fromUserId + '&access_token=' + accessToken,
                    data: 'itemId=' + offset + '&fromUserId=' + fromUserId + "&access_token=" + accessToken,
                    timeout: 30000,
                    success: function(response) {

                        window.location.href = "/admin/photo_reports.php";

                        $('div.post_item[data-id=' + offset + ']').remove();
                    },
                    error: function(xhr, type){

                    }
                });
            };

        </script>

    </div>

</body>
</html>

<?php

    function draw($post, $helper = null) {

        $fromUserPhoto = "/img/profile_default_photo.png";

        if (strlen($post['fromUserPhoto']) != 0) {

            $fromUserPhoto = $post['fromUserPhoto'];
        }

        ?>

        <div class="post post_item" data-id="<?php echo $post['id']; ?>">

            <a class="profile_cont" href="/admin/profile.php/?id=<?php echo $post['fromUserId']; ?>">
                <img src="<?php echo $fromUserPhoto; ?>">
            </a>

            <div class="post_content">

                <div class="action_remove" onclick="Photo.remove('<?php echo $post['id']; ?>', '<?php echo $post['fromUserId']; ?>', '<?php echo admin::getAccessToken(); ?>'); return false;"></div>

                <div class="post_data" style="font-weight: normal">
                    <?php echo $post['comment']; ?>
                </div>

                <?php

                    if (strlen($post['previewImgUrl']) != 0) {

                        ?>
                            <div class="post_img">
                                <img style="max-width: 600px;" src="<?php echo $post['previewImgUrl']; ?>">
                            </div>
                        <?php
                    }
                ?>

                <div class="post_footer">
                    <span class="time"><?php echo $post['timeAgo']; ?> | <?php echo $post['date']; ?></span>
                </div>

            </div>
        </div>

    <?php
    }