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
debug(10,"Loading File: tasks.php");
checkPermissions($SESSION_TIMEOUT); // if not logged in, or session has timed out...
/******************************************/
/* HEADER: INCLUDE/SECURITY CHECK - END   */
/******************************************/


//set_todo("allow to baseline 'now' with label");

//echo "D:1:".$_FORM['txt_action2']."<BR>\n";


/******************************************/
/* COMPUTING - NO OUTPUT - START          */
/******************************************/
$strProject =isset($_FORM['txt_project'])?postedData($_FORM['txt_project']):0;
$strPerson =isset($_FORM['txt_person'])?postedData($_FORM['txt_person']):0;

debug(10,"selected project id = $strProject");
debug(10,"selected peson id = $strPerson");

if(isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Save Changes")
{
  $somethingupdated=0; /* set to 1 to make us show a success message */
  $strCount =isset($_FORM['total_rows'])?postedData($_FORM['total_rows']):0;
  //echo "strCount($strCount)<BR>\n";
  if($strCount)
  {
    for($i=1;$i<=$strCount;$i++)
    {
      $x="row".$i;
      $t=isset($_FORM[$x])?postedData($_FORM[$x]):0;
      //echo "-T($t)-";
      list($strPersonID,$strProjectID) =split("-",$t);
      //echo "ROW($i) PersonID($strPersonID) ProjectID($strProjectID): ";
      for($j=0;$j<24;$j++)
      {
        $x="hour".$i."_".$j;
        $t=isset($_FORM[$x])?postedData($_FORM[$x]):0;
        //echo "MON($j)=$t ";
        /* look to see if this person/project/month exists.
         * if it does not AND this is not zero or null ---> then insert new element
         * if it does AND the date is today --> then update the identified element
         * if it does AND it is not today --> create a new entry
         */
        $y=date("Y");if($j>=12)$y++;
        $m=($j+1)%12;if(!$m)$m=12;
        $d="01";
        $_date="$y-$m-$d";
        $strSQL1 = "SELECT * ";
        $strSQL1.= " FROM $TABLE_ACTIVEPERSON";
        $strSQL1.= " WHERE person_id='$strPersonID' AND project_id='$strProjectID' AND date='$_date'";
        $result1 = dbquery($strSQL1);
        if($row1 = mysql_fetch_array($result1))
        {
          $tmpID=$row1['id'];
          //update it -OR- delete it
          if($t>0)
          {
            $strSQL2 = "UPDATE $TABLE_ACTIVEPERSON SET";
            $strSQL2.= " hours='$t'";
            $strSQL1.= ",modified='".date("Y-m-d")."'";
            $strSQL2.= " WHERE id='$tmpID'";
            $result2 = dbquery($strSQL2);
            //set_status("Updated Field [PersonID=$strPersonID,ProjectID=$strProjectID,HoursID=$tmpID,$Hours=$t,DATE=$_date,J=$j].");
            $somethingupdated=1; /* set to 1 to make us show a success message */
          }
          else
          {
            $strSQL2 = "DELETE FROM $TABLE_ACTIVEPERSON WHERE id='$tmpID'";
            $result2 = dbquery($strSQL2);
            //set_status("Deleted Field [PersonID=$strPersonID,ProjectID=$strProjectID,HoursID=$tmpID,DATE=$_date,J=$j].");
            $somethingupdated=1; /* set to 1 to make us show a success message */
          }
        }
        else
        {
          // create new one (If new value is not zero/null)
          if($t>0)
          {
            $strSQL2 = "INSERT INTO $TABLE_ACTIVEPERSON SET";
            $strSQL2.= " person_id='$strPersonID'";
            $strSQL2.= ",project_id='$strProjectID'";
            $strSQL2.= ",date='$_date'";
            $strSQL2.= ",hours='$t'";
            $strSQL2.= ",created='".date("Y-m-d")."'";
            $strSQL2.= ",modified='".date("Y-m-d")."'";
            $result2 = dbquery($strSQL2);
            //set_status("Created Field [PersonID=$strPersonID,ProjectID=$strProjectID,Hours=$t,DATE=$_date,J=$j].");
            $somethingupdated=1; /* set to 1 to make us show a success message */
          }
        }
      }
      //echo "<BR>\n";
    }
  }
  if($somethingupdated)set_status("Updates Saved");
}

/******************************************/
/* COMPUTING - NO OUTPUT - END            */
/******************************************/





















/******************************************/
/* COMPUTING - SHOW OUTPUT - START        */
/******************************************/
$TITLE="Edit Hours";
show_header();
show_menu("HOURS");
show_status();
show_error();

?>
<script>
_c=1;_x=2;_p=3;_f=4;_ml=5;_mr=6;
_COPY=0;
_CUT=0;
function dothis(_this,_a,_v)
{
  if(_a==_ml)
  {
      for(i=0;i<23;i++)
      {
        j=i+1;
        _this.form.elements["hour"+_v+"_"+i].value=_this.form.elements["hour"+_v+"_"+j].value+" ";
      }
      _this.form.elements["hour"+_v+"_23"].value='';
  }
  if(_a==_mr)
  {
      for(i=23;i>0;i--)
      {
        j=i-1;
        _this.form.elements["hour"+_v+"_"+i].value=_this.form.elements["hour"+_v+"_"+j].value+" ";
      }
      _this.form.elements["hour"+_v+"_0"].value='';
  }
  if(_a==_f)
  {
      x=23;
      for(i=23;i>0;i--)
      {
        if(_this.form.elements["hour"+_v+"_"+i].value>0)
        {
          x=i;break;
        }
      }
      for(i=x;i<24;i++)
      {
        _this.form.elements["hour"+_v+"_"+i].value=_this.form.elements["hour"+_v+"_"+x].value+" ";
      }
  }
  if(_a==_c)
  {
    _label="c";
    if(_COPY)
    {
      document.getElementById("c"+_COPY).setAttribute("class","action");
    }
    if(_CUT)
    {
      document.getElementById("x"+_CUT).setAttribute("class","action");
      _CUT=0;
    }
    _COPY=_v;
    document.getElementById(_label+_v).setAttribute("class","action2");
  }
  if(_a==_x)
  {
    _label="x";
    if(_CUT)
    {
      document.getElementById("x"+_CUT).setAttribute("class","action");
    }
    if(_COPY)
    {
      document.getElementById("c"+_COPY).setAttribute("class","action");
      _COPY=0;
    }
    _CUT=_v;
    document.getElementById(_label+_v).setAttribute("class","action2");
  }
  if(_a==_p)
  {
    if(_CUT) 
    {
      //alert("CUT FROM "+_CUT+" TO "+_v);
      for(i=0;i<24;i++)
      {
        _this.form.elements["hour"+_v+"_"+i].value=_this.form.elements["hour"+_CUT+"_"+i].value+" ";
        _this.form.elements["hour"+_CUT+"_"+i].value="";
      }
      document.getElementById("x"+_CUT).setAttribute("class","action");
      _CUT=0;
    }
    if(_COPY) 
    {
      //alert("COPY FROM "+_COPY+" TO "+_v);
      for(i=0;i<24;i++)
      {
        _this.form.elements["hour"+_v+"_"+i].value=_this.form.elements["hour"+_COPY+"_"+i].value+" ";
      }
      document.getElementById("c"+_COPY).setAttribute("class","action");
      _COPY=0;
    }
  }
}
</script>
<?


/* SHOW THE PROJECT TO VIEW/EDIT */
if(isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Set Filter" || $strProject || $strPerson)
{
  //$str_project=isset($_FORM['txt_project'])?postedData($_FORM['txt_project']):"";
  //$str_project=$strProject;

  $FilterDepartment=isset($_FORM['txt_department'])?postedData($_FORM['txt_department']):"";

  $editLabel="";
  if($strProject)
  {
    $strSQL1 = "SELECT * FROM $TABLE_PROJECT WHERE id='$strProject'";
    $result1 = dbquery($strSQL1);
    $row1 = mysql_fetch_array($result1);
    $strProjectName=$row1['name'];
    $strPD=$row1['percent_direct'];
    $strF=$row1['future'];
    $strHS=$row1['headcount_scaling'];
    //$strSD=$row1['start_date'];
    //$strED=$row1['end_date'];

    $strSD=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1-$2",$row1['start_date']);
    $START_Year=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1",$row1['start_date']);
    $START_Mon =preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$2",$row1['start_date']);


    $strED=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1-$2",$row1['end_date']);
    $END_Year=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1",$row1['end_date']);
    $END_Mon =preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$2",$row1['end_date']);

    $strED2=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1-$2",$row1['end_date2']);
    $END_Year2=preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$1",$row1['end_date2']);
    $END_Mon2 =preg_replace('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',"$2",$row1['end_date2']);

    //echo "[SY/SM]=$START_Year/$START_Mon<BR>\n";
    //echo "[EY/EM]=$END_Year/$END_Mon<BR>\n";

    $editLabel=$strProjectName;
    if($strF)
    {
      $editLabel.=" (Future Program: $strHS% Scaled)";
    }
    else
    {
      if($strPD==100)
      {
        $editLabel.=" (100% Direct)";
      }
      else if($strPD==0)
      {
        $editLabel.=" (100% Indirect)";
      }
      else
      {
        $editLabel.=" ($strPD% Direct)";
      }
    }
    if($strSD && ! $strED && ! $strED2)
      $editLabel.=" [Starts: $strSD]";
    else if(($strED || $strED2) && ! $strSD)
    {
      if(!$strED2)
        $editLabel.=" [Eng-Ends: $strED]";
      else if(!$strED)
        $editLabel.=" [Sus-Ends: $strED2]";
      else
        $editLabel.=" [Eng-Ends: $strED | Sus-Ends: $strED2]";
    }
    else if(($strED2 || $strED) && $strSD)
    {
      if(!$strED2)
        $editLabel.=" [Spans: $strSD - (Eng)$strED]";
      else if(!$strED)
        $editLabel.=" [Spans: $strSD - (Sus)$strED2]";
      else
        $editLabel.=" [Spans: $strSD - (Eng)$strED (Sus)$strED2]";
    }
    $filterSQL3= " WHERE AP.project_id='$strProject'";
  }
  if($strPerson)
  {
    $strSQL1 = "SELECT * FROM $TABLE_PERSON WHERE id='$strPerson'";
    $result1 = dbquery($strSQL1);
    $row1 = mysql_fetch_array($result1);
    $strPersonName=$row1['lname'].", ".$row1['fname'];
    if(strlen($editLabel))
    {
      $editLabel.=" and ".$strPersonName;
      $filterSQL3.= " AND AP.person_id='$strPerson'";
    }
    else
    {
      $editLabel=$strPersonName;
      $filterSQL3= " WHERE AP.person_id='$strPerson'";
    }
  }
  if(!$strPerson && !$strProject)
  {
    $FilterDepartment=isset($_FORM['txt_department'])?postedData($_FORM['txt_department']):"";
    if($FilterDepartment=='sw')      {$filterSQL3=" WHERE P.in_sw='1'"; $dep="Software";}
    else if($FilterDepartment=='se') {$filterSQL3=" WHERE P.in_se='1'"; $dep="Systems";}
    else if($FilterDepartment=='de') {$filterSQL3=" WHERE P.in_de='1'"; $dep="Design Eng";}
    else if($FilterDepartment=='eo') {$filterSQL3=" WHERE P.in_eo='1'"; $dep="Eng Ops";}
    else if($FilterDepartment=='ts') {$filterSQL3=" WHERE P.in_ts='1'"; $dep="Test Solutions";}
    else if($FilterDepartment=='en') {$filterSQL3=" WHERE P.in_en='1'"; $dep="Bob's Staff";}
    else
    {
      /* all that this person can access */
      $strSQL4 = "SELECT * FROM $TABLE_PERSON WHERE id='".$_SESSION['userID']."'";
      $result4 = dbquery($strSQL4);
      $row4 = mysql_fetch_array($result4);
      $filterSQL3=" WHERE (";
      $or="";
      $dep="";
      if($row4['access_sw']) {$filterSQL3.=" $or P.in_sw='1'";$or="OR"; $dep.=" SW";}
      if($row4['access_se']) {$filterSQL3.=" $or P.in_se='1'";$or="OR"; $dep.=" SE";}
      if($row4['access_de']) {$filterSQL3.=" $or P.in_de='1'";$or="OR"; $dep.=" DE";}
      if($row4['access_ts']) {$filterSQL3.=" $or P.in_ts='1'";$or="OR"; $dep.=" TS";}
      if($row4['access_en']) {$filterSQL3.=" $or P.in_en='1'";$or="OR"; $dep.=" EN";}
      if($row4['access_eo']) {$filterSQL3.=" $or P.in_eo='1'";$or="OR"; $dep.=" EO";}
      $filterSQL3.=")";
    }
    $editLabel="Department: $dep";
  }

?>
<BR>
<form action="<?=$PAGE_HOURS;?>" method=POST>
<input type=hidden name=txt_project value="<?=$strProject;?>">
<input type=hidden name=txt_person value="<?=$strPerson;?>">
<input type=hidden name=txt_department value="<?=$FilterDepartment;?>">
<fieldset>
<legend><b>Edit Project Tasks: <?=$editLabel;?></b></legend>
<table class=list>
<tr>
  <th>Project</th>
  <th>Person</th>

<?
/* print the column header for this year and next year - 24 months */
$cy=date("y"); /* current year */
$ny=$cy+1; /* next year */
$months=Array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
$y1="";
$y2="";
//echo "[SY/SM]=$START_Year/$START_Mon<BR>\n";
//echo "[EY/EM]=$END_Year/$END_Mon<BR>\n";
$start_month=strtotime($START_Mon."/01/".$START_Year);
$end_month  =strtotime($END_Mon."/01/".$END_Year);
$end_month2  =strtotime($END_Mon2."/01/".$END_Year2);
foreach ($months as $i=>$v)
{
  $this_month1=strtotime($v." 20".$cy);
  $warn="";
  if($start_month<=$this_month1 && $this_month1 <=$end_month) $warn="warn2";
  else if($this_month1 > $end_month && $this_month1 <=$end_month2) $warn="warn3";
  $y1.="<th class='$warn'>".$v."-".$cy."</th>";
  $this_month2=strtotime($v." 20".$ny);
  $warn="";
  if($start_month<=$this_month2 && $this_month2 <=$end_month) $warn="warn2";
  else if($this_month1 > $end_month && $this_month1 <=$end_month2) $warn="warn3";
  $y2.="<th class='$warn'>".$v."-".$ny."</th>";
  //echo "[$v $cy/$ny] = [SM] $start_month [EM] $end_month [TM1] $this_month1 [TM2] $this_month2<BR>\n";

}
echo $y1.$y2;

echo "<th>Total-$cy</th>";
echo "<th>Total-$ny</th>";
echo "<th>Action</th>";
?>

</tr>
<?
/* END OF THE HEADER */


/* look to see if we should filter by the department access */
if($FilterDepartment=='sw')      $filterSQL3.=" AND in_sw='1'";
else if($FilterDepartment=='se') $filterSQL3.=" AND in_se='1'";
else if($FilterDepartment=='de') $filterSQL3.=" AND in_de='1'";
else if($FilterDepartment=='eo') $filterSQL3.=" AND in_eo='1'";
else if($FilterDepartment=='ts') $filterSQL3.=" AND in_ts='1'";
else if($FilterDepartment=='en') $filterSQL3.=" AND in_en='1'";
else
{
/* all that this person can access */
$strSQL4 = "SELECT * FROM $TABLE_PERSON WHERE id='".$_SESSION['userID']."'";
$result4 = dbquery($strSQL4);
$row4 = mysql_fetch_array($result4);
$filterSQL3.=" AND (";
$or="";
if($row4['access_sw']) {$filterSQL3.=" $or in_sw='1'";$or="OR";}
if($row4['access_se']) {$filterSQL3.=" $or in_se='1'";$or="OR";}
if($row4['access_de']) {$filterSQL3.=" $or in_de='1'";$or="OR";}
if($row4['access_ts']) {$filterSQL3.=" $or in_ts='1'";$or="OR";}
if($row4['access_en']) {$filterSQL3.=" $or in_en='1'";$or="OR";}
if($row4['access_eo']) {$filterSQL3.=" $or in_eo='1'";$or="OR";}
$filterSQL3.=")";

}




/* PUT THE DATA FROM THE DB HERE - START */
/* in one query - find all hours for this project/person filter. */
$strSQL3 = "SELECT AP.hours, AP.date, P.lname, P.fname, P.id as person_id, R.name, R.id as project_id";
$strSQL3.= " FROM $TABLE_ACTIVEPERSON AS AP ";
$strSQL3.= " LEFT JOIN $TABLE_PERSON AS P ON P.id=AP.person_id";
$strSQL3.= " LEFT JOIN $TABLE_PROJECT AS R ON R.id=AP.project_id";
$strSQL3.= " ".$filterSQL3;
$strSQL3.= " AND (EXTRACT(YEAR FROM AP.date) >= 20$cy AND EXTRACT(YEAR FROM AP.date) <= 20$ny)";
$strSQL3.= " ORDER BY R.name ASC, lname ASC, fname ASC, date ASC";
//$strSQL3.= " ORDER BY project_id ASC, lname ASC, fname ASC, date ASC";
//echo "DB<dir>$strSQL3</dir>";
$result3 = dbquery($strSQL3);

//set_todo("add all people to this list if the filter for people is N/A");

$Total_CY=0; /* current year total */
$Total_NY=0; /* next year total */
$lastPerson=0;
$lastProject=0;
$startedRow=0; /* only set after first row started */
resetRowColor();
$nextYear=date("Y")+1;
$total_rows=0;
$TotalY1=0;
$TotalY2=0;
$MonthlyTotals=Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
$OUTPUT="";
while($row3 = mysql_fetch_array($result3))
{
  $newRow=0; /* set when we want to start a new row */
  if($lastPerson!=$row3['person_id'])
  {
    //echo "New person: ".$row3['lname'].", ".$row3['fname']."<BR>\n";
    $lastPerson=$row3['person_id'];
    $newRow=1;
  }
  if($lastProject!=$row3['project_id'])
  {
    //echo "New project: ".$row3['name']."<BR>\n";
    $lastProject=$row3['project_id'];
    $newRow=1;
  }
  if($newRow)
  {
    if($startedRow)
    {
      $max=500;
      $count=0;
      //echo "LY($lastYear) NY($nextYear) LM($lastMonth) M($m)<BR>\n";
      while ($lastYear<$nextYear || ($lastYear==$nextYear && $lastMonth<=12))
      {
        $t="&nbsp;";
        //$t="$lastYear-$lastMonth";
        if($count++>$max){echo "MAX";exit;} /* in case of error - dont run forever */
        //echo "<td>$t</td>";
        $tmp1=$lastMonth+1; $tmp2=$lastYear; if($tmp1>12){$tmp1=1;$tmp2++;}
        $this_month1=strtotime($tmp1."/01/".$tmp2);
        $warn="";
        if($start_month<=$this_month1 && $this_month1 <=$end_month) $warn="warn2";
        else if($this_month1 > $end_month && $this_month1 <=$end_month2) $warn="warn3";
        $OUTPUT.= "<td class='$warn'><input class='hours' type=text size=1 name='hour".$total_rows."_".$spot."' value=''></td>";
        $lastMonth++; if($lastMonth>12){$lastMonth=1;$lastYear++;}
        $spot++;
      }
      $OUTPUT.= "<td align=center>".$TotalY1."</td>";
      $OUTPUT.= "<td align=center>".$TotalY2."</td>";
      $OUTPUT.= "<td>";
      $OUTPUT.= "<input class=action id='c$total_rows' type=button value='C' onclick='dothis(this,_c,$total_rows)'>";
      $OUTPUT.= "<input class=action id='x$total_rows' type=button value='X' onclick='dothis(this,_x,$total_rows)'>";
      $OUTPUT.= "<input class=action id='p$total_rows' type=button value='P' onclick='dothis(this,_p,$total_rows)'>";
      $OUTPUT.= "<input class=action id='p$total_rows' type=button value='F' onclick='dothis(this,_f,$total_rows)'>";
      $OUTPUT.= "<input class=action id='p$total_rows' type=button value='<' onclick='dothis(this,_ml,$total_rows)'>";
      $OUTPUT.= "<input class=action id='p$total_rows' type=button value='>' onclick='dothis(this,_mr,$total_rows)'>";
      $OUTPUT.= "</td>";
      $OUTPUT.= "</tr>";
    }
    $TotalY1=0;
    $TotalY2=0;
    $spot=0;
    $total_rows++;
    $OUTPUT.= "<input type=hidden name=row".$total_rows." value='".$row3['person_id']."-".$row3['project_id']."'>";
    $OUTPUT.= "<tr class='".alternateRowColor()."'>";
    $OUTPUT.= "<td><a href='".$PAGE_HOURS."?txt_project=".$row3['project_id']."'>".$row3['name']."</a></td>";
    $OUTPUT.= "<td><a href='".$PAGE_HOURS."?txt_person=".$row3['person_id']."'>".$row3['lname'].", ".$row3['fname']."</a></td>";
    $lastYear=date('Y');
    $lastMonth=1;
  }
  $startedRow=1; /* done at least one row */
  list($y,$m,$d)=split("-",$row3['date']);
  $max=500;
  $count=0;
  //echo "LR($lastYear) Y($y) LM($lastMonth) M($m)<BR>\n";
  while ($lastYear<$y || ($lastYear==$y && $lastMonth<$m))
  {
    //$t="&nbsp;";
    if($count++>$max){echo "MAX";exit;} /* in case of error - dont run forever */
    $tmp1=$lastMonth; $tmp2=$lastYear; if($tmp1>12){$tmp1=1;$tmp2++;}
    $this_month1=strtotime($tmp1."/01/".$tmp2);
    $warn="";
    if($start_month<=$this_month1 && $this_month1 <=$end_month) $warn="warn2";
    else if($this_month1 > $end_month && $this_month1 <=$end_month2) $warn="warn3";
    $OUTPUT.= "<td class='$warn'><input class='hours' type=text size=1 name='hour".$total_rows."_".$spot."' value=''></td>";
    //if($y==date("Y"))$TotalY1+=$t;else $TotalY2+=$t;
    $lastMonth++; if($lastMonth>12){$lastMonth=1;$lastYear++;}
    $spot++;
  }
  $lastMonth=$m;
  $lastYear=$y;
  $t=$row3['hours'];
  $tmp1=$lastMonth; $tmp2=$lastYear; if($tmp1>12){$tmp1=1;$tmp2++;}
  $this_month1=strtotime($tmp1."/01/".$tmp2);
  $warn="";
  if($start_month<=$this_month1 && $this_month1 <=$end_month) $warn="warn2";
  else if($this_month1 > $end_month && $this_month1 <=$end_month2) $warn="warn3";
  $OUTPUT.= "<td class='$warn'><input class=hours type=text size=1 name='hour".$total_rows."_".$spot."' value='$t'></td>";
  $MonthlyTotals[$spot]+=$t;
  if((int)$y==(int)date("Y"))
  {
    //echo " Y1 ";
    $TotalY1=(int)$TotalY1+(int)$t;
  }
  else
  {
    //echo " Y2 ";
    $TotalY2=(int)$TotalY2+(int)$t;
  }
  //echo "Y($y) dateY(".date("Y").") HOURS($t) TY1($TotalY1) TY2($TotalY2)<BR>\n";
  $lastMonth++; if($lastMonth>12){$lastMonth=1;$lastYear++;}
  $spot++;
}
$max=500;
$count=0;
if(!$lastYear)$lastYear=date("Y");
if(!$lastMonth)$lastMonth=0;
if(!$spot)$spot=0;
if(!$TotalY1)$TotalY1=0;
if(!$TotalY2)$TotalY2=0;
if(!$startedRow)
{
  //$strProject - $strPerson - $strProjectname - $strPersonName
  $total_rows++;
  $OUTPUT.= "<input type=hidden name=row".$total_rows." value='".$strPerson."-".$strProject."'>";
  $OUTPUT.= "<tr class='".alternateRowColor()."'>";
  $OUTPUT.= "<td>".$strProjectName."</td>";
  $OUTPUT.= "<td>".$strPersonName."</td>";
}
//echo "LY($lastYear) NY($nextYear) LM($lastMonth) M(12)<BR>\n";
while ($lastYear<$nextYear || ($lastYear==$nextYear && $lastMonth<=12))
{
  //$t="&nbsp;";
  if($count++>$max){echo "MAX";exit;} /* in case of error - dont run forever */
  //if($lastYear<$nextYear || $lastMonth<23)
  if($spot<24)
  {
    $tmp1=$lastMonth; $tmp2=$lastYear; if($tmp1>12){$tmp1=1;$tmp2++;}
    $this_month1=strtotime($tmp1."/01/".$tmp2);
    //echo "LY($lastYear) NY($nextYear) LM($lastMonth) SPOT($spot) TM[$this_month1] SM[$start_month] EM[$end_month]<BR>\n";
    $warn="";
    if($start_month<=$this_month1 && $this_month1 <=$end_month) $warn="warn2";
    else if($this_month1 > $end_month && $this_month1 <=$end_month2) $warn="warn3";
    $OUTPUT.= "<td class='$warn'><input class='hours' type=text size=1 name='hour".$total_rows."_".$spot."' value=''></td>";
  }
  $lastMonth++; if($lastMonth>12){$lastMonth=1;$lastYear++;}
  $spot++;
}
$OUTPUT.= "<td align=center>$TotalY1</td>";
$OUTPUT.= "<td align=center>$TotalY2</td>";
$OUTPUT.= "<td>";
$OUTPUT.= "<input class=action id='c$total_rows' type=button value='C' onclick='dothis(this,_c,$total_rows)'>";
$OUTPUT.= "<input class=action id='x$total_rows' type=button value='X' onclick='dothis(this,_x,$total_rows)'>";
$OUTPUT.= "<input class=action id='p$total_rows' type=button value='P' onclick='dothis(this,_p,$total_rows)'>";
$OUTPUT.= "<input class=action id='p$total_rows' type=button value='F' onclick='dothis(this,_f,$total_rows)'>";
$OUTPUT.= "<input class=action id='p$total_rows' type=button value='<' onclick='dothis(this,_ml,$total_rows)'>";
$OUTPUT.= "<input class=action id='p$total_rows' type=button value='>' onclick='dothis(this,_mr,$total_rows)'>";
$OUTPUT.= "</td>";
$OUTPUT.= "</tr>";
$OUTPUT2="";
$OUTPUT2.= "<tr><td colspan=2 align=right><b>TOTALS:</b></td>";
$yt1=0;
$yt2=0;
for($i=0;$i<24;$i++)
{
  $OUTPUT2.= "<td align=center>".$MonthlyTotals[$i]."</td>";
  if($i<12)
  {
    $yt1+=$MonthlyTotals[$i];
  }
  else
  {
    $yt2+=$MonthlyTotals[$i];
  }
}
$OUTPUT2.= "<td align=center>$yt1</td>";
$OUTPUT2.= "<td align=center>$yt2</td>";
$OUTPUT2.= "</tr>";
echo $OUTPUT2;
echo $OUTPUT;
echo $OUTPUT2;

/* PUT THE DATA FROM THE DB HERE - END   */














/* PUT THE BLANK ROW - TO ADD ANOTHER TASK HERE */
?>

</table>
<BR>
<center>Total Rows(<?=$total_rows;?>) -- <input class=but type=submit value='Save Changes' name='txt_action'></center>
</fieldset>
<input type=hidden name=total_rows value="<?=$total_rows;?>">
</form>



<?
} /* end of showing a project's tasks/wbs */








/* do the dbquery outside the select list so the debug will appear */
$strSQL2 = "SELECT * FROM $TABLE_PROJECT ORDER BY name ASC";
$result2 = dbquery($strSQL2);
$strSQL3 = "SELECT * FROM $TABLE_PERSON ORDER BY lname ASC,fname ASC, id ASC";
$result3 = dbquery($strSQL3);
?>
<BR>
<form action="<?=$PAGE_HOURS;?>" method=POST>
<fieldset>
<legend><b>Select Project or Person</b></legend>
<table align=center>
<tr><td align=right>Pick Project: 
<input type=button value="(-)" onclick="this.form.txt_project.selectedIndex=0;">
</td>
<td><select name=txt_project><option value=''>-N/A-</option>
<?
while($row2 = mysql_fetch_array($result2))
{
  $t1=$row2['id'];
  $t2=$row2['name'];
  $SEL="";if($t1==$strProject) $SEL="SELECTED";
  echo "<option $SEL value='$t1'>$t2</option>\n";
}
?>
</select></td></tr>
<tr><td align=right>Pick Person:
<input type=button value="(-)" onclick="this.form.txt_person.selectedIndex=0;">
 </td>
<td><select name=txt_person><option value=''>-N/A-</option>
<?
while($row3 = mysql_fetch_array($result3))
{
  $t1=$row3['id'];
  $t2=$row3['lname'].", ".$row3['fname'];
  $SEL="";if($t1==$strPerson) $SEL="SELECTED";
  echo "<option $SEL value='$t1'>$t2</option>\n";
}
?>
</select></td></tr>
<tr><td align=right>Department Filter
<input type=button value="(-)" onclick="this.form.txt_department.selectedIndex=0;">
</td><td>
<?
$strSQL4 = "SELECT * FROM $TABLE_PERSON WHERE id='".$_SESSION['userID']."'";
$result4 = dbquery($strSQL4);
$row4 = mysql_fetch_array($result4);
if($row4['access_sw'])$access_sw=1;else $access_sw=0;
if($row4['access_se'])$access_se=1;else $access_se=0;
if($row4['access_de'])$access_de=1;else $access_de=0;
if($row4['access_ts'])$access_ts=1;else $access_ts=0;
if($row4['access_en'])$access_en=1;else $access_en=0;
if($row4['access_eo'])$access_eo=1;else $access_eo=0;
$count=$access_sw+$access_se+$access_de+$access_ts+$access_en+$access_eo;
if($count>1)$show_all=1;
?>
<select name=txt_department>
<?
if($FilterDepartment=='sw')$pick_sw="SELECTED"; else $pick_sw="";
if($FilterDepartment=='se')$pick_se="SELECTED"; else $pick_se="";
if($FilterDepartment=='de')$pick_de="SELECTED"; else $pick_de="";
if($FilterDepartment=='eo')$pick_eo="SELECTED"; else $pick_eo="";
if($FilterDepartment=='ts')$pick_ts="SELECTED"; else $pick_ts="";
if($FilterDepartment=='en')$pick_en="SELECTED"; else $pick_en="";
if($show_all)  { echo "<option value='all'>All</option>"; }
if($access_sw) { echo "<option $pick_sw value='sw'>Software</option>"; }
if($access_se) { echo "<option $pick_se value='se'>Systems</option>"; }
if($access_de) { echo "<option $pick_de value='de'>Design Eng</option>"; }
if($access_eo) { echo "<option $pick_eo value='eo'>Eng Ops</option>"; }
if($access_ts) { echo "<option $pick_ts value='ts'>Test Solutions</option>"; }
if($access_en) { echo "<option $pick_en value='en'>Bob's Staff</option>"; }
?>
</select>
</td></tr>
<tr><td align=center colspan=2><input class=but type=submit value='Set Filter' name='txt_action'></td></tr>
</table>
</fieldset>
</form>

<?
show_footer();


/******************************************/
/* COMPUTING - SHOW OUTPUT - END          */
/******************************************/


?>
