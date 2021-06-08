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

global $HACK_CHECK;
global $PAGE;
if(!isset($HACK_CHECK) || !$HACK_CHECK)exit; // DO NOT DIRECTLY LOAD THIS FILE
$t1='';$t2='';$t3='';$t4='';$t5='';$t6='';$t7='';
if(isset($_which))
{
  switch($_which)
  {
    case 'PEOPLE':   $t1='1';break;
    case 'PROJECTS': $t2='1';break;
    case 'HOURS':    $t3='1';break;
    case 'LOGOUT':   $t4='1';break;
    case 'LOGIN':    $t5='1';break;
    case 'REPORTS':  $t7='1';break;
    case 'TEMP':     // show nothing - its okay :-)
                     break;
    default: show_error("Invalid SWITCH Option in the templates/menubar.php");
  }
  debug(10, "WHICH is: $_which");
}
if(isset($_SESSION) && isset($_SESSION['userID']) && $_SESSION['userID'] && $t4<>1)
{
  $m1="logout.php";
  $m2="LOGOUT";
  $t6=$t4;
}
else
{
  $m1="login.php";
  $m2="LOGIN";
  $t6=$t5;
}
?>
<div class="menuBar"
  ><a class="menuButton<?=$t1;?>"
      href="people.php"
      tabindex="1"
  >PEOPLE</a
  ><a class="menuButton<?=$t2;?>"
      href="projects.php"
      tabindex="2"
  >PROJECTS</a
  ><a class="menuButton<?=$t3;?>"
      href="hours.php"
      tabindex="3"
  >HOURS</a
  ><a class="menuButton<?=$t7;?>"
      href="reports.php"
      tabindex="3"
  >REPORTS</a
  ><a class="menuButton<?=$t6;?>"
      href="<?=$m1;?>"
      tabindex="3"
  ><?=$m2;?></a>
</div>
