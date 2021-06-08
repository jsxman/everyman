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


/******************************************/
/* HEADER: INCLUDE/SECURITY CHECK - START */
/******************************************/
$HACK_CHECK=1; Include("config/global.inc.php");
debug(10,"Loading File: people.php");
debug(10,"User Level is DB-Admin (".$_SESSION['dbAdmin'].")");
checkPermissions($SESSION_TIMEOUT); // if not logged in, or session has timed out...
/******************************************/
/* HEADER: INCLUDE/SECURITY CHECK - END   */
/******************************************/

set_todo("click on project to see report on: total, graph, non-graph, by projects");

$show2011=1;



$strSQL4 = "SELECT * FROM $TABLE_PERSON WHERE id='".$_SESSION['userID']."'";
$result4 = dbquery($strSQL4);
//echo "Q:<DIR>$strSQL4</DIR><BR>\n";
$row4 = mysql_fetch_array($result4);
$PROJECT_ADMIN=0;
if($row4['program_admin'])
{
  $PROJECT_ADMIN=1;
}
//echo "PA{$PROJECT_ADMIN}<BR>\n";


/******************************************/
/* COMPUTING - NO OUTPUT - START          */
/******************************************/
$strName = "";
//if(isset($_FORM['txt_action']) && $_FORM['txt_action'] == "summary")
if($PROJECT_ADMIN) //always do this - its fast now
{
    $strSQL0 = "DELETE FROM $TABLE_PROJECT_SUMMARY";
    $result0 = dbquery($strSQL0);

    $sYear=date("Y");$eYear=$sYear+1;$nYear=$eYear+1;
    $strSQL4 = "SELECT ";
    $strSQL4.= " P.id";
    $strSQL4.= ", sum(AP.hours) as shours";
    //$strSQL4.= ", sum(AP2.hours) as nhours";
    $strSQL4.= " FROM $TABLE_PROJECT AS P";
    $strSQL4.= " LEFT JOIN $TABLE_ACTIVEPERSON AS AP ON AP.project_id = P.id";
    //$strSQL4.= " AND AP.date >= '$sYear-01-01' AND AP.date < '$eYear-01-01'";
    $strSQL4.= " WHERE AP.date >= '$sYear-01-01' AND AP.date < '$eYear-01-01'";
    //$strSQL4.= " LEFT JOIN $TABLE_ACTIVEPERSON AS AP2 ON AP2.project_id = P.id";
    //$strSQL4.= " AND AP2.date >= '$eYear-01-01' AND AP2.date < '$nYear-01-01'";
    $strSQL4.= " GROUP BY P.id";
    $result4 = dbquery($strSQL4);
    while($row4 = mysql_fetch_array($result4))
    {
      $strSQL1 = "INSERT INTO $TABLE_PROJECT_SUMMARY SET";
      $strSQL1.= " project_id='".$row4['id']."'";
      $strSQL1.= ",year_2010_total='".$row4['shours']."'";
      //$strSQL1.= ",year_2011_total='".$row4['nhours']."'";
      $strSQL1.= ",modified='".date("Y-m-d")."'";
      $result1 = dbquery($strSQL1);
    }
    $strSQL4 = "SELECT ";
    $strSQL4.= " P.id";
    $strSQL4.= ", sum(AP.hours) as shours";
    $strSQL4.= " FROM $TABLE_PROJECT AS P";
    $strSQL4.= " LEFT JOIN $TABLE_ACTIVEPERSON AS AP ON AP.project_id = P.id";
    //$strSQL4.= " AND AP.date >= '$eYear-01-01' AND AP.date < '$nYear-01-01'";
    $strSQL4.= " WHERE AP.date >= '$eYear-01-01' AND AP.date < '$nYear-01-01'";
    $strSQL4.= " GROUP BY P.id";
    $result4 = dbquery($strSQL4);
    while($row4 = mysql_fetch_array($result4))
    {
      //$strSQL1 = "INSERT INTO $TABLE_PROJECT_SUMMARY SET";
      //$strSQL1.= " project_id='".$row4['id']."'";
      //$strSQL1.= ",year_2011_total='".$row4['shours']."'";
      //$strSQL1.= ",modified='".date("Y-m-d")."'";
      $strSQL1 = "UPDATE $TABLE_PROJECT_SUMMARY SET";
      $strSQL1.= " year_2011_total='".$row4['shours']."'";
      $strSQL1.= " WHERE project_id='".$row4['id']."'";
      //echo "Q<DIR>$strSQL1</DIR>";
      $result1 = dbquery($strSQL1);
    }
    //set_status("Success: Project summary hours updated.");
}



