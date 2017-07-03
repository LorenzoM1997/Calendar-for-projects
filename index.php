<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
        <style>
        
            .main{
                width:94%;
                margin:3%;
            }
            
            .card{
                width:800px;
                margin-left:auto;
                margin-right:auto;
            }
            .bar{
                height:40px;
                border:1px solid #bbbbbb;
                float: left;
                margin: 0;
                margin-bottom: 20px;
                border-left:0px;
                border-right:1px solid #bbbbbb;
                text-align:center;
                padding-top: 4px;
            }
            .top_bar{
                 height:20px;
                border:1px solid black;
                border-left:0px;
                float: left;
                margin-bottom: 10px;
                border-bottom:0px;
                text-align:center;
            }
            .middle_bar{
                height:26px;
                border:1px solid #bbbbbb;
                border-left:0px;
                float: left;
                margin-bottom: 14;
                border-bottom:0px;
                text-align:center;
            }
            .finished_phase{
                border-bottom:2px solid #bbbbbb;
                width:100%;
                float:left;
                height:60px;
                padding-top:10px;
            }
            .first{
                border-left:1px solid #bbbbbb;
                border-radius:8px 0px 0px 8px;
            }
            .first_top{
                border-left:1px solid black;
            }
            .first_middle{
                border-left:1px solid #bbbbbb;
            }
            .last{
                border-radius:0px 8px 8px 0px;
            }
            .green{
                background-color: green;
                color: white;
            }
            .red{
                background-color: red;
                color:white;
            }
            .gray{
                background-color: #eeeeee;
            }
        </style>
    </head>
    
    <?php
    include('db.php');
    
    function isWeekend($date) {
        return (date('N', strtotime($date)) >= 6);
    }
    ?>
    
    <body>
        <div class="main">
            <form action="new_project.php" method="post">
                <div class="card">
                    <div class="card-block">
                        <div class="form-group">
                            <label for="title">Titolo progetto</label>
                            <input type="text" name="title" class="form-control" id="title" aria-describedby="emailHelp" placeholder="Inserisci titolo">
                        </div>
                        <div class="form-group">
                            <label for="description">Descrizione</label>
                            <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Crea progetto</button>
                    </div>
                </div>
            </form>
            
            <div id="accordion" role="tablist" aria-multiselectable="true">
            
            <?php
            
                 $mydata = $mysqli->query("SELECT * from projects");

                $num_row = $mydata->num_rows;

                //for every project in the selection
                for ($i = 0;$i <$num_row; $i++){

                $row = $mydata->fetch_assoc();
                    
                echo "
                    <div class=\"card\">
                        <div class=\"card-header\" role=\"tab\" id=\"headingOne\">
                        <h5 class=\"mb-0\">
                            <a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse".$i."\" aria-expanded=\"true\" aria-controls=\"collapseOne\">
                            ".$row['nome']."
                            </a>
                        </h5>
                        </div>";
                    
                echo "
                    <div id=\"collapse".$i."\" class=\"collapse\" role=\"tabpanel\" aria-labelledby=\"headingOne\">
                        <div id='txthint".$i."' class=\"card-block\">";
                            
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
                                    echo " green";
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
                            
                            echo "<br><b>PROSSIMA SCADENZA</b>: ".$next_deadline."<br><br>"; 
                            }
                            //form to insert new phase of the project
                             echo "<form action=\"new_phase.php\" method=\"post\">

                                    <div class=\"form-group\">
                                    <input type='hidden' name='id' value='".$row['id']."'>
                                        <label for=\"title_phase\"></label>
                                        <input type=\"text\" name=\"title_phase\" class=\"form-control\" id=\"title_phase\" aria-describedby=\"emailHelp\" placeholder=\"Inserisci nuova fase...\">
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

                            </form>
                        </div>
                    </div>
                </div>";
               
                }
                
            ?>
            </div>
        </div>
        <script>
            var new_end_date = document.getElementById("update_end").value;
            function updatephase(completeness,id,id_proj) {
                if (window.XMLHttpRequest) {
                    xmlhttp = new XMLHttpRequest();
                } else {
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        document.getElementById("txthint"+id_proj).innerHTML = xmlhttp.responseText;
                    }
                };
                var new_end_date = document.getElementById("update_end").value;
                if (completeness == '1'){
                    xmlhttp.open("GET","updatephase.php?complete="+completeness+"&id="+id+"&i="+id_proj,true);
                }
                else{
                    xmlhttp.open("GET","updatephase.php?complete="+completeness+"&id="+id+"&i="+id_proj+"&end="+new_end_date,true);
                }
                xmlhttp.send();
            }
        </script>
    </body>
</html>