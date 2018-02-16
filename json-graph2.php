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
function latest($startTime, $endTime, $options){
    $conn = mysqli_connect("localhost","root","Dalmas!4","timje_se");

    // Check connection
    if (mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }else{
        $sql = "SELECT dateTime ".$options."  FROM weewx WHERE dateTime BETWEEN $endTime AND $startTime ORDER BY dateTime DESC"; // get latest
	//var_dump($sql, $options);
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
	$data = [];
	$dayrain = 0;
	$first = 0;
	$pre = "";
            while($row = $result->fetch_assoc()) {
		$dayrain += $row['rain'];
		
		/*var_dump($row);
		echo "<br>";*/ 
		
		//samlar hop rängn mängd per dag
		if ($first != 0){
			$row['dayRain'] = $dayrain;
			if(sameday($pre, $row['dateTime'])){
                       		$dayrain = 0;
			}
		}else{
			$first = 1;
		}
               	$data[] = $row;

		$pre = $row['dateTime'];
            }
	return $data;

        } else {
            echo "0 results";
        }
        $conn->close();
    }
}

function sameday($firstDateIn, $secondDateIn){ // kollar om det är samma dag på 2 tider
	$firstDate = date("Y-m-d", $firstDateIn);
	$secondDate = date("Y-m-d", $secondDateIn);
	if($firstDate != $secondDate){
		return true;
	}else{
		return false;
	}
}

function test_input($data) {

  $conn = mysqli_connect("localhost","root","Dalmas!4","timje_se");

  $data = trim($data);

  $data = stripslashes($data);

  $data = htmlspecialchars($data);

  $data = mysqli_real_escape_string ( $conn , $data );

  return $data;

}

function requsts(){
	//array med möjliga variabled
	$options =array("outTemp"=>1, "rain"=>1, "windGust"=>1, "outHumidity"=>1, "barometer"=>1, "windSpeed"=>1, "windDir"=>1);

	//$array = array("foo", "bar", "hello", "world");
	foreach($_GET as $key => $value){
		//echo $key. " med value = ". $value."\n";
		//echo $key."\n";
		if(array_key_exists($key, $options)){
			$options[$key] = 2;
			//echo $options[$key]."\n";
		}
	}
	$returns = "";
	
	foreach ($options as $option => $status){
		if ($status == 2){
			$returns = $returns.", $option";
			//echo "\n".$returns;
		}
	}
	return $returns;
}

$client = $_SERVER['REMOTE_ADDR'];

	$options = requsts();
	if (empty($options)){
		$options = ", outTemp, outHumidity, barometer, rain, windGust, windSpeed, windDir";
	}
	//var_dump($options);



if (isset($_GET['start_date']) && isset($_GET['end_date'])){
    $date = test_input($_GET['start_date']);
    $date = strtotime($date);
    $end_date = test_input($_GET['end_date']);
    $end_date = strtotime($end_date);
    //convertera unix tiden så att det blir kl 00
    $data = latest($date, $end_date, $options);

}elseif (isset($_GET['start_date'])){
    $date = test_input($_GET['start_date']);
    $date = strtotime($date);
    $end_date = strtotime('-1 day', $date); //Date + standard värde
    //convertera unix tiden så att det blir kl 00
    $data = latest($date, $end_date, $options);
} else {
    $date = date("U");
    $end_date = strtotime('-1 day', $date);
    $data = latest($date, $end_date, $options);
}

//$data = latest("1459033200");

//var_dump($data);
$json = json_encode($data);
if(array_key_exists('callback', $_GET)){

    header('Content-Type: text/javascript; charset=utf8');
    header('Access-Control-Allow-Origin: http://www.timje.se/');
    header('Access-Control-Max-Age: 0');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

    $callback = test_input($_GET['callback']);
    echo $callback.'('.$json.');';

}else{
    // normal JSON string
    header('Content-Type: application/json; charset=utf8');

    print $json;
}

?>
