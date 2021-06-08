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


$strSQL4 = "SELECT * FROM $TABLE_PERSON WHERE id='".$_SESSION['userID']."'";
$result4 = dbquery($strSQL4);
$row4 = mysql_fetch_array($result4);
if(!$row4['is_admin']) 
{
$TITLE="People Editing";
show_header();
show_menu("PEOPLE");
set_error("Only database admins can access this page.");
show_error();
exit;
}

$SHOW_PASSWORD=0; /* to enable set to 1 */




/******************************************/
/* COMPUTING - NO OUTPUT - START          */
/******************************************/
$strUsername = "";
$strFirst    = "";
$strLast     = "";
$strPassword = "";
if(isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Create")
{
  $strUsername = postedData($_FORM['txt_username']);
  $strFirst    = postedData($_FORM['txt_first']);
  $strLast     = postedData($_FORM['txt_last']);
  $strPassword = postedData($_FORM['txt_password']);
  $strEmployeeType = postedData($_FORM['txt_employeetype']);
  $strSupervisor = postedData($_FORM['txt_supervisor']);
  $strInSW = postedData($_FORM['txt_in_sw']);
  $strInSE = postedData($_FORM['txt_in_se']);
  $strInDE = postedData($_FORM['txt_in_de']);
  $strInEO = postedData($_FORM['txt_in_eo']);
  $strInTS = postedData($_FORM['txt_in_ts']);
  $strInEN = postedData($_FORM['txt_in_en']);
  $strAccessSW = postedData($_FORM['txt_access_sw']);
  $strAccessSE = postedData($_FORM['txt_access_se']);
  $strAccessDE = postedData($_FORM['txt_access_de']);
  $strAccessEO = postedData($_FORM['txt_access_eo']);
  $strAccessTS = postedData($_FORM['txt_access_ts']);
  $strAccessEN = postedData($_FORM['txt_access_en']);
  $strCanLogin = postedData($_FORM['txt_canlogin']);
  $strIsAdmin  = postedData($_FORM['txt_isadmin']);
  $strProgAdmin = postedData($_FORM['txt_progadmin']);
  debug(3,"CREATE ACTION POSTED (U:$strUsername, F:$strFirst, L:$strLast, P:$strPassword, E:$strEmployeeType, S:$strSupervisor)");
  $err=0;
  if(!strlen($strUsername)){$err=1;set_error("Username must be defined.");}
  if(!strlen($strFirst)   ){$err=1;set_error("First Name must be defined.");}
  if(!strlen($strLast)    ){$err=1;set_error("Last Name must be defined.");}
  if(!$err)
  {
    /* find out if the username already exists */
    $strSQL0 = "SELECT P.username FROM $TABLE_PERSON AS P WHERE P.username='$strUsername'";
    $result0 = dbquery($strSQL0);
    $row0 = mysql_fetch_array($result0);
    if($row0 && $row0['username'])
    {
      set_error("You can not create another account with the same username ($strUsername)");
    }
    else
    {
      $encPassword=encryptData($strPassword);
      $strSQL1 = "INSERT INTO $TABLE_PERSON SET";
      $strSQL1.= " username='$strUsername'";
      $strSQL1.= ",fname='$strFirst'";
      $strSQL1.= ",lname='$strLast'";
      $strSQL1.= ",supervisor_id='$strSupervisor'";
      $strSQL1.= ",employee_type='$strEmployeeType'";
      $strSQL1.= ",in_sw='$strInSW'";
      $strSQL1.= ",in_se='$strInSE'";
      $strSQL1.= ",in_de='$strInDE'";
      $strSQL1.= ",in_eo='$strInEO'";
      $strSQL1.= ",in_ts='$strInTS'";
      $strSQL1.= ",in_en='$strInEN'";
      $strSQL1.= ",access_sw='$strAccessSW'";
      $strSQL1.= ",access_se='$strAccessSE'";
      $strSQL1.= ",access_de='$strAccessDE'";
      $strSQL1.= ",access_eo='$strAccessEO'";
      $strSQL1.= ",access_ts='$strAccessTS'";
      $strSQL1.= ",access_en='$strAccessEN'";
      $strSQL1.= ",can_login='$strCanLogin'";
      $strSQL1.= ",is_admin='$strIsAdmin'";
      $strSQL1.= ",program_admin='$strProgAdmin'";
      if(strlen($strPassword)<5)
        $strSQL1.= ",password=NULL";
      else
        $strSQL1.= ",password='$encPassword'";
      $strSQL1.= ",created='".date("Y-m-d")."'";
      $strSQL1.= ",modified='".date("Y-m-d")."'";
      $result1 = dbquery($strSQL1);
      set_status("Success: Account created.");
      /* remove these from the new create box - as we are creating this account now! */
      $strUsername = "";
      $strFirst    = "";
      $strLast     = "";
      $strPassword = "";
    }
  }
}



$str_eda=isset($_FORM['txt_eda'])?postedData($_FORM['txt_eda']):"";
$str_person=isset($_FORM['txt_person'])?postedData($_FORM['txt_person']):"";
debug(10,"Action to edit, delete, assign person ($str_eda) for ($str_person)");
if(isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Do Now" && $str_eda == "delete")
{
  $str_delconfirm=isset($_FORM['txt_delconfirm'])?postedData($_FORM['txt_delconfirm']):"";
  if(!$str_delconfirm)
  {
    set_error("The confirm checkbox was not selected.  No action taken.");
  }
  else if($str_person == $_SESSION['userID'])
  {
    set_error("You can not delete your own account.");
  }
  else
  {
    $strSQL0 = "DELETE FROM $TABLE_PERSON WHERE id='$str_person'";
    $result0 = dbquery($strSQL0);
    $strSQL0 = "DELETE FROM $TABLE_ACTIVEPERSON WHERE person_id='$str_person'";
    $result0 = dbquery($strSQL0);
    $strSQL1 = "SELECT id FROM $TABLE_PERSON WHERE supervisor_id='$str_person'";
    $result1 = dbquery($strSQL1);
    while($row1 = mysql_fetch_array($result1))
    {
      $strSQL2 = "UPDATE $TABLE_PERSON SET";
      $strSQL2.= " supervisor_id = NULL";
      $strSQL2.= " WHERE id='".$row1['id']."'";
      $result2 = dbquery($strSQL2);
    }
    set_status("User deleted.");
  }
}
if(isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Do Now" && $str_eda == "assign")
{
  set_todo("to assign people to supervisors.");
}
  if(isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Change")
  {
    $tmpPerson   = postedData($_FORM['txt_personid']);
    $tmpUsername = postedData($_FORM['txt_username']);
    $tmpFirst    = postedData($_FORM['txt_first']);
    $tmpLast     = postedData($_FORM['txt_last']);
    $tmpPassword = postedData($_FORM['txt_password']);
    $tmpEmployeeType = postedData($_FORM['txt_employeetype']);
    $tmpSupervisor = postedData($_FORM['txt_supervisor']);
    $strInSW = postedData($_FORM['txt_in_sw']);
    $strInSE = postedData($_FORM['txt_in_se']);
    $strInDE = postedData($_FORM['txt_in_de']);
    $strInEO = postedData($_FORM['txt_in_eo']);
    $strInTS = postedData($_FORM['txt_in_ts']);
    $strInEN = postedData($_FORM['txt_in_en']);
    $strAccessSW = postedData($_FORM['txt_access_sw']);
    $strAccessSE = postedData($_FORM['txt_access_se']);
    $strAccessDE = postedData($_FORM['txt_access_de']);
    $strAccessEO = postedData($_FORM['txt_access_eo']);
    $strAccessTS = postedData($_FORM['txt_access_ts']);
    $strAccessEN = postedData($_FORM['txt_access_en']);
    $strCanLogin = postedData($_FORM['txt_canlogin']);
    $strIsAdmin  = postedData($_FORM['txt_isadmin']);
    $strProgAdmin  = postedData($_FORM['txt_progadmin']);
    debug(10,"CHANGE ACTION POSTED (U:$tmpUsername, F:$tmpFirst, L:$tmpLast, P:$tmpPassword)");
    $strSQL4 = "UPDATE $TABLE_PERSON SET";
    $strSQL4.= " username='$tmpUsername'";
    $strSQL4.= ",fname='$tmpFirst'";
    $strSQL4.= ",lname='$tmpLast'";
    $strSQL4.= ",in_sw='$strInSW'";
    $strSQL4.= ",in_se='$strInSE'";
    $strSQL4.= ",in_de='$strInDE'";
    $strSQL4.= ",in_eo='$strInEO'";
    $strSQL4.= ",in_ts='$strInTS'";
    $strSQL4.= ",in_en='$strInEN'";
    $strSQL4.= ",access_sw='$strAccessSW'";
    $strSQL4.= ",access_se='$strAccessSE'";
    $strSQL4.= ",access_de='$strAccessDE'";
    $strSQL4.= ",access_eo='$strAccessEO'";
    $strSQL4.= ",access_ts='$strAccessTS'";
    $strSQL4.= ",access_en='$strAccessEN'";
    $strSQL4.= ",can_login='$strCanLogin'";
    $strSQL4.= ",is_admin='$strIsAdmin'";
    $strSQL4.= ",program_admin='$strProgAdmin'";
    $strSQL4.= ",employee_type='$tmpEmployeeType'";
    $strSQL4.= ",supervisor_id='$tmpSupervisor'";
    $strSQL4.= ",modified='".date("Y-m-d")."'";
    if(strlen($tmpPassword))$strSQL4.=",password='".encryptData($tmpPassword)."'";
    else $strSQL4.=",password = NULL";
    $strSQL4.= " WHERE id='$tmpPerson'";
    $result4 = dbquery($strSQL4);
    set_status("Person Updated.");
  }

/******************************************/
/* COMPUTING - NO OUTPUT - END            */
/******************************************/

/******************************************/
/* COMPUTING - SHOW OUTPUT - START        */
/******************************************/
$TITLE="People Editing";
show_header();
show_menu("PEOPLE");
show_status();
show_error();



  if(isset($_FORM['txt_action']) && $_FORM['txt_action'] == "Do Now" && $str_eda == "edit")
  {
        /* do the dbquery outside the select list so the debug will appear */
        $strSQL3 = "SELECT * FROM $TABLE_PERSON AS P WHERE P.id='$str_person'";
        $result3 = dbquery($strSQL3);
        $row3 = mysql_fetch_array($result3);
        $tmpUsername=$row3['username'];
        $tmpFirst=$row3['fname'];
        $tmpLast=$row3['lname'];
        $tmpPersonID=$row3['id'];
        $tmpEmployeeType=$row3['employee_type'];
$tmpSel1="";$tmpSel2="";$tmpSel3="";
if($tmpEmployeeType=="Regular")$tmpSel1="SELECTED";
if($tmpEmployeeType=="Contractor")$tmpSel2="SELECTED";
if($tmpEmployeeType=="Intern")$tmpSel3="SELECTED";
if($row3['in_sw'])$CK_IN_SW="CHECKED";
if($row3['in_se'])$CK_IN_SE="CHECKED";
if($row3['in_de'])$CK_IN_DE="CHECKED";
if($row3['in_eo'])$CK_IN_EO="CHECKED";
if($row3['in_ts'])$CK_IN_TS="CHECKED";
if($row3['in_en'])$CK_IN_EN="CHECKED";
if($row3['access_sw'])$CK_AC_SW="CHECKED";
if($row3['access_se'])$CK_AC_SE="CHECKED";
if($row3['access_de'])$CK_AC_DE="CHECKED";
if($row3['access_eo'])$CK_AC_EO="CHECKED";
if($row3['access_ts'])$CK_AC_TS="CHECKED";
if($row3['access_en'])$CK_AC_EN="CHECKED";
        $tmpSupervisorID=$row3['supervisor_id'];
        $tmpCreated=$row3['created'];
        $tmpModified=$row3['modified'];
        $tmpPassword="";
        if($row3['password']) $tmpPassword=unEncryptData($row3['password']);
$ck_login="";$ck_isadmin="";$ck_progadmin="";
if($row3['is_admin'])$ck_isadmin="CHECKED";
if($row3['can_login'])$ck_login="CHECKED";
if($row3['program_admin'])$ck_progadmin="CHECKED";
?>
<BR>
<form action="<?=$PAGE_PEOPLE;?>" method=POST>
<input type=hidden value='<?=$tmpPersonID;?>' name=txt_personid>
<fieldset>
<legend><b>Edit Person</b></legend>
<table align=center>
<tr><td>username</td><td><input type=text name=txt_username value='<?=$tmpUsername;?>'></td></tr>
<tr><td>First Name</td><td><input type=text name=txt_first value='<?=$tmpFirst;?>'></td></tr>
<tr><td>Last Name</td><td><input type=text name=txt_last value='<?=$tmpLast;?>'></td></tr>
<?
if($SHOW_PASSWORD)
{
?>
<tr><td>Password</td><td><input type=text name=txt_password value='<?=$tmpPassword;?>'></td></tr>
<?
}
?>
<tr><td>Can Login</td><td><input type=checkbox <?=$ck_login;?> name=txt_canlogin value='1'> Check to enable</td></tr>
<tr><td>Is Admin</td><td><input type=checkbox <?=$ck_isadmin;?> name=txt_isadmin value='1'> Check to enable</td></tr>
<tr><td>Program Admin</td><td><input type=checkbox <?=$ck_progadmin;?> name=txt_progadmin value='1'> Check to enable</td></tr>
<tr><td>EmployeeType</td><td><select name=txt_employeetype>
<option value="Regular" <?=$tmpSel1;?>>Regular</option>
<option value="Contractor" <?=$tmpSel2;?>>Contractor</option>
<option value="Intern" <?=$tmpSel3;?>>Intern</option>
</select></td></tr>
<tr><td>Supervisor</td><td>
<select name=txt_supervisor>
<option value='0'>-none-</option>
<?
$strSQL3 = "SELECT supervisor_id FROM $TABLE_PERSON WHERE id='".$tmpPersonID."'";
$result3 = dbquery($strSQL3);
$row3 = mysql_fetch_array($result3);
$strSQL4 = "SELECT P.id,P.lname,P.fname";
$strSQL4.= " FROM $TABLE_PERSON AS P";
$strSQL4.= " ORDER BY P.lname ASC, P.fname ASC";
$result4 = dbquery($strSQL4);
while ($row4 = mysql_fetch_array($result4))
{
  $tmp="";
  if($row4['id']==$row3['supervisor_id'])$tmp="SELECTED";
  echo "<option value='".$row4['id']."' $tmp>".$row4['lname'].", ".$row4['fname']."</option>";
}
?>
</select>
</td></tr>
<tr class=row1><td valign=top>Member Of</td><td>
<input type=checkbox name='txt_in_sw' <?=$CK_IN_SW;?> value='1'> Software<BR>
<input type=checkbox name='txt_in_se' <?=$CK_IN_SE;?> value='1'> Systems<BR>
<input type=checkbox name='txt_in_de' <?=$CK_IN_DE;?> value='1'> Design Eng.<BR>
<input type=checkbox name='txt_in_eo' <?=$CK_IN_EO;?> value='1'> Eng. Operations<BR>
<input type=checkbox name='txt_in_ts' <?=$CK_IN_TS;?> value='1'> Test Solutions<BR>
<input type=checkbox name='txt_in_en' <?=$CK_IN_EN;?> value='1'> Engineering
</td></tr>
<tr class=row0><td valign=top>Access To</td><td>
<input type=checkbox name='txt_access_sw' <?=$CK_AC_SW;?> value='1'> Software<BR>
<input type=checkbox name='txt_access_se' <?=$CK_AC_SE;?> value='1'> Systems<BR>
<input type=checkbox name='txt_access_de' <?=$CK_AC_DE;?> value='1'> Design Eng.<BR>
<input type=checkbox name='txt_access_eo' <?=$CK_AC_EO;?> value='1'> Eng. Operations<BR>
<input type=checkbox name='txt_access_ts' <?=$CK_AC_TS;?> value='1'> Test Solutions<BR>
<input type=checkbox name='txt_access_en' <?=$CK_AC_EN;?> value='1'> Engineering
</td></tr>
<tr><td colspan=1><input class=but type=submit name=txt_action value="Change"></td></tr>
</table>
</fieldset>
</form>
<?
  }
if(1)
{
  /* do the dbquery outside the select list so the debug will appear */
  $strSQL2 = "SELECT P.fname,P.lname,P.id FROM $TABLE_PERSON AS P ORDER BY P.lname ASC,P.fname ASC, P.id ASC";
  $result2 = dbquery($strSQL2);
?>
<BR>
<form action="<?=$PAGE_PEOPLE;?>" method=POST>
<fieldset>
<legend><b>Modify Person</b></legend>
<table align=center>
<tr><td>Pick A Person: <select name=txt_person>
<?
  while($row2 = mysql_fetch_array($result2))
  {
    $lf=$row2['lname'].", ".$row2['fname'];
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
</table>
<?
$i=0;
$strSQL4 = "SELECT ";
$strSQL4.= " P.id,P.username,P.fname,P.lname,P.employee_type,P.supervisor_id,P.created,P.modified,P.password,P.is_admin,P.can_login,P.program_admin";
$strSQL4.= ",S.fname as sup_fname, S.lname as sup_lname";
$strSQL4.= ",P.in_sw, P.in_se, P.in_de, P.in_eo, P.in_ts, P.in_en";
$strSQL4.= ",P.access_sw, P.access_se, P.access_de, P.access_eo, P.access_ts, P.access_en";
$strSQL4.= " FROM $TABLE_PERSON AS P";
$strSQL4.= " LEFT JOIN $TABLE_PERSON AS S ON P.supervisor_id=S.id";
$strSQL4.= " ORDER BY P.lname ASC, P.fname ASC";
$result4 = dbquery($strSQL4);
echo "<table align=center>";
echo "<tr><th>#</th><th>Name</th>";
//echo "<th>DB-ID</th>";
echo "<th>Emp-Type</th><th>Supervisor</th>";
//echo "<th>DB-ID</th>";
echo "<th>Created</th><th>Modified</th>";
if($SHOW_PASSWORD) echo "<th>Password</th>";
echo "<th>Can Login</th><th>In Dept</th><th>Access to Dept</th><th>Is Admin</th><th>Program Admin</th><th>Action</th></tr>";
$row=0;
while ($row4 = mysql_fetch_array($result4))
{
  $IN="";$ACCESS="";
  if($row4['in_sw']){if(strlen($IN)>1)$IN.=" ";$IN.="SW";}
  if($row4['in_se']){if(strlen($IN)>1)$IN.=" ";$IN.="SE";}
  if($row4['in_de']){if(strlen($IN)>1)$IN.=" ";$IN.="DE";}
  if($row4['in_eo']){if(strlen($IN)>1)$IN.=" ";$IN.="EO";}
  if($row4['in_ts']){if(strlen($IN)>1)$IN.=" ";$IN.="TS";}
  if($row4['in_en']){if(strlen($IN)>1)$IN.=" ";$IN.="EN";}

  if($row4['access_sw']){if(strlen($ACCESS)>1)$ACCESS.=" ";$ACCESS.="SW";}
  if($row4['access_se']){if(strlen($ACCESS)>1)$ACCESS.=" ";$ACCESS.="SE";}
  if($row4['access_de']){if(strlen($ACCESS)>1)$ACCESS.=" ";$ACCESS.="DE";}
  if($row4['access_eo']){if(strlen($ACCESS)>1)$ACCESS.=" ";$ACCESS.="EO";}
  if($row4['access_ts']){if(strlen($ACCESS)>1)$ACCESS.=" ";$ACCESS.="TS";}
  if($row4['access_en']){if(strlen($ACCESS)>1)$ACCESS.=" ";$ACCESS.="EN";}
  $tmpUsername=$row4['username'];
$row++;
if($row>1)$row=0;
echo "<tr class=row$row>";
echo "<td>".++$i."</td>";
echo "<td><a href='".$PAGE_HOURS."?txt_person=".$row4['id']."'>".$row4['lname'].", ".$row4['fname']."</a></td>";
//echo "<td>".$row4['id']."</td>";

echo "<td>".$row4['employee_type']."</td>";
if($row4['supervisor_id'])
{
  echo "<td><a href='$PAGE_REPORT?graph=bysup&parm=txtperson&txtperson=".$row4['supervisor_id']."'>".$row4['sup_lname'].", ".$row4['sup_fname']."</a></td>";
  //echo "<td>".$row4['supervisor_id']."</td>";
}
else
{
  //echo "<td colspan=2>-none-</td>";
  echo "<td colspan=1>-none-</td>";
}
echo "<td>".$row4['created']."</td>";
echo "<td>".$row4['modified']."</td>";

if($SHOW_PASSWORD)
{
if($row4['password'])
  echo "<td>".unEncryptData($row4['password'])."</td>";
else
  echo "<td>-none-</td>";
}

$cl="-NO-";$cl1="";if($row4['can_login']){$cl="-YES-";$cl1="class=warn";}
echo "<td $cl1>".$cl."</td>";
echo "<td>$IN</td>";
echo "<td>$ACCESS</td>";
$ia="-NO-";$ia1="";if($row4['is_admin']){$ia="-YES-";$ia1="class=warn2";}
echo "<td $ia1>".$ia."</td>";
$pa="-NO-";$pa1="";if($row4['program_admin']){$pa="-YES-";$pa1="class=warn3";}
echo "<td $pa1>".$pa."</td>";
echo "<td><a href='".$PAGE_PERSON."?txt_action=Do Now&txt_person=".$row4['id']."&txt_eda=edit'>Edit</a></td>";
echo "</tr>";
}
echo "</table>";
?>
</fieldset>
<BR>
<form action="<?=$PAGE_PEOPLE;?>" method=POST>
<fieldset>
<legend><b>Create New Person</b></legend>
<table align=center>
<tr><td>username</td><td><input type=text name=txt_username value=''></td></tr>
<tr><td>First Name</td><td><input type=text name=txt_first value=''></td></tr>
<tr><td>Last Name</td><td><input type=text name=txt_last value=''></td></tr>
<?
if($SHOW_PASSWORD) {
?>
<tr><td>Password</td><td><input type=text name=txt_password value=''></td></tr>
<?
}
?>
<tr><td>Can Login</td><td><input type=checkbox name=txt_canlogin value='1'> Check to enable</td></tr>
<tr><td>Is Admin</td><td><input type=checkbox name=txt_isadmin value='1'> Check to enable</td></tr>
<tr><td>Program Admin</td><td><input type=checkbox name=txt_progadmin value='1'> Check to enable</td></tr>
<tr><td>EmployeeType</td><td><select name=txt_employeetype>
<option value="Regular">Regular</option>
<option value="Contractor">Contractor</option>
<option value="Intern">Intern</option>
</select></td></tr>
<tr><td>Supervisor</td><td>
<select name=txt_supervisor>
<option value='0'>-none-</option>
<?
$strSQL4 = "SELECT P.id,P.lname,P.fname";
$strSQL4.= " FROM $TABLE_PERSON AS P";
$strSQL4.= " ORDER BY P.lname ASC, P.fname ASC";
$result4 = dbquery($strSQL4);
while ($row4 = mysql_fetch_array($result4))
{
  echo "<option value='".$row4['id']."'>".$row4['lname'].", ".$row4['fname']."</option>";
}
?>
</select>
</td></tr>
<tr class=row1><td valign=top>Member Of</td><td>
<input type=checkbox name='txt_in_sw' value='1'> Software<BR>
<input type=checkbox name='txt_in_se' value='1'> Systems<BR>
<input type=checkbox name='txt_in_de' value='1'> Design Eng.<BR>
<input type=checkbox name='txt_in_eo' value='1'> Eng. Operations<BR>
<input type=checkbox name='txt_in_ts' value='1'> Test Solutions<BR>
<input type=checkbox name='txt_in_en' value='1'> Engineering
</td></tr>
<tr class=row0><td valign=top>Access To</td><td>
<input type=checkbox name='txt_access_sw' value='1'> Software<BR>
<input type=checkbox name='txt_access_se' value='1'> Systems<BR>
<input type=checkbox name='txt_access_de' value='1'> Design Eng.<BR>
<input type=checkbox name='txt_access_eo' value='1'> Eng. Operations<BR>
<input type=checkbox name='txt_access_ts' value='1'> Test Solutions<BR>
<input type=checkbox name='txt_access_en' value='1'> Engineering
</td></tr>
<tr><td colspan=1 align=center><input class=but type=submit name=txt_action value="Create"></td></tr>
</table>
</fieldset>
</form>
<?
} /* end check for DB ADMIN */
else
{
?>
<form action="<?=$PAGE_PEOPLE;?>" method=POST>
<input type=hidden name=txt_eda value='edit'>
<tr><td>Edit Account: <input class=but type=submit value='Do Now' name='txt_action'></td></tr>
</form>
<?
}

show_footer();


/******************************************/
/* COMPUTING - SHOW OUTPUT - END          */
/******************************************/


?>
