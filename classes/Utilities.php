<?php
/**
 * Created by PhpStorm.
 * User: andy123
 * Date: 10/1/18
 * Time: 1:38 PM
 */

namespace Stanford\AdminNinja;

use REDCap;
use User;

class Utilities
{

    /**
     * Get all projects in the system
     *
     * @param null $filterString
     * @return array
     */
    public static function getAllProjects($filterString = null) {
        $projects = array();
        $sql = "SELECT project_id, app_title FROM redcap_projects";
        if (!empty($filterString)) $sql .= " WHERE project_id LIKE '%$filterString%' OR app_title LIKE '%$filterString%'";
        $q = db_query($sql);
        while ($row = db_fetch_assoc($q)) {
            $pid = $row['project_id'];
            $projects[$pid] = $row['app_title'];
        }
        return $projects;
    }


    /**
     * Get Projects in format that is good for select2 ajax call
     * @param null $query
     * @return array
     */
    public static function getAllProjectOptions($query = null) {
        $projects = self::getAllProjects($query);
        $options = array();
        foreach ($projects as $pid => $title) {
            $options[] = array(
                "id" => $pid,
                "text" => "[" . $pid . "] " . $title
            );
        }
        return $options;
    }







    //Returns the next Record Id number of a project
    function getNextAutoNumberedRecordId($pid){

        $results = $this->query("
                 select record from redcap_data
                 where project_id = $pid
                 group by record
                 order by cast(record as unsigned integer) desc limit 1
                 ");
        $row = $results->fetch_assoc();
        if(empty($row)){
            return 1;
        }
        else{
            return $row['record']+1;
        }
    }


    //returns the project title given an array os project IDs
    function getPojectsbyPid($array){

        $projects=Array();
        foreach ($array as $pid){

            $sql = "select   p.project_id, p.app_title
					from redcap_projects p  where p.project_id like '%$pid%'";
            $results = $this->query($sql);
            // $row = $results->fetch_assoc();
            $row = $results->fetch_assoc();
            if(!empty($row)){
                array_push($projects,$row);
            }
        }
        return $projects;
    }


    //Returns a list of projects given the project title
    function getProjectsbyTitle($project_name){

        $projects=Array();
        $sql = "select   p.project_id, p.app_title, p.status, p.draft_mode, p.surveys_enabled, p.repeatforms
					from redcap_projects p  where p.app_title like '%$project_name%' order by p.project_id";
        $results = $this->query($sql);
        // $row = $results->fetch_assoc();
        while ( $query_res = db_fetch_assoc( $results ) )
        {
            array_push($projects,$query_res);
        }
        return $projects;
        //return array("Volvo", "BMW", "Toyota");;
    }


    // returns a the list of projects from an given the username
    function getProjectsbyUser($username){
        $projects=Array();
        $username=trim($username);
        if($username !="" and isset($username)){
            $projects= User::getProjectsByUser($username);
            return $projects;
        }
        return $projects;
    }

    //Returns  user name, name and last name given the email address
    function SearchBasicData($searchtext){

        $projects=Array();
        $searchtext=trim($searchtext);
        if($searchtext !="" and isset($searchtext) ){//filter_var($email_address, FILTER_VALIDATE_EMAIL)
            $sql = "select p.username, p.user_email, p.user_firstname,p.user_lastname 
                            from redcap_user_information p 
                              where p.user_email like '$searchtext' 
                                or 
                              p.username like '%$searchtext%' 
                                or 
                              p.user_firstname like '%$searchtext%' 
                                or 
                              p.user_lastname like '%$searchtext%' limit 5";
            $results = $this->query($sql);
            $projects = $results->fetch_assoc();
        }
        return $projects;
    }


    //  return last viewed projects from a given user the variable top represents the number or rows to return.
    function LastViewedProjects($username,$top){


        $projects=Array();
        $username=trim($username);
        if($username !="" and isset($username) ){//filter_var($email_address, FILTER_VALIDATE_EMAIL)
            $sql = "SELECT
                        rlv.project_id as 'Pid',
                        rp.app_title as 'Project Name',                       
                        max(rlv.ts) as 'Date',
                        datediff(NOW(),max(rlv.ts)) as 'Days_Ago'
                    FROM redcap_log_view as rlv
                    INNER JOIN redcap_projects as rp on rp.project_id = rlv.project_id
                        where rlv.user = '$username'
                        group by rlv.project_id
                        order by max(rlv.ts) desc
                        limit $top ";
            $results = $this->query($sql);
            while ( $query_res = db_fetch_assoc( $results ) )
            {


                array_push($projects,$query_res);
            }

        }
        return $projects;
    }



    //search by IRB or PI name

    function SearchbyPIorIRB($username,$top){

        $projects=Array();
        $username=trim($username);
        if($username !="" and isset($username) ){//filter_var($email_address, FILTER_VALIDATE_EMAIL)
            $sql = "SELECT
                        rp.app_title,
                        rlv.project_id,
                        max(rlv.ts) as 'date',
                        datediff(NOW(),max(rlv.ts)) as 'days_ago'
                    FROM redcap_log_view as rlv
                    INNER JOIN redcap_projects as rp on rp.project_id = rlv.project_id
                        where rlv.user = '$username'
                        group by rlv.project_id
                        order by max(rlv.ts) desc
                        limit $top ";
            $results = $this->query($sql);
            while ( $query_res = db_fetch_assoc( $results ) )
            {
                array_push($projects,$query_res);
            }

        }
        return $projects;
    }


    function html_headers_table($headers=array()){

        $table_headers= "";
        foreach ($headers as $h){

            $table_headers=$table_headers."<div class='san_cell'>".$h."</div>";

        }

        return "<div class='san_row san_header san_blue'>$table_headers</div>";

    }


    //Array to Html Table
    function html_table($data = [], $headers, $link, $title)
    {
        $rows = [];

        foreach ($data as $row) {
            $cells = [];
            foreach ($row as  $cell) {

                $cells[] = "<div class='san_cell'>{$cell}</div>";

            }
            $rows[] = "<div class='san_row'>" . implode('', $cells) . "</div>";
        }
        return "<div class='san_wrapper'><div class='san_title'>$title</div><div class='san_table'>" .$this->html_headers_table($headers). implode('', $rows) . "</div></div>";
    }



    //SURVEY HASH
    /**
     * Extract the section of the input string that looks like a survey hash
     * @param string $lookup_val The string from which the survey hash value
     * will be extracted.
     * @return array Array with two elements: 1) lookup_success (bool),
     * indicating whether a valid hash was found in $lookup_val;
     * 2) lookup_result (mixed), array of survey details or error message
     */
    public function lookup_Hash($lookup_val) {
        $resultArray = array(
            // 'lookup_success' => false,
            'lookup_result' => ''
        );

        if (!isset($lookup_val) || $lookup_val=='') {
            $resultArray['lookup_result'] = Array();
        } else {
            $hash = $this->extractHash($lookup_val);
            if (!isset($hash) || $hash=='') {
                $resultArray['lookup_result'] = Array();
            } else {
                try {
                    $details = $this->readSurveyDetailsFromHash($hash);
                    if (count($details) > 0) {
                        //   $resultArray['lookup_success'] = true;
                        $resultArray['lookup_result'] = $details;
                    } else {
                        $resultArray['lookup_result'] = Array();
                    }
                } catch (Exception $ex) {
                    $resultArray['lookup_result'] = $ex->getMessage();
                }
            }
        }
        return $resultArray['lookup_result'];
    }


    /**
     * Extract the section of the input string that looks like a survey hash
     * @param string $lookup_val The string from which the survey hash value
     * will be extracted.
     * @return string Hash value (generally 10 characters), or empty string
     * if no hash value found.
     */
    private function extractHash($lookup_val) {
        $hash = '';
        $matches = array();
        if (strpos($lookup_val, 's=')!==false) {
            if (preg_match('/(?<=s=)[^\&]*/', $lookup_val, $matches)) {
                $hashPart = $matches[0];
            }
        } else {
            $hashPart = $lookup_val;
        }
        if (preg_match('/^\w{6,10}$/', $hashPart, $matches)) {
            $hash = $matches[0];
        }
        return $hash;
    }


    /**
     * Look up details of survey corresponding to the hash value provided
     * @param string $hash A (generally) 10-character value identifying an
     * individual participant survey.
     * @return array
     */
    private function readSurveyDetailsFromHash($hash) {
        global $lang;

        $details = array();
        if (isset($hash) && $hash!=='') {
            $sql = "SELECT s.survey_id,s.project_id,s.form_name,s.title as survey_title".
                ",pr.app_title,pr.repeatforms".
                ",p.participant_id,p.event_id,p.hash,IF(p.participant_email IS NULL,1,0) as is_public_survey_link".
                ",em.descrip".
                ",ea.arm_id,ea.arm_num,ea.arm_name".
                ",proj_ea.num_project_arms".
                ",r.response_id,r.record,r.instance,r.start_time,r.first_submit_time,r.completion_time,r.return_code,r.results_code ".
                "FROM redcap_surveys s ".
                "INNER JOIN redcap_projects pr ON s.project_id = pr.project_id ".
                "INNER JOIN redcap_surveys_participants p ON s.survey_id = p.survey_id ".
                "INNER JOIN redcap_events_metadata em ON em.event_id = p.event_id ".
                "INNER JOIN redcap_events_arms ea ON ea.arm_id = em.arm_id ".
                "INNER JOIN (SELECT project_id, COUNT(arm_id) as num_project_arms FROM redcap_events_arms GROUP BY project_id) proj_ea ON proj_ea.project_id = pr.project_id ".
                "LEFT OUTER JOIN redcap_surveys_response r ON p.participant_id = r.participant_id ".
                "WHERE hash = '".db_real_escape_string($hash)."' LIMIT 1";
            $result = db_query($sql);
            $details = db_fetch_assoc($result);
            db_free_result($result);

            // get event name (with arm ref, if multiple)
            if (isset($details['project_id']) && intval($details['project_id']) > 0) {
                $event_name = '';

                if ($details['is_public_survey_link']) {
                    $details['record'] = $lang['survey_279']; // Public Survey Link
                    if (intval($details['num_project_arms']) > 1) { $event_name = $details['descrip']." (".$details['arm_name'].")"; }
                    $details['instance'] = '';

                } else if (!$details['repeatforms']) {
                    $event_name = $lang['control_center_149']; // N/A (not a longitudinal project)
                } else {
                    $event_name = (intval($details['num_project_arms']) > 1)
                        ? $event_name = $details['descrip']." (".$details['arm_name'].")"
                        : $event_name = $details['descrip'];
                }
                $details['event_name'] = $event_name;
            }
        }
        return $details;
    }


    function Max_Current_Pid(){
        //
        $results = $this->query("select max(p.project_id) as max from redcap_projects as p");
        $row = $results->fetch_assoc();
        if(empty($row)){
            return 0;
        }
        else{
            return $row['max']+1;
        }
    }
}