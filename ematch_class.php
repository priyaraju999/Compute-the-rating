<?php

$idx = new ematch_class(1);

class ematch_class {

    var $allow_unicode = 1;

    var $output    = "";

    var $resultRows = array();

    var $numberOfRows = 0;
    var $numberOfMatchedRows = 0;
    var $numberOfColumns = 0;

    var $columnTypes = array();
    var $columnTypeNames = array();
    var $rowPreFetch;

    var $paraValues = array();
    var $rating = array();

    //for Title rating search
    var $isTitleSearch = 0;
    var $askedTitle;

    //for Speciality rating search
    var $isSpecialitySearch = 0;
    var $askedSpeciality;

    //for Keywords based search
    var $isKeywordSearch = 0;
    var $askedKeyword;

	//for Type rating search
	var $isTypeSearch = 0;
    var $askedType;

	//for Company rating search
	var $isCompanySearch = 0;
	var $askedCompany;
	
	// for matching the results with a company's speciality if company search is selected
	var $companySpeciality = array();

    var $askedLocationName;
    var $askedLocationIndex;
    var $askedMinSal=0;          // For job search

    var $askedMaxSal=0;          // For member (candidate search)
    var $askedDegree;            // For member (candidate search)
    var $askedYearOfExp;            // For member (candidate search)

    var $searchType;             // 1: job search; 2: member search
    var $locationSearchType = 1; // 1: by country; 2: by region; 3: by state; 4: by city

    var $originalIndex = array();

    var $neighborCityBeginningIndex = array();
    var $neighborStateBeginningIndex = array();
    var $usCityInStateBeginningIndex = array();
    var $usStateInRegionBeginningIndex =array();

    //function ematch ( $ematchOrInsert )
    function __construct ( $ematchOrInsert )
    {

	global $ibforums, $DB, $std, $std1;

	

	if ($ematchOrInsert == $std1->EMATCH_) {

		$this->neighborCityBeginningIndex[0] = 0;
		$this->neighborStateBeginningIndex[0] = 0;
		$this->usCityInStateBeginningIndex[0] = 0;
		$this->usStateInRegionBeginningIndex[0] = 0;

		for ($i = 1; $i< $std1->NUMBER_OF_US_CITIES; $i++) {
			$this->neighborCityBeginningIndex[$i] =
					$this->neighborCityBeginningIndex[$i-1] +
						2*$std1->usNeighborCityNumber[$i-1];
		}

		for ($i = 1; $i< $std1->NUMBER_OF_US_STATES; $i++) {
			$this->usCityInStateBeginningIndex[$i] +=
				 $this->usCityInStateBeginningIndex[$i-1] + 
						$std1->usCitiesInStatesNumber[$i-1];
		}

		for ($i = 1; $i< $std1->NUMBER_OF_US_STATES; $i++) {

			$this->neighborStateBeginningIndex[$i] =
					$this->neighborStateBeginningIndex[$i-1] +
						2*$std1->usNeighborStateNumber[$i-1];

		}

		for ($i = 1; $i< $std1->NUMBER_OF_US_REGIONS; $i++) {
			$this->usStateInRegionBeginningIndex[$i] =
					$this->usStateInRegionBeginningIndex[$i-1] + 
					$std1->usStatesInRegionsNumber[$i-1] + 1;
		}

		for ($i = 0; $i < $std1->MAX_MATCH_NUMBER; $i++)
			$this->originalIndex[$i] = $i;

		// Initial ratings
		for ($i = 0; $i < $std1->MAX_MATCH_NUMBER; $i++) {
			$this->rating[$i] = 100;
		}
	}

    }

    function buildJobQuery ($sType)
    { 
	global $ibforums, $DB, $std, $std1, $print, $skin_universal;

	$addAND = 0;


	$this->searchType = $sType;
	$jobQuerySqlStmt = "SELECT * FROM job";    // base query


	$this->paraValues[$std1->JOB_TABLE_JOB_TYPE] = $ibforums->input['jobType'];

	if ( (strtolower( $this->paraValues[$std1->JOB_TABLE_JOB_TYPE] ) != "any" ) &&
	     (trim( $this->paraValues[$std1->JOB_TABLE_JOB_TYPE] ) != "" ) ){
		  $this->isTypeSearch = 1;
		  $this->askedType = $ibforums->input['jobType'];
	}
	
	
	/*
	New search which will search based on realted Titles along with rating. We are enabling this 
	feature only when the user wants to serach based on Title. We assign the isTitleSearch as 1 and
	set the user selected title to askedTitle with the help of Input from $ibforums. The class members
	isTitleSearch and askedTitle will help us know if we need to search each record based on title
	in computeAJobRating function for each record of database.
	*/

	$this->paraValues[$std1->JOB_TABLE_JOB_TITLE] = $ibforums->input['jobTitle'];

	if ( (strtolower( $this->paraValues[$std1->JOB_TABLE_JOB_TITLE] ) != "any" ) &&
	     (trim( $this->paraValues[$std1->JOB_TABLE_JOB_TITLE] ) != "" ) ){
		  $this->isTitleSearch = 1;
		  $this->askedTitle = $ibforums->input['jobTitle'];
	}


	/*
	New search which will search based on specialization along with rating. We are enabling this 
	feature only when the user wants to search based on specialization. We assign the isSpecialitySearch as 1 and
	set the user selected title to askedSpeciality with the help of Input from $ibforums. The class members
	isSpecialitySearch and askedSpeciality will help us know if we need to search each record based on similar
	speciality in computeAJobRating function for each record of database.
	*/

	$this->paraValues[$std1->JOB_TABLE_SPECIALIZATION] = $ibforums->input['specialty'];

	if ( (strtolower( $this->paraValues[$std1->JOB_TABLE_SPECIALIZATION] ) != "any" ) &&
	      (trim( $this->paraValues[$std1->JOB_TABLE_SPECIALIZATION] ) != "" ) )
	{
		$this->isSpecialitySearch = 1;
		$this->askedSpeciality = $ibforums->input['specialty'];
	}

	/*
	New code which will search based on keywords along with rating. We are enabling this 
	feature only when the user wants to search based on keywords. We assign the isKeywordSearch as 1 and
	set the user give keywords to askedKeyword with the help of Input from $ibforums. The class members
	isKeywordSearch and askedKeyword will help us know if we need to search each record based on similar
	keyword in computeAJobRating function for each record of database.
	*/

	$this->paraValues[$std1->JOB_TABLE_DESCRIPTION] = $ibforums->input['field_keyword'];


	if ( (strtolower( $this->paraValues[$std1->JOB_TABLE_DESCRIPTION] ) != "any" ) &&
	      (trim( $this->paraValues[$std1->JOB_TABLE_DESCRIPTION] ) != "" ) )
	{
		$this->isKeywordSearch = 1;
		$this->askedKeyword = $ibforums->input['field_keyword'];
	}
	
	$this->paraValues[$std1->JOB_TABLE_COMPANY_NAME] = $ibforums->input['companyName'];

	if ( (strtolower( $this->paraValues[$std1->JOB_TABLE_COMPANY_NAME] ) != "any" ) &&
	     (trim( $this->paraValues[$std1->JOB_TABLE_COMPANY_NAME] ) != "" ) ){
		  $this->isCompanySearch = 1;
		  $this->askedCompany = $ibforums->input['companyName'];
	}

	$minSal = $ibforums->input['minSalary'];
	if ( strtolower( $minSal ) !=  "any")
	{
		  $this->askedMinSal = $minSal;
		  $this->paraValues[$std1->JOB_TABLE_MIN_SALARY] = $minSal;
	}

	$this->locationType = $ibforums->input['locationType'];
	if ( $this->locationType == "region") {
		  $this->locationSearchType = $std1->SEARCH_BY_REGION;
		  $this->askedLocationName = $ibforums->input['region'];
		  $this->askedLocationIndex = $this->findRegionIndex( strtolower($this->askedLocationName));
		  $this->paraValues[$std1->JOB_TABLE_REGION_NAME] = $this->askedLocationName;
	} else if ( $this->locationType == "state" ) {
		  $this->locationSearchType = $std1->SEARCH_BY_STATE;
		  $this->askedLocationName = $ibforums->input['state'];
		  $this->askedLocationIndex = $this->findStateIndex( strtolower($this->askedLocationName));
		  $this->paraValues[$std1->JOB_TABLE_STATE_NAME] = $this->askedLocationName;
	} else if ( $this->locationType == "city" ) {
		  $this->locationSearchType = $std1->SEARCH_BY_CITY;
		  $this->askedLocationName = $ibforums->input['city'];
		  $this->askedLocationIndex = $this->findCityIndex( strtolower($this->askedLocationName));
		  $this->paraValues[$std1->JOB_TABLE_LOCATION] = $this->askedLocationName;
	}

	return $jobQuerySqlStmt;
    } 

