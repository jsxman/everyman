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
debug(10,"Loading File: graph-levels.php");
checkPermissions($SESSION_TIMEOUT); // if not logged in, or session has timed out...

$strAccum =isset($_FORM['txtaccum'])?postedData($_FORM['txtaccum']):0;
$sMonth = isset($_FORM['sMonth'])?postedData($_FORM['sMonth']):1;
$sYear = isset($_FORM['sYear'])?postedData($_FORM['sYear']):date("Y");
$eMonth = isset($_FORM['eMonth'])?postedData($_FORM['eMonth']):12;
$eYear = isset($_FORM['eYear'])?postedData($_FORM['eYear']):date("Y");

//$Y=date("Y");
//$NY=$Y+1;
/* FIND THE MAX HOURS to use as a SCALE */
$strSQL0 = "SELECT";
$strSQL0.= " date, sum(hours) as thours";
$strSQL0.= " FROM $TABLE_ACTIVEPERSON";
$strSQL0.= " WHERE date >= '$sYear-$sMonth-01' AND date <= '$eYear-$eMonth-01'";
$strSQL0.= " GROUP BY date";
$strSQL0.= " ORDER BY date ASC";
$result0 = dbquery($strSQL0);
debug(10,$strSQL0);
$max=0;
while($row0 = mysql_fetch_array($result0))
{
  if($max < $row0['thours'])$max=$row0['thours'];
}

/*
 * $nRED  =rand(100,200); $nGREEN=rand(0,100); $nBLUE =rand(0,100);
 * $c0=$nRED*256*256+$nGREEN*256+$nBLUE;
 * $wbsC0=$c0;
 */

$colors=Array();
$names=Array();
$data=Array();
$labels = Array();
$strSQL1 = "SELECT";
$strSQL1.= " S.fname";
$strSQL1.= ",S.lname";
$strSQL1.= ",P.supervisor_id";
$strSQL1.= " FROM $TABLE_PERSON AS P";
$strSQL1.= " LEFT JOIN $TABLE_PERSON AS S ON P.supervisor_id=S.id";
$strSQL1.= " WHERE P.supervisor_id > 0";
$strSQL1.= " GROUP BY P.supervisor_id";
$result1 = dbquery($strSQL1);
$supCount=0;
while($row1 = mysql_fetch_array($result1))
{
  $count=$supCount++;
  $nRED  =rand(0,200); $nGREEN=rand(0,200); $nBLUE =rand(0,200);
  $colors[$count]=$nRED*256*256+$nGREEN*256+$nBLUE;
  $names[$count]=$row1['lname'].", ".$row1['fname'];
  //echo "SUP ID :".$row1['supervisor_id']." COLOR(".$colors[$count].") NAMES(".$names[$count].")<BR>";

  $strSQL2 = "SELECT";
  $strSQL2.= " AP.date, sum(AP.hours) as thours";
  $strSQL2.= " FROM $TABLE_ACTIVEPERSON AS AP";
  $strSQL2.= " LEFT JOIN $TABLE_PERSON AS P ON AP.person_id = P.id";
  $strSQL2.= " WHERE AP.date >= '$sYear-$sMonth-01' AND AP.date <= '$eYear-$eMonth-01'";
  $strSQL2.= "       AND P.supervisor_id='".$row1['supervisor_id']."'";
  $strSQL2.= " GROUP BY AP.date";
  $strSQL2.= " ORDER BY AP.date ASC";
  $result2 = dbquery($strSQL2);
  if(!isset($revlabels)) $revlabels=Array();
  if(!isset($data[$count])) $data[$count]=Array();
  $countItem=0;
  while($row2 = mysql_fetch_array($result2))
  {
    $tdate=$row2['date'];
    $row2['thours']=round($row2['thours']/160,1);
    if(!isset($revlabels[$tdate]))
    {
      $count2=count($labels);
      list($y,$m,$d) = split("-",$tdate);
      if($count==1)
        $labels[$count2]=$y."-".$m;
      $revlabels[$tdate]=$count2;
    }
    else
    {
      $count2=$revlabels[$tdate];
    }
    //echo " -- DATA[$count][$countItem]=".$row2['thours']."<BR>";
    if($strAccum)
    {
      $v=$row2['thours'];
      //for($u=0;$u<$count;$u++)
        //$v+=$data[$u][$countItem];
      //if($count > 0){
      //   $v+=$data[$count-1][$countItem];
      //}
      $data[$count][$countItem]=$v;
    }
    else
    {
      $data[$count][$countItem]=$row2['thours'];
    }
    $countItem++;
  }
  unset($revlabels);
}

