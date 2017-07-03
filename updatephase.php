<?php

$completeness = strval($_GET['complete']);
$id = strval($_GET['id']);
$limit = strval($_GET['i']);  

include('db.php');

 function isWeekend($date) {
        return (date('N', strtotime($date)) >= 6);
    }

if ($completeness == '1'){
    $myphases = $mysqli->query("SELECT * FROM `fasi` WHERE `fasi`.`id` = '".$id."'");
    $phase = $myphases->fetch_assoc();
    if ($phase['end'] < date("Y-m-d")){
        $mydata = $mysqli->query("UPDATE `fasi` SET `complete` = '1' WHERE `fasi`.`id` = '".$id."'");
    }
    else{
        $datetime1 = date_create(date("Y-m-d"));
        $datetime2 = date_create($phase['end']);     
        $interval = date_diff($datetime1, $datetime2);
        $intervallo = ''.$interval->format("%a");
        
        $mydata = $mysqli->query("UPDATE `fasi` SET `complete` = '1', end = '".date("Y-m-d")."' WHERE `fasi`.`id` = '".$id."'");
        
        $phases_to_update = $mysqli->query("SELECT * FROM `fasi` WHERE `fasi`.`id_project` = '".$phase['id_project']."' AND start >= '".date("Y-m-d")."'ORDER BY start ASC");
        $num_phases_to_update = $phases_to_update->num_rows;
        
        for ($i = 0; $i< $num_phases_to_update;$i++){
            $phase_to_up = $phases_to_update->fetch_assoc();
            
            $mydata = $mysqli->query("UPDATE `fasi` SET start = '".($day = date("Y-m-d", strtotime($phase_to_up['start'] . ' -'.$intervallo.' day')))."', end = '".($day = date("Y-m-d", strtotime($phase_to_up['end'] . ' -'.$intervallo.' day')))."' WHERE `fasi`.`id` = '".$phase_to_up['id']."'");
            
        }
        
    }
    
}
else{
    $end = strval($_GET['end']);
   
    $mydata = $mysqli->query("SELECT * FROM `fasi` WHERE `fasi`.`id` = ".$id."");
    $main_phase = $mydata->fetch_assoc();
    
    $datetime1 = date_create($main_phase['end']);
    $datetime2 = date_create($end);     
    $interval = date_diff($datetime1, $datetime2);
    
   
    
    $intervallo = ''.$interval->format("%a");
    
    $mydata = $mysqli->query("UPDATE `fasi` SET `end` = '".$end."' WHERE `id` = ".$id."");
    
    $phases_to_update = $mysqli->query("SELECT * FROM `fasi` WHERE `fasi`.`id_project` = '".$main_phase['id_project']."' AND start > '".$main_phase['start']."'ORDER BY start ASC");
    $num_phases_to_update = $phases_to_update->num_rows;
        
    for ($i = 0; $i< $num_phases_to_update;$i++){
        $phase_to_up = $phases_to_update->fetch_assoc();
        
        if ($datetime1 > $datetime2){
            $mydata = $mysqli->query("UPDATE `fasi` SET start = '".(date("Y-m-d", strtotime($phase_to_up['start'] . ' -'.$intervallo.' day')))."', end = '".(date("Y-m-d", strtotime($phase_to_up['end'] . ' -'.$intervallo.' day')))."' WHERE `fasi`.`id` = '".$phase_to_up['id']."'");
        }   
        else{
            $mydata = $mysqli->query("UPDATE `fasi` SET start = '".(date("Y-m-d", strtotime($phase_to_up['start'] . ' +'.$intervallo.' day')))."', end = '".(date("Y-m-d", strtotime($phase_to_up['end'] . ' +'.$intervallo.' day')))."' WHERE `fasi`.`id` = '".$phase_to_up['id']."'");
        }
    }
      
}

$myphases = $mysqli->query("SELECT * FROM `fasi` WHERE `fasi`.`id` = '".$id."'");
$phase = $myphases->fetch_assoc();

$myphas = $mysqli->query("SELECT * FROM `fasi` WHERE `id_project` = '".$phase['id_project']."' AND complete = '0'");
$num_phas = $myphas->num_rows;

if($num_phas == 0){
    $mydata = $mysqli->query("DELETE FROM `projects` WHERE `projects`.`id` = '".$phase['id_project']."'");
    $mydata = $mysqli->query("DELETE FROM `fasi` WHERE `id_project` = '".$phase['id_project']."'");
}

