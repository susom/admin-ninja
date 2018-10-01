<?php
/** @var \Stanford\AdminNinja\AdminNinja $this */

// Insert NINJA Console
?>
<!-- Admin Ninja Console    style="display:none" -->
<div id="shell-wrap" class="hidden">
    <div class="shell-top-bar" >
        <ul class="nav nav-tabs"  id="console_menu" role="tablist">
            <li class="nav-item"><a id="san_search_link" class="nav-link active" data-toggle="tab" href="#san_search">Search</a></li>
            <li class="nav-item"><a id="san_editor_link" class="nav-link" data-toggle="tab"  href="#san_editor">Text Editor</a></li>
            <li class="nav-item"><a class="nav-link"  data-toggle="tab" onclick="AutofillFoms()" href="#">Autofill Forms</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab"  href="#user_results">Hotkeys</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab"  href="#">Email adresses</a></li>
        </ul>
    </div>
    <div class="tab-content" id="myTabContent">

        <div id="san_search" class="tab-pane active">
            <h3>
                <i class="move_search_icon fab fa-searchengin"></i>
            </h3>
            <input class="shell-searchbox" placeholder="Search by Project Id, Project name, username, first name, last name, primary email, Survey link, Survey hash, PI or IRB Number:" >
            <!--                         <span>Search by Project Id, Project name, username, first name, last name, primary email, Survey link, Survey hash, PI or IRB Number.</span>-->


            <!-- SAN HOME TAB -->
            <div class="row">
                <div class="col recent-projects" valign="top">
                    <?php
                        //$array= $this->LastViewedProjects("site_admin",5);
                        //$url=APP_PATH_WEBROOT."ProjectSetup/index.php?pid=";
                        //echo $this->html_table($array,array("Pid","Project Name","Date","Days Ago"),$url,'Recent Projects');
                    ?>
                </div>
                <div class="col pinned-projects" valign="top">
                    <?php
                        //$pinned=$this->getPojectsbyPid($this->getSystemSetting('pinned-projects'));
                        //echo $this->html_table($pinned,array("Pid","Project Name"),$url,"<i class='fas fa-thumbtack'></i> Pinned Projects");
                    ?>
                </div>
                <div class="col san-links" valign="top">
                    <div class='san_wrapper'>
                        <div class='san_title'>Quick Access</div>

                        <div class="row san_link_table">
<!--                            <div class="san_col">-->
<!--                                <a class="san_link" href="--><?php //echo APP_PATH_WEBROOT_PARENT?><!--/index.php?action=create"><i class="fas fa-plus"></i> New Project</a>-->
<!--                            </div>-->
<!--                            <div class="san_col">-->
<!--                                <a class="san_link" href="--><?php //echo APP_PATH_WEBROOT_PARENT?><!--index.php?action=myprojects"><i class="far fa-list-alt"></i> My Projects</a>-->
<!--                            </div>-->
<!--                            <div class="san_col">-->
<!--                                <a class="san_link" href="--><?php //echo APP_PATH_WEBROOT?><!--/ControlCenter/index.php"><i class="fas fa-cog"></i> Control Center</a>-->
<!--                            </div>-->
<!--                            <div class="san_col">-->
<!--                                <a class="san_link" href="--><?php //echo APP_PATH_WEBROOT?><!--/ControlCenter/homepage_settings.php"><i class="fas fa-home"></i> Home Page Configuration</a>-->
<!--                            </div>-->
<!--                            <div class="san_col">-->
<!--                                <a class="san_link" href="https://community.projectredcap.org"><i class="fas fa-globe"></i> REDCap Community website </a>-->
<!--                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="san_editor" class="tab-pane fade" >
            <textarea id="san_textarea" class="shell-textarea" rows="10" title="edit" placeholder=".. paste your text or code here.." ></textarea>
        </div>
<!---->
<!--        <div id="user_results" class="tab-pane fade ">-->
<!--            -->
<!--            --><?php
//            //todo: REDCap::filterHtml  REDCap::escapeHtml
//
//            $array= $this->LastViewedProjects("site_admin",5);
//            $url=APP_PATH_WEBROOT."ProjectSetup/index.php?pid=";
//            echo $this->html_table($array,array("Pid","Project Name","Date","Days Ago"),$url,'Recent Projects');?>
<!---->
<!---->
<!---->
<!---->
<!---->
<!--        </div>-->

    </div>
</div>