//for($i=0;$i<count($data);$i++)
  //for($j=0;$j<count($data[$i]);$j++)
    //echo "($i,$j)=[".$data[$i][$j]."]<BR>\n";

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

# require_once("/home/usergreyguru/public_html/ChartDirector/lib/phpchartdir.php");
require_once("/usr/share/php5/ChartDirector/lib/phpchartdir.php");

$chartH=600;
$chartW=950;
$plotH=450;
$plotW=850;

# Create an XYChart object of size 600 x 300 pixels, with a light blue (EEEEFF)
# background, black border, 1 pxiel 3D border effect and rounded corners
$c = new XYChart($chartW, $chartH, 0xeeee99, 0x000000, 1);
//$c->setRoundedFrame();

# Set the plotarea at (55, 58) and of size 520 x 195 pixels, with white background.
# Turn on both horizontal and vertical grid lines with light grey color (0xcccccc)
$c->setPlotArea(50, 70, $plotW, $plotH, 0xffffff, -1, -1, 0xcccccc, 0xcccccc);

# Add a legend box at (50, 30) (top of the chart) with horizontal layout. Use 9 pts
# Arial Bold font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(20, 25, false, "arialbd.ttf", 9);
$legendObj->setBackground(Transparent);

# Add a title box to the chart using 15 pts Times Bold Italic font, on a light blue
# (CCCCFF) background with glass effect. white (0xffffff) on a dark red (0x800000)
# background, with a 1 pixel 3D border.
$x=$strAccum?" Accum":"";
$textBoxObj = $c->addTitle("Staff Level $x ", "timesbi.ttf", 12);
unset($x);
$textBoxObj->setBackground(0xcccc77, 0x000000, glassEffect());

# Add a title to the y axis
$c->yAxis->setTitle("FTE");

# Set the labels on the x axis.
$c->xAxis->setLabels($labels);

# Display 1 out of 3 labels on the x-axis.
# now it will make 5 dates
$c->xAxis->setLabelStep(4);

if(empty($labels)){
   $title = "No Data for time period: $sMonth-$sYear to $eMonth-$eYear";
}
else{
   $title = "Date";
}
# Add a title to the x axis
$c->xAxis->setTitle($title);

# Add a line layer to the chart
if($strAccum)
{
  $layer = $c->addAreaLayer2(Stack);
}
else
{
  $layer = $c->addLineLayer2();
}


# Set the default line width to 2 pixels
$layer->setLineWidth(2);

# Add the three data sets to the line layer. For demo purpose, we use a dash line
# color for the last line
//$layer->addDataSet($data0, 0xff0000, "Everyman Data");

for($i=0;$i<count($colors);$i++)
{
  $dataSetObj = $layer->addDataSet($data[$i], $colors[$i], $names[$i]);
  $t=rand(0,4);
  if($t==1)$dataSetObj->setDataSymbol(CircleSymbol, 8, $colors[$i]);
  if($t==2)$dataSetObj->setDataSymbol(DiamondSymbol, 8, $colors[$i]);
  if($t==3)$dataSetObj->setDataSymbol(SquareSymbol, 8, $colors[$i]);
  if($t==4)$dataSetObj->setDataSymbol(Cross2Shape(), 8, $colors[$i]);
}
$layer->setDataLabelFormat("{={value}|1}");

//$TB = $layer->setAggregateLabelStyle("timesbi.ttf", 10);
//$TB->setBackground(0xffcc66, Transparent, 1);


# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
