<?php
function timecalc($time){// recalc time from unix to "Ymd His"
	$epoch =  $time; 
	$dt = new DateTime("@$epoch");
	$timeOut = $dt->format('Y-m-d H:i:s');
	return $timeOut;
	}
function latest(){
$conn = mysqli_connect("localhost","root","Dalmas!4","timje_se");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }else{
	  	$sql = "SELECT * FROM weewx ORDER BY dateTime DESC LIMIT 1"; // get latest
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				
				$array = [ // sets an array whit wether data to return from function
				'time' => $row["dateTime"],
				'outTemp' => $row["outTemp"],
					//high and low in other table;
				'windchill' => $row["windchill"],
				'heatindex' => $row["heatindex"],
				'dewpoint' => $row["dewpoint"],
				'outHumidity' => $row["outHumidity"],
				'pressure' => $row["pressure"],
					//trend in other table;
				'rain' => $row["rain"],
				'windSpeed' => $row["windSpeed"],
				'windDir' => $row["windDir"],
				'inTemp' => $row["inTemp"],
				];
				
				return $array;
			}
		} else {
			echo "0 results";
		}
		$conn->close();
  }
  
/*
+----------------------+---------+------+-----+---------+-------+
| Field                | Type    | Null | Key | Default | Extra |
+----------------------+---------+------+-----+---------+-------+
| dateTime             | int(11) | NO   | PRI | NULL    |       |
| usUnits              | int(11) | NO   |     | NULL    |       |
| interval             | int(11) | NO   |     | NULL    |       |
| barometer            | double  | YES  |     | NULL    |       |
| pressure             | double  | YES  |     | NULL    |       |
| altimeter            | double  | YES  |     | NULL    |       |
| inTemp               | double  | YES  |     | NULL    |       |
| outTemp              | double  | YES  |     | NULL    |       |
| inHumidity           | double  | YES  |     | NULL    |       |
| outHumidity          | double  | YES  |     | NULL    |       |
| windSpeed            | double  | YES  |     | NULL    |       |
| windDir              | double  | YES  |     | NULL    |       |
| windGust             | double  | YES  |     | NULL    |       |
| windGustDir          | double  | YES  |     | NULL    |       |
| rainRate             | double  | YES  |     | NULL    |       |
| rain                 | double  | YES  |     | NULL    |       |
| dewpoint             | double  | YES  |     | NULL    |       |
| windchill            | double  | YES  |     | NULL    |       |
| heatindex            | double  | YES  |     | NULL    |       |
| ET                   | double  | YES  |     | NULL    |       |
| radiation            | double  | YES  |     | NULL    |       |
| UV                   | double  | YES  |     | NULL    |       |
| extraTemp1           | double  | YES  |     | NULL    |       |
| extraTemp2           | double  | YES  |     | NULL    |       |
| extraTemp3           | double  | YES  |     | NULL    |       |
| soilTemp1            | double  | YES  |     | NULL    |       |
| soilTemp2            | double  | YES  |     | NULL    |       |
| soilTemp3            | double  | YES  |     | NULL    |       |
| soilTemp4            | double  | YES  |     | NULL    |       |
| leafTemp1            | double  | YES  |     | NULL    |       |
| leafTemp2            | double  | YES  |     | NULL    |       |
| extraHumid1          | double  | YES  |     | NULL    |       |
| extraHumid2          | double  | YES  |     | NULL    |       |
| soilMoist1           | double  | YES  |     | NULL    |       |
| soilMoist2           | double  | YES  |     | NULL    |       |
| soilMoist3           | double  | YES  |     | NULL    |       |
| soilMoist4           | double  | YES  |     | NULL    |       |
| leafWet1             | double  | YES  |     | NULL    |       |
| leafWet2             | double  | YES  |     | NULL    |       |
| rxCheckPercent       | double  | YES  |     | NULL    |       |
| txBatteryStatus      | double  | YES  |     | NULL    |       |
| consBatteryVoltage   | double  | YES  |     | NULL    |       |
| hail                 | double  | YES  |     | NULL    |       |
| hailRate             | double  | YES  |     | NULL    |       |
| heatingTemp          | double  | YES  |     | NULL    |       |
| heatingVoltage       | double  | YES  |     | NULL    |       |
| supplyVoltage        | double  | YES  |     | NULL    |       |
| referenceVoltage     | double  | YES  |     | NULL    |       |
| windBatteryStatus    | double  | YES  |     | NULL    |       |
| rainBatteryStatus    | double  | YES  |     | NULL    |       |
| outTempBatteryStatus | double  | YES  |     | NULL    |       |
| inTempBatteryStatus  | double  | YES  |     | NULL    |       |
+----------------------+---------+------+-----+---------+-------+

  */ //values
}
function outTemp(){
	$conn = mysqli_connect("localhost","root","Dalmas!4","timje_se");
	
	// Check connection
	if (mysqli_connect_errno())
	  {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	  }else{
			$sql = "SELECT * FROM weewx_day_outTemp ORDER BY dateTime DESC LIMIT 1"; // get latest
			$result = $conn->query($sql);
			
			if ($result->num_rows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					
					$array= [
					'time' => $row["dateTime"],
					'min' => $row["min"],
					'mintime' => $row["mintime"],
					'max' => $row["max"],
					'maxtime' => $row["maxtime"],
					];
					
					return $array;
				}
			} else {
				echo "0 results";
			}
			
	$conn->close();
	}
}

$outTemp = outTemp();
$latest = latest();
//var_dump($latest,$outTemp);//Test rad
echo "latest data colected at ".timecalc($latest['time']);
echo "<br>";
echo "Out temp now: ".$latest["outTemp"];
echo "<br>";
echo "Max out temp today: ". $outTemp["max"]. " at ".timecalc($outTemp["maxtime"]);
echo "<br>";
echo "Min out temp today: ". $outTemp["min"]. " at ".timecalc($outTemp["mintime"]);
echo "<br>";
echo "Wind Chill: ".$latest["windchill"];
echo "<br>";
echo "Heat Index: ".$latest["heatindex"];
echo "<br>";
echo "Dewpoint: ".$latest["dewpoint"];
echo "<br>";
echo "Humidity: ".$latest["outHumidity"];
echo "<br>";
echo "Air presure: ".$latest["pressure"];
echo "<br>";
echo "Today's Rain: ".$latest["rain"];
echo "<br>";
echo "Wind: ".$latest["wind"]. " from ". $latest["windDir"];
echo "<br>";
echo "Inside Temperature: ".$latest["inTemp"];
echo "<br>";



//echo timecalc($outTemp['mintime']);
echo "<br>";
echo timecalc(date('U'));
//echo "hej";

?>
