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
debug(10,"Loading File: graph-projtotal.php");
checkPermissions($SESSION_TIMEOUT); // if not logged in, or session has timed out...


$SCALE_LIMIT=60;

$strAccum = 0;
$sMonth = isset($_FORM['sMonth'])?postedData($_FORM['sMonth']):1;
$sYear = isset($_FORM['sYear'])?postedData($_FORM['sYear']):date("Y");
$eMonth = isset($_FORM['eMonth'])?postedData($_FORM['eMonth']):12;
$eYear = isset($_FORM['eYear'])?postedData($_FORM['eYear']):date("Y");



/* all that this person can access */
$strSQL4 = "SELECT * FROM $TABLE_PERSON WHERE id='".$_SESSION['userID']."'";
$result4 = dbquery($strSQL4);
$row4 = mysql_fetch_array($result4);
if(!$row4['access_eo'])
{
header("Content-Type: image/gif");
readfile("shim.gif");
// show the no access image
exit;
}


//$Y=date("Y");
//$NY=$Y+1;
/* FIND THE MAX HOURS to use as a SCALE */
$strSQL0 = "SELECT";
$strSQL0.= " AP.date, sum(AP.hours) as thours";
$strSQL0.= " FROM $TABLE_ACTIVEPERSON AS AP";
$strSQL0.= " LEFT JOIN $TABLE_PERSON AS TP ON TP.id = AP.person_id";
$strSQL0.= " WHERE AP.date >= '$sYear-$sMonth-01' AND AP.date <= '$eYear-$eMonth-01'";
$strSQL0.= " AND TP.employee_type<>'Intern'";
$strSQL0.= " GROUP BY AP.date";
$strSQL0.= " ORDER BY AP.date ASC";
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

$colors=Array();
/*
 * $nRED  =rand(100,200); $nGREEN=rand(0,100); $nBLUE =rand(0,100);
 * $colors[1]=$nRED*256*256+$nGREEN*256+$nBLUE;
 * $nRED  =rand(100,200); $nGREEN=rand(0,100); $nBLUE =rand(0,100);
 * $colors[2]=$nRED*256*256+$nGREEN*256+$nBLUE;
 * $nRED  =rand(100,200); $nGREEN=rand(0,100); $nBLUE =rand(0,100);
 * $colors[3]=$nRED*256*256+$nGREEN*256+$nBLUE;
 */
$colors[1]=0x0000FF;
$colors[2]=0xFFFF00;
$colors[3]=0xAAAA00;
$colors[4]=0x00FF00;
$colors[5]=0x88AA88;


$strSQL1 = "SELECT";
$strSQL1.= " AP.date, sum(AP.hours) as thours, AP.project_id, PR.percent_direct";
$strSQL1.= ",PR.future, PR.headcount_scaling, PR.absent";
$strSQL1.= " FROM $TABLE_ACTIVEPERSON AS AP";
$strSQL1.= " LEFT JOIN $TABLE_PERSON AS TP ON TP.id = AP.person_id";
$strSQL1.= " LEFT JOIN $TABLE_PROJECT AS PR ON PR.id = AP.project_id";
$strSQL1.= " WHERE AP.date >= '$sYear-$sMonth-01' AND AP.date <= '$eYear-$eMonth-01'";
//LOOK HERE
$strSQL1.= " AND TP.in_eo='1'";
//$strSQL1.= " AND TP.employee_type<>'Intern'";
$strSQL1.= " GROUP BY AP.date, AP.project_id";
$strSQL1.= " ORDER BY AP.date ASC";
//echo "QUERY:<dir>$strSQL1</dir>";
$result1 = dbquery($strSQL1);
debug(10,$strSQL1);
$labels = Array();
$names = Array();
while($row1 = mysql_fetch_array($result1))
{
   $tdate = $row1['date'];
   if(!isset($revlabels[$tdate]))
   {
      $count = count($labels);
      list($y,$m,$d) = split("-",$tdate);
      $labels[$count] = $y."-".$m;
      $revlabels[$tdate] = $count;
   }
   else
   {
      $count = $revlabels[$tdate];
   }
   //$project = $row1['project_id'];
   /* rules for finding the right bucket: direct, indirect, or future/scaled */
   if($row1['future']==1)
   {
     $project=4; $names[$project]="Future (>=60%)";
     if(!isset($data0[$project][$count]))$data0[$project][$count]=0;
     $scale= $row1['headcount_scaling'];
     if($scale<$SCALE_LIMIT){$project=5;$names[$project]="Future (<60%)";}
     $data0[$project][$count]+= $row1['thours'] * $scale / 100;
   }
   else
   {
     $project=1; $names[$project]="Direct";
     if(!isset($data0[$project][$count]))$data0[$project][$count]=0;
     $data0[$project][$count]+= $row1['thours'] * $row1['percent_direct'] / 100;

     if($row1['absent']){ $project=2; $names[$project]="Absences"; }
     else               { $project=3; $names[$project]="Indirect"; }
     if(!isset($data0[$project][$count]))$data0[$project][$count]=0;
     $data0[$project][$count]+= $row1['thours'] * (100 - $row1['percent_direct']) / 100;
   }
}

