<php header("Cache-Control: no-cache"); ?>
<html>
<head>
  <title>Little Miami River Conditions</title>
</head>
<body>
<?php
date_default_timezone_set("America/New_York");
echo date("l jS \of F Y h:i A"). "<br>";

$RiverXml = "https://water.weather.gov/ahps2/hydrograph_to_xml.php?gage=kimo1&output=xml";
$noaaHydro = simplexml_load_file($RiverXml) or die("Error: Cannot find RiverFlow data");

//print_r($noaaHydro);

$currentExemption = $noaaHydro->observed;
$currentLevelName = $noaaHydro->observed->datum[0]->primary['name'];
$currentLevel = $noaaHydro->observed->datum[0]->primary;
$currentLevelUnit = $noaaHydro->observed->datum[0]->primary['units'];
$previousLevelUnit = $noaaHydro->observed->datum[1]->primary['units'];
$currentFlowName = $noaaHydro->observed->datum[0]->secondary['name'];
$currentFlowRate = $noaaHydro->observed->datum[0]->secondary;
$currentFlowUnit = $noaaHydro->observed->datum[0]->secondary['units'];
$currentValidDate = $noaaHydro->observed->datum[0]->valid;

if(isset($currentLevelUnit) && $currentLevelUnit > $previousLevelUnit){
  $rateChange = "&#x25BC;";
} elseif(isset($currentLevelUnit) && $currentLevelUnit < $previousLevelUnit) {
  $rateChange = "&#x25B2;";
} else {
  $rateChange = "&#x25B2;";
}

echo "<h3>".$noaaHydro['name']."</h3>";
if(empty($currentExemption)){
  echo $currentExemption. "no river flow data<br>";
} else {
echo "Current Bank Level: ";
echo $currentLevel . $currentLevelUnit . $rateChange ." @ <small>". $currentValidDate. "</small><br>";
echo $currentFlowName . ": " . $currentFlowRate . $currentFlowUnit ."<br>";
echo $currentLevelName . ": ";

$stageLow = $noaaHydro->sigstages[0]->low;
$stageAction = $noaaHydro->sigstages[0]->action;
$stageBankFull = $noaaHydro->sigstages[0]->bankfull;
$stageFlood = $noaaHydro->sigstages[0]->flood;
$stageFloodModerate = $noaaHydro->sigstages[0]->moderate;
$stageFloodMajor = $noaaHydro->sigstages[0]->major;
$stageFloodRecord = $noaaHydro->sigstages[0]->record;

$currentStage =
    // "switch" comparison for $count
    $stageCurrent <= $stageLow ? 'low' :
    ($stageCurrent <= $stageAction ? 'action' :
    ($stageCurrent <= $stageBankFull ? 'full' :
    ($stageCurrent <= $stageFloodModerate ? 'moderate flood' :
    ($stageCurrent <= $stageFloodMajor ? 'major flood' :
    ($stageCurrent <= $stageFloodRecord ? 'record flood' :
    // default above 60
    'UNKNOWN')))));

echo $currentStage;
}

$uvDate = date("Ymd");
$epaEnviroUV = "https://s3.amazonaws.com/dmap-api-cache-ncc-production/$uvDate/hourly/zip/45039.xml";
$MainevilleUV = simplexml_load_file($epaEnviroUV) or die("Error: Cannot find UV data");

echo "<h3>UV Rating</h3>";

foreach($MainevilleUV->getEnvirofactsUVHourly as $a) {
    echo $a->DATE_TIME," <strong>", $a->UV_VALUE,"</strong><br>";
}

$noaaWeatherForecast = "https://forecast.weather.gov/MapClick.php?lat=39.3574&lon=-84.247&unit=0&lg=english&FcstType=xml";
$MainevilleWeather = simplexml_load_file($noaaWeatherForecast) or die("Error: Cannot find weather data");
print_r($MainevilleWeather);

$weatherLocation = $MainevilleWeather->data[0]->location->description;
//echo $weatherLocation;
