<?php

class skin_ematch {

function display_front_page($top_part, $bot_part)
{
    global $std, $std1, $ibforums;

return <<<EOF

<HTML>
<HEAD>
<TITLE>Final Project- Job Search</TITLE>
<script language=Javascript>
var title_flag=1;
</script>
</HEAD>
<BODY BGCOLOR="#FFC0CB" LINK="#0088ff" ALINK="#FF0000" VLINK="#CC0000">

{$top_part}
{$bot_part}

</body>
</html>
EOF;
}

function display_jobsearch_html()
{
    global $std, $std1, $ibforums;

return <<<EOF

<html>
<head>
<title>Final Project - Job Search</title>
<script language=Javascript>
var title_flag=1;
</script>
</head>

<body BGCOLOR="#FFC0CB" LINK="#0088ff" ALINK="#FF0000" VLINK="#CC0000">
<center><h2>Job Search</h2></center>
<form action='{$ibforums->base_url}act=Ematch&CODE=11' method='POST'>

<table width="100%" cellpadding="0" border="0" >
<th width="10%" align=left>
	<th align=center>
	<table bgcolor="#FFC0CB">
		<tr>
			<td> Job Type:
				<select size=1 name=jobType>
				<option>Any
				<option>regular
				<option>entry_level
				<option>intern
				<option>co-op
				</select>
				<br>
		<tr>

			<td> Job Title: 
				<select size=1 name=jobTitle>
				<option>Any
				<option>Analyst
				<option>Senior Analyst
				<option>Entry Level Engineer
				<option>Engineer
				<option>Senior Engineer
				<option>Programmer Analyst
				<option>Senior Programmer Analyst
				<option>Staff Engineer
				<option>Senior Staff Engineer
				<option>Member of Technical Staff
				<option>Senior Member of Technical Staff
				<option>Production Operator
				<option>Production Technician
				<option>Supervisor
				<option>Manager
				<option>Project Manager
				<option>Senior Manager
				<option>Director
				<option>Vice President
				<option>Executive Vice President
				<option>COO
				<option>CFO
				<option>CTO
				<option>CEO
				</select>
				<br>
		<tr>
			<td> Specialized field:
			<select size=1 name=specialty>
			<option>Any
			<option>Accounting
			<option>Analog Design
			<option>Client Server Application Development
			<option>Database Administration
			<option>Database Development
			<option>Device Driver Development
			<option>Digital Design
			<option>District Sales
			<option>E-Commerce Development
			<option>Embedded Software Development
			<option>Embedded System
			<option>Engineering
			<option>Equipment
			<option>Field Application
			<option>Field Service
			<option>Java Development
			<option>Logic Design
			<option>Mac Development
			<option>Manufacturing
			<option>Marketing
			<option>MS Access Development
			<option>Multimedia Application Development
			<option>Network Security
			<option>Network Programming
			<option>Operation
			<option>Oracle DBA
			<option>Oracle Development
			<option>Process
			<option>Process Integration
			<option>Product
			<option>Product Development
			<option>Production
			<option>Project Management
			<option>Quality Control
			<option>R&D
			<option>Reliability
			<option>Sales
			<option>Signal Integrity
			<option>Strategic Marketing
			<option>System
			<option>System Administration
			<option>System Design
			<option>System Integration
			<option>System Quality Control
			<option>System Testing
			<option>Technical Support
			<option>UNIX Administration
			<option>UNIX System Programming
			<option>Visual Basic Development
			<option>VLSI Design
			<option>Web Application Development
			<option>Web Development
			<option>Windows Administration
			<option>Windows Development
			<option>Other
			</select>
		<p>
		Please enter the keyword here:
		<input type=text name=field_keyword value="">
		<tr>
			<td> Geophysical location:
			<UL>
			<LI><input type=radio value=region name=locationType checked>
			 By region:<br> 
				<select size=4 name=region>
				<option selected>Any
				<option>Atlantic Coast
				<option>East
				<option>Great Lakes
				<option>Middle
				<option>Middlewest
				<option>Mountain
				<option>New England
				<option>North
				<option>Northeast
				<option>Northwest
				<option>Pacific Coast
				<option>Pacific West
				<option>South
				<option>Southeast
				<option>Southwest
				<option>West
				</select>
			<LI><input type=radio name=locationType value=state>
			 By state: <br>
			<select size=4 name=state>
				<option selected>Any
				<option>Alabama
				<option>Alaska
				<option>Arizona
				<option>Arkansas
				<option>California
				<option>Colorado
				<option>Connecticut
				<option>Delaware
				<option>Florida
				<option>Georgia
				<option>Hawaii
				<option>Idaho
				<option>Illinois
				<option>Indiana
				<option>Iowa
				<option>Kansas
				<option>Kentucky
				<option>Louisiana
				<option>Maine
				<option>Maryland
				<option>Massachusetts
				<option>Michigan
				<option>Minnesota
				<option>Mississippi
				<option>Missouri
				<option>Montana
				<option>Nebraska
				<option>Nevada
				<option>New Hampshire
				<option>New Jersey
				<option>New Mexico
				<option>New York
				<option>North Carolina
				<option>North Dakota
				<option>Ohio
				<option>Oklahoma
				<option>Oregon
				<option>Pennsylvania
				<option>Rhode Island
				<option>South Carolina
				<option>South Dakota
				<option>Tennessee
				<option>Texas
				<option>Utah
				<option>Vermont
				<option>Virginia
				<option>Washington
				<option>WV
				<option>Wisconsin
				<option>Wyoming
				 </select>
			 <LI><input type=radio name=locationType value=city>
			  By City: <br>
				  <select size=4 name=city>
				  <option selected>Any
				  <option>Albuquerque
				  <option>Alexandria
				  <option>Allentown
				  <option>Anchorage
				  <option>Atlanta
				  <option>Augusta
				  <option>Austin
				  <option>Baltimore
				  <option>Boise
				  <option>Boston
				  <option>Buffalo
				  <option>Burlington
				  <option>Charlotte
				  <option>Chicago
				  <option>Cincinnati
				  <option>Cleveland
				  <option>Colorado Springs
				  <option>Columbia
				  <option>Columbus
				  <option>Cupertino
				  <option>Dallas
				  <option>Dayton
				  <option>Denver
				  <option>Des Moines
				  <option>Detroit
				  <option>El Paso
				  <option>Fort Worth
				  <option>Fremont
				  <option>Green Bay
				  <option>Harrisburg
				  <option>Hartford
				  <option>Hillsboro
				  <option>Honolulu
				  <option>Houston
				  <option>Indianapolis
				  <option>Kansas City
				  <option>Las Vegas
				  <option>Lexington
				  <option>Lincoln
				  <option>Little Rock
				  <option>Los Angeles
				  <option>Miami
				  <option>Milpitas
				  <option>Milwaukee
				  <option>Minneapolis
				  <option>Mobile
				  <option>Morristown
				  <option>Mountain View
				  <option>Nashville
				  <option>New Orleans
				  <option>New York
				  <option>Newark
				  <option>Oakland
				  <option>Oklahoma City
				  <option>Omaha
				  <option>Orlando
				  <option>Palo Alto
				  <option>Philadelphia
				  <option>Phoenix
				  <option>Pittsburgh
				  <option>Portland
				  <option>Raleigh
				  <option>Reno
				  <option>Richmond
				  <option>Sacramento
				  <option>Saint Louis
				  <option>Salt Lake City
				  <option>San Antonio
				  <option>San Diego
				  <option>San Francisco
				  <option>San Jose
				  <option>Santa Clara
				  <option>Seattle
				  <option>Shreveport
				  <option>Syracuse
				  <option>Tampa Bay
				  <option>Tempe
				  <option>Toledo
				  <option>Tulsa
				  <option>Washington
				  <option>Wichita
				  <option>Wilmington
				  </select>
			  </UL>
		<tr>
			<td> Expected Minimum Salary:
			<select size=1 name=minSalary>
			<option value=Any>Any
			<option value=20000>$20k
			<option value=30000>$30k
			<option value=40000>$40k
			<option value=45000>$45k
			<option value=50000>$50k
			<option value=55000>$55k
			<option value=60000>$60k
			<option value=65000>$65k
			<option value=70000>$70k
			<option value=75000>$75k
			<option value=80000>$80k
			<option value=85000>$85k
			<option value=90000>$90k
			<option value=100000>$100k
			<option value=110000>$110k
			<option value=120000>$120k
			<option value=150000>$150k
			</select>
		<tr>
			<td> Preferred company:
			<select size=1 name=companyName>
			<option selected> Any
			<option> Apple Computers
			<option> AMD
			<option> Cirrus Logic
			<option> Compaq
			<option> Computer Associates
			<option> CSC
			<option> Dell
			<option> EDS
			<option> GE
			<option> GMC
			<option> Hewlett Packard
			<option> IBM
			<option> Intel
			<option> Motorola
			<option> National Semiconductor
			<option> NEC
			<option> Oracle
			<option> SUN Microsystems
			<option> Texas Instrument
			<option> USDA
			</select>
			<br>
			<input type=submit value="Go Search!">
		
	</table>
</table>
</form>

<br>
<br>
<center>
<table cellspacing="0" cellpadding="3" border="0">
<tr>	
	<td> <A href="http://newfirebird.cs.txstate.edu/~juz5/cs5334-php/index.php?act=Ematch&CODE=00">Home </A>
	<td> <A href="http://newfirebird.cs.txstate.edu/~juz5/cs5334-php/index.php?act=Ematch&CODE=10">Job Search </a>
</tr>
</table>
	</center>
</body>
</html>
EOF;
}

function display_newjob_submission ()
{
global $ibforums, $std, $std1;

return <<<EOF

<HTML>
<HEAD>
<TITLE>DrPengsAIIPDemos Job Search</TITLE>
</HEAD>

<BODY BGCOLOR="#FFFFFF" LINK="#0088ff" ALINK="#FF0000" VLINK="#CC0000">

<center><h3> Job submissions by employers </h3>
	<b>(fields with <font color='red'>*</font> are required)</b>
</center>
<form action='{$ibforums->base_url}act=Ematch&CODE=21' method='POST'>
<input type=hidden name=type value=newjob>
<table>
<tr>
	<td width="30%">
	<table>
	<tr>
		<td>
		<a href="/~juz5/cs5334/demo/proc/unix-solaris-version/html/employers_search.html">
		Search for a professional candidate
		</a>
	<tr>
		<td>
		<a href="/~juz5/cs5334/demo/proc/unix-solaris-version/html/employers_newgraduatesearch.html">
		Search for a new graduate candidate
		</a>
	<tr>
		<td>
		<a href="/~juz5/cs5334/demo/proc/unix-solaris-version/html/submit_new_job.html">
		Submit a new job
		</a>
	<tr>
		<td>
		<a href="/cgi-bin/cs5334/employers_profile.pl">
		Update your profile
		</a>
	</table>
	<td>
	<table bgcolor="#ffffcc" align=center>
		<tr>
			<td> Job Type <font color='red'>*</font>:
				<select size=1 name=jobType>
				<option>Any
				<option>regular
				<option>entry_level
				<option>intern
				<option>co-op
				</select>
				<br>
			</td>
		<tr>
			<td> Job Title <font color='red'>*</font>: 
				<select size=1 name=jobTitle>
				<option>Any
				<option>Analyst
				<option>Senior Analyst
				<option>Entry Level Engineer
				<option>Engineer
				<option>Senior Engineer
				<option>Programmer Analyst
				<option>Senior Programmer Analyst
				<option>Staff Engineer
				<option>Senior Staff Engineer
				<option>Member of Technical Staff
				<option>Senior Member of Technical Staff
				<option>Production Operator
				<option>Production Technician
				<option>Supervisor
				<option>Manager
				<option>Project Manager
				<option>Senior Manager
				<option>Director
				<option>Vice President
				<option>Executive Vice President
				<option>COO
				<option>CFO
				<option>CTO
				<option>CEO
				</select>
				<br>
			</td>
		<tr>
			<td> Specialized field <font color='red'>*</font>:
				<select size=1 name=specialty>
				<option>Any
				<option>Accounting
				<option>Analog Design
				<option>Client Server Application Development
				<option>Database Administration
				<option>Database Development
				<option>Device Driver Development
				<option>Digital Design
				<option>District Sales
				<option>E-Commerce Development
				<option>Embedded Software Development
				<option>Embedded System
				<option>Engineering
				<option>Equipment
				<option>Field Application
				<option>Field Service
				<option>Java Development
				<option>Logic Design
				<option>Mac Development
				<option>Manufacturing
				<option>Marketing
				<option>MS Access Development
				<option>Multimedia Application Development
				<option>Network Security
				<option>Network Programming
				<option>Operation
				<option>Oracle DBA
				<option>Oracle Development
				<option>Process
				<option>Process Integration
				<option>Product
				<option>Product Development
				<option>Production
				<option>Project Management
				<option>Quality Control
				<option>R&D
				<option>Reliability
				<option>Sales
				<option>Signal Integrity
				<option>Strategic Marketing
				<option>System
				<option>System Administration
				<option>System Design
				<option>System Integration
				<option>System Quality Control
				<option>System Testing
				<option>Technical Support
				<option>UNIX Administration
				<option>UNIX System Programming
				<option>Visual Basic Development
				<option>VLSI Design
				<option>Web Application Development
				<option>Web Development
				<option>Windows Administration
				<option>Windows Development
				<option>Other
				</select>
			</td>
		<tr>
		    <td>Country/area code
				<select size=1 name=countryOrAreaCode>
				<option value=1>USA
				<option value=2>JAPAN
				<option value=3>BRITAIN
				<option value=4>FRANCE
				<option value=5>GERMANY
				<option value=6>HONG_KONG
				<option value=7>TAIWAN
				<option value=8>SINGAPORE
				<option value=9>KOREA
				<option value=10>CHINA
				</select>
			</td>
		<tr>
			<td>
			 <br>
			 (At least one of the region, state, city info must be provided)
			</td>
		<tr>
		    <td>Job region:
				<select size=4 name=regionName> 
				<option selected>Any
				<option>Atlantic Coast
				<option>East
				<option>Great Lakes
				<option>Middle
				<option>Middlewest
				<option>Mountain
				<option>New England
				<option>North
				<option>Northeast
				<option>Northwest
				<option>Pacific Coast
				<option>Pacific West
				<option>South
				<option>Southeast
				<option>Southwest
				<option>West
				</select>
			</td>
		<tr>
		    <td>Job state:
			<select size=4 name=stateName>
				<option selected>Any
				<option>Alabama
				<option>Alaska
				<option>Arizona
				<option>Arkansas
				<option>California
				<option>Colorado
				<option>Connecticut
				<option>Delaware
				<option>Florida
				<option>Georgia
				<option>Hawaii
				<option>Idaho
				<option>Illinois
				<option>Indiana
				<option>Iowa
				<option>Kansas
				<option>Kentucky
				<option>Louisiana
				<option>Maine
				<option>Maryland
				<option>Massachusetts
				<option>Michigan
				<option>Minnesota
				<option>Mississippi
				<option>Missouri
				<option>Montana
				<option>Nebraska
				<option>Nevada
				<option>New Hampshire
				<option>New Jersey
				<option>New Mexico
				<option>New York
				<option>North Carolina
				<option>North Dakota
				<option>Ohio
				<option>Oklahoma
				<option>Oregon
				<option>Pennsylvania
				<option>Rhode Island
				<option>South Carolina
				<option>South Dakota
				<option>Tennessee
				<option>Texas
				<option>Utah
				<option>Vermont
				<option>Virginia
				<option>Washington
				<option>WV
				<option>Wisconsin
				<option>Wyoming
				</select>
			</td>
		<tr>
		    <td>Job city:
			 <select size=4 name=cityName>
				  <option selected>Any
				  <option>Albuquerque
				  <option>Alexandria
				  <option>Allentown
				  <option>Anchorage
				  <option>Atlanta
				  <option>Augusta
				  <option>Austin
				  <option>Baltimore
				  <option>Boise
				  <option>Boston
				  <option>Buffalo
				  <option>Burlington
				  <option>Charlotte
				  <option>Chicago
				  <option>Cincinnati
				  <option>Cleveland
				  <option>Colorado Springs
				  <option>Columbia
				  <option>Columbus
				  <option>Cupertino
				  <option>Dallas
				  <option>Dayton
				  <option>Denver
				  <option>Des Moines
				  <option>Detroit
				  <option>El Paso
				  <option>Fort Worth
				  <option>Fremont
				  <option>Green Bay
				  <option>Harrisburg
				  <option>Hartford
				  <option>Hillsboro
				  <option>Honolulu
				  <option>Houston
				  <option>Indianapolis
				  <option>Kansas City
				  <option>Las Vegas
				  <option>Lexington
				  <option>Lincoln
				  <option>Little Rock
				  <option>Los Angeles
				  <option>Miami
				  <option>Milpitas
				  <option>Milwaukee
				  <option>Minneapolis
				  <option>Mobile
				  <option>Morristown
				  <option>Mountain View
				  <option>Nashville
				  <option>New Orleans
				  <option>New York
				  <option>Newark
				  <option>Oakland
				  <option>Oklahoma City
				  <option>Omaha
				  <option>Orlando
				  <option>Palo Alto
				  <option>Philadelphia
				  <option>Phoenix
				  <option>Pittsburgh
				  <option>Portland
				  <option>Raleigh
				  <option>Reno
				  <option>Richmond
				  <option>Sacramento
				  <option>Saint Louis
				  <option>Salt Lake City
				  <option>San Anotonio
				  <option>San Diego
				  <option>San Francisco
				  <option>San Jose
				  <option>Santa Clara
				  <option>Seattle
				  <option>Shreveport
				  <option>Syracuse
				  <option>Tampa Bay
				  <option>Tempe
				  <option>Toledo
				  <option>Tulsa
				  <option>Washington
				  <option>Wichita
				  <option>Wilmington
				</select>
			</td>
		<tr>
			<td> Minimum salary:
				<select size=1 name=minSalary>
				<option value=Any>Any
				<option value=20000>$20k
				<option value=30000>$30k
				<option value=40000>$40k
				<option value=45000>$45k
				<option value=50000>$50k
				<option value=55000>$55k
				<option value=60000>$60k
				<option value=65000>$65k
				<option value=70000>$70k
				<option value=75000>$75k
				<option value=80000>$80k
				<option value=85000>$85k
				<option value=90000>$90k
				<option value=100000>$100k
				<option value=110000>$110k
				<option value=120000>$120k
				<option value=150000>$150k
				</select>
			</td>
		<tr>
			<td> Maximum salary:
				<select size=1 name=maxSalary>
				<option value=Any>Any
				<option value=20000>$20k
				<option value=30000>$30k
				<option value=40000>$40k
				<option value=45000>$45k
				<option value=50000>$50k
				<option value=55000>$55k
				<option value=60000>$60k
				<option value=65000>$65k
				<option value=70000>$70k
				<option value=75000>$75k
				<option value=80000>$80k
				<option value=85000>$85k
				<option value=90000>$90k
				<option value=100000>$100k
				<option value=110000>$110k
				<option value=120000>$120k
				<option value=150000>$150k
				</select>
			</td>
		<tr>
			<td>
			 Company name <font color='red'>*</font>:
			<input type=text name=companyName>
			<br>
		<tr>
			<td>
			Start Date (mm-dd-yyyy) <font color='red'>*</font>:
			<input type=text name=startDate>
		<tr>
			<td>
			Reference number:
			<input type=text name=referenceNum>
		<tr>
			<td>
			Contact (email) <font color='red'>*</font>:
			<input type=text name=contact>
		</table>
	<br>
	<center>
		Job description:<br>
		<textarea cols=50 rows=10 name=job_description>
		</textarea>
		<br>
	<br>
		Qualification:<br>
		<textarea cols=50 rows=10 name=qualification>
		</textarea>
		<br>
		<input type=submit value="Submit">
		</center>
</table>
</form>
	
<br>
<br>
<center>
<TABLE CELLSPACING="0" CELLPADDING="3" BORDER="0">
<tr>    
	<td> <a href="/~juz5/cs5334-php/index.php?act=Ematch&CODE=00">Home </a>
	<td> <a href="/~juz5/cs5334-php/index.php?act=Ematch&CODE=10">Job Search </a></tr>
</table>
	</center>


</body>
</html>
EOF;

}


function display_top_frame ()
{

return <<<EOF

<form name="advancedmanpower_title">
<table width="800">
<tr>
<center><H1> <i><font color="black"> AIIP Project </font></i></H1>
<tr>
<td>
<td>
</tr>
<tr>
<td colspan = 2>
<TABLE CELLSPACING="0" CELLPADDING="0" width="45%" bgcolor="#FFC0CB">
<tr>
	<td> <input type=button value="Home" onClick="location='http://newfirebird.cs.txstate.edu/~juz5/cs5334-php/index.php?act=Ematch&CODE=00'"></td>
	<td> <input type=button value="Job Search" onClick="location='http://newfirebird.cs.txstate.edu/~juz5/cs5334-php/index.php?act=Ematch&CODE=10'"></td></tr>
</table>
</td>
</tr>
</table>

</form>

EOF;

}


function display_bottom_frame($job_count=0, $member_count=0, $c_g_count=0)
{
global $ibforums;
return <<<EOF

<center><h2>Welcome! </h2></center>
<center>  

<IMG src="{$ibforums->vars['img_url']}/background.gif" width="550" height="180"  usemap="#map1" border="0"><map name=map1>
<area shape="rect" coords="3,3,215,180" href="{$ibforums->base_url}act=Ematch&CODE=10">
<area shape="rect" coords="310,3, 550,180" href="{$ibforums->base_url}act=Employers">
</map>
        
<br>
<br>
<center>
<i>
Registered Engineers: $member_count<br>
Total jobs: $job_count<br>
Total registered college graduates: $c_g_count<br>
</i>    
</center> 
<br>      
<br>
<br>
<br>
<table cellspacing="0" cellpadding="3" border="0" align="center">
<tr>
        <td> <a href='{$ibforums->base_url}act=Ematch&CODE=00'>{$ibforums->lang['tb_home']}</a>  |
        <td> <a href='{$ibforums->base_url}act=Ematch&CODE=10'>{$ibforums->lang['tb_jobsearch']}</a> </tr>


</table>
</center>


<br>    
        
EOF;
}


}

?>
