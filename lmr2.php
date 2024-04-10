<?php header("Cache-Control: no-cache"); ?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.5">
<link rel="icon" href="https://smallvalleymedia.com/favicon-32x32.png" type="image/png"/>
    <title>Little Miami River Conditions</title>
    <style>
        @media(prefers-color-scheme: dark) {
            .body-dark{
            background: #333;
            color: #fff;
            padding:10px;
        }
        body{
            margin:0;
            background: #333;
        }
        }
        .body-light{
            background: #fff;
            color: #000;
            padding:10px;
        }
        [id^="riverLevel"] {
            /*width:390px; */
            text-align:center;
            border:solid 0px #000;
            overflow:hidden;
        }
        [id^="riverLevel"] div {
            /*width:180px;*/
            text-align:left;
            border:solid 0px #000;
            margin:1px;
            padding:2px 5px;
        }
        [class^="observedFlow"] {
            float:left;
            clear:both;
        }
        [class^="observedDate"] {
            float:right;
        }
        #UVLevel {
            /*width:390px; */
            text-align:center;
            border:solid 0px #000;
            overflow:hidden;
        }
        #UVLevel div {
            /*width:180px;*/
            text-align:left;
            border:solid 0px #000;
            margin:1px;
            padding:1px 5px;
        }
        [class^="uvLevel"] { 
            float:left;
            clear:both;
        }
        [class^="uvTime"] { 
            float:right;
            font-size:1.1em;
        }
        #tempLevel {
            /*width:390px; */
            text-align:center;
            border:solid 0px #000;
            overflow:hidden;
        }
        #tempLevel div {
            /*width:180px;*/
            text-align:left;
            border:solid 0px #000;
            margin:1px;
            padding:2px 5px;
        }
        [class^="tempLevelNum"] { 
            float:left;
            clear:both;
        }
        [class^="tempLevelDescr"] { 
            float:right;
        }
        #todayDate {
            font-size:1.2em;
        }
        #container{
            max-width: 550px;
        }
    </style>
</head>
<body>
<div id="container" class="body-dark">

<p id="todayDate">Today's Date <img src="loading-buffering.gif" width="50px" /></p>

<h3 id="locationTitle1">River Location 1<img src="loading-buffering.gif" width="50px" /></h3>
    <!-- <p>Data extracted from <span id="source">XML</span></p> -->
    <div id="riverLevel1"></div>

<h3 id="locationTitle2">River Location 2<img src="loading-buffering.gif" width="50px" /></h3>
    <!-- <p>Data extracted from <span id="source">XML</span></p> -->
    <div id="riverLevel2"></div>

<h3 id="uvTitle">UV Data <img src="loading-buffering.gif" width="50px" /></h3>
<div id="UVLevel"></div>

<h3 id="weatherTitle">Weather Data <img src="loading-buffering.gif" width="50px" /></h3>
<div id="tempLevel"></div>
<br><br>
<!--<input type="button" onclick="chBackcolor('white','black');" value="Light"> -->
<script>
function chBackcolor(bgColor,fontColor) {
   document.getElementById("container").style.background = bgColor;
   document.getElementById("container").style.color  = fontColor;
   var lightScheme = "chBackcolor('red','green')";
   document.getElementById("container").onlick = lightScheme;
   var lightValue = "Dark";
}

const d = new Date();
const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
function addZero(i) {
  if (i < 10) {i = "0" + i}
  return i;
}

var dateFull = Date.now();
var dateY = d.getFullYear();
let dateMon = months[d.getMonth()];
let dateMonth = addZero(d.getMonth()+1);
var dateD = d.getDate();;
var dateH = d.getHours();
let dateHours = d.toLocaleString("en-US", {
 hour: "numeric",
 minute: "numeric",
 hour12: true,
});
var dateMin = addZero(d.getMinutes());

document.getElementById("todayDate").innerHTML = dateMon + " " + dateD + " " + dateY + ", " + dateHours;
const uvDate = d.getFullYear()+dateMonth+addZero(d.getDate());
</script>

