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
//debug(10,"Loading File: tasks.php");
?>
<?
//debug(5, "Enc(password)='".encryptData("password")."'<BR>\n");

if (isset($_FORM['btnSubmit']))
{
//Begin Langroup login addition
  require_once('config/everyman_user_auth.php');
  $strUserName = postedData($_FORM['txtUserName']);
  $strPassword = postedData($_FORM['txtPassword']);
  $column_names = array(0=>"id","username");
  $strSQL1 = "SELECT P.id, P.username, P.password, P.can_login";
  $strSQL1.= " FROM $TABLE_PERSON AS P";
  $strSQL1.= " WHERE P.username='$strUserName'";
  $result1 = dbquery($strSQL1);
  $row = mysql_fetch_array($result1);
/*
  if(!$row1){debug(5, "User not found.<BR>\n");}
      else {deubg(5, "personID=(".$row1['id'].") strPass(".$strPassword.") unc(pass)=(".unEncryptData($row1['password']).")<BR>\n");}
*/
/*
  if ($row1['id'] && $row1['id'] != "" && unEncryptData($row1['password']) == $strPassword)
  {
    //debug(5, "user exists and account active(login)");
    $_SESSION['userID']   = $row1['id'];
    $_SESSION['time']     = time();
    //$_page=$PAGE_PEOPLE;
    $_page=$PAGE_REPORT;
    if (isset($_FORM['ref'])&& strlen($_FORM['ref']))$_page=$_FORM['ref'];
//echo "NOW GOING TO : $_page"; exit;
    header ("Location: $_page");
    exit;
*/
  //if(isset($row['password'])){
  if(isset($row['can_login'])){
      if($row1 = valid_user($strUserName,$_FORM['txtPassword'],$column_names,$TABLE_PERSON,'username',NULL)){

         $_SESSION['userID']   = $row1['id'];
         $_SESSION['time']     = time();
         //$_page=$PAGE_PEOPLE;
         $_page=$PAGE_REPORT;
         if (isset($_FORM['ref'])&& strlen($_FORM['ref']))$_page=$_FORM['ref'];
           //echo "NOW GOING TO : $_page"; exit;
         header ("Location: $_page");
         exit;
      }else{
         set_error("<big>Incorrect Username/Password Combination</big>");
         set_error("<big>Three incorrect login attempts will lock out your LANGROUP account. Contact the help desk if this occurs.</big>");
      }
  }else{
     set_error("<big>Your account needs to be activated to access this tool.</big>");
  }

}
$TITLE="Login";
show_header();
show_menu("LOGIN");
//set_todo("the mycrypt is not installed on every server...");
show_error();

if(!isset($LOGGED_IN) || !$LOGGED_IN)
{
?>

<script>
function myLoad()
{
document.form1.txtUserName.focus();
}
onload=myLoad;
function pword()
{
  var _f=document.form1;
  if(_f.txtPassword1.value!='')
  {
    _f.txtPassword.value=_f.txtPassword1.value;
    _f.txtPassword1.value='';
  }
  return true;
}
</script>
<table align=center><tr><td><b><font size=3>Please use your LANGROUP username/password to login</font></b></td></tr></table>
<form name="form1" method="POST" action="<?=$PAGE_LOGIN;?>" onsubmit="pword(this.form)">
<input type=hidden name="txtPassword">
  <br><table align=center border='0' cellspacing=0 cellpadding=0>
    <tr><td colspan=3 align=center></td></tr>
    <tr>
      <td class=forms_login >Username:</td>
      <td class=forms_login width=10>&nbsp;</td>
      <td class=forms_login ><input class=forms_login type="text" name="txtUserName" value="<?echo isset($strUserName)?$strUserName:"";?>" size="20"></td>
    </tr>
    <tr>
      <td class=forms_login >Password:</td>
      <td class=forms_login width=10>&nbsp;</td>
      <td class=forms_login ><input class=forms_login type="password" name="txtPassword1" size="20"></td>
    </tr>
    <tr height=10><td colspan=3></td></tr>
    <tr>
      <td class=form_login colspan=3 align=center>
         <input class=but type="submit" value="Submit" name="btnSubmit">
         <input class=but type="reset" value="Reset" name="reset">
         <input type=hidden value="<?=$_FORM['ref'];?>" name="ref">
      </td>
    </tr>
  </table><br>
</form>
<?
}
?>
<?
show_footer();
?>

