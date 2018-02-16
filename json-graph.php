<?php
function decimal($number){
        $x = number_format($number, 1);
        return $x;
}
function fot_to_meter($fot){
        $meter = $fot/3.6;
        $meter = decimal($meter);
        return $meter;
}
function timecalc($time){// recalc time from unix to "Ymd H:i"
        $epoch =  $time+3600;
        $dt = new DateTime("@$epoch");
        $timeOut = $dt->format('Y-m-d H:i');
        return $timeOut;
}
function timecalc_day($time){// recalc time from unix to "H:i"
        $epoch =  $time+3600;
        $dt = new DateTime("@$epoch");
        $timeOut = $dt->format('H:i');
        return $timeOut;
}
function latest(){
    $conn = mysqli_connect("localhost","root","Dalmas!4","timje_se");

    // Check connection
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }else{
        $sql = "SELECT * FROM weewx ORDER BY dateTime DESC LIMIT 1"; // get latest
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {

                $finfo = $result->fetch_fields();
                foreach ($finfo as $val) {// hemtar namnen på alla rader
                    $array2[] =$val->name;
                }
                // lägger in värdet i array där namnen är hemtade sedan innan
                foreach ($array2 as $key){
                    $array[$key] =$row[$key];
                }
                //resunerar array (värden men där array key är samma som i mysql
                return $array;
            }
        } else {
            echo "0 results";
        }
        $conn->close();
    }
}
function select_other($thing){
    $conn = mysqli_connect("localhost","root","Dalmas!4","timje_se");

    // Check connection
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }else{
        $sql = "SELECT * FROM weewx_day_$thing ORDER BY dateTime DESC LIMIT 1"; // get latest
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $finfo = $result->fetch_fields();
                foreach ($finfo as $val) {// hemtar namnen på alla rader
                    $array2[] =$val->name;
                }
                // lägger in värdet i array där namnen är hemtade sedan innan
                foreach ($array2 as $key){
                    $array[$key] = $row[$key];
                }
                //var_dump($array);
                //resunerar array (värden men där array key är samma som i mysql
                return $array;
            }
        } else {
            echo "0 results";
        }
    $conn->close();
    }
}
function latest_close_to($date, $end_date){
$conn = mysqli_connect("localhost","root","Dalmas!4","timje_se");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }else{
                $sql = "SELECT * FROM weewx WHERE dateTime BETWEEN $end_date AND $date ORDER BY dateTime DESC"; // get latest
                 $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                                // output data of each row
				$ost = [];
                                while($row = $result->fetch_assoc()) {
					$ost[]=$row;
				}
				return $ost;
                } else {
                        echo "0 results";
                }
                $conn->close();
  }
}
function select_other_close_to($thing, $date, $end_date){
        $conn = mysqli_connect("localhost","root","Dalmas!4","timje_se");

        // Check connection
        if (mysqli_connect_errno())
          {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
          }else{
                        $sql = "SELECT * FROM weewx_day_$thing ORDER BY dateTime DESC"; // get latest
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                                 while($row = $result->fetch_assoc()) {
					$tid= [];
					foreach($result->num_rows as $rad){
						$tid[] = $row['dateTime'];
					}
					return $tid;
				}
                        } else {
                                echo "0 results";
                        }

        $conn->close();
        }
}
if (isset($_GET['start_date']) && isset($_GET['end_date'])){
    $date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    //convertera unix tiden så att det blir kl 00
    $windGust = select_other_close_to("windGust",$date, $end_date);
    $outTemp = select_other_close_to("outTemp",$date, $end_date);
    $rain = select_other_close_to("rain",$date, $end_date);
    $latest = latest_close_to($date, $end_date);
var_dump($latest);

}elseif (isset($_GET['start_date'])){
    $date = $_GET['start_date'];
    $end_date = strtotime('-1 day', $date); //Date + standard värde
    //convertera unix tiden så att det blir kl 00
    $windGust = select_other_close_to("windGust",$date, $end_date);
    $outTemp = select_other_close_to("outTemp",$date, $end_date);
    $rain = select_other_close_to("rain",$date, $end_date);
    $latest = latest_close_to($date, $end_date);
//var_dump($latest);
var_dump($rain);
} else {
    $date = date("U");
    //limit i selectorn
    //hämtar datan start
    $windGust = select_other("windGust");
    $outTemp = select_other("outTemp");
    $rain = select_other("rain");
    $latest = latest();
}


