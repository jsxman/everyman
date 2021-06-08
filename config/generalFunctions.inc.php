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
if($DEBUG>10)echo"DEBUG(10): Loading File: generalFunctions.inc.php<BR>\n";

function set_error($str)
{
  global $ERROR;
  if(strlen($str)) $ERROR[count($ERROR)]=$str;
}
function show_version()
{
  global $PEM_VERSION;
  echo "<fieldset class=version><legend><b>Version</b></legend>";
  echo "<table width=100%><tr><td align=right>PEM Version $PEM_VERSION.</td></tr></table>\n";
  echo "</fieldset>";
}
function show_pageloadtime()
{
  global $PAGE_TIME_START;
  $currenttime=microtime();
  $temp=$currenttime-$PAGE_TIME_START;
  echo "<fieldset class=pagetime><legend><b>Page Load Time</b></legend>";
  echo "<table width=100%><tr><td align=right>This page loaded in $temp seconds.</td></tr></table>\n";
  echo "</fieldset>";
}
function show_error()
{
  global $ERROR;
  if(!count($ERROR))return;
  echo "<fieldset class=error><legend><b>ERROR MESSAGE</b></legend>";
  echo "<table width=100%><tr><td>";
  print "<ul>";
  for($i=0;$i<count($ERROR);$i++)
    print "<li>".$ERROR[$i]."</li>\n";
  print "</ul></td></tr></table>";
  echo "</fieldset>";
}
function set_status($str)
{
  global $STATUS;
  if(strlen($str)) $STATUS[count($STATUS)]=$str;
}
function show_status()
{
  global $STATUS;
  if(!count($STATUS))return;
  echo "<fieldset class=status><legend><b>STATUS MESSAGE</b></legend>";
  echo "<table width=100%></tr><tr><td>";
  print "<ul>";
  for($i=0;$i<count($STATUS);$i++)
    print "<li>".$STATUS[$i]."</li>\n";
  print "</ul></td></tr></table>";
  echo "</fieldset>";
}
function set_todo($str)
{
  global $TODO;
  if(strlen($str)) $TODO[count($TODO)]=$str;
}
function show_todo()
{
  global $TODO;
  if(!count($TODO))return;
  echo "<fieldset class=warn><legend><b>To Do List</b></legend>";
  print "<table width=100%><tr><td><ul>";
  for($i=0;$i<count($TODO);$i++)
    print "<li>".$TODO[$i]."</li>";
  print "</ul></td></tr></table>";
  echo "</fieldset>";
}
function show_menu($_which)
{
  global $MENU_FILE;
  global $DEBUG;
  debug(10,"Function: show_menu()");
  if(isset($MENU_FILE) && file_exists($MENU_FILE))
    Include($MENU_FILE);
  else
    show_error("File not found: \$MENU_FILE ($MENU_FILE).<BR>\nYou must edit the config/global.inc.php file.<BR>\n");
}
function show_header()
{
  global $HEADER_FILE;
  global $DEBUG;
  debug(10,"Function: show_header()");
  if(isset($HEADER_FILE) && file_exists($HEADER_FILE))
    Include($HEADER_FILE);
  else
    show_error("File not found: \$HEADER_FILE ($HEADER_FILE).<BR>\nYou must edit the config/global.inc.php file.<BR>\n");
}
function show_footer()
{
  global $FOOTER_FILE;
  //show_todo();
  //show_pageloadtime();
  //show_dbstat();
  //show_version();
  if(isset($FOOTER_FILE) && file_exists($FOOTER_FILE))
    Include($FOOTER_FILE);
  else
    show_error("File not found: \$FOOTER_FILE ($FOOTER_FILE).<BR>\nYou must edit the config/global.inc.php file.<BR>\n");
}
function debug($level,$str)
{
  global $DEBUG;
  if($DEBUG>$level)
    print "<table width=100% class=debug><tr><td>DEBUG($level): $str</td></tr></table>\n";
}

  Function getPostedData()
  {
    global $_POST,$_GET;
    global $_FORM;
    global $DEBUG;
    global $_SESSION;

    $test="";
    if(isset($_SESSION['TEST']))$test=$_SESSION['TEST'];
    $str="";
    $str.="TESTING=$test<BR>";
    $str.="SESSION-FORM<BR>\n";
    /* get the saved FORM data from a session tie out if it exists */
    if(isset($_SESSION['FORM']))
    {
      $str.= "XXXX1XXXX<BR>\n";
      $x=unserialize($_SESSION['FORM']);
      $str.="x:<DIR>$x</DIR>\n";
      if(is_array($x))
      {
        $str.= "XXXX2XXXX<BR>\n";
        reset($x);
        {
          while(list($name,$value)=each($x))
          {
            $str.= "XXXX3XXXX $name , $value<BR>\n";
            if(!is_array($value))
            {
              $_FORM[$name]=$value;
              $str.= "[SESSION-FORM] $name=\"$value\"<BR>\n";
            }
            else
            {
              while(list($name2,$value2)=each($x[$name]))
              {
                $str.= "XXXX4XXXX $name , $value<BR>\n";
                if(!is_array($_FORM[$name]))
                {
                  $_FORM[$name]=array($name2=>$value2);
                  $str.= "[SESSION-FORM]".$name."[".$name2."]=\"$value2\"<BR>\n";
                }
                else
                {
                  $_FORM[$name][$name2]=$value2;
                  $str.= "[SESSION-FORM]".$name."[".$name2."]=\"$value2\"<BR>\n";
                }
              }
            }
          }
        }
      }
    } /* end of _SESSION['FORM'] */
    //unset($_SESSION['FORM']);

    $str.="GET-FORM<BR>\n";
    reset($_GET);
    while(list($name,$value)=each($_GET))
    {
      if(!is_array($value))
      {
        $_FORM[$name]=$value;
        $str.= "$name=\"$value\"<BR>\n";
      }
      else
      {
        while(list($name2,$value2)=each($_GET[$name]))
        {
          if(!is_array($_FORM[$name]))
          {
            $_FORM[$name]=array($name2=>$value2);
            $str.= $name."[".$name2."]=\"$value2\"<BR>\n";
          }
          else
          {
            $_FORM[$name][$name2]=$value2;
            $str.= $name."[".$name2."]=\"$value2\"<BR>\n";
          }
        }
      }
    }
    $str.="POST-FORM<BR>\n";
    reset($_POST);
    while(list($name,$value)=each($_POST))
    {
      if(!is_array($value))
      {
        $_FORM[$name]=$value;
        $str.= "$name=\"$value\"<BR>\n";
      }
      else
      {
        while(list($name2,$value2)=each($_POST[$name]))
        {
          if(!is_array($_FORM[$name]))
          {
            $_FORM[$name]=array($name2=>$value2);
            $str.= $name."[".$name2."]=\"$value2\"<BR>\n";
          }
          else
          {
            $_FORM[$name][$name2]=$value2;
            $str.= $name."[".$name2."]=\"$value2\"<BR>\n";
          }
        }
      }
    }
    return $str;
  }

