<?php
namespace Stanford\AdminNinja;

require_once "emLoggerTrait.php";

require_once "classes/Utilities.php";

class AdminNinja extends \ExternalModules\AbstractExternalModule
{
    use emLoggerTrait;

    function hook_every_page_top($project_id) {

        if (!SUPER_USER) {
            return;
        }

        $this->injectNinja();

    }

    function injectNinja() {
        // Insert CSS
        ?>
        <link rel="stylesheet" href="<?php echo $this->getUrl("css/san_styles.css");?>">
        <link rel="stylesheet" href="<?php echo $this->getUrl("css/tables.css");?>">
        <?php

        // Add the ninja console
        include_once $this->getModulePath() . "pages/ninja_console.php";

        // Add the javascript
        ?>
        <script src="<?php echo $this->getUrl("js/hotkeys.min.js");?>"></script>
        <script src="<?php echo $this->getUrl("js/admin_ninja.js");?>"></script>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>


        <script>
            // Single global scope object containing all variables/functions
            var SAN = SAN || {};
            SAN.isDev = <?php echo $this->getSystemSetting("enable-system-debug-logging"); ?>;
            SAN.ajax_endpoint="<?php echo $this->getUrl('pages/ajax.php'); ?>";
        </script>
        <?php

    }

}

