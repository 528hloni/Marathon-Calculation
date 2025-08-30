<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Marathon</h1>
    <br>

<form method = "post">
    <label for="total_distance">Total Marathon Distance (km):</label>
    <input type="number" name="total_distance" id="total_distance" step="any" />
    <br>
    <label for="distance_covered">Distance Already Covered (km):</label>
    <input type="number" name="distance_covered" id="distance_covered" step="any" />
    <br>
    <label for="elapsed_time">Elapsed Time Since Start (hours):</label>
    <input type="number" name="elapsed_time" id="elapsed_time" step="any"/>
    <br>
    <label for="target_time">Target Time To Complete Marathon (hours):</label>
    <input type="number" name="target_time" id="target_time" step="any" />
    <br>
    <br>
    <button type="submit" name="action" value="Calculate"> Calculate </button>
    <hr>
    <h2> Calculations Results: </h2>
    <br>
</form>

    



    
</body>
</html>


<?php
$valid = true; // boolean variable for input validation


function validate_inputs($total_distance, $distance_covered, $elapsed_time, $target_time) { // input validation function
    if ($total_distance <= 0 || $distance_covered < 0 || $elapsed_time < 0 || $target_time <= 0) {
        return false;
       
    }else{
        return true;
    }
}

function calculate_average_speed($distance_covered, $elapsed_time) { // calculate average speed function
     if ($elapsed_time == 0) return 0;
    return $distance_covered / $elapsed_time;

}



function calculate_required_speed($total_distance, $distance_covered, $elapsed_time, $target_time) { // calculate required speed function
 $remaining_distance = $total_distance - $distance_covered;
 $remaining_time = $target_time - $elapsed_time;
 if ($remaining_time <= 0) return 0;
 return $remaining_distance / $remaining_time;
 
}

function formated_results($current_average_speed, $required_speed){
    $f_current_average_speed = number_format($current_average_speed, 2) . " km/h";
    $f_required_speed = number_format($required_speed, 2) . " km/h";
    echo "Current Average Speed: " . $f_current_average_speed . "<br>";
    echo "Required Speed To Finish On Time: " . $f_required_speed . "<br> <br>";

}


function store_race_data(&$race_history, $total_distance, $covered_distance, $elapsed_time, $target_time, $current_speed, $required_speed) {
    // Create array for current session data
    $current_session = [
        $total_distance,
        $covered_distance,
        $elapsed_time,
        $target_time,
        $current_speed,
        $required_speed
    ];
    
    // Add current session to the historical array
    $race_history[] = $current_session;
    
    echo "Race data stored successfully!<br>";
}

function save_to_file($race_history, $file_name = "marathon_calculations.txt") {
    // Prepare the data to write
    $file_content = "Marathon Speed Calculator - Race Data\n";
    $file_content .= "Generated on: " . date("Y-m-d H:i:s") . "\n";
    $file_content .= str_repeat("=", 50) . "\n\n";
    
    // Check if there's data to save
    if (empty($race_history)) {
        $file_content .= "No race data available.\n";
    } else {
        // Add headers
        $file_content .= "Session | Total Distance | Covered Distance | Elapsed Time | Target Time | Current Speed | Required Speed\n";
        $file_content .= str_repeat("-", 100) . "\n";
        
        // Add each session's data
        foreach ($race_history as $index => $session) {
            $session_num = $index + 1;
            $file_content .= sprintf(
                "   %d    |     %.2f km    |     %.2f km      |    %.2f h    |   %.2f h    |    %.2f km/h   |    %.2f km/h\n",
                $session_num,
                $session[0], // total distance
                $session[1], // covered distance
                $session[2], // elapsed time
                $session[3], // target time
                $session[4], // current speed
                $session[5]  // required speed

             
            );
        


            
        }
    }

     // Add footer
    $file_content .= "\n" . str_repeat("=", 50) . "\n";
    $file_content .= "End of Data\n";
    
    // Write to file
    $result = file_put_contents($file_name, $file_content);
    
    if ($result !== false) {
        echo "Data saved to file: " . $file_name . " successfully!<br>";
        return true;
    } else {
        echo "Error: Could not save data to file.<br>";
        return false;
    }
}




$race_history = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $total_distance = filter_input(INPUT_POST, 'total_distance', FILTER_VALIDATE_FLOAT);
    $distance_covered = filter_input(INPUT_POST, 'distance_covered', FILTER_VALIDATE_FLOAT);
    $elapsed_time = filter_input(INPUT_POST, 'elapsed_time', FILTER_VALIDATE_FLOAT);
    $target_time = filter_input(INPUT_POST, 'target_time', FILTER_VALIDATE_FLOAT);


      $valid = validate_inputs($total_distance, $distance_covered, $elapsed_time, $target_time);

    


    if($valid == false){
        echo"Inputs are invalid";
        $valid = true;
    }else if ($valid == true){
      
        $current_average_speed = calculate_average_speed($distance_covered, $elapsed_time);
        $required_speed = calculate_required_speed($total_distance, $distance_covered, $elapsed_time, $target_time);
        
        if($action == 'Calculate'){
           formated_results($current_average_speed, $required_speed);
           store_race_data($race_history, $total_distance, $distance_covered, $elapsed_time, $target_time, $current_average_speed, $required_speed);
           save_to_file($race_history);
          

        
        }

    }
}




?>