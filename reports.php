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

$HACK_CHECK=1; Include("config/global.inc.php");
debug(10,"Loading File: tasks.php");
checkPermissions($SESSION_TIMEOUT); // if not logged in, or session has timed out...

$strGraph =isset($_FORM['graph'])?postedData($_FORM['graph']):0;
$strParm =isset($_FORM['parm'])?postedData($_FORM['parm']):0;
$strData =isset($_FORM[$strParm])?postedData($_FORM[$strParm]):0;
$sMonth = isset($_FORM['sMonth'])?postedData($_FORM['sMonth']):1;
$sYear = isset($_FORM['sYear'])?postedData($_FORM['sYear']):date("Y");
$eMonth = isset($_FORM['eMonth'])?postedData($_FORM['eMonth']):12;
$eYear = isset($_FORM['eYear'])?postedData($_FORM['eYear']):date("Y");
//$strData2 =isset($_FORM['txt_person'])?postedData($_FORM['txt_person']):0;
//$t="txt_person";
//$strData3 =isset($_FORM[$t])?postedData($_FORM[$t]):0;
//echo "GRAPH($strGraph) PARM($strParm) DATA($strData) DATA2($strData2) DATA3($strData3)<BR>";

$parms="";
if(!$strGraph)
{
  //$strGraph="levels160";
  //$strGraph="dirindfut";
  //$strGraph="dirindfuthighlowscaleabsent";
  $strGraph=0;
}
else
{
  $parms="?".$strParm."=".$strData."&sMonth=$sMonth&sYear=$sYear&eMonth=$eMonth&eYear=$eYear";
}
if(!file_exists("graph-".$strGraph.".php"))
{
  show_error("Error: Graph selected does not exist ($strGraph)");
}

$TITLE="Reports";
show_header();
show_menu("REPORTS");
?>
<script>
  var sMonth = "<?=$sMonth;?>";
  var sYear = "<?=$sYear;?>";
  var eMonth = "<?=$eMonth;?>";
  var eYear = "<?=$eYear;?>";

function updateLinks()
{
  sMonth = document.getElementById("sMonth").value;
  sYear = document.getElementById("sYear").value;
  eMonth = document.getElementById("eMonth").value;
  eYear = document.getElementById("eYear").value;

  if(eYear < sYear)
  {
     alert("Invalid Start/End Date Combination!");
  }else if (eYear == sYear && eMonth < sMonth)
  {
     alert("Invalid Start/End Date Combination!");
  }
}

function show(url)
{
   url += "&sMonth="+sMonth+"&sYear="+sYear+"&eMonth="+eMonth+"&eYear="+eYear;
   window.location = url;
}
function show2()
{
   if(window.location.href == "http://swdevtesting/everyman/reports.php")
   {
      var url =  "http://swdevtesting/everyman/reports.php"+"?graph=levels&sMonth="+sMonth+"&sYear="+sYear+"&eMonth="+eMonth+"&eYear="+eYear;
   }else
   {
      var cur = window.location.href;
      cur = cur.split("&sMonth");
      var url = cur[0] + "&sMonth="+sMonth+"&sYear="+sYear+"&eMonth="+eMonth+"&eYear="+eYear;
   }
   window.location = url;
}

</script>
<?

