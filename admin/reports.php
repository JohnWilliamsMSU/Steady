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

    $page_id = "reports";

    $error = false;
    $error_message = '';

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = "Reports";

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
                                    <span>Reports (Latest reports)</span>
                                </div>
                            </div>

                            <?php

                                $result = $stats->getReports();

                                $inbox_loaded = count($result['reports']);

                                if ($inbox_loaded != 0) {

                                    ?>

                                        <table class="admin_table">
                                            <tr>
                                                <th class="text-left">Id</th>
                                                <th>From account</th>
                                                <th>To account</th>
                                                <th>Abuse</th>
                                                <th>Date</th>
                                            </tr>

                                    <?php

                                        foreach ($result['reports'] as $key => $value) {

                                            draw($value);
                                        }

                                    ?>

                                        </table>

                                    <?php

                                } else {

                                    ?>

                                    <div class="info">
                                        List is empty.
                                    </div>

                                    <?php
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


        </script>

    </div>

</body>
</html>

<?php

    function draw($user)
    {
        ?>

        <tr>
            <td class="text-left"><?php echo $user['id']; ?></td>
            <td><?php if ($user['abuseFromUserId'] == 0) {echo "-";} else {echo "<a href=\"/admin/profile.php/?id={$user['abuseFromUserId']}\">From profile Id ({$user['abuseFromUserId']})</a>";} ?></td>
            <td><?php echo "<a href=\"/admin/profile.php/?id={$user['abuseToUserId']}\">To profile Id ({$user['abuseToUserId']})</a>"; ?></td>
            <td>
                <?php

                    switch ($user['abuseId']) {

                        case 0: {

                            echo "This is spam.";

                            break;
                        }

                        case 1: {

                            echo "Hate Speech or violence.";

                            break;
                        }

                        case 2: {

                            echo "Nudity or Pornography.";

                            break;
                        }

                        default: {

                            echo "Fake profile.";

                            break;
                        }
                    }
                ?>
            </td>
            <td><?php echo $user['date']; ?></td>
<!--            <td><a href="/admin/profile/?id=--><?php //echo $user['id']; ?><!--">Go to account</a></td>-->
        </tr>

        <?php
    }