$mydata = $mysqli->query("SELECT * from projects");

for ($i = 0; $i < $limit; $i++){
    $row = $mydata->fetch_assoc();
}

$row = $mydata->fetch_assoc();
                            
                            //calculate the total interval of the project, knowing all the phases which take part on it
                            $myphases = $mysqli->query("SELECT * from fasi WHERE id_project = '".$row['id']."' ORDER by start ASC");
                            
                            $num_phases = $myphases->num_rows;
                            if ($num_phases > 0){
                            $phase = $myphases->fetch_assoc();
                    
                            $first = $phase['start'];
                            
                            for ($j = 0;$j <$num_phases-1; $j++){
                                $phase = $myphases->fetch_assoc();
                            
                            }
                            $second = $phase['end'];
                            
                            $datetime1 = date_create($first);
                            $datetime2 = date_create($second);
                    
                            $interval = date_diff($datetime1, $datetime2);
                            $max = $interval->format('%a')+1;
                    
                            //need to reselect/restart the selection
                            //maybe there is a more efficient way to do this
                            $myphases = $mysqli->query("SELECT * from fasi WHERE id_project = '".$row['id']."' ORDER by start ASC");
                            $num_phases = $myphases->num_rows;
                            
                            //phases division
                            for ($j = 0;$j <$num_phases; $j++){

                                $phase = $myphases->fetch_assoc();
                                echo "<div class='top_bar";
                                if($j == 0){
                                        echo " first_top";
                                    }
                                echo "' style='width:".(99*(date_diff(date_create($phase['start']), date_create($phase['end']))->format('%a')+1)/$max)."%'>";
                                echo $phase['name'];
                                echo "</div>";
                            }
                            
                            $day = date("Y-m-d", strtotime($first . ' -1 day'));
                            //days divider
                            for ($j = 0;$j <$max; $j++){
                                $day = date("Y-m-d", strtotime($day . ' +1 day'));
                                $date = DateTime::createFromFormat("Y-m-d", $day);
                                echo "<div class='middle_bar";
                                if($j == 0){
                                        echo " first_middle";
                                }
                                if($day == date("Y-m-d")){
                                    echo " red";
                                }
                                else if(isWeekend($day)){
                                    echo " gray";
                                }
                                echo "' style='width:".(99/$max)."%'>";
                                
                                echo $date->format("d");
                                echo "</div>";
                            }
                    
                            //progress bar
                            $myphases = $mysqli->query("SELECT * from fasi WHERE id_project = '".$row['id']."' ORDER by start ASC");
                            $num_phases = $myphases->num_rows;
                            $before_complete = 0;
                            for ($j = 0;$j <$num_phases; $j++){

                                $phase = $myphases->fetch_assoc();
                                echo "<div class='bar";
                                
                                //if the phase has already finished
                                if (date("Y-m-d")>$phase['end'] ){
                                    if($j == 0){
                                        echo " first";
                                    }
                                    else if($j == $num_phases - 1){
                                        echo " last";
                                    }
                                    if ($phase['complete']== 1){
                                        echo " green";
                                    }
                                    else{
                                        echo " red";
                                    }
                                    echo "' style='width:".(99*(date_diff(date_create($phase['start']), date_create($phase['end']))->format('%a')+1)/$max)."%'>";
                                    echo "</div>";
                                }
                                //if the phase is in progress
                                else if (date("Y-m-d")>=$phase['start'] && date("Y-m-d")<=$phase['end']){
                                    if($j == 0){
                                        echo " first green";
                                    }
                                    else if($before_complete == '1'){
                                        echo " green";
                                    }
                                    else{
                                        echo " red";
                                    }
                                    echo "' style='width:".(99*(date_diff(date_create($phase['start']), date_create(date("Y-m-d")))->format('%a')+1)/$max)."%'>";
                                    echo "</div>";
                                    echo "<div class='bar";
                                    if($j == $num_phases - 1){
                                        echo " last";
                                    }
                                    echo "' style='width:".(99*(date_diff(date_create(date("Y-m-d")), date_create($phase['end']))->format('%a'))/$max)."%'></div>";
                                }
                                //if the phase has not started yet
                                else{
                                    if($j == 0){
                                        echo " first";
                                    }
                                    else if($j == $num_phases - 1){
                                        echo " last";
                                    }
                                    echo "' style='width:".(99*(date_diff(date_create($phase['start']), date_create($phase['end']))->format('%a')+1)/$max)."%'>";
                                    echo "</div>";
                                }
                                $before_complete = $phase['complete'];
                            }
                            
                            $myphases = $mysqli->query("SELECT * from fasi WHERE id_project = '".$row['id']."' ORDER by start ASC");
                            $num_phases = $myphases->num_rows;
                            
                            //calculate next deadline and display it
                            for ($j = 0;$j <$num_phases; $j++){
                                $phase = $myphases->fetch_assoc();
                                $next_deadline = "Ultima scadenza già passata";
                                if (date("Y-m-d")>=$phase['start'] && date("Y-m-d")<=$phase['end']){
                                    $next_deadline = $phase['name'].", fra ".date_diff(date_create(date("Y-m-d")), date_create($phase['end']))->format('%a')." giorni";
                                }
                                if (date("Y-m-d")>=$phase['start'] && $phase['complete']==0){
                                    echo "<div class='finished_phase'>";
                                    echo "Hai finito ".$phase['name']."?";
                                    echo "<button type=\"button\" class=\"btn btn-success\" style='margin-left:20px' onClick=\"updatephase('1','".$phase['id']."','".$i."')\">Sì</button>";
                                    echo "<button type=\"button\" class=\"btn btn-danger\" style='margin-left:20px' data-toggle=\"modal\" data-target=\"#modal".$i.$j."\">No</button>
                                    <div class=\"modal fade\" id=\"modal".$i.$j."\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">
                                        <div class=\"modal-dialog\" role=\"document\">
                                            <div class=\"modal-content\">
                                                <div class=\"modal-header\">
                                                    <h5 class=\"modal-title\" id=\"exampleModalLabel\">".$phase['name']."</h5>
                                                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                                        <span aria-hidden=\"true\">&times;</span>
                                                    </button>
                                                </div>
                                                <div class=\"modal-body\">
                                                    <label for=\"update_end\" class=\"col-5 col-form-label\">Nuova data Fine</label>
                                                    <div class=\"col-10\">
                                                        <input class=\"form-control\" type=\"date\" name='end' id=\"update_end\">
                                                    </div>
                                                </div>
                                                <div class=\"modal-footer\">
                                                    <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Chiudi</button>
                                                    <button type=\"button\" onclick=\"updatephase('0','".$phase['id']."','".$i."')\" data-dismiss=\"modal\" class=\"btn btn-primary\">Salva modifiche</button>
                                                </div>
                                            </div>
                                        </div>";
                                    echo "</div></div>";
                                }
                            }
                            
                            echo "<br><b>PROSSIMA SCADENZA</b>: ".$next_deadline." giorni<br><br>"; 
                            }
                            //form to insert new phase of the project
                             echo "<form action=\"new_phase.php\" method=\"post\">

                                    <div class=\"form-group\">
                                    <input type='hidden' name='id' value='".$row['id']."'>
                                        <label for=\"title_phase\"></label>
                                        <input type=\"text\" name=\"title_phase\" class=\"form-control\" id=\"title_phase\" aria-describedby=\"emailHelp\" placeholder=\"Inserisci titolo\">
                                    </div>
                                    <div class=\"form-group row\">
                                        <label for=\"start\" class=\"col-2 col-form-label\">Data inizio</label>
                                        <div class=\"col-10\">
                                        <input class=\"form-control\" type=\"date\" value='";
                                    
                                    //suggest the day after the previous phase has finished as start for new phase
                                    $myphases = $mysqli->query("SELECT * from fasi WHERE id_project = '".$row['id']."' ORDER by start DESC");
                                    $phase = $myphases->fetch_assoc();
                                    echo date("Y-m-d", strtotime($phase['end'] . ' +1 day'));
                                    
                                    echo "' name='start' id=\"start\">
                                        </div>
                                    </div>
                                    <div class=\"form-group row\">
                                        <label for=\"end\" class=\"col-2 col-form-label\">Data fine</label>
                                        <div class=\"col-10\">
                                        <input class=\"form-control\" type=\"date\" value=\"";
                    
                                        //suggest the day after the suggested start as end for new phase
                                        echo date("Y-m-d", strtotime($phase['end'] . ' +2 day'));
                                        echo "\" name='end' id=\"end\">
                                        </div>
                                    </div>
                                    <button type=\"submit\" class=\"btn btn-success\">Crea</button>

                            </form>";

?>