    function buildInsertJobStmt ()
    {
	global $ibforums, $DB, $std, $std1, $print, $skin_universal;

	$parsing_err = "";
	$insertJobStmt = "INSERT INTO job VALUES (";    // base query

	$jobId = $this->getANewJobId();

	$insertJobStmt .= $jobId . ",";

	$paraValues[$std1->JOB_TABLE_JOB_TYPE] = trim($ibforums->input['jobType']);
	if ( ($paraValues[$std1->JOB_TABLE_JOB_TYPE] == "") ||
	     (strtolower($paraValues[$std1->JOB_TABLE_JOB_TYPE]) == "any") )
	{
	     $parsing_err .= "JobType missing.\n";
	     $numberOfMissingFields++;
	} 
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_JOB_TYPE] .
	  $paraValues[$std1->JOB_TABLE_JOB_TYPE] .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_JOB_TYPE] . ", ";

	$paraValues[$std1->JOB_TABLE_JOB_TITLE] = trim($ibforums->input['jobTitle']);
	if ( ($paraValues[$std1->JOB_TABLE_JOB_TITLE] == "") ||
	     (strtolower($paraValues[$std1->JOB_TABLE_JOB_TITLE]) == "any") )
	{
	     $parsing_err .= "JobTitle missing.\n";
	     $numberOfMissingFields++;
	}
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_JOB_TITLE] .
	  $paraValues[$std1->JOB_TABLE_JOB_TITLE] .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_JOB_TITLE] . ", ";

	$paraValues[$std1->JOB_TABLE_SPECIALIZATION] = trim($ibforums->input['specialty']);
	if ( ($paraValues[$std1->JOB_TABLE_SPECIALIZATION] == "" ) && 
	     (strtolower($paraValues[$std1->JOB_TABLE_SPECIALIZATION]) == "any"))
	{
	     $parsing_err .= "specialty missing.\n";
	     $numberOfMissingFields++;
	}
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_SPECIALIZATION] .
	  $paraValues[$std1->JOB_TABLE_SPECIALIZATION] .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_SPECIALIZATION] . ", ";

	$paraValues[$std1->JOB_TABLE_COUNTRY_CODE] = trim($ibforums->input['countryOrAreaCode']);
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_COUNTRY_CODE] .$paraValues[$std1->JOB_TABLE_COUNTRY_CODE].$std1->jobTableColumnQuotes[$std1->JOB_TABLE_COUNTRY_CODE].",";

	$paraValues[$std1->JOB_TABLE_REGION_NAME] = trim($ibforums->input['regionName']);
	if ( (strtolower($paraValues[$std1->JOB_TABLE_REGION_NAME]) == "any") || 
		  ($paraValues[$std1->JOB_TABLE_REGION_NAME] == "") )
	{
	     $paraValues[$std1->JOB_TABLE_REGION_NAME] = "";
	     $parsing_err .= "regionName missing.\n";
	     $regionMissing = 1;
	}
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_REGION_NAME] .
	  $paraValues[$std1->JOB_TABLE_REGION_NAME] .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_REGION_NAME] . ", ";

	$paraValues[$std1->JOB_TABLE_STATE_NAME] = trim($ibforums->input['stateName']);
	if ( (strtolower($paraValues[$std1->JOB_TABLE_STATE_NAME]) == "any" ) ||
	     ($paraValues[$std1->JOB_TABLE_STATE_NAME] == "") )
	{
	     $paraValues[$std1->JOB_TABLE_STATE_NAME] = "";
	     $parsing_err .= "stateName missing.\n";
	     $stateMissing = 1;
	}
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_STATE_NAME] .
	  $paraValues[$std1->JOB_TABLE_STATE_NAME] .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_STATE_NAME] . ", ";

	$paraValues[$std1->JOB_TABLE_LOCATION] = trim($ibforums->input['cityName']);
	if ( (strtolower($paraValues[$std1->JOB_TABLE_LOCATION]) == "any" ) ||
	     ($paraValues[$std1->JOB_TABLE_LOCATION] == "") )
	{
	     $paraValues[$std1->JOB_TABLE_LOCATION] = "";
	     $parsing_err .= "cityName missing.\n";
	     $cityMissing = 1;
	}
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_LOCATION] .
	  $paraValues[$std1->JOB_TABLE_LOCATION] .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_LOCATION] . ", ";

	$paraValues[$std1->JOB_TABLE_MIN_SALARY] = trim($ibforums->input['minSalary']);
	if ( ($paraValues[$std1->JOB_TABLE_MIN_SALARY] != "") &&
	     (strtolower($paraValues[$std1->JOB_TABLE_MIN_SALARY]) != "any") )
	{
	     $insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MIN_SALARY] .
		  $paraValues[$std1->JOB_TABLE_MIN_SALARY] .
		  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MIN_SALARY] . ", ";
	} else {   // set min salary to 0 if it is not supplied
	     $insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MIN_SALARY] . 0 .
		  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MIN_SALARY] . ", ";
	}

	$paraValues[$std1->JOB_TABLE_MAX_SALARY] = trim($ibforums->input['maxSalary']);
	if ( ($paraValues[$std1->JOB_TABLE_MAX_SALARY] != "") &&
	     (strtolower($paraValues[$std1->JOB_TABLE_MAX_SALARY]) != "any"))
	{
	     if ( ($paraValues[$std1->JOB_TABLE_MIN_SALARY] == "") ||
		  (strtolower($paraValues[$std1->JOB_TABLE_MIN_SALARY]) == "any") ) // No minimum salary
		$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MAX_SALARY] .
		     $paraValues[$std1->JOB_TABLE_MAX_SALARY] .
		     $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MAX_SALARY] . ", ";
	     else if ($paraValues[$std1->JOB_TABLE_MAX_SALARY] < 
			$paraValues[$std1->JOB_TABLE_MIN_SALARY]) // Set max salary same as min salary
		$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MIN_SALARY] . 
		     trim($paraValues[$std1->JOB_TABLE_MIN_SALARY]) .
		     $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MIN_SALARY] . ", ";
	     else
		$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MAX_SALARY] .
		     $paraValues[$std1->JOB_TABLE_MAX_SALARY] .
		     $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MAX_SALARY] . ", ";
	} else { 
		if ( ($paraValues[$std1->JOB_TABLE_MIN_SALARY] == "") ||
		     (strtolower($paraValues[$std1->JOB_TABLE_MIN_SALARY]) == "any") )
	     	   $insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MAX_SALARY] . 0 .
		        $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MAX_SALARY] . ", ";
		else // otherwise, set max salary same as min salary
	     	   $insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MAX_SALARY] . 
		  	trim($paraValues[$std1->JOB_TABLE_MIN_SALARY]) .
		        $std1->jobTableColumnQuotes[$std1->JOB_TABLE_MAX_SALARY] . ", ";
	}

	$paraValues[$std1->JOB_TABLE_COMPANY_NAME] = trim($ibforums->input['companyName']);
	if ( ($paraValues[$std1->JOB_TABLE_COMPANY_NAME] == "") ||
	     (strtolower($paraValues[$std1->JOB_TABLE_COMPANY_NAME]) == "any") )
	{
	    $parsing_err .= "companyName missing.\n";
	    $numberOfMissingFields++;
	    $companyNameMissing = 1;
	} 
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_COMPANY_NAME] .
	  $paraValues[$std1->JOB_TABLE_COMPANY_NAME] .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_COMPANY_NAME] . ", ";

	$paraValues[$std1->JOB_TABLE_START_DATE] = trim($ibforums->input['startDate']);
	if ( ($paraValues[$std1->JOB_TABLE_START_DATE] == "" ) ||
	     (strtolower($paraValues[$std1->JOB_TABLE_START_DATE]) == "any") )
	{
	    $startDateMissing = 1;
	    $parsing_err .= "startDate missing.\n";
	    $numberOfMissingFields++;
	} else
	{
	    $mon = strtok($paraValues[$std1->JOB_TABLE_START_DATE],'-');
	    $day = strtok('-');
	    $year = strtok('-');
	    if ( checkdate($mon, $day, $year) )
	    {
	       $days = mktime ($mon, $day, $year );
	       $paraValues[$std1->JOB_TABLE_START_DATE] = $days;
	    }
	    else {
	       $numberOfMissingFields++;
	       $parsing_err .= "startDate format is invalid.\n";
	    }
	} 
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_START_DATE] .
	  trim($paraValues[$std1->JOB_TABLE_START_DATE]) .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_START_DATE] . ", ";

	$paraValues[$std1->JOB_TABLE_REFERENCE_NUM] = trim($ibforums->input['referenceNum']);
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_REFERENCE_NUM] .
	  trim($paraValues[$std1->JOB_TABLE_REFERENCE_NUM]) .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_REFERENCE_NUM] . ", ";

	$paraValues[$std1->JOB_TABLE_CONTACT_PERSON] = trim($ibforums->input['contact']);
	if ( ($paraValues[$std1->JOB_TABLE_CONTACT_PERSON] == "" ) ||
	     (strtolower($paraValues[$std1->JOB_TABLE_CONTACT_PERSON]) == "any") )
	{
	    $parsing_err .= "contact missing.\n";
	    $numberOfMissingFields++;
	} 
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_CONTACT_PERSON] .
	  trim($paraValues[$std1->JOB_TABLE_CONTACT_PERSON]) .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_CONTACT_PERSON] . ", ";

	$paraValues[$std1->JOB_TABLE_DESCRIPTION] = trim($ibforums->input['description']);
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_DESCRIPTION] .
	  trim($paraValues[$std1->JOB_TABLE_DESCRIPTION]) .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_DESCRIPTION] . ", ";

	$paraValues[$std1->JOB_TABLE_QUALIFICATION] = trim($ibforums->input['qualification']);
	$insertJobStmt .= $std1->jobTableColumnQuotes[$std1->JOB_TABLE_QUALIFICATION] .
	  trim($paraValues[$std1->JOB_TABLE_QUALIFICATION]) .
	  $std1->jobTableColumnQuotes[$std1->JOB_TABLE_QUALIFICATION] . ")";

	if (($regionMissing == 1) && ($stateMissing == 1) && ($cityMissing == 1)) {
		  $parsing_err .= "Error: at least one of region, state, city names should be given.\n";
	    $numberOfMissingFields++;
	}


	if ($numberOfMissingFields > 0)
		  $parsing_err .= "Error: total " . $numberOfMissingFields .
			 " fields missing.\n";

	$parsing_err .= "The partially formed SQL stmt is:" . $insertJobStmt;

	$this->output .= $parsing_err;
	if ( $numberOfMissingFields > 0 )
		return "Parsing error!";
	else 
		return $insertJobStmt;
    } // End of buildInsertJobStmt()


    function doJobQuery ($sqlStmt)
    { 
	global $ibforums, $DB, $std, $std1, $print, $skin_universal;


	$DB->query($sqlStmt);
	$one_row = $DB->fetch_row();

	$j = 0;
	$myResultsKey = 0;
	$myResults = array();

	while ( !empty($one_row) ) {
		$companyIndexNew = $std1->jobTableColumnNames[$std1->JOB_TABLE_COMPANY_NAME];
		if($this->isCompanySearch && strcmp(strtolower($one_row[$companyIndexNew]), strtolower($this->askedCompany)) == 0){
			$specialityIndex = $std1->jobTableColumnNames[$std1->JOB_TABLE_SPECIALIZATION];
			$this->companySpeciality[$one_row[$specialityIndex]] = true;
					}

		$myResults[$myResultsKey++] = $one_row;	    
	     $one_row = $DB->fetch_row();
	}

	foreach ($myResults as $rowResult) {
	    $tmpRating = $this->computeAJobRating($rowResult);
	    if ($tmpRating > 0) {
		  $this->resultRows[$j] = $rowResult;
		  $this->rating[$j++] = $tmpRating;
	    }
	}
	
	$this->numberOfMatchedRows = $j;

	$this->output .= "<pre>End of doJobQuery </pre>";

	$this->sortResultRows();

	return $this->output;
    }  // End of doJobQuery 

    function doInsert ( $insertStmt )
    { 
	global $ibforums, $DB, $std, $std1, $print, $skin_universal;

	$DB->query($insertStmt);
    }  // End of doInsert

    function printHeaderWithBodyTag ($bodyTag)
    {    	
	global $ibforums, $DB, $std, $std1, $print, $skin_universal;
	$this->output .= "<html>";
	$this->output .= $bodyTag;
    }

    function printHeader ()
    {    	
	global $ibforums, $DB, $std, $std1, $print, $skin_universal;

	$this->output .= "<html>";
	$this->output .= "<BODY BGCOLOR=\"#FFFFFF\">";
    }

    function printJobQueryResultBody()
    {
	global $ibforums, $DB, $std, $std1, $print, $skin_universal;

	$return_html =  "<center>\n";
	$return_html .= "<h3>Job Search Results</h3> (total " . $this->numberOfMatchedRows . " matches)\n";
	$return_html .= "<table >\n";
	$return_html .= "<th>Rating";
	$return_html .= "<th>Job Type";
	$return_html .= "<th>Job Title";
	$return_html .= "<th>Specialty";
	$return_html .= "<th>Location";
	$return_html .= "<th>Company";
	$return_html .= "<th>Salary Range\n";

	for ( $i = 0; $i < $this->numberOfMatchedRows; $i++)
	{
	    
	    $k = $this->originalIndex[$i];
	    $return_html .= "<tr bgcolor=#ffffcc>";
	    $return_html .= "   <td class='row4'>" . $this->rating[$i]."</td>\n";
	    $return_html .= "   <td class='row4'>" . $this->resultRows[$k]['job_type'] . "</td>\n";
	    $return_html .= "   <td class='row4'><a href=\"{$ibforums->base_url}/getJob.php?job_id=" .
		    $this->resultRows[$k]['job_id'] . "\"" . "target=display>" .
			     $this->resultRows[$k]['job_title'] . "</a> </td>\n";
	    $return_html .= "   <td class='row4'>" . $this->resultRows[$k]['specialization'] . "</td>\n";
	    if ( ! (is_null($this->resultRows[$k]['location'])  ||
		   ($this->resultRows[$k]['location'] == "")    ||
		   (strtolower($this->resultRows[$k]['location']) == "any") ) )
	    {
		    $return_html .= "   <td class='row4'>" . $this->resultRows[$k]['location'];
		    if ( ! (is_null($this->resultRows[$k]['state_name']) ||
			   ($this->resultRows[$k]['state_name']=="") ||
			   (strtolower($this->resultRows[$k]['state_name']) == "any") ) )
		    {
		       $return_html .= " (" . $this->resultRows[$k]['state_name']; 
		       if ( ! (is_null($this->resultRows[$k]['region_name']) ||
			      ($this->resultRows[$k]['region_name']=="") ||
			      (strtolower($this->resultRows[$k]['region_name']) == "any") ) )
			  $return_html .= ", " . $this->resultRows[$k]['region_name'];
		       $return_html .= ")";
		    }
		    else if ( ! (is_null($this->resultRows[$k]['region_name']) ||
		    		($this->resultRows[$k]['region_name']) == ""   ||
		    		(strtolower($this->resultRows[$k]['region_name']) == "any" ) ) )
		       $return_html .= " (" . $this->resultRows[$k]['region_name'] . ")";
		    $return_html .= "</td>\n";
	    } else if ( ! (is_null($this->resultRows[$k]['state_name']) ||
		   ($this->resultRows[$k]['state_name']=="") ||
		   (strtolower($this->resultRows[$k]['state_name']) == "any") ) )
	    {
		    $return_html .= "<td class='row4'>" . $this->resultRows[$k]['state_name'];
		    if ( ! (is_null($this->resultRows[$k]['region_name']) ||
		    		($this->resultRows[$k]['region_name']) == ""   ||
		    		(strtolower($this->resultRows[$k]['region_name']) == "any" ) ) )
		       $return_html .= " (" . $this->resultRows[$k]['region_name'] . ")</td>\n";
		    else
		       $return_html .= "</td>\n";
	    } else
		    $return_html .= "<td class='row4'>" . $this->resultRows[$k]['region_name']."</td>\n"; 

	    $return_html .= "<td class='row4'>" . $this->resultRows[$k]['company_name']."</td>\n";
	    $return_html .= "<td class='row4'>" . $this->resultRows[$k]['min_salary'] . "-" . $this->resultRows[$k]['max_salary']."</td>\n";
	}
	$return_html .= "</tr>";
	$return_html .= "</table></center>";

	$this->output .= $return_html;
	return $return_html;

} // End of printJobQueryResultBody()


    function printTailer ()
    {
        $this->output .= "<center>";
        $this->output .= "<TABLE CELLSPACING=\"0\" CELLPADDING=\"3\" BORDER=\"0\">";
        $this->output .= "<tr>";
        $this->output .= "	<td> <A href=\"http://newfirebird.cs.txstate.edu/~juz5/cs5334-php/index.php?act=Ematch&CODE=00\">Home |</A> </td>" ;
        $this->output .= "	<td> <A href=\"http://newfirebird.cs.txstate.edu/~juz5/cs5334-php/index.php?act=Ematch&CODE=10\">Job Search | </a> </td>";
        $this->output .= "	<td> <A href=\"http://newfirebird.cs.txstate.edu/~juz5/cs5334-php/index.php?act=Ematch&CODE=20\">Employers |</A> </td>";
        $this->output .= "	<td> <A href=\"http://newfirebird.cs.txstate.edu/~juz5/cs5334-php/index.php?act=Ematch&CODE=30\">Members </A> </td>";
        $this->output .= "</tr>";
        $this->output .= "</table>";
        $this->output .= "</center>";

	$this->output .= "</body>";
        $this->output .= "</html>";

	return $this->output;
    }

    function printTailerWithoutReference ()
    {

	$return_html = "</body>";
        $return_html .= "</html>";

	return $return_html;
    }

    // Sort the list of output tuples in ascending order of their ratings.
    // For efficiency consideration, sorting is done in place -- only the
    // ratings array is rearranged and the order of elements of result_array
    // is unchanged. The array original_index keeps the index of the final
    // sorted order. That is, if original_index[$i] == k, then the ith
    // output tuple is in the kth position of result_array.
    ///
    function sortResultRows()
    {

	//out.println( "In sortResultRows, total_matched_rows=" + numberOfMatchedRows);
	//for (i=0; $i < numberOfMatchedRows; $i++)
	//	out.println(i + ":" + rating[$i]);

	for ( $i = 0; $i < $this->numberOfMatchedRows; $i++) {

	    //out.println( "Row " + i + "Rating = " + rating[$i]);

	    $maxIndex = $i; 
	    for ( $j = $i+1; $j < $this->numberOfMatchedRows; $j++)
	    {

		if ( $this->rating[$j] > $this->rating[$maxIndex])
		{
			$maxIndex = $j;
		}
	    }
		    
	    //out.println(maxIndex + "===" + i + " with " + rating[maxIndex] + "===" + rating[$i]);

	    $k = $this->originalIndex[$maxIndex];
	    $this->originalIndex[$maxIndex] = $this->originalIndex[$i];
	    $this->originalIndex[$i] = $k;

	    $k = $this->rating[$maxIndex];
	    $this->rating[$maxIndex] = $this->rating[$i];
	    $this->rating[$i] = $k;
	}

    } // End of sortResultRows

    function computeAJobRating ($curRow)
    {    	
	global $std1;

	$tmpRating = 100;

	for ($i = 0; $i < $std1->COLUMN_NUMBER_IN_JOB_TABLE; $i++)
	{
	    if (( !is_null($this->paraValues[$i])) && ( strtolower($this->paraValues[$i]) != "any") &&
					   ( strtolower($this->paraValues[$i]) != "") )
	    {
	       /* Search by region */
	       if (( $i == $std1->JOB_TABLE_REGION_NAME ) &&
			       ( $this->locationSearchType == $std1->SEARCH_BY_REGION ))
	       {
		       $tmpRating -= $this->computeRegionRating($curRow);
	       } /* Search by state */
	       else if (( $i == $std1->JOB_TABLE_STATE_NAME ) &&
			       ($this->locationSearchType == $std1->SEARCH_BY_STATE))
	       {
		       $tmpRating -= $this->computeStateRating($this->askedLocationIndex, $curRow);
	       } /* Search by city */
	       else if (( $i == $std1->JOB_TABLE_LOCATION) &&
			       ($this->locationSearchType == $std1->SEARCH_BY_CITY))
	       {
		       $tmpRating -= $this->computeCityRating($this->askedLocationIndex, $curRow);
	       } // Type
		   else if (( $i == $std1->JOB_TABLE_JOB_TYPE) && ($this->isTypeSearch == 1) && ($this->isKeywordSearch == 0))
	       {
		       $tmpRating -= $this->computeJobTypeRating($this->askedType, $curRow);
	       }/* Search by Title, ignoring the check if keyword based search is enabled*/
	       else if (( $i == $std1->JOB_TABLE_JOB_TITLE) && ($this->isTitleSearch == 1) && ($this->isKeywordSearch == 0))
	       {
		       $tmpRating -= $this->computeJobTitleRating($this->askedTitle, $curRow);
	       }/* Search by specialization, ignoring the check if keyword based search is enabled */
	       else if (( $i == $std1->JOB_TABLE_SPECIALIZATION) && ($this->isSpecialitySearch == 1) && ($this->isKeywordSearch == 0))
	       {
		       $tmpRating -= $this->computeSpecialityRating($this->askedSpeciality, $curRow);
	       } // company
		   else if (( $i == $std1->JOB_TABLE_COMPANY_NAME) && ($this->isCompanySearch == 1) && ($this->isKeywordSearch == 0))
	       {
		       $tmpRating -= $this->computeJobCompanyRating($this->askedCompany, $curRow);
	       }/* Search by keywords */
	        else if (( $i == $std1->JOB_TABLE_DESCRIPTION) && ($this->isKeywordSearch == 1))
	       {
		       $tmpRating -= $this->computeKeywordRating($this->askedKeyword, $curRow);
	       } // salary
	       else  if (( $i == $std1->JOB_TABLE_MIN_SALARY) && ( $this->askedMinSal!= 0) &&
					       ( !is_null($curRow['min_salary']) ) )
	       {
		       $sal = $curRow['min_salary'];
		       //out.println("Check salary for row " + i + " with sal = " + sal);
		       $tmpRating -= $this->computeSalRating($sal);
	       }

	       // un-qualified job entry
	       if ($tmpRating <= 0)
		       break;
	    }
	}

	return $tmpRating;
    }	// End of computeAJobRating()