if($PROJECT_ADMIN && isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Create")
{
  $strName    = postedData($_FORM['txt_name']);
  $strSDate   = postedData($_FORM['txt_start_date']);
  $strEDate   = postedData($_FORM['txt_end_date']);
  $strEDate2  = postedData($_FORM['txt_end_date2']);
  $strPDirect = postedData($_FORM['txt_pdirect']);
  $strPDirect = preg_replace("[^0-9]","",$strPDirect);
  $strFuture  = postedData($_FORM['txt_future']);
  $strFuture = preg_replace("[^0-1]","",$strFuture);
  $strScale   = postedData($_FORM['txt_scale']);
  $strScale = preg_replace("[^0-9]","",$strScale);
  $strAbsent  = postedData($_FORM['txt_absent']);
  $strAbsent = preg_replace("[^0-1]","",$strAbsent);
  debug(3,"CREATE ACTION POSTED (N:$strName)");
  $err=0;
  if(!strlen($strName)){$err=1;set_error("Name must be defined.");}
  if($strPDirect<0 || $strPDirect>100){$err=1;set_error("% Direct must be between 0 and 100.");}
  if($strScale<0   || $strScale>100){$err=1;set_error("% Scaling must be between 0 and 100.");}
  if($strFuture<0  || $strFuture>1){$err=1;set_error("Future must be 0 or 1.");}
  if($strAbsent<0  || $strAbsent>1){$err=1;set_error("Absent must be 0 or 1.");}
  if($strSDate && !preg_match('/\d{4}.\d{1,2}/',$strSDate)){$err=1;set_error("Start Date format is wrong. (YYYY-MM)");}
  if($strEDate && !preg_match('/\d{4}.\d{1,2}/',$strEDate)){$err=1;set_error("Eng-End Date format is wrong. (YYYY-MM)");}
  if($strEDate2 && !preg_match('/\d{4}.\d{1,2}/',$strEDate2)){$err=1;set_error("Sus-End Date format is wrong. (YYYY-MM)");}
  $strSDate.="-01";
  $strEDate.="-01";
  $strEDate2.="-01";
  if(!$err)
  {
    /* find out if the username already exists */
    $strSQL0 = "SELECT P.name FROM $TABLE_PROJECT AS P WHERE P.name='$strName'";
    $result0 = dbquery($strSQL0);
    $row0 = mysql_fetch_array($result0);
    if($row0 && $row0['username'])
    {
      set_error("You can not create another project with the same name ($strName)");
    }
    else
    {
      $strSQL1 = "INSERT INTO $TABLE_PROJECT SET";
      $strSQL1.= " name='$strName'";
      $strSQL1.= ",percent_direct='$strPDirect'";
      $strSQL1.= ",headcount_scaling='$strScale'";
      $strSQL1.= ",future='$strFuture'";
      $strSQL1.= ",absent='$strAbsent'";
      $strSQL1.= ",created='".date("Y-m-d")."'";
      $strSQL1.= ",modified='".date("Y-m-d")."'";
      if($strSDate) $strSQL1.= ",start_date='".$strSDate."'";
      if($strEDate) $strSQL1.= ",end_date='".$strEDate."'";
      if($strEDate2) $strSQL1.= ",end_date2='".$strEDate2."'";
      $result1 = dbquery($strSQL1);
      set_status("Success: Project created.");
      /* remove these from the new create box - as we are creating this account now! */
      $strName = "";
    }
  }
}



$str_eda=isset($_FORM['txt_eda'])?postedData($_FORM['txt_eda']):"";
$str_name=isset($_FORM['txt_name'])?postedData($_FORM['txt_name']):"";
//set_status("1:$str_eda, 2:$str_name");
debug(10,"Action to edit, delete ($str_eda) for ($str_name)");
if($PROJECT_ADMIN && isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Do Now" && $str_eda == "delete")
{
  $str_delconfirm=isset($_FORM['txt_delconfirm'])?postedData($_FORM['txt_delconfirm']):"";
  if(!$str_delconfirm)
  {
    set_error("The confirm checkbox was not selected.  No action taken.");
  }
  else
  {
    $strSQL0 = "DELETE FROM $TABLE_PROJECT WHERE id='$str_name'";
    $result0 = dbquery($strSQL0);
    $strSQL0 = "DELETE FROM $TABLE_ACTIVEPERSON WHERE project_id='$str_name'";
    $result0 = dbquery($strSQL0);
    set_status("Project deleted.");
  }
}
  if($PROJECT_ADMIN && isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Change")
  {
    $tmpID   = postedData($_FORM['txt_projectid']);
    $tmpName = postedData($_FORM['txt_name']);
    $tmpSDate = postedData($_FORM['txt_start_date']);
    $tmpEDate = postedData($_FORM['txt_end_date']);
    $tmpEDate2 = postedData($_FORM['txt_end_date2']);
  if($tmpSDate && !preg_match('/\d{4}.\d{1,2}/',$tmpSDate)){$err=1;set_error("Start Date format is wrong. (YYYY-MM)");}
  if($tmpEDate && !preg_match('/\d{4}.\d{1,2}/',$tmpEDate)){$err=1;set_error("Eng-End Date format is wrong. (YYYY-MM)");}
  if($tmpEDate2 && !preg_match('/\d{4}.\d{1,2}/',$tmpEDate2)){$err=1;set_error("Sus-End Date format is wrong. (YYYY-MM)");}
    $tmpSDate.="-01";
    $tmpEDate.="-01";
    $tmpEDate2.="-01";
    $tmpPDirect = postedData($_FORM['txt_pdirect']);
    $tmpPDirect = preg_replace("[^0-9]","",$tmpPDirect);
    $tmpFuture  = postedData($_FORM['txt_future']);
    $tmpFuture = preg_replace("[^0-1]","",$tmpFuture);
    $tmpScale   = postedData($_FORM['txt_scale']);
    $tmpScale = preg_replace("[^0-9]","",$tmpScale);
    $tmpAbsent  = postedData($_FORM['txt_absent']);
    $tmpAbsent = preg_replace("[^0-1]","",$tmpAbsent);
    debug(10,"CHANGE ACTION POSTED (I:$tmpID, N:$tmpName)");
    $strSQL4 = "UPDATE $TABLE_PROJECT SET";
    $strSQL4.= " name='$tmpName'";
    $strSQL4.= ",percent_direct='$tmpPDirect'";
    $strSQL4.= ",headcount_scaling='$tmpScale'";
    $strSQL4.= ",future='$tmpFuture'";
    $strSQL4.= ",absent='$tmpAbsent'";
    $strSQL4.= ",modified='".date("Y-m-d")."'";
    if($tmpSDate) $strSQL4.= ",start_date='".$tmpSDate."'";
    else $strSQL4.= ",start_date= NULL";
    if($tmpEDate) $strSQL4.= ",end_date='".$tmpEDate."'";
    else $strSQL4.= ",end_date= NULL";
    if($tmpEDate2) $strSQL4.= ",end_date2='".$tmpEDate2."'";
    else $strSQL4.= ",end_date2= NULL";
    $strSQL4.= " WHERE id='$tmpID'";
    $result4 = dbquery($strSQL4);
    set_status("Project Updated.");
  }

/******************************************/
/* COMPUTING - NO OUTPUT - END            */
/******************************************/

/******************************************/
/* COMPUTING - SHOW OUTPUT - START        */
/******************************************/
$TITLE="Project Editing";
show_header();
show_menu("PROJECTS");
show_status();
show_error();



  //$str_name = postedData($_FORM['txt_name']);
  if($PROJECT_ADMIN && isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Do Now" && $str_eda == "edit")
  {
        /* do the dbquery outside the select list so the debug will appear */
        $strSQL3 = "SELECT * FROM $TABLE_PROJECT AS P WHERE P.id='$str_name'";
        $result3 = dbquery($strSQL3);
        $row3 = mysql_fetch_array($result3);
        $tmpName=$row3['name'];
        $tmpPDirect=$row3['percent_direct'];
if($tmpPDirect>0)$ck100="SELECTED"; else $ck0="SELECTED";
        $tmpFuture=$row3['future'];
        $tmpAbsent=$row3['absent'];
        $tmpScale=$row3['headcount_scaling'];
        $tmpSDate=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1-$2",$row3['start_date']);
        $tmpEDate=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1-$2",$row3['end_date']);
        $tmpEDate2=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1-$2",$row3['end_date2']);
        $tmpID=$row3['id'];
        $tmpCreated=$row3['created'];
//set_status("test:".$str_name.":".$tmpName.":".$tmpID);
//show_status();
        $tmpModified=$row3['modified'];
?>
<BR>
<form action="<?=$PAGE_PROJECT;?>" method=POST>
<input type=hidden value='<?=$tmpID;?>' name=txt_projectid>
<fieldset>
<legend><b>Edit Project</b></legend>
<table align=center>
<tr><td>Name</td><td><input type=text name=txt_name value='<?=$tmpName;?>'></td></tr>
<tr><td>Direct or Indirect</td><td><select name=txt_pdirect><option <?=$ck100;?> value='100'>Direct</option><option <?=$ck0;?> value='0'>Indirect</select></td></tr>
<?
$x="";
if($tmpFuture)$x="CHECKED";
$y="";
if($tmpAbsent)$y="CHECKED";
?>
<tr><td>Future Work</td><td><input type=checkbox <?=$x;?> name=txt_future value='1'>(Check for future program)</td></tr>
<tr><td>Headcount Scaling(PWin)</td><td><input type=text name=txt_scale value='<?=$tmpScale;?>'>(for scaled graphs - reduces value graphed <B>FOR FUTURE WORK ONLY</b>)</td></tr>
<tr><td>Absence Code</td><td><input type=checkbox <?=$y;?> name=txt_absent value='1'>(Check for absent code)</td></tr>
<tr><td>Start Date</td><td><input type=text name=txt_start_date value='<?=$tmpSDate;?>'>[YYYY-MM](leave blank for none)</td></tr>
<tr><td>Eng End Date</td><td><input type=text name=txt_end_date value='<?=$tmpEDate;?>'>[YYYY-MM](leave blank for none)</td></tr>
<tr><td>Sus End Date</td><td><input type=text name=txt_end_date2 value='<?=$tmpEDate2;?>'>[YYYY-MM](leave blank for none)</td></tr>
<tr><td colspan=1><input class=but type=submit name=txt_action value="Change"></td></tr>
</table>
</fieldset>
</form>
<?
  }
if(1)
{


if($PROJECT_ADMIN)
{
  /* do the dbquery outside the select list so the debug will appear */
  $strSQL2 = "SELECT P.name,P.id FROM $TABLE_PROJECT AS P ORDER BY P.name ASC, P.id ASC";
  $result2 = dbquery($strSQL2);



?>
<BR>
<form action="<?=$PAGE_PROJECT;?>" method=POST>
<fieldset>
<legend><b>Modify Project</b></legend>
<table align=center>
<tr><td>Pick A Project: <select name="txt_name">
<?
  while($row2 = mysql_fetch_array($result2))
  {
    $lf=$row2['name'];
    echo "<option value='".$row2['id']."'>$lf</option>\n";
  }
?>
</select></td></tr>
<tr><td>Pick an Action: <select name=txt_eda>
<option value='edit'>Edit</option>
<option value='delete'>Delete</option>
</select></td></tr>
<tr><td>Must check to confirm a delete selection: <input type=checkbox name=txt_delconfirm value=1><td></tr></tr>
<tr><td align=center><input class=but type=submit value='Do Now' name='txt_action'></td></tr>
<?

if(0)
{
  $strSQL5 = "SELECT modified FROM $TABLE_PROJECT_SUMMARY ORDER BY modified DESC LIMIT 1";
  $result5 = dbquery($strSQL5);
  $row5 = mysql_fetch_array($result5);
  $last_mod=$row5['modified'];
?>
<tr><td align=center><a href='?txt_action=summary'>Update Project Yearly Totals</a> - Last Updated <?=$last_mod;?></td></tr>
<?
}
?>
</table>
</form>
<?
} /*end of project admin access to modify a project or delete it */

$asc = postedData($_FORM['asc']); if($asc=="ASC")$asc="DESC";else $asc="ASC";
$orderby = postedData($_FORM['orderby']);$orderby=preg_replace("[^_a-z]","",$orderby);
if($orderby=="percentdirect")$orderby="percent_direct";
if($orderby=="headcountscaling")$orderby="headcount_scaling";
if(strlen($orderby)<2)$orderby="name";
$i=0;
$sYear=date("Y");$eYear=$sYear+1;$nYear=$eYear+1;
$strSQL4 = "SELECT ";
$strSQL4.= " P.id, P.name, P.created, P.modified, P.percent_direct, P.future, P.headcount_scaling";
$strSQL4.= ",P.absent, P.start_date, P.end_date, P.end_date2";
$strSQL4.= ", PS.year_2010_total";
$strSQL4.= ", PS.year_2011_total";
//$strSQL4.= ", sum(AP.hours) as shours";
$strSQL4.= " FROM $TABLE_PROJECT AS P";
$strSQL4.= " LEFT JOIN $TABLE_PROJECT_SUMMARY AS PS ON PS.project_id = P.id";
//$strSQL4.= " LEFT JOIN $TABLE_ACTIVEPERSON AS AP ON AP.project_id = P.id";
//$strSQL4.= " AND AP.date >= '$sYear-01-01' AND AP.date < '$eYear-01-01'";
$strSQL4.= " GROUP BY P.id";
$strSQL4.= " ORDER BY P.".$orderby." ".$asc;
//echo "q:<dir>$strSQL4</dir><BR>\n";
$result4 = dbquery($strSQL4);
echo "<table align=center>";
echo "<tr><th>#</th>";
echo "<th><a href=$PAGE_PROJECT?orderby=name&asc=$asc>Name</a></th>";
echo "<th><a href=$PAGE_PROJECT?orderby=percent_direct&asc=$asc>% Direct</a></th>";
echo "<th><a href=$PAGE_PROJECT?orderby=future&asc=$asc>Future</a></th>";
echo "<th><a href=$PAGE_PROJECT?orderby=headcount_scaling&asc=$asc>Headcount Scaling(PWin)</a></th>";
echo "<th>Graph</th>";
//echo "<th>DB-ID</th>";
echo "<th><a href=$PAGE_PROJECT?orderby=absent&asc=$asc>Absence</a></th>";
echo "<th>Start-Date</th>";
echo "<th>Eng-End-Date</th>";
echo "<th>Sus-End-Date</th>";
if($PROJECT_ADMIN)
{
echo "<th>Hours in $sYear</th>";
if($show2011)echo "<th>Hours in $eYear</th>";
}
echo "<th><a href=$PAGE_PROJECT?orderby=created&asc=$asc>Created</a></th>";
echo "<th><a href=$PAGE_PROJECT?orderby=modified&asc=$asc>Modified</a></th>";
if($PROJECT_ADMIN) {echo "<th>Action</th></tr>";}
$num=0;
while ($row4 = mysql_fetch_array($result4))
{
  $num=($num+1)%2;
  $tmpName=$row4['name'];
echo "<tr class=row".$num.">";
echo "<td>".++$i."</td>";
echo "<td><a href='$PAGE_HOURS?txt_project=".$row4['id']."'>".$row4['name']."</a></td>";
$pd=$row4['percent_direct'];
$pd_style='';
if($pd==100)
{
  $pd="DIRECT";
  $pd_style="warn3";
}
else if(!$pd)
{
  $pd="INDIRECT";
  $pd_style="warn";
}
echo "<td class='$pd_style' align=center>".$pd."</td>";

$hcs=$row4['headcount_scaling']; $hcs_style="";
$fut="Yes"; $fut_style="";
if(!$row4['future']) {$hcs='-N/A-';$fut="No"; $hcs_style="na";$fut_style="na";}
else if ($hcs>=60)$hcs_style="warn2";
echo "<td align=center class='$fut_style'>".$fut."</td>";
echo "<td align=center class='$hcs_style'>".$hcs."</td>";
echo "<td><a href='$PAGE_REPORT?graph=byproj&parm=txtproject&txtproject=".$row4['id']."'>Graph</a></td>";
//echo "<td>".$row4['id']."</td>";

$absent=$row4['absent']?"Yes":"-";
echo "<td align=center>".$absent."</td>";

$tmpSDate=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1-$2",$row4['start_date']);
$tmpEDate=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1-$2",$row4['end_date']);
$tmpEDate2=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1-$2",$row4['end_date2']);


echo "<td align=center>".$tmpSDate."</td>";
echo "<td align=center>".$tmpEDate."</td>";
echo "<td align=center>".$tmpEDate2."</td>";

if($PROJECT_ADMIN)
{
if($row4['year_2010_total']<1)$row4['year_2010_total']='-none-';
echo "<td>".$row4['year_2010_total']."</td>";

if($row4['year_2011_total']<1)$row4['year_2011_total']='-none-';
if($show2011)echo "<td>".$row4['year_2011_total']."</td>";
}

echo "<td>".$row4['created']."</td>";
echo "<td>".$row4['modified']."</td>";
if($PROJECT_ADMIN) {echo "<td><a href='".$PAGE_PROJECT."?txt_action=Do Now&txt_name=".$row4['id']."&txt_eda=edit'>Edit</a></td>";}
echo "</tr>";
}
echo "</table>";
?>
</fieldset>
<BR>
<?
if($PROJECT_ADMIN)
{
?>
<form action="<?=$PAGE_PROJECT;?>" method=POST>
<fieldset>
<legend><b>Create New Project</b></legend>
<table align=center>
<tr><td>Name</td><td><input type=text name=txt_name value=''></td></tr>
<tr><td>Direct or Indirect</td><td><select name=txt_pdirect><option value='100'>Direct</option><option value='0'>Indirect</select></td></tr>
<tr><td>Future Work</td><td><input type=checkbox name=txt_future value='1'>(Check for future program)</td></tr>
<tr><td>Headcount Scaling(PWin)</td><td><input type=text name=txt_scale value=''>(for scaled graphs - reduces value graphed for <b>FUTURE WORK ONLY</b>)</td></tr>
<tr><td>Absence Code</td><td><input type=checkbox name=txt_absent value='1'>(Check for absence code)</td></tr>
<tr><td>Start Date</td><td><input type=text name=txt_start_date value=''>[YYYY-MM](leave blank for none)</td></tr>
<tr><td>Eng. End Date</td><td><input type=text name=txt_end_date value=''>[YYYY-MM](leave blank for none)</td></tr>
<tr><td>Sus. End Date</td><td><input type=text name=txt_end_date2 value=''>[YYYY-MM](leave blank for none)</td></tr>
<tr><td colspan=1 align=center><input class=but type=submit name=txt_action value="Create"></td></tr>
</table>
</fieldset>
</form>
<?
} /* project admin - to create a new project */
} /* end check for DB ADMIN */

show_footer();


/******************************************/
/* COMPUTING - SHOW OUTPUT - END          */
/******************************************/


?>
