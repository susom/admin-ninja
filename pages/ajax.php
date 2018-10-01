<?php
namespace Stanford\AdminNinja;
/** @var \Stanford\AdminNinja\AdminNinja $module */

require_once $module->getModulePath() . "classes/Utilities.php";


if (!SUPER_USER) {
    header("Content-type: application/json");
    echo json_encode(array("Error" => "This utility is only available for REDCap Administrators"));
    exit();
}


$module->emDebug("Here with: ", $_POST);

if (isset($_GET['_type']) && $_GET['_type'] == "query") {
    //if (isset($_POST['getProjects'])) {
        // Get all projects
        $q = isset($_GET['q']) ? $_GET['q'] : null;
        $projects = Utilities::getAllProjectOptions($q);

        // Add option for ALL projects
        array_unshift($projects, array("id"=>"-- ALL", "text"=>"ALL " . count($projects) . " PROJECTS --"));

        // Return Results
        header("Content-type: application/json");
        //echo json_encode(array(
        //    "results" => array(
        //        array(
        //            "text" => "Projects",
        //            "children" => $projects
        //        )
        //    )
        //));
        //
        echo json_encode(
            [
                "results" => [
                    [
                        "text" => "Projects",
                        "children" => $projects
                    ]
                ]
            ]
        );
    //}
}


if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : "";

    $result = array("error" => "Invalid Action");

    if ($action == "analyze-project") {
        if (empty($project_id)) {
            $result = array( "error" => "Missing project id" );
        } else {
            // Get project
            $result = $module->getDuplicateCounts($project_id);
        }
    }

    if ($action == "get-all-projects") {
        $result = $module->getAllProjects();
    }

    if ($action == "dedup-project") {
        $result = $module->deduplicateProject($project_id);
    }

    echo json_encode($result);
//    echo json_encode(array("action"=>$action, "project_id" => $project_id));
}


//// BUILD PROJECT SELECT
//$options = array("<option value=''>Select a Project</option>");
//foreach ($projects as $project_id => $title) {
//    $options[] = "<option value='$project_id'" .
//        ( $project_id == $select_pid ? " selected" : "" ).
//        ">[$project_id] $title</option>";
//}
//$select = "<select style='height: 32px;' id='project_select' class='form_control' name='select_pid'>" . implode("",$options) . "</select>";