/*
    *  Based on the Type information a user has requested, calculate the rating.
    *  The job table's oneRow argument has a tuple that meets the minimum criteria.
	*  
	*  A keyword match results in a deduction of 20 points, whereas an exact match results in a deduction of 0 points.
    *  Value returned: an integer between 0 and 100. 0 means matching is perfect,
    *  100 means no matching at all.  
	   The method computeJobTypeRating() will deduct the returned value from its final rating.
    */

    function computeJobTypeRating($keyword,$curRow)
    {    
    	global $std1;

		$deduction = 0;

		//Finding the title index name with help of constants declared in ematchConstStruct.php
		$typeIndex = $std1->jobTableColumnNames[$std1->JOB_TABLE_JOB_TYPE];
		$inputKeywordNoSpclChar = preg_replace('/[^A-Za-z0-9\-]/', '', $keyword);
		$existingJobTypeNoSpclChar = preg_replace('/[^A-Za-z0-9\-]/', '', $curRow[$typeIndex]);

		if(strcmp(strtolower($inputKeywordNoSpclChar), strtolower($existingJobTypeNoSpclChar)) == 0){
			$deduction = 0;
		}else  {
		    $deduction = 20; 
		}

		return $deduction;
    }


    /*
    *  Compute the rating based on the Title information requested by a user.
    *  The parameter oneRow contains a tuple retrieved from the job table that
    *  satisfies basic requirement.
	*  
	*	If title is a exact match then 0 points are deducted and if it matches a
	*	keyword then 10 points are deducted.
	*
    *  Value returned:  an integer between 0 and 100. 0 means perfect match,
    *		100 means no matching at all.  The method computeJobTitleRating()
    *		will deduct the returned value from its final rating.
    */

    function computeJobTitleRating($keyword,$curRow)
    {    
    	global $std1;

		$deduction = 0;

		//Finding the title index name with help of constants declared in ematchConstStruct.php
		$titleIndex = $std1->jobTableColumnNames[$std1->JOB_TABLE_JOB_TITLE];

		if(strcmp(strtolower($curRow[$titleIndex]), strtolower($keyword)) == 0){
			$deduction = 0;
		}else if (strpos(strtolower($curRow[$titleIndex]), strtolower($keyword)) !== false) {
		   $deduction = 10; 
		}else{
			 $deduction = 100; 
		}

		return $deduction;
    }

    /*
    *  Compute the rating based on the specialization information requested by a user.
    *  The parameter oneRow contains a tuple retrieved from the job table that
    *  satisfies basic requirement.
	*  
	*	If speciality is a exact match then 0 points are deducted and if it matches a
	*	keyword then 30 points are deducted.
	*
    *  Value returned:  an integer between 0 and 100. 0 means perfect match,
    *		100 means no matching at all.  The method computeSpecialityRating()
    *		will deduct the returned value from its final rating.
    */

    function computeSpecialityRating($keyword,$curRow)
    {    
    	global $std1;

		$deduction = 0;

		//Finding the specialization index name with help of constants declared in ematchConstStruct.php
		$specialityIndex = $std1->jobTableColumnNames[$std1->JOB_TABLE_SPECIALIZATION];

		$specialtyKeywords = explode(' ', $keyword);

		//In this we are checking if it's a exact match or atleast matches the first keyword of given specialization
		if(strcmp($curRow[$specialityIndex], $keyword) == 0){
			$deduction = 0;
		}else if (strpos(strtolower($curRow[$specialityIndex]), strtolower($specialtyKeywords[0])) !== false) {
			//echo strtolower($curRow[$specialityIndex])."--".strtolower($specialtyKeywords[0])."<br>";
		   $deduction = 30; 
		}else{
			 $deduction = 100; 
		}

		return $deduction;
    }

	/* Using the company data that a user has requested, calculate the rating.
    * OneRow has a tuple that fulfills the fundamental condition that was taken from the job table. 
	*	If title is a exact match then 0 points are deducted and if it matches a
	*	keyword then 10 points are deducted.
	*
    *  Value returned:  an integer between 0 and 100. 0 means the whole word is matching,
    *		100 means no matching at all.  The method computeJobCompanyRating()
    *		will deduct the returned value from its final rating.
    */

    function computeJobCompanyRating($keyword, $curRow)
    {    
    	global $std1;

		$deduction = 100;
		$companyIndex = $std1->jobTableColumnNames[$std1->JOB_TABLE_COMPANY_NAME];
		$specialityIndex = $std1->jobTableColumnNames[$std1->JOB_TABLE_SPECIALIZATION];

		if(strcmp(strtolower($curRow[$companyIndex]), strtolower($keyword)) == 0){
			$deduction = 0;
		} else {
			foreach (array_keys($this->companySpeciality) as $companySpecialityKey) {
				$specialtyKeywords = explode(' ', $companySpecialityKey);
				if (strpos(strtolower($curRow[$specialityIndex]), strtolower($specialtyKeywords[0])) !== false) {
					$deduction = 20; 
				}
			}  
		}

		return $deduction;
    }
    /*  This method computes keywords based on rating.
    *  The specified keywords are broken up into words, and each word is compared to the title, description, and specialization of each record, 
	*   as well as the number of matches.
	*   If the count exceeds 2, no points are deducted; if it exceeds 1, 20 points are deducted; 
	*   otherwise, if the count is less than 1, 100 points are deducted.
	*	
	*	When this keyword feature is activated by user then Title and Specialization based 
	*	search are ignored.
	*
    *  Value returned:  an integer between 0 and 100. 0 means perfect match,
    *		100 means no matching at all.  The method computeKeywordRating()
    *		will deduct the returned value from its final rating.
    */

    function computeKeywordRating($keywordSentence,$curRow) {    
    	global $std1;
		$deduction = 0;
		$matchCount = 0;
		$keywords = explode(' ',strtolower($keywordSentence));
		//Finding the index names with help of constants declared in ematchConstStruct.php
		$descriptionIndex = $std1->jobTableColumnNames[$std1->JOB_TABLE_DESCRIPTION];
		$specialtyIndex = $std1->jobTableColumnNames[$std1->JOB_TABLE_SPECIALIZATION];
		$titleIndex = $std1->jobTableColumnNames[$std1->JOB_TABLE_JOB_TITLE];
		foreach($keywords as $keyword){
			if(strpos(strtolower($curRow[$descriptionIndex]), $keyword) !== false || 
				strpos(strtolower($curRow[$specialtyIndex]), $keyword) !== false || 
				strpos(strtolower($curRow[$titleIndex]), $keyword) !== false
			){
				$matchCount++;				
			}
		}
		if($matchCount >= 2){
			$deduction = 0;
		}else if($matchCount >= 1){
			$deduction = 20;
		}else if($matchCount < 1){
			$deduction = 100;
		}
		return $deduction;
    }

   /*
    *  Compute the rating based on the region information requested by a user.
    *  The parameter oneRow contains a tuple retrieved from the job table that
    *		satisfies basic requirement.
    *  Value returned:  an integer between 0 and 100. 0 means perfect match,
    *		100 means no matching at all.  The method computeAJobRating()
    *		will deduct the returned value from its final rating.
    */
    function computeRegionRating ( $oneRow )
    {    	
	global $std1;

	$deduction    = 100;
	$tmpDeduction = 100;

	$regionIndex = -1;
	$stateIndex  = -1;
	$cityIndex   = -1;
	$locationIndex=-1;

	//$this->output .= "<br>\nIn computeRegionRating<br>\n";
	// Job search
	if ( $this->searchType == $std1->JOB_SEARCH ) {
		if ( !is_null($oneRow['region_name']) &&
		     ($oneRow['region_name'] != "") )
		{
			$regionName  = $oneRow['region_name']; 
			$regionIndex = $this->findRegionIndex( strtolower($regionName) );
		}
		if ( !is_null($oneRow['state_name']) &&
		     ($oneRow['state_name'] != "") )
		{
			$stateName  = $oneRow['state_name']; 
			$stateIndex = $this->findStateIndex( strtolower($stateName) );
		}
		if ( !is_null($oneRow['location']) && ($oneRow['location'] != "") )
		{
			$cityName  = $oneRow['location']; 
			$cityIndex = $this->findCityIndex( strtolower($cityName) );
		}

		if ( $regionIndex != -1 )
		{
			if ($std1->regionCompatibility[$regionIndex][$this->askedLocationIndex] == 1)
				$deduction = 0;
			else if ($std1->regionCompatibility[$regionIndex][$this->askedLocationIndex] == 2)
			{
			    $deduction = 70;
			    if ( $stateIndex != -1 )
			       $tmpDeduction = $this->checkIfStateInRegion($stateIndex, $this->askedLocationIndex);
			    else if ( $cityIndex != -1 )
			       $tmpDeduction = $this->checkIfCityInRegion($cityIndex, $this->askedLocationIndex);
			    $deduction = min($deduction, $tmpDeduction);
			}
		} else if ( $stateIndex != -1 )
		{
			$deduction = $this->checkIfStateInRegion($stateIndex, $this->askedLocationIndex);
		} else if ( $cityIndex != -1 )
		{
			$deduction = $this->checkIfCityInRegion( $cityIndex, $this->askedLocationIndex);
		}
	// Member search
	} else if ( $this->searchType == $std1->MEMBER_SEARCH )
	{
		//Detailes to be filled
	// C_G search
	} else if ( $this->searchType == $std1->C_G_SEARCH ) {
		if ( !is_null($oneRow[$std1->C_G_TABLE_DESIRED_JOB_LOCATION+1]) ) 
		{
			$locationName = $oneRow[$std1->C_G_TABLE_DESIRED_JOB_LOCATION+1]; 
			$locationIndex = findCityIndex($locationName);
			// Details to be filled
		}
	} else {
		$this->output .= "Unknown Search Type!";
		$deduction = 100;
	}

	return $deduction;

    } // End of computeRegionRating

    function checkIfStateInRegion ( $stateIndex, $regionIndex )
    {
	 $deduction = 100;

	if ( $this->theStateIsInTheRegion( $stateIndex, $regionIndex ) )
	{ 
		$deduction = 0;
	}
	return $deduction;
    }

    function checkIfCityInState ( $cityIndex, $stateIndex )
    {
	$deduction = 100;

	if ( $this->theCityIsInTheState( $cityIndex, $stateIndex ) )
	{ 
		$deduction = 0;
	}
	return $deduction;
    }

    function checkIfCityInRegion ( $cityIndex, $regionIndex )
    {
	global $std1;

	$deduction = 100;

	$r = $this->usStateInRegionBeginningIndex[$regionIndex];

	for ( $i = $r; $std1->usStatesInRegions[$i] != $std1->END_SUB_LIST; $i++)
	{
		$st = $std1->usStatesInRegions[$i];
		$s  = $this->usCityInStateBeginningIndex[$st];
		$l  = $std1->usCitiesInStatesNumber[$st];
		for ( $j = 0; $j < $l; $j++) {
			if ( $this->theCityIsInTheState($cityIndex, $st ) )
			{ 
				$deduction = 0;
			}
		}
	}
	return $deduction;
    }

   /*
    *  Compute the rating based on the state information specified by a user.
    *  The parameter askedStateIndex is the index of the state the search is looking for.
    *  The parameter oneRow contains a tuple retrieved from the job table that
    *	  satisfies basic requirement. The one_row_ind is the corresponding
    *	  indicate array.
    *  Value returned:  an integer between 0 and 100. 0 means perfect match,
    *		100 means no matching at all.  The method computeAJobRating()
    *		will deduct the returned value from its final rating.
    */
    function computeStateRating ( $askedStateIndex,  $oneRow )
    {
	global $std1;

	$deduction  = 100;
	$tmpDeduction=100;

	$regionIndex = -1;
	$stateIndex  = -1;
	$cityIndex   = -1;
	$locationIndex=-1;

	$this->output .= "<br>Calling computeStateRating";
	if ( $this->searchType == $std1->JOB_SEARCH )
	{
		if ( !is_null( $oneRow['region_name'] ) &&
		     ($oneRow['region_name'] != "") )
		{
			$regionName  = $oneRow['region_name']; 
			$regionIndex = $this->findRegionIndex( strtolower($regionName) );
		}
		if ( !is_null($oneRow['state_name']) &&
		     ($oneRow['state_name'] != "") )
		{
			$stateName  = $oneRow['state_name']; 
			$stateIndex = $this->findStateIndex( strtolower($stateName) );
		}
		if ( !is_null($oneRow['location']) && ($oneRow['location'] != "") )
		{
			$cityName  = $oneRow['location']; 
			$cityIndex = $this->findCityIndex( strtolower($cityName) );
		}

		$this->output .= "regionindex=".$regionIndex.",stateIndex=".$stateIndex.
				"askedLocationIndex=".$this->askedLocationIndex."<br>\n";
		if ( $stateIndex != -1 )
		{
			$deduction = $this->findNeighborStateRating($stateIndex,
								$askedStateIndex);
		} else if ( $cityIndex != -1 )
		{ // The row does not have a valid state, but has a valid city

			$deduction = $this->findCityInStateRating($cityIndex,$askedStateIndex);

		} else if ($regionIndex != -1) { // The row does not have a valid state or city,
						//    but does have a valid region name
			// by default, the rating deduction is 40% for this case
			$deduction = 40;
			$tmpDeduction = $this->checkIfStateInRegion($askedStateIndex,
				 		$regionIndex);
			$deduction = max($deduction, $tmpDeduction);
		}
	// Member search
	} else if ($searchType == $std1->MEMBER_SEARCH)
	{
		//
	} else {
		$deduction = 100;
	}

	return $deduction;
    } // End of computeStateIndex


    function findNeighborStateRating ($stateIndex, $askedStateIndex)
    {
	global $std1;

	$this->output.= "Asked state index is " . $askedStateIndex;
	$this->output.= ", asked state name is " . $std1->usStates[$askedStateIndex]."<br>\n";

	$deduction = 100;
	$j = $this->neighborStateBeginningIndex[$askedStateIndex];
	$k = $std1->usNeighborStateNumber[$askedStateIndex];

	$this->output.= "beginning state index = " . $j . " and # of neighbors is ".$k."<br>\n";
	for ($i = 0; $i < 2*$k; $i=$i+2)
	{
	     $this->output.="State ".$std1->usStates[$std1->usNeighborStateRatingList[$i+$j]]." with index ".$std1->usNeighborStateRatingList[$i+$j]."<br>";
	     if ($std1->usNeighborStateRatingList[$i+$j] == $stateIndex) {
		     $deduction = $std1->usNeighborStateRatingList[$j+$i+1];
		     break;
	     }
	}
	return $deduction;
    }


    function findCityInStateRating ($cityIndex, $askedStateIndex)
    {
	global $std1;

	$j = $this->neighborStateBeginningIndex[$askedStateIndex];
	$k = $std1->usNeighborStateNumber[$askedStateIndex];

	$deduction = 100;

	for ($i = 0; $i < 2*$k; $i+=2)
	{

	    $stateIndex = $std1->usNeighborStateRatingList[$j+$i];
	    if ($this->theCityIsInTheState($cityIndex, $stateIndex))
	    { 
		    $deduction = $std1->usNeighborStateRatingList[$j+$i+1];
		    break;
	    }
	}
	
	return $deduction;
    }

   /*
    *  Compute the rating based on the city information specified by a user.
    *  The parameter oneRow contains a tuple retrieved from the job table that
    *		satisfies basic requirement. The one_row_ind is the corresponding
    *		indicate array
    *  Value returned:  an integer between 0 and 100. 0 means perfect match,
    *		100 means no matching at all.  The method computeAJobRating()
    *		will deduct the returned value from its final rating.
    */
    function computeCityRating ( $askedCityIndex, $oneRow )
    {    	
	global $std1;

	$deduction  = 100;
	$tmpDeduction=100;

	$regionIndex = -1;
	$stateIndex  = -1;
	$cityIndex   = -1;
	$locationIndex=-1;

	$this->output.= "askedCityIndex=" . $askedCityIndex."==";
	$this->output.= "cityName=" . $oneRow['location']."<br>\n";

	if ( $this->searchType == $std1->JOB_SEARCH )
	{
		if ( !is_null( $oneRow['region_name'] ) &&
		     ($oneRow['region_name'] != "") )
		{
			$regionName  = $oneRow['region_name']; 
			$regionIndex = $this->findRegionIndex( strtolower($regionName) );
		}
		if ( !is_null($oneRow['state_name']) &&
		     ($oneRow['state_name'] != "") )
		{
			$stateName  = $oneRow['state_name']; 
			$stateIndex = $this->findStateIndex( strtolower($stateName) );
		}
		if ( !is_null($oneRow['location']) && ($oneRow['location'] != "") )
		{
			$cityName  = $oneRow['location']; 
			$cityIndex = $this->findCityIndex( strtolower($cityName) );
		}

		if ($cityIndex != -1)

			$deduction = $this->findNeighborCityRating($cityIndex,
						$askedCityIndex);
		// The current job does not have city info. But it does have state info.
		//   Check to see if the asked city is in the same state of this job,
		//     or if it is in one of the current job's neighbor states.
		else if ($stateIndex != -1 )
		{
		    $askedStateIndex = $this->findTheStateContainingTheCity($askedCityIndex);
		    if ( $stateIndex == $askedStateIndex )
			$deduction = 35;
		    else
			$deduction = 10+$this->findNeighborStateRating($stateIndex,$askedStateIndex);
		}
		else if ($regionIndex != -1 )
		{
		    $askedStateIndex = $this->findTheStateContainingTheCity($askedCityIndex);
		    if ($this->checkIfStateInRegion($askedStateIndex,$regionIndex) == 0)
			$deduction = 50;
		}
	} else
	{
		print "<pre>Error: unknown search type.</pre>";
		return -1;
	}

	return $deduction;
    } // End of computeCityRating()

    function findNeighborCityRating ($cityIndex, $askedCityIndex)
    {
	global $std1;

	$j = $this->neighborCityBeginningIndex[$askedCityIndex];

	$k = $std1->usNeighborCityNumber[$askedCityIndex];


	$deduction = 100;
	for ($i = 0; $i < 2*$k; $i=$i+2) {
		$this->output .= "Check ".$std1->usCities[$std1->usNeighborCityRatingList[$j+$i]]."<br>\n";
		if ($std1->usNeighborCityRatingList[$j+$i] == $cityIndex)
		{
		    $deduction = $std1->usNeighborCityRatingList[$j+$i+1];
		    break;
		}
	}

	return $deduction;

    } // End of findNeighborCityRating


    function computeSalRating ($sal)
    {    	
	global $std1;

	$deduction = 0;
	$percent   = 0;
	$calDeduction=false;

	$this->output .= "askedMinSal=".$this->askedMinSal.", and sal=".$sal;
	if ( $this->searchType == $std1->JOB_SEARCH ) {
		$percent = (((float)($this->askedMinSal - $sal)) / ((float)($this->askedMinSal + 1)));
		$calDeduction = $this->askedMinSal > $sal;
	} else if ( $this->searchType == $std1->MEMBER_SEARCH ) {
		$percent = (((float)($sal - $this->askedMaxSal)) / ((float)($this->askedMaxSal + 1)));
		$calDeduction = $sal > $this->askedMaxSal;
	}

	if ( $calDeduction )
	{
										
		if ( $percent <= 0.05 )		// salary is 95% or above
			$deduction = 5;
		else if ( $percent <= 0.10 )	// salary is between 90% and 95%
			$deduction = 10;
		else if ($percent <= 0.15)	// salary is between 85% and 90%
			$deduction = 15;
		else if ($percent <= 0.20)	// salary is between 80% and 85%
			$deduction = 20;
		else if ($percent <= 0.25)	// salary is between 75% and 80%
			$deduction = 25;
		else if ( $percent <= 0.30)	// salary is between 70% and 75% 
			$deduction = 30;
		else if ( $percent <= 0.35)	// salary is between 65% and 70%
			$deduction = 35;
		else if ( $percent <= 0.40)	// salary is between 60% and 65%
			$deduction = 40;
		else if ( $percent <= 0.45)	// salary is between 55% and 60%
			$deduction = 45;
		else if ( $percent <= 0.50)	// salary is between 50% and 55%
			$deduction = 50;
		else if ( $percent <= 0.55)	// salary is between 45% and 50%
			$deduction = 55;
		else if ( $percent <= 0.60)	// salary is between 40% and 45%
			$deduction = 60;
		else if ( $percent <= 0.65)	// salary is between 35% and 40%
			$deduction = 65;
		else $deduction = 70;		// salary is at most 35%
	}
	return $deduction;
    }  // End of computeSalRating()


    function computeYearOfExpRating($yearOfExp)
    {    	
	$deduction = 0;
	$givenYearOfExp = $yearOfExp;

	$diff = $askedYearOfExp - $givenYearOfExp;

	print "askedYearOfExp=". $askedYearOfExp . ", givenYearOfExp=" . $givenYearOfExp;
	if ($diff > 0) {
		if ($diff <= 1)
			$deduction = 10;
		else if ($diff <= 2)
			$deduction = 20;
		else if ($diff <= 3)
			$deduction = 30;
		else if ($diff <= 4)
			$deduction = 40;
		else
			$deduction = 50;
	}
	return $deduction;
    }


    function computeDegreeRating( $degree )
    {    	
	global $std1;

	$deduction = 0;
	$givenDegree = $degree;

	$diff = $askedDegree - $givenDegree;

	print "askedDegree=". $askedDegree . ", givenDegree=" . $givenDegree;
	if ($diff > 0) {
		if ($diff <= 1)
			$deduction = 5;
		else if ($diff <= 2)
			$deduction = 10;
		else if ($diff <= 3)
			$deduction = 15;
		else if ($diff <= 4)
			$deduction = 20;
		else
			$deduction = 25;
	}
	return $deduction;
    }


    function findRegionIndex( $a_name )
    {
	global $std1;

	for ( $i = 0; $i < $std1->NUMBER_OF_US_REGIONS; $i++)
		if ( $a_name == $std1->usRegions[$i])
			return $i;
	return -1;

    }


    function findStateIndex( $a_name )
    {
	global $std1;

	for ( $i = 0; $i < $std1->NUMBER_OF_US_STATES; $i++)
		if ( $a_name == $std1->usStates[$i])
			return $i;
	return -1;

    }


    function findCityIndex( $a_name )
    {
	global $std1;

	for ( $i = 0; $i < $std1->NUMBER_OF_US_CITIES; $i++)
		if ( $a_name == $std1->usCities[$i])
			return $i;
	return -1;

    }


    function theStateIsInTheRegion( $stateIndx, $regionIndx )
    {
	global $std1;

	$k = $this->usStateInRegionBeginningIndex[$regionIndx];
	$j=0;
	for ( $i = 0; $std1->usStatesInRegions[$k+$i] >= 0; $i++) {

		if ($stateIndx == $std1->usStatesInRegions[$k+$i]) {
			return true;
		}
		$j++;
		if ($j > 100)
			break;
	}

	return false;
    }

    
    function theCityIsInTheState( $cityIndx, $stateIndx)
    {
	global $std1;

	$k = $this->usCityInStateBeginningIndex[$stateIndx];
	$limit = $std1->usCitiesInStatesNumber[$stateIndx];

	for ( $i = 0; $i < $limit; $i++)
	{			
	    if ( $cityIndx == $std1->usCitiesInStates[$k+$i] )
		return true;
	}
	return false;
    }
   
    function findTheStateContainingTheCity( $cityIndex )
    {
	global $std1;

     	for ( $i = 0; $i < $std1->NUMBER_OF_US_STATES; $i++ )
	{

		$k = $this->usCityInStateBeginningIndex[$i];
		$limit = $std1->usCitiesInStatesNumber[$i];
		for ( $j = 0;  $j <  $limit;  $j++)
		 	if ( $cityIndex == $std1->usCitiesInStates[$k+$j])
			 	return $i;
	}

	return -1;
    }

    function getANewJobId ()
    {

	global $DB;

	$DB->query ("SELECT MAX(job_id) as m FROM job");

	// Found the number of columns
	if ($one_row = $DB->fetch_row() )
		$maxJobId = $one_row['m'] + 1;
	else $maxJobId = 1;

	return $maxJobId;
    }

    // Test if the given empId or loginId is unique in the employer or member table
    function idIsUnique ( $id, $tableNameIndex )
    {
	global $DB;

	$isUnique = 1;

	// Query the employee names 
	if ( $tableNameIndex == $std1->MEMBER_TABLE_INDEX )
		$queryStmt = "SELECT login_id as id FROM " . $std1->tableNames[$tableNameIndex-1];
	else if ( $tableNameIndex == $std1->EMPLOYER_TABLE_INDEX )
		$queryStmt = "SELECT emp_id as id FROM " . $std1->tableNames[$tableNameIndex-1];
	else
	 	return 0;
	$DB->query ($queryStmt);

	//anEmpId = new char[$std1->MAX_COLUMN_NAME_LENGTH];
	$givenId = strtolower($id);
        $one_row = $DB->fetch_row();

        //while ( !empty($one_row = $DB->fetch_row()) )
        while ( !empty($one_row) )
	{
		$anId = $one_row['id'];
		if ( strtolower($anId) == $givenId ) {
		   $isUnique = 0;
		   break;
		}
	}

	return $isUnique;
    }


    /*-------------------------------------------------------------------------*/
    // Makes incoming info "safe"              
    /*-------------------------------------------------------------------------*/
    
    function parse_incoming()
    {
    	global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_CLIENT_IP, $REQUEST_METHOD, $REMOTE_ADDR, $HTTP_PROXY_USER, $HTTP_X_FORWARDED_FOR;
    	$return = array();
    	
		if( is_array($HTTP_GET_VARS) )
		{
			while( list($k, $v) = each($HTTP_GET_VARS) )
			{
				if( is_array($HTTP_GET_VARS[$k]) )
				{
					while( list($k2, $v2) = each($HTTP_GET_VARS[$k]) )
					{
						$return[$k][ $this->clean_key($k2) ] = $this->clean_value($v2);
					}
				}
				else
				{
					$return[$k] = $this->clean_value($v);
				}
			}
		}
		
		// Overwrite GET data with post data
		
		if( is_array($HTTP_POST_VARS) )
		{
			while( list($k, $v) = each($HTTP_POST_VARS) )
			{
				if ( is_array($HTTP_POST_VARS[$k]) )
				{
					while( list($k2, $v2) = each($HTTP_POST_VARS[$k]) )
					{
						$return[$k][ $this->clean_key($k2) ] = $this->clean_value($v2);
					}
				}
				else
				{
					$return[$k] = $this->clean_value($v);
				}
			}
		}
		
		//----------------------------------------
		// Sort out the accessing IP
		// (Thanks to Cosmos and schickb)
		//----------------------------------------
		
		$addrs = array();
		
		foreach( array_reverse( explode( ',', $HTTP_X_FORWARDED_FOR ) ) as $x_f )
		{
			$x_f = trim($x_f);
			
			if ( preg_match( '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $x_f ) )
			{
				$addrs[] = $x_f;
			}
		}
		
		$addrs[] = $_SERVER['REMOTE_ADDR'];
		$addrs[] = $HTTP_PROXY_USER;
		$addrs[] = $REMOTE_ADDR;
		
		//header("Content-type: text/plain"); print_r($addrs); print $_SERVER['HTTP_X_FORWARDED_FOR']; exit();
		
		$return['IP_ADDRESS'] = $this->select_var( $addrs );
												 
		// Make sure we take a valid IP address
		
		$return['IP_ADDRESS'] = preg_replace( "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", "\\1.\\2.\\3.\\4", $return['IP_ADDRESS'] );
		
		$return['request_method'] = ( $_SERVER['REQUEST_METHOD'] != "" ) ? strtolower($_SERVER['REQUEST_METHOD']) : strtolower($REQUEST_METHOD);
		
		return $return;
	}
	
    /*-------------------------------------------------------------------------*/
    // Key Cleaner - ensures no funny business with form elements             
    /*-------------------------------------------------------------------------*/
    
    function clean_key($key) {
    
    	if ($key == "")
    	{
    		return "";
    	}
    	$key = preg_replace( "/\.\./"           , ""  , $key );
    	$key = preg_replace( "/\_\_(.+?)\_\_/"  , ""  , $key );
    	$key = preg_replace( "/^([\w\.\-\_]+)$/", "$1", $key );
    	return $key;
    }
    
    function clean_value($val)
    {
    	global $ibforums;
    	
    	if ($val == "")
    	{
    		return "";
    	}
    	
    	$val = str_replace( "&#032;", " ", $val );
    	
    	if ( $ibforums->vars['strip_space_chr'] )
    	{
    		$val = str_replace( chr(0xCA), "", $val );  //Remove sneaky spaces
    	}
    	
    	$val = str_replace( "&"            , "&amp;"         , $val );
    	$val = str_replace( "<!--"         , "&#60;&#33;--"  , $val );
    	$val = str_replace( "-->"          , "--&#62;"       , $val );
    	$val = preg_replace( "/<script/i"  , "&#60;script"   , $val );
    	$val = str_replace( ">"            , "&gt;"          , $val );
    	$val = str_replace( "<"            , "&lt;"          , $val );
    	$val = str_replace( "\""           , "&quot;"        , $val );
    	$val = preg_replace( "/\n/"        , "<br>"          , $val ); // Convert literal newlines
    	$val = preg_replace( "/\\\$/"      , "&#036;"        , $val );
    	$val = preg_replace( "/\r/"        , ""              , $val ); // Remove literal carriage returns
    	$val = str_replace( "!"            , "&#33;"         , $val );
    	$val = str_replace( "'"            , "&#39;"         , $val ); // IMPORTANT: It helps to increase sql query safety.
    	
    	// Ensure unicode chars are OK
    	
    	if ( $this->allow_unicode )
		{
			$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );
		}
		
		// Strip slashes if not already done so.
		
    	if ( $this->get_magic_quotes )
    	{
    		$val = stripslashes($val);
    	}
    	
    	// Swop user inputted backslashes
    	
    	$val = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $val ); 
    	
    	return $val;
    }
    

    /*-------------------------------------------------------------------------*/
    // Variable chooser
    /*-------------------------------------------------------------------------*/

    function select_var($array) {

	if ( !is_array($array) ) return -1;

	ksort($array);


	$chosen = -1;  // Ensure that we return zero if nothing else is available

	foreach ($array as $k => $v)
	{
		if (isset($v))
		{
			$chosen = $v;
			break;
		}
	}

	return $chosen;
    }

}

?>