if(!$strGraph)
{
?>
<BR>
<table cellpadding=0 cellspacing=0>
<tr>
<th width=130 align=center>#1: PW Scaled-A</th>
<th width=130 align=center>#2: PW Scaled-B</th>
<th width=130 align=center>#3: PW Scale/Absences</th>
<th width=130 align=center>#4: PW Scale>=60%</th>
<th width=130 align=center>#5: PW No Scale</th>
</tr>
<tr>
<td align=center><a href="javascript:show('<?=$PAGE_REPORT;?>?graph=dirindfut');"><img border=0 width=130 src=graph-dirindfut.php<?=$parms;?>></a></td>
<td align=center><a href="javascript:show('<?=$PAGE_REPORT;?>?graph=dirindfuthighlowscale');"><img border=0 width=130 src=graph-dirindfuthighlowscale.php<?=$parms;?>></a></td>
<td align=center><a href="javascript:show('<?=$PAGE_REPORT;?>?graph=dirindfuthighlowscaleabsent');"><img border=0 width=130 src=graph-dirindfuthighlowscaleabsent.php<?=$parms;?>></a></td>
<td align=center><a href="javascript:show('<?=$PAGE_REPORT;?>?graph=dirindfut60');"><img border=0 width=130 src=graph-dirindfut60.php<?=$parms;?>></a></td>
<td align=center><a href="javascript:show('<?=$PAGE_REPORT;?>?graph=dirindfuthighlow');"><img border=0 width=130 src=graph-dirindfuthighlow.php<?=$parms;?>></a></td>
</tr>
</table>
<BR>
<table cellpadding=0 cellspacing=0>
<tr>
<th width=130 align=center>#6: Test Solutions</th>
<th width=130 align=center>#7: Software</th>
<th width=130 align=center>#8: Design Eng</th>
<th width=130 align=center>#9: Eng Ops</th>
<th width=130 align=center>#10: Sys Eng</th>
<th width=130 align=center>#11: Bob's Staff</th>
</tr>
<tr>
<td align=center><a href= "javascript:show('<?=$PAGE_REPORT;?>?graph=bytsdept');"><img border=0 width=130 src=graph-bytsdept.php<?=$parms;?>></a></td>
<td align=center><a href= "javascript:show('<?=$PAGE_REPORT;?>?graph=byswdept');"><img border=0 width=130 src=graph-byswdept.php<?=$parms;?>></a></td>
<td align=center><a href= "javascript:show('<?=$PAGE_REPORT;?>?graph=bydedept');"><img border=0 width=130 src=graph-bydedept.php<?=$parms;?>></a></td>
<td align=center><a href= "javascript:show('<?=$PAGE_REPORT;?>?graph=byopsdept');"><img border=0 width=130 src=graph-byopsdept.php<?=$parms;?>></a></td>
<td align=center><a href= "javascript:show('<?=$PAGE_REPORT;?>?graph=bysysdept');"><img border=0 width=130 src=graph-bysysdept.php<?=$parms;?>></a></td>
<td align=center><a href= "javascript:show('<?=$PAGE_REPORT;?>?graph=byendept');"><img border=0 width=130 src=graph-byendept.php<?=$parms;?>></a></td>
</tr>
</table>
<?
}
else
{
  echo "<img border=0 src=graph-".$strGraph.".php".$parms.">";
}
?>
<br><br>
<table>
   <tr><td colspan="2" align="center">Select Start/End Month and Year</td></tr>
   <tr><th align="center">Start</th><th align="center">End</th>
   <tr><td align="center"><select name="sMonth" id="sMonth" onchange="updateLinks();">
              <option value="01" <?if($sMonth=="01")echo "SELECTED";?>>Jan</option>
              <option value="02" <?if($sMonth=="02")echo "SELECTED";?>>Feb</option>
              <option value="03" <?if($sMonth=="03")echo "SELECTED";?>>Mar</option>
              <option value="04" <?if($sMonth=="04")echo "SELECTED";?>>Apr</option>
              <option value="05" <?if($sMonth=="05")echo "SELECTED";?>>May</option>
              <option value="06" <?if($sMonth=="06")echo "SELECTED";?>>Jun</option>
              <option value="07" <?if($sMonth=="07")echo "SELECTED";?>>Jul</option>
              <option value="08" <?if($sMonth=="08")echo "SELECTED";?>>Aug</option>
              <option value="09" <?if($sMonth=="09")echo "SELECTED";?>>Sep</option>
              <option value="10" <?if($sMonth=="10")echo "SELECTED";?>>Oct</option>
              <option value="11" <?if($sMonth=="11")echo "SELECTED";?>>Nov</option>
              <option value="12" <?if($sMonth=="12")echo "SELECTED";?>>Dec</option>
           </select>&nbsp;
           <select name="sYear" id="sYear" onchange="updateLinks();">
<?
           for($i=2009; $i<date("Y",time())+3; $i++){
              $sel = ($i == $sYear) ? "SELECTED" : "";
              echo "<option value=\"$i\" $sel>$i</option>";
           }
?>
           </select>
        </td>
        <td align="center"><select name="eMonth" id="eMonth" onchange="updateLinks();">
              <option value="01" <?if($eMonth=="01")echo "SELECTED";?>>Jan</option>
              <option value="02" <?if($eMonth=="02")echo "SELECTED";?>>Feb</option>
              <option value="03" <?if($eMonth=="03")echo "SELECTED";?>>Mar</option>
              <option value="04" <?if($eMonth=="04")echo "SELECTED";?>>Apr</option>
              <option value="05" <?if($eMonth=="05")echo "SELECTED";?>>May</option>
              <option value="06" <?if($eMonth=="06")echo "SELECTED";?>>Jun</option>
              <option value="07" <?if($eMonth=="07")echo "SELECTED";?>>Jul</option>
              <option value="08" <?if($eMonth=="08")echo "SELECTED";?>>Aug</option>
              <option value="09" <?if($eMonth=="09")echo "SELECTED";?>>Sep</option>
              <option value="10" <?if($eMonth=="10")echo "SELECTED";?>>Oct</option>
              <option value="11" <?if($eMonth=="11")echo "SELECTED";?>>Nov</option>
              <option value="12" <?if($eMonth=="12")echo "SELECTED";?>>Dec</option>
           </select>&nbsp;
           <select name="eYear" id="eYear" onchange="updateLinks();">
<?
           for($i=2009; $i<date("Y",time())+3; $i++){
              $sel = ($i == $eYear) ? "SELECTED" : "";
              echo "<option value=\"$i\" $sel>$i</option>";
           }
?>
           </select>
        </td>
    </tr>
    <tr><td colspan="2" align="center"><input type="button" class="but" value="Refresh Graph" onclick="show2();"></td></tr>
</table>
<?
echo "<BR><BR><ul>";
echo "<li>Quick Links to Graphs:";