//This loop is to correct for missing array elements and allow to graph correctly
foreach($data0 as $key=>$array)
{
   $k=0;
   for($i=0; $i<count($labels); $i++)
   {
      if(!isset($array[$i]))
      {
         $data0[$key][$i] = 0;
      }
      $k+=$data0[$key][$i];
   }
/* only would need to do this if we pulled all zero data from the DB - which does not happen */
   //if(!$k){unset($data0[$key]);}
}

$sum=Array();
/* sum up direct and indirect */
for($i=0;$i<count($labels);$i++)
{
  if(!isset($sum[$i]))$sum[$i]=0;
  $sum[$i]=($data0[1][$i])/160+($data0[2][$i])/160;
}



/*
$strSQL2 = "SELECT id, name FROM Project";
$result2 = dbquery($strSQL2);
while($row2 = mysql_fetch_array($result2))
{
   $id = $row2['id'];
   $names[$id] = $row2['name'];
}
*/

//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

# require_once("/home/usergreyguru/public_html/ChartDirector/lib/phpchartdir.php");
//require_once("/home/www/srv0/test/aquota/ChartDirector/lib/phpchartdir.php");
require_once("/usr/share/php5/ChartDirector/lib/phpchartdir.php");

$chartH=650;
$chartW=950;
$plotH=450;
$plotW=850;

# Create an XYChart object of size 600 x 300 pixels, with a light blue (EEEEFF)
# background, black border, 1 pxiel 3D border effect and rounded corners
$c = new XYChart($chartW, $chartH, 0xeeee99, 0x000000, 1);
//$c->setRoundedFrame();

# Set the plotarea at (55, 58) and of size 520 x 195 pixels, with white background.
# Turn on both horizontal and vertical grid lines with light grey color (0xcccccc)
$c->setPlotArea(50, 150, $plotW, $plotH, 0xffffff, -1, -1, 0xcccccc, 0xcccccc);

# Add a legend box at (50, 30) (top of the chart) with horizontal layout. Use 9 pts
# Arial Bold font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(20, 25, false, "arialbd.ttf", 9);
$legendObj->setBackground(Transparent);

# Add a title box to the chart using 15 pts Times Bold Italic font, on a light blue
# (CCCCFF) background with glass effect. white (0xffffff) on a dark red (0x800000)
# background, with a 1 pixel 3D border.
$x=$strAccum?" Accum":"";
$textBoxObj = $c->addTitle("Eng Ops Dept Staff Level (Future Split & Scaled) Absences", "timesbi.ttf", 12);
unset($x);
$textBoxObj->setBackground(0xcccc77, 0x000000, glassEffect());

# Add a title to the y axis
$c->yAxis->setTitle("FTE-Headcount");

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

$dataX=Array();
for($i=0;$i<12;$i++){$dataX[$i]=$installs['eo']['staff'];}
$SL = $c->addLineLayer($dataX);
$SL->setLineWidth(1);
/* ZONE COLORS... Does not work
 * $v=date("Y-m-d");
 * $zone1=100*256*256+  0*256+0;
 * $zone2=  0*256*256+100*256+0;
 * $SL->xZoneColor($v,$zone1,$zone2);
 */
//$SL_DO=$SL->getDataSet(0);
//$SL_DO->setDataSymbol(SquareSymbol,9);

/*
$dataShadeMin=Array(5,5,5,5,5,5,5,5,5,10,10,10);
$dataShadeMax=Array(10,10,10,10,10,10,5,5,5,5,5,5);
$layer2=$c->addLineLayer($dataShadeMin,$colors[3],"");
$layer3=$c->addLineLayer($dataShadeMax,$colors[2],"");
$c->addInterLineLayer($layer2->getLine(), $layer3->getLine(),$colors[3],$colors[2]);
*/


# Add a line layer to the chart
//$layer = $c->addLineLayer2();
$layer = $c->addAreaLayer2(Stack);

# Set the default line width to 2 pixels
$layer->setLineWidth(2);

# Add the three data sets to the line layer. For demo purpose, we use a dash line
# color for the last line
//$layer->addDataSet($data0, 0xff0000, "Everyman Data");
for($i=1;$i<=count($data0);$i++) //foreach($data0 as $key=>$value)
{
   $key=$i;$value=$data0[$key];
   $projName = $names[$key];
   foreach ($value as $k=>$v){$value[$k]=round($v/160,1);}
/*
   if($projName=="Direct")
   {
     $layer3=$c->addLineLayer($value,$colors[2],"");
     //$layer3=$c->addLineLayer($sum,$colors[2],"");
   }
   if($projName=="Absences")
   {
     //$layer2=$c->addLineLayer($value,$colors[3],"");
     $layer2=$c->addLineLayer($sum,$colors[3],"");
  //   $c->addInterLineLayer($layer2->getLine(), $layer3->getLine(),$colors[3],$colors[3]);
   }
*/
   $ds=$layer->addDataSet($value, $colors[$key], $projName);
   $box=$ds->setDataLabelStyle("timessbi.tff",6, 0x000000);
   $box->setBackground(0xFFFFFF,0x000000);
}
//$dataSetObj->setDataSymbol(CircleSymbol, 3);
//$layer->setDataLabelFormat("{={value}/160|1}");


$TB = $layer->setAggregateLabelStyle("timesbi.ttf", 10);
//$layer->setDataLabelFormat("{={value}|1}");
$TB->setBackground(0xffcc66, Transparent, 1);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
