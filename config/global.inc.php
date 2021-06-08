<?
/**************************************************************************
 *  PEM - PHP Everyman                                                    *
 *          (pem.JS-X.com)                                                *
 *                                                                        *
 *  This program is free software: you can redistribute it and/or modify  *
 *  it under the terms of the GNU General Public License as published by  *
 *  the Free Software Foundation, either version 3 of the License, or     *
 *  any later version.                                                    *
 *                                                                        *
 *  This program is distributed in the hope that it will be useful,       *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 *  GNU General Public License for more details.                          *
 *                                                                        *
 *  You should have received a copy of the GNU General Public License     *
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>. *
 **************************************************************************/

if(!isset($HACK_CHECK) || !$HACK_CHECK)exit; // DO NOT DIRECTLY LOAD THIS FILE

  /* start the timer on the page loading */
  /* dont change this */
  if(!isset($PAGE_TIME_START)) $PAGE_TIME_START=microtime();

  $DEBUG=3; // debug output on or off (1 is on , 0 is off)
  $SHOW_TODO=0; // set to 1 to show the message. 0 to not show them

  $SESSION_TIMEOUT=2*60*60; // 2 hours - This keeps the session alive for 60 minutes longer

  // These are the templates to use
  $TEMPLATE_PATH="templates";
  $HEADER_FILE="$TEMPLATE_PATH/header.php";
  $MENU_FILE  ="$TEMPLATE_PATH/menubar.php";
  $FOOTER_FILE="$TEMPLATE_PATH/footer.php";



  # -------------------------------------------------------------------- #
  /* these are the names of the pages - change only if you change file names */
  /* there is no need to change them */
  $PAGE_INDEX        ="index.php";
  $PAGE_HOURS        ="hours.php";
  $PAGE_PROJECT      ="projects.php";
  $PAGE_PEOPLE       ="people.php";
  $PAGE_LOGIN        ="login.php";
  $PAGE_REPORT       ="reports.php";
  //$PAGE_GRAPHBYSUP   ="graph-bysup.php";
  //$PAGE_SHOW         ="show.php";

  # -------------------------------------------------------------------- #
  /* all access to Mysql table names is via these defined names */
  /* there is no need to change them */

  $MYSQL_PREFIX="";
  $TABLE_PERSON            =$MYSQL_PREFIX."Person";
  $TABLE_PROJECT_SUMMARY   =$MYSQL_PREFIX."Project_Summary";
  //$TABLE_PROJECT           =$MYSQL_PREFIX."Project2";
  $TABLE_PROJECT           =$MYSQL_PREFIX."Project";
  $TABLE_BASELINE          =$MYSQL_PREFIX."Basline";
  $TABLE_HEADCOUNT         =$MYSQL_PREFIX."HeadCount";
  //$TABLE_ACTIVEPERSON      =$MYSQL_PREFIX."ActivePerson2";
  $TABLE_ACTIVEPERSON      =$MYSQL_PREFIX."ActivePerson";

$installs=Array('eo','se','de','ts','sw');
$installs['eo']=Array('db'=>'everyman2','label'=>'Engineering Operations','staff'=>17.5 );
$installs['se']=Array('db'=>'everyman2','label'=>'System Engineering','staff'=>41   );
$installs['de']=Array('db'=>'everyman2','label'=>'Design Engineering','staff'=>26   );
$installs['ts']=Array('db'=>'everyman2','label'=>'Test Solutions','staff'=>30   );
$installs['sw']=Array('db'=>'everyman2','label'=>'Software Engineering','staff'=>33);
$installs['en']=Array('db'=>'everyman2','label'=>'Engineering','staff'=>7);

$tmp=$_SERVER['PHP_SELF'];
if(preg_match("/_engops/",$tmp))  {$flag='eo'; }
else if(preg_match("/_se/",$tmp)) {$flag='se'; }
else if(preg_match("/_de/",$tmp)) {$flag='de'; }
else if(preg_match("/_ts/",$tmp)) {$flag='ts'; }
else /* must be SW */             {$flag='sw'; }

$STAFF_LEVEL=$installs[$flag]['staff'];
$TOTAL_STAFF=0;
$TOTAL_STAFF+=$installs['eo']['staff'];
$TOTAL_STAFF+=$installs['se']['staff'];
$TOTAL_STAFF+=$installs['de']['staff'];
$TOTAL_STAFF+=$installs['ts']['staff'];
$TOTAL_STAFF+=$installs['sw']['staff'];
$TOTAL_STAFF+=$installs['en']['staff'];
$DEPARTMENT_NAME=$installs[$flag]['label'];

  ## This defines your connection to your MySQL database
  $DB_HOST     ="localhost";
  $DB_DATABASE = "everyman2";
  $DB_USER     ="everyman";
  $DB_PASSWORD ='online';

  //change before you create any passwords! or you will have to
  //change them all after you set a new encrypt key
  $encryptKey="something that is your secret. shh!!";

  // If you change something other than the above, change this VERSION
  $PEM_VERSION="0.0.1"; /* this is the version of this tool */


  /************** FROB NO FURTHER **********************/
  /************** FROB NO FURTHER **********************/
  /************** FROB NO FURTHER **********************/
  /************** FROB NO FURTHER **********************/
  /************** FROB NO FURTHER **********************/
  /************** FROB NO FURTHER **********************/
  /************** FROB NO FURTHER **********************/
  /************** FROB NO FURTHER **********************/
  /************** FROB NO FURTHER **********************/
  /************** FROB NO FURTHER **********************/

  if($DEBUG>10) echo "DEBUG(10): Loading File: global.inc.php<BR>\n";

  // no need to change these - these are used to lookup access levels
  $ACCESS_NONE=0;$ACCESS_USER=1;$ACCESS_ADMIN=2;


  /* declare global variables that will be used */
  $TODO;
  $STATUS;
  $ERROR;

  /* don't change these */
  session_register("userID");
  session_register("time");
  //session_register("dbAdmin");
  //session_register("projectID");
  //echo "SESS(userID)=".$_SESSION['userID']."<BR>\n";

  $strIncludePrefix = "config";
  $sequence1="seq1";
  Include($strIncludePrefix."/db.inc.php");
  Include($strIncludePrefix."/generalFunctions.inc.php");
  Include($strIncludePrefix."/securityFunctions.inc.php");

  $DEBUG_POSTED=getPostedData(); // This builds a global variable called $_FORM[] - keys are posted varaible name, values are values of posted data

  header("Cache-control: private"); // enables forms to retain values when user hits 'back' button

?>