echo "<ul><li>#1: <a href='#' onclick=\"show('$PAGE_REPORT?graph=dirindfut');\">Future reduced by each program PWIN</a></li>";
echo "<li>#2: <a href='#' onclick=\"show('$PAGE_REPORT?graph=dirindfuthighlowscale');\">High-Low(scaled by PWIN)</a></li>";
echo "<li>#3: <a href='#' onclick=\"show('$PAGE_REPORT?graph=dirindfuthighlowscaleabsent');\">High-Low(scaled by PWIN) Absence Shaded - in work</a></li>";
echo "<li>#4: <a href='#' onclick=\"show('$PAGE_REPORT?graph=dirindfut60');\">Future 100% of program if PWIN > 60% (0% of program PWIN < 60%)</a></li>";
echo "<li>#5: <a href='#' onclick=\"show('$PAGE_REPORT?graph=dirindfuthighlow');\">High-Low(not scaled)</a></li>";
echo "</ul></li>";


echo "<li>To Veiw by Department:<ul>";
echo "<li>#6: <a href= '#' onclick=\"show('$PAGE_REPORT?graph=bytsdept');\">Test Solutions Department</a>";
echo "<li>#7: <a href= '#' onclick=\"show('$PAGE_REPORT?graph=byswdept');\">Software Engineering Department</a>";
echo "<li>#8: <a href= '#' onclick=\"show('$PAGE_REPORT?graph=bydedept');\">Design Engineering Department</a>";
echo "<li>#9: <a href= '#' onclick=\"show('$PAGE_REPORT?graph=byopsdept');\">Engineering Operations Department</a>";
echo "<li>#10: <a href= '#' onclick=\"show('$PAGE_REPORT?graph=bysysdept');\">Systems Engineering Department</a>";
echo "<li>#11: <a href= '#' onclick=\"show('$PAGE_REPORT?graph=byendept');\">Bob's Staff</a>";
echo "</ul></li>";




echo "<li>Old Graphs:<ul>";
echo "<li><a href='#' onclick=\"show('$PAGE_REPORT?graph=levels');\">Total Headcount</a>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=levels160');\">(160/month)</a></li>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=levels147');\">(147/month)</a></li>";
echo "<li><a href='#' onclick=\"show('$PAGE_REPORT?graph=projtotal');\">By Project</a>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=projtotal-nolabels');\">(no labels)</a>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=projtotal160');\">(160/month)</a>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=projtotalinterns');\">(160/month - interns separated)</a>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=projtotal147');\">(147/month)</a></li>";
echo "<li><a href='#' onclick=\"show('$PAGE_REPORT?graph=suplevels&parm=txtaccum&txtaccum=0');\">By Supervisors</a>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=suplevels160&parm=txtaccum&txtaccum=0');\">(160/month)</a>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=suplevels147&parm=txtaccum&txtaccum=0');\">(147/month)</a></li>";
echo "<li><a href='#' onclick=\"show('$PAGE_REPORT?graph=suplevels&parm=txtaccum&txtaccum=1');\">By Supervisors Accum</a>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=suplevels160&parm=txtaccum&txtaccum=1');\">(160/month)</a>";
echo " - <a href='#' onclick=\"show('$PAGE_REPORT?graph=suplevels147&parm=txtaccum&txtaccum=1');\">(147/month)</a></li>";
echo "</ul></li>";



$strSQL0 = "SELECT";
$strSQL0.= " S.lname, S.fname, S.id";
$strSQL0.= " FROM $TABLE_PERSON AS P";
$strSQL0.= " LEFT JOIN $TABLE_PERSON AS S ON P.supervisor_id=S.id";
//$strSQL0.= " ORDER BY P.lname ASC, P.fname ASC";
$strSQL0.= " GROUP BY S.id";
$result0 = dbquery($strSQL0);
echo "<BR><BR>Click to see a graph of those directly reporting to these supervisors:<ul>";
while($row0 = mysql_fetch_array($result0))
{
  if($row0['id'])
  {
    //echo "<li><a href='$PAGE_GRAPHBYSUP?txt_person=".$row0['id']."'>".$row0['lname'].", ".$row0['fname']."</a></li>";
    echo "<li><a href='#' onclick=\"show('$PAGE_REPORT?graph=bysup&parm=txtperson&txtperson=".$row0['id']."');\">".$row0['lname'].", ".$row0['fname']."</a></li>";
  }
}
echo "</ul>";



$strSQL0 = "SELECT";
$strSQL0.= " P.name, P.id";
$strSQL0.= " FROM $TABLE_PROJECT AS P";
$strSQL0.= " ORDER BY P.name ASC";
//$strSQL0.= " GROUP BY S.id";
$result0 = dbquery($strSQL0);
echo "<BR><BR>Click to see a graph of those working on that program:<ul>";
while($row0 = mysql_fetch_array($result0))
{
  if($row0['id'])
  {
    //echo "<li><a href='$PAGE_GRAPHBYSUP?txt_person=".$row0['id']."'>".$row0['lname'].", ".$row0['fname']."</a></li>";
    echo "<li><a href='#' onclick=\"show('$PAGE_REPORT?graph=byproj&parm=txtproject&txtproject=".$row0['id']."');\">".$row0['name']."</a></li>";
  }
}
echo "</ul>";


show_footer();
?>