<script>
// Kings Mills: https://water.weather.gov/ahps2/hydrograph_to_xml.php?gage=kimo1&output=xml
// S Lebanon: https://water.weather.gov/ahps2/hydrograph_to_xml.php?gage=slbo1&output=xml
// Spring Valley: https://water.weather.gov/ahps2/hydrograph_to_xml.php?gage=llmo1&output=xml

const xmlSources = {
riverData1: 'https://water.weather.gov/ahps2/hydrograph_to_xml.php?gage=kimo1&output=xml',
riverData2: 'https://water.weather.gov/ahps2/hydrograph_to_xml.php?gage=kimo1&output=xml',
uvData: 'https://s3.amazonaws.com/dmap-api-cache-ncc-production/'+uvDate+'/hourly/zip/45039.xml',
weatherData: 'https://forecast.weather.gov/MapClick.php?lat=39.3574&lon=-84.247&unit=0&lg=english&FcstType=xml'
}
const keys = Object.keys(xmlSources);

for (var i = 0; i < Object.keys(xmlSources).length; i++){



switch(i) {
  case 0:

      let oXHR_river1 = new XMLHttpRequest();

    function reportStatus1() {
        if (this.readyState == 4 && this.status == 200)               // REQUEST COMPLETED.
            observationList1(this.responseXML);      // ALL SET. NOW SHOW XML DATA.
    }

    oXHR_river1.onreadystatechange = reportStatus1;
    oXHR_river1.open("GET", xmlSources[keys[i]], true);      // true = ASYNCHRONOUS REQUEST (DESIRABLE), false = SYNCHRONOUS REQUEST.
    oXHR_river1.send();

    function observationList1(xml) {
        var divRecording = document.getElementById('riverLevel1');      // THE PARENT DIV.
        let Observation = xml.getElementsByTagName('observed');       // THE XML TAG NAME.

        let riverSiteAttr = Observation[0].parentNode.attributes;
        let riverSiteName = riverSiteAttr.getNamedItem("name").nodeValue;
        locationTitle1.innerHTML = riverSiteName;
        var loopXML = xml.getElementsByTagName('observed')[0].childNodes.length;

        for (var i = 0; i < xml.getElementsByTagName('observed')[0].childNodes.length; i++) {
            // CREATE CHILD DIVS INSIDE THE PARENT DIV.
            var divLeft = document.createElement('div');
            divLeft.className = 'observedFlow'+i;
            let obsPrimaryAttr = Observation[0].getElementsByTagName("primary")[i].attributes;
            let obsHeight = Number(Observation[0].getElementsByTagName("primary")[i].childNodes[0].nodeValue);
            let obsHeightPrev = (loopXML > i+1) ?
            Observation[0].getElementsByTagName("primary")[i+1].childNodes[0].nodeValue : 0;
            let obsHeightUnit = obsPrimaryAttr.getNamedItem("units").nodeValue;
            let obsHeightName = obsPrimaryAttr.getNamedItem("name").nodeValue;
            let rateChange = (obsHeight <= obsHeightPrev) ? "&#x25BC;":"&#x25B2;";
            rateChange = (obsHeightPrev == 0) ? " " : (obsHeight <= obsHeightPrev) ? "&#x25BC;":"&#x25B2;";
            let obsSecondaryAttr = Observation[0].getElementsByTagName("secondary")[i].attributes;
            let obsFlowRate = Observation[0].getElementsByTagName("secondary")[i].childNodes[0].nodeValue;
            let obsFlowUnit = obsSecondaryAttr.getNamedItem("units").nodeValue;
            let obsFlowName = obsSecondaryAttr.getNamedItem("name").nodeValue;
            let obsDateString = Observation[0].getElementsByTagName("valid")[i].childNodes[0].nodeValue;
            const obsDateArray = obsDateString.split("T");
            let obsDate = obsDateArray[0];

            divLeft.innerHTML = obsDate;

            var divRight = document.createElement('div');
            divRight.className = 'observedDate'+i;
            let stageLow = Observation[0].parentNode.getElementsByTagName("low")[0].childNodes[0].nodeValue;
            let stageAction = Observation[0].parentNode.getElementsByTagName("action")[0].childNodes[0].nodeValue;
            let stageBankFull = Observation[0].parentNode.getElementsByTagName("bankfull")[0].childNodes[0].nodeValue;
            let stageFlood = Observation[0].parentNode.getElementsByTagName("flood")[0].childNodes[0].nodeValue;
            let stageFloodModerate = Observation[0].parentNode.getElementsByTagName("moderate")[0].childNodes[0].nodeValue;
            //if (Observation[0].parentNode.getElementsByTagName("major")[0].childNodes[0].nodeValue){
                let stageFloodMajor = Observation[0].parentNode.getElementsByTagName("major")[0].childNodes[0].nodeValue;
            //} else {
            //    let stageFloodMajor = '100';      }
            let stageFloodRecord = Observation[0].parentNode.getElementsByTagName("record")[0].childNodes[0].nodeValue;
            
          //  console.log('height:'+obsHeight);
          //  console.log('low:'+stageLow);
          //  console.log('action:'+stageAction);
          //  console.log('full:'+stageBankFull);
          //  console.log('flood:'+stageFlood);
          //  console.log('moderate:'+stageFloodModerate);
          //  console.log('major:'+stageFloodMajor);
          //  console.log('record:'+stageFloodRecord);

if (obsHeight >= stageFloodRecord) { currentStage = 'record flood' } else
if (obsHeight >= stageFloodMajor) { currentStage = 'major flood' } else
if (obsHeight >= stageFloodModerate) { currentStage = 'moderate flood' } else
if (obsHeight >= stageBankFull) { currentStage = 'full' } else
if (obsHeight >= stageAction) { currentStage = 'action' } else
if (obsHeight > stageLow) { currentStage = 'low' } 
else { currentStage = "UNKNOWN2" }



            divRight.innerHTML = obsHeightName + ": " + obsHeight + obsHeightUnit + " " + rateChange + " " + currentStage + "<br>" + obsFlowName + ": " + obsFlowRate + obsFlowUnit;

            // ADD THE CHILD TO THE PARENT DIV.
            divRecording.appendChild(divLeft);
            divRecording.appendChild(divRight);
        }

    };
    console.log('xmlSource ' +xmlSources[keys[i]]+ ' called');
break;
case 1:

      let oXHR_river2 = new XMLHttpRequest();

    function reportStatus2() {
        if (this.readyState == 4 && this.status == 200)               // REQUEST COMPLETED.
            observationList2(this.responseXML);      // ALL SET. NOW SHOW XML DATA.
    }

    oXHR_river2.onreadystatechange = reportStatus2;
    oXHR_river2.open("GET", xmlSources[keys[i]], true);      // true = ASYNCHRONOUS REQUEST (DESIRABLE), false = SYNCHRONOUS REQUEST.
    oXHR_river2.send();

    function observationList2(xml) {
        var divRecording = document.getElementById('riverLevel2');      // THE PARENT DIV.
        let Observation = xml.getElementsByTagName('observed');       // THE XML TAG NAME.

        let riverSiteAttr = Observation[0].parentNode.attributes;
        let riverSiteName = riverSiteAttr.getNamedItem("name").nodeValue;
        locationTitle2.innerHTML = riverSiteName;
        var loopXML = xml.getElementsByTagName('observed')[0].childNodes.length;

        for (var i = 0; i < xml.getElementsByTagName('observed')[0].childNodes.length; i++) {
            // CREATE CHILD DIVS INSIDE THE PARENT DIV.
            var divLeft = document.createElement('div');
            divLeft.className = 'observedFlow'+i;
            let obsPrimaryAttr = Observation[0].getElementsByTagName("primary")[i].attributes;
            let obsHeight = Number(Observation[0].getElementsByTagName("primary")[i].childNodes[0].nodeValue);
            let obsHeightPrev = (loopXML > i+1) ?
            Observation[0].getElementsByTagName("primary")[i+1].childNodes[0].nodeValue : 0;
            let obsHeightUnit = obsPrimaryAttr.getNamedItem("units").nodeValue;
            let obsHeightName = obsPrimaryAttr.getNamedItem("name").nodeValue;
            let rateChange = (obsHeight <= obsHeightPrev) ? "&#x25BC;":"&#x25B2;";
            rateChange = (obsHeightPrev == 0) ? " " : (obsHeight <= obsHeightPrev) ? "&#x25BC;":"&#x25B2;";
            let obsSecondaryAttr = Observation[0].getElementsByTagName("secondary")[i].attributes;
            let obsFlowRate = Observation[0].getElementsByTagName("secondary")[i].childNodes[0].nodeValue;
            let obsFlowUnit = obsSecondaryAttr.getNamedItem("units").nodeValue;
            let obsFlowName = obsSecondaryAttr.getNamedItem("name").nodeValue;
            let obsDateString = Observation[0].getElementsByTagName("valid")[i].childNodes[0].nodeValue;
            const obsDateArray = obsDateString.split("T");
            let obsDate = obsDateArray[0];

            divLeft.innerHTML = obsDate;

            var divRight = document.createElement('div');
            divRight.className = 'observedDate'+i;
            let stageLow = Observation[0].parentNode.getElementsByTagName("low")[0].childNodes[0].nodeValue;
            let stageAction = Observation[0].parentNode.getElementsByTagName("action")[0].childNodes[0].nodeValue;
            let stageBankFull = Observation[0].parentNode.getElementsByTagName("bankfull")[0].childNodes[0].nodeValue;
            let stageFlood = Observation[0].parentNode.getElementsByTagName("flood")[0].childNodes[0].nodeValue;
            let stageFloodModerate = Observation[0].parentNode.getElementsByTagName("moderate")[0].childNodes[0].nodeValue;
            //if (Observation[0].parentNode.getElementsByTagName("major")[0].childNodes[0].nodeValue){
                let stageFloodMajor = Observation[0].parentNode.getElementsByTagName("major")[0].childNodes[0].nodeValue;
            //} else {
            //    let stageFloodMajor = '100';      }
            let stageFloodRecord = Observation[0].parentNode.getElementsByTagName("record")[0].childNodes[0].nodeValue;
            
          //  console.log('height:'+obsHeight);
          //  console.log('low:'+stageLow);
          //  console.log('action:'+stageAction);
          //  console.log('full:'+stageBankFull);
          //  console.log('flood:'+stageFlood);
          //  console.log('moderate:'+stageFloodModerate);
          //  console.log('major:'+stageFloodMajor);
          //  console.log('record:'+stageFloodRecord);

if (obsHeight >= stageFloodRecord) { currentStage = 'record flood' } else
if (obsHeight >= stageFloodMajor) { currentStage = 'major flood' } else
if (obsHeight >= stageFloodModerate) { currentStage = 'moderate flood' } else
if (obsHeight >= stageBankFull) { currentStage = 'full' } else
if (obsHeight >= stageAction) { currentStage = 'action' } else
if (obsHeight > stageLow) { currentStage = 'low' } 
else { currentStage = "UNKNOWN2" }



            divRight.innerHTML = obsHeightName + ": " + obsHeight + obsHeightUnit + " " + rateChange + " " + currentStage + "<br>" + obsFlowName + ": " + obsFlowRate + obsFlowUnit;

            // ADD THE CHILD TO THE PARENT DIV.
            divRecording.appendChild(divLeft);
            divRecording.appendChild(divRight);
        }

    };
    console.log('xmlSource ' +xmlSources[keys[i]]+ ' called');
break;
case 2:

    let oXHR1 = new XMLHttpRequest();

    function reportStatus2() {
        if (this.readyState == 4 && this.status == 200)               // REQUEST COMPLETED.
            uvList(this.responseXML);      // ALL SET. NOW SHOW XML DATA.
    }

    oXHR1.onreadystatechange = reportStatus2;
    oXHR1.open("GET", xmlSources[keys[i]], true);      // true = ASYNCHRONOUS REQUEST (DESIRABLE), false = SYNCHRONOUS REQUEST.
    oXHR1.send();

    function uvList(xml) {
        var divRecording = document.getElementById('UVLevel');      // THE PARENT DIV.
        let Observation = xml.getElementsByTagName('getEnvirofactsUVHourly');       // THE XML TAG NAME.

        //let UVAttr = Observation[0].attributes;
        //let UVCount = UVAttr.getNamedItem("Count").nodeValue;
        //uvTitle.innerHTML = UVCount;
        uvTitle.innerHTML = 'UV Data from EPA';

        for (var i = 0; i < Observation.length; i++) {

            // CREATE CHILD DIVS INSIDE THE PARENT DIV.
            var divLeft = document.createElement('div');
            divLeft.className = 'uvLevel'+i;
            let uvTime = Observation[i].getElementsByTagName("DATE_TIME")[0].childNodes[0].nodeValue;

            divLeft.innerHTML = uvTime;

            var divRight = document.createElement('div');
            divRight.className = 'uvTime'+i;
            let uvLevelNum = Number(Observation[i].getElementsByTagName("UV_VALUE")[0].childNodes[0].nodeValue);

            divRight.innerHTML = '<strong>' + uvLevelNum + '</strong>';

            // ADD THE CHILD TO THE PARENT DIV.
            divRecording.appendChild(divLeft);
            divRecording.appendChild(divRight);
        }

    };
    console.log('xmlSource ' +xmlSources[keys[i]]+ ' called');
break;
case 3:
    let oXHR2 = new XMLHttpRequest();

    function reportStatus3() {
        if (this.readyState == 4 && this.status == 200)               // REQUEST COMPLETED.
            tempList(this.responseXML);      // ALL SET. NOW SHOW XML DATA.
    }

    oXHR2.onreadystatechange = reportStatus3;
    oXHR2.open("GET", xmlSources[keys[i]], true);      // true = ASYNCHRONOUS REQUEST (DESIRABLE), false = SYNCHRONOUS REQUEST.
    oXHR2.send();

    function tempList(xml) {
        var divRecording = document.getElementById('tempLevel');      // THE PARENT DIV.
        let Observation = xml.getElementsByTagName('period');       // THE XML TAG NAME.

        //let UVAttr = Observation[0].attributes;
        //let UVCount = UVAttr.getNamedItem("Count").nodeValue;
        //uvTitle.innerHTML = UVCount;
        weatherTitle.innerHTML = 'Weather Data from NOAA';

        for (var i = 0; i < xml.getElementsByTagName('period').length; i++) {

            // CREATE CHILD DIVS INSIDE THE PARENT DIV.
            var divLeft = document.createElement('div');
            divLeft.className = 'tempLevelNum'+i;
            let tempLevelValid = Observation[i].getElementsByTagName("valid")[0].childNodes[0].nodeValue;
            
            

            divLeft.innerHTML = '<strong>' + tempLevelValid + '</strong> ';

            var divRight = document.createElement('div');
            divRight.className = 'tempLevelDescr'+i;
            let tempTime = Observation[i].getElementsByTagName("text")[0].childNodes[0].nodeValue;
            let tempLevelNum = Number(Observation[i].getElementsByTagName("temp")[0].childNodes[0].nodeValue);
            let tempLevelDescr = Observation[i].getElementsByTagName("weather")[0].childNodes[0].nodeValue;
            
            divRight.innerHTML =  tempLevelNum + '&deg; <small>' + tempLevelDescr + '</small>';

            // ADD THE CHILD TO THE PARENT DIV.
            divRecording.appendChild(divLeft);
            divRecording.appendChild(divRight);
        }

    };
    console.log('xmlSource ' +xmlSources[keys[i]]+ ' called');
break;
default:
console.log('default xmlSource called');
}

//var xhttp = new XMLHttpRequest();
//xhttp.onreadystatechange = function() {
//    if (this.readyState == 4 && this.status == 200) {
//        myFunction(this);
//    }
//};

}
</script>
</div>
</body>
</html>