// keep this old method around just in case we need it later...
  Function getPostedData2()
  {
    global $_POST,$_GET;
    global $_FORM;
    global $DEBUG;
    for(reset($_POST);$key=key($_POST);next($_POST))
    {
      $_FORM[$key] =  $_POST[$key];
      if($DEBUG)print "$key=&quot;".$_POST[$key]."&quot;<BR>\n";
    }
                                                                                                                                                                                                        
    for(reset($_GET);$key=key($_GET);next($_GET))
    {
      $_FORM[$key] =  $_GET[$key];
      if($DEBUG)print "$key=&quot;".$_GET[$key]."&quot;<BR>\n";
    }
  }

  Function encryptData($m) {
     global $encryptKey;

     if(function_exists('mcrypt_create_iv') && function_exists('mcrypt_encrypt') && function_exists('mcrypt_decrypt'))
     {
       $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_BLOWFISH,MCRYPT_MODE_CBC),MCRYPT_RAND);
       $c = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryptKey, $m, MCRYPT_MODE_CBC, $iv);
       // encode and tack on the iv
       $c1 = base64_encode($c . "\$IV\$" . $iv);
       return $c1;
     }
     else
     {
       return $m;
     }
  }

// all data goes through here - can validate or do security here later...
  function postedData($v)
  {
   /* only allow letters or numbers or spaces or period */
    $v=preg_replace("/[^a-zA-Z0-9\-:\/\. ]/","",$v);
    return $v;
  }

  /* input is MM/DD/YY, output is seconds since the EPOC */
  function date_save($d)
  {
    $val=0;
    if(!strlen($d))return 0;
    $_d=explode("/",$d);
    if(!isset($_d)||!isset($_d[2]))return 0;
    if($_d[2]<1999)$_d[2]+=2000;
    $val=date("U",mktime(0,0,0,$_d[0],$_d[1],$_d[2]));
    //$val=date("F j, Y",mktime(0,0,0,$_d[0],$_d[1],$_d[2]));
    return $val;
  }
  /* output is MM/DD/YY, input is seconds since the EPOC */
  function date_read($d)
  {
    if(strlen($d) && $d)
      return date("n/j/y",$d);
    else
      return "";
  }

  Function unEncryptData($c) {
     global $encryptKey;

     if(function_exists('mcrypt_create_iv') && function_exists('mcrypt_encrypt') && function_exists('mcrypt_decrypt'))
     {
       // decode and get the iv off
       list($c1,$iv)=explode("\$IV\$",base64_decode($c));
       $m = mcrypt_decrypt(MCRYPT_BLOWFISH,$encryptKey,$c1,MCRYPT_MODE_CBC,$iv);
       return rtrim($m);
     }
     else
     {
       return $c;
     }
  }
  $rowStyle=0; // initial value
  Function resetRowColor()
  {
     global $rowStyle;
     $rowStyle=0;
  }
  # use this to set the class tag of alternating rows in tables (in conjunction with stylesheet)
  Function alternateRowColor() {
      global $rowStyle;
      $rowStyle ++;
      If ($rowStyle%2 == 1) {
           Return "row1";
      } Else {
           Return "row2";
      }
  }

?>
