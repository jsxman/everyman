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

$strAccum = 0;
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
//echo "QUERY:<dir>$strSQL0</dir>";
debug(10,$strSQL0);
//$MAX_HOURS=Array();
$max=0;
while($row0 = mysql_fetch_array($result0))
{
  if($max < $row0['thours'])$max=$row0['thours'];
  //echo "DATE(".$row0['date'].") ";
  //echo "THOURS(".$row0['thours'].") ";
  //echo "MAX($max)<BR>";
}

$nRED  =rand(100,200); $nGREEN=rand(0,100); $nBLUE =rand(0,100);
$c0=$nRED*256*256+$nGREEN*256+$nBLUE;
$c1=0;
$c2=0;
debug(10,"Colors ($c0,$c1,$c2)");
$wbsC0=$c0;

$strSQL1 = "SELECT";
$strSQL1.= " date, sum(hours) as thours";
$strSQL1.= " FROM $TABLE_ACTIVEPERSON";
$strSQL1.= " WHERE date >= '$sYear-$sMonth-01' AND date <= '$eYear-$eMonth-01'";
$strSQL1.= " GROUP BY date";
$strSQL1.= " ORDER BY date ASC";
$result1 = dbquery($strSQL1);
$labels = Array();
debug(10,$strSQL1);
if(!isset($revlabels)) $revlabels=Array();
if(!isset($data0)) $data0=Array();
while($row1 = mysql_fetch_array($result1))
{
  $tdate=$row1['date'];
  if(!isset($revlabels[$tdate]))
  {
    $count=count($labels);
    list($y,$m,$d) = split("-",$tdate);
    $labels[$count]=$y."-".$m;
    $revlabels[$tdate]=$count;
  }
  else
  {
    $count=$revlabels[$tdate];
  }
  $data0[$count]=$row1['thours'];
}

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

# require_once("/home/usergreyguru/public_html/ChartDirector/lib/phpchartdir.php");
//require_once("/home/www/srv0/test/aquota/ChartDirector/lib/phpchartdir.php");
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
$textBoxObj = $c->addTitle("Staff Level", "timesbi.ttf", 12);
unset($x);
$textBoxObj->setBackground(0xcccc77, 0x000000, glassEffect());

# Add a title to the y axis
$c->yAxis->setTitle("FTE-Headcount");

# make sure the y axis starts at zero
$c->yAxis->setAutoScale(0.1,0.1,1);

# Set the labels on the x axis.
$c->xAxis->setLabels($labels);

# Display 1 out of 3 labels on the x-axis.
# now it will make 5 dates
$c->xAxis->setLabelStep(2);

if(empty($labels)){
   $title = "No Data for time period: $sMonth-$sYear to $eMonth-$eYear";
}
else{
   $title = "Date";
}
# Add a title to the x axis
$c->xAxis->setTitle($title);

# Add a line layer to the chart
$layer = $c->addLineLayer2();

# Set the default line width to 2 pixels
$layer->setLineWidth(2);

# Add the three data sets to the line layer. For demo purpose, we use a dash line
# color for the last line
//$layer->addDataSet($data0, 0xff0000, "Everyman Data");

foreach($data0 as $key => $value) { $data0[$key]=$value/160; }
foreach($wbsC0 as $key => $value) { $wbsC0[$key]=$value/160; }

$dataSetObj = $layer->addDataSet($data0, $wbsC0, "Planned");
$dataSetObj->setDataSymbol(CircleSymbol, 3);
$layer->setDataLabelFormat("{={value}|1}");

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
