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
                margin-bottom: 20;
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

                //for every post in the selection
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
                        <div class=\"card-block\">";
                        
                            $myphases = $mysqli->query("SELECT * from fasi WHERE id_project = '".$row['id']."' ORDER by start ASC");
                            
                            $num_phases = $myphases->num_rows;
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
                    
                            
                            $myphases = $mysqli->query("SELECT * from fasi WHERE id_project = '".$row['id']."' ORDER by start ASC");
                            $num_phases = $myphases->num_rows;
                            
                            //barra divisione date
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
                            //barra divisione giorni
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
                    
                            //barra completamento
                            $myphases = $mysqli->query("SELECT * from fasi WHERE id_project = '".$row['id']."' ORDER by start ASC");
                            $num_phases = $myphases->num_rows;
                            for ($j = 0;$j <$num_phases; $j++){

                                $phase = $myphases->fetch_assoc();
                                echo "<div class='bar";
                                $before_complete = 0;
                                //se la fase è già completamente passata
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
                                //se la fase è in completamento
                                else if (date("Y-m-d")>=$phase['start'] && date("Y-m-d")<=$phase['end']){
                                    if($j == 0){
                                        echo " first green";
                                    }
                                    else if($before_complete){
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
                                //se la fase deve ancora arrivare
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
                            
                            //calcola la prossima scadenza
                            for ($j = 0;$j <$num_phases; $j++){
                                $phase = $myphases->fetch_assoc();
                                if (date("Y-m-d")>=$phase['start'] && date("Y-m-d")<=$phase['end']){
                                    echo "<br><br><b>PROSSIMA SCADENZA</b>: ".$phase['name'].", ".date_diff(date_create(date("Y-m-d")), date_create($phase['end']))->format('%a')." giorni<br><br>";
                                }
                            }
                            
                             echo "<form action=\"new_phase.php\" method=\"post\">

                                    <div class=\"form-group\">
                                    <input type='hidden' name='id' value='".$row['id']."'>
                                        <label for=\"title_phase\">Nome fase</label>
                                        <input type=\"text\" name=\"title_phase\" class=\"form-control\" id=\"title_phase\" aria-describedby=\"emailHelp\" placeholder=\"Inserisci titolo\">
                                    </div>
                                    <div class=\"form-group row\">
                                        <label for=\"start\" class=\"col-2 col-form-label\">Data inizio</label>
                                        <div class=\"col-10\">
                                        <input class=\"form-control\" type=\"date\" value='";
                                    
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
        
    </body>
</html>