if ($latest["windDir"]=== NULL){
        $windDir= "0";
}else{
        $windDir = $latest["windDir"];
}
/*slut*/
//var_dump($latest,$outTemp);//Test rad
/*output Start*/

$time = timecalc($latest['dateTime']); // time Y-m-d H:i
$outdoor = decimal($latest["outTemp"]). "&deg;C"; //Outdoor temp decgre C
$dayHigh = decimal($outTemp["max"]). "&deg;C";//outdoor high temp whit one decimal
$dayHighTime = timecalc_day($outTemp["maxtime"]); //day high time
$dayLow = decimal($outTemp["min"]). "&deg;C";
$dayLowTime = timecalc_day($outTemp["mintime"]);//no decimal

$windChill = decimal($latest["windchill"])."&deg;C";
$heatIndex = decimal($latest["heatindex"])."&deg;C";

$dewpoint = decimal($latest["dewpoint"])."&deg;C";

$humidity = decimal($latest["outHumidity"])."%Rh";//no decimal

$airPressure = round($latest["barometer"])." mbar";//no decimal

$rain = decimal(($rain["sum"]*10))." mm";
$rainRate = $latest["rainRate"];


$wind = fot_to_meter($latest["windSpeed"]). " m/sec";
$windSpeed = decimal($latest["windSpeed"]). " km/h";
$windDir = $windDir. "&deg;"; //no decimal riktning
$windDayHigh = fot_to_meter($windGust['max']). " m/sec";
$windDayHighTime = timecalc_day($windGust['maxtime']);
$windDayLow = fot_to_meter($windGust['min']). " m/sec";
$windDayLowTime = timecalc_day($windGust['mintime']);

$indoor = decimal($latest["inTemp"])."&deg;C ";


$sunriseTime = date_sunrise(time(), SUNFUNCS_RET_STRING,57.7777 , 014.3355, 90, 2);
$sunsetTime = date_sunset(time(), SUNFUNCS_RET_STRING,57.7777 , 014.3355, 90, 2);


// json string
$data = "{
    'time': '$time',
    'Outdoor': '$outdoor',
    'dayHigh': '$dayHigh',
    'dayHighTime': '$dayHighTime',
    'dayLow': '$dayLow',
    'dayLowTime': '$dayLowTime',

    'windChill': '$windChill',
    'heatIndex': '$heatIndex',

    'dewpoint': '$heatIndex',
    'humidity': '$humidity',

    'airPressure': '$airPressure',
    'rain':'$rain',
    'rainRate':'$rainRate',

    'wind': '$wind',
    'windSpeed': '$windSpeed',
    'windDir': '$windDir',
    'windDayHigh': '$windDayHigh',
    'windDayHighTime': '$windDayHighTime',
    'windDayLow': '$windDayLowTime',
    'windDayLowTime': '$windDayLowTime',

    'indoor': '$indoor',

    'sunriseTime': '$sunriseTime',
    'sunsetTime': '$sunsetTime'
}"; 

if(array_key_exists('callback', $_GET)){

    header('Content-Type: text/javascript; charset=utf8');
    header('Access-Control-Allow-Origin: http://www.timje.se/');
    header('Access-Control-Max-Age: 0');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

    $callback = $_GET['callback'];
    echo $callback.'('.$data.');';

}else{
    // normal JSON string
    header('Content-Type: application/json; charset=utf8');

    echo $data;
}

?>

