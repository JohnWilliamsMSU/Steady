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
    $gift = new gift($dbo);

    $page_id = "gifts";

    $error = false;
    $error_message = '';

    if (isset($_GET['action'])) {

        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        $action = helper::clearText($action);
        $action = helper::escapeText($action);

        $id = helper::clearInt($id);

        if (!APP_DEMO) {

            switch($action) {

                case 'remove': {

                    $gift->db_remove($id);

                    header("Location: /admin/gifts.php");

                    break;
                }

                default: {

                    header("Location: /admin/gifts.php");

                    break;
                }
            }
        }
    }

    if (!empty($_POST)) {

        $authToken = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';
        $cost = isset($_POST['cost']) ? $_POST['cost'] : 3;
        $category = isset($_POST['category']) ? $_POST['category'] : 0;

        $cost = helper::clearInt($cost);
        $category = helper::clearInt($category);

        if ($authToken === helper::getAuthenticityToken() && !APP_DEMO) {

            if (isset($_FILES['uploaded_file']['name'])) {

                $uploaded_file = $_FILES['uploaded_file']['tmp_name'];
                $uploaded_file_name = basename($_FILES['uploaded_file']['name']);
                $uploaded_file_ext = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);

                $gift_next_id = $gift->db_getMaxId();
                $gift_next_id++;

                if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], GIFTS_PATH.$gift_next_id.".".$uploaded_file_ext)) {

                    $gift->db_add($cost, $category, APP_URL."/".GIFTS_PATH.$gift_next_id.".".$uploaded_file_ext);
                }
            }
        }

        header("Location: /admin/gifts.php");
    }

    helper::newAuthenticityToken();

    $css_files = array("admin.css");
    $page_title = "Gifts";

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
                                    <span>Add New Gift</span>
                                </div>
                            </div>

                            <form method="post" action="/admin/gifts.php" enctype="multipart/form-data" class="support_wrap">

                                <input type="hidden" name="authenticity_token" value="<?php echo helper::getAuthenticityToken(); ?>">

                                <div class="ticket_email">
                                    <label for="category" class="noselect">Gift Category:</label>
                                    <div>
                                        <select name="category">
                                           <option selected="selected" value="0">Default Category</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="ticket_email">
                                    <label for="cost" class="noselect">Cost (In Credits):</label>
                                    <div><input type="text" maxlength="100" name="cost" value="3"></div>
                                </div>

                                <div class="ticket_email">
                                    <label for="uploaded_file" class="noselect">Image File (Attention! To view images correctly - we recommend using the image size of 256x256 pixels. Formats: JPG and PNG.):</label>
                                    <input class="upload_file" id="upload_photo_input" name="uploaded_file" type="file">
                                </div>

                                <div class="ticket_controls">
                                    <button class="primary_btn big_btn">Add</button>
                                </div>
                            </form>

                            <div class="header" style="margin-top: 30px;">
                                <div class="title">
                                    <span>Gifts</span>
                                </div>
                            </div>

                            <?php

                                $result = $gift->db_get(0, 100);

                                $inbox_loaded = count($result['items']);

                                if ($inbox_loaded != 0) {

                                    ?>

                                        <table class="admin_table">
                                            <tr>
                                                <th class="text-left">Id</th>
                                                <th>Gift Image</th>
                                                <th>Cost</th>
                                                <th>Category</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>

                                    <?php

                                        foreach ($result['items'] as $key => $value) {

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

    function draw($gift)
    {
        ?>

        <tr data-id="<?php echo $gift['id']; ?>">
            <td class="text-left"><?php echo $gift['id']; ?></td>
            <td style="text-align: left;"><img style="width: 64px; border: 1px solid #ccc" src="<?php echo $gift['imgUrl']; ?>"></td>
            <td><?php echo $gift['cost'] ?></td>
            <td><?php echo $gift['category'] ?></td>
            <td><?php echo $gift['date']; ?></td>
            <td><a href="/admin/gifts.php/?id=<?php echo $gift['id']; ?>&action=remove">Remove</a></td>
        </tr>

        <?php
    }