<?php 
    session_start();
    
    require_once 'connection.php';
    
      try{
        
         $valid=true;
         $conection = new mysqli($host, $db_user, $password, $db_name);
         
         
          if($conection->connect_errno!=0){
             echo"Error: ".$conection->connect_errno;
         }else {
            
            $query = "SELECT * FROM chart";
            
            if($valid==true){
                
                $result = $conection->query($query);
                
                $row = $result->fetch_assoc();
                
                $howMany = $result->num_rows;
                 for($i=1; $i<=$howMany; $i++){
                      $res = $conection->query("SELECT * FROM chart WHERE id = '$i'");
                      $row2 = $res->fetch_assoc();
                      
                      $tabDay[$i] = $row2['Day'];

                 }


            }else{
                throw new Exception($conection->errno);
            }
         }
         
         $conection->close();
       
        
    }  catch (Exceptine $e){
        echo'<span class="error">Błąd serwera</span>';
        echo '</br>';
        echo $e;
    }
    
    
    
    if(isset($_POST['date1']) && isset($_POST['date2']) && 
            isset($_POST['proc']) && isset($_POST['value'])){
        
     
        
        $date1 = $_POST['date1'];
        $date2 = $_POST['date2'];
        $proc = $_POST['proc'];
        $value = $_POST['value'];
        
      
        try{
            
            $connection = new mysqli($host, $db_user, $password, $db_name);
            
         

             if($connection->connect_errno!=0){
                throw new Exception(mysqli_connect_errno());
             } else {
                 
                 $valid = true;
       
                 
                 if($valid==true){
                
                 $res1 = $connection->query("SELECT * FROM chart WHERE Day='$date1'");
                 $res2 = $connection->query("SELECT * FROM chart WHERE Day='$date2'");
                 
                 $row1 = $res1->fetch_assoc();
                 $row2 = $res2->fetch_assoc();
                 
                 $idstart= $row1['id'];
                 $idend = $row2['id'];
                 
                if($idstart>$idend){
                     $befId = $idend;
                     $idend = $idstart;
                     $idstart = $idend;
                 }
                    
                    for($i = $idstart; $i<=$idend; $i++){
                        
                     $result = $connection->query("SELECT * FROM chart WHERE id = '$i'");
                     
                        $rows = $result->fetch_assoc();
                        
                         $a2[$i] = $rows['Day'];
                         $a3[$i] = $rows['Value'];
                         $rezultat[$i] = ($proc*$a2[$i]) - $a3[$i];
                         
                        $do_wykresu[] = "['".$a2[$i]."', ".$rezultat[$i]."]";

                    }
                    $data_for_chart = implode(",", $do_wykresu);
               
                }
             }
            
             $connection->close();
             
             }  catch (Exception $e){
                    echo'<span class="error">Błąd serwera</span>';
                    //deweloperskie
                    // echo'</br>';
                     // echo 'Dev info: '.$e;
                }
        
            }

            


?>


<!DOCTYPE html>  

<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Generator wykresów</title>
        
        <link rel="stylesheet" href="style.css" type="text/css"> 
        
         
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
        <script type='text/javascript'>
                            google.charts.load('current', {'packages':['corechart']});
                             google.charts.setOnLoadCallback(drawChart);
                             function drawChart() {
           
                             var data = google.visualization.arrayToDataTable([
                                ['Data', 'Wartość'],
                               
                                <?php echo $data_for_chart; 
                                ?>
                                ]);
           

                                var options = {
                                title: 'Wykres',
                                curveType: 'function',
                              
                                };

                                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

                                chart.draw(data, options);
                                }
                             </script>
        
        
    </head>
    <body>
        
        <div class="container">
            
            <div class="header">
                <a href="index.php">Strona Grówna</a>
                
            </div>
             <div class="left">
                <form method="post">
                    data od:
                    <br/>

                     <select name="date1" id="textfield">
                        
                    <?php 
                        for($i=1; $i<=$howMany; $i++){
                            echo '<option>'.$tabDay[$i].'</option>';
                    }
                    
                    ?>    
                    </select>
                    
                 
                    <br/>
                    <br/>
                    data do:
                    <br/>
               
                    <select name="date2" id="textfield">
                    
                    <?php 
                        for($i=1; $i<=$howMany; $i++){
                            echo '<option>'.$tabDay[$i].'</option>';
                    }
                    
                    ?>
                    </select>
                    <br/>
                    <br/>
                    oprocentowanie:
                    <br/>
                    <input type="text" name="proc" id="textfield"/>
                    <br/>
                    <br/>
                    kwota:
                    <br/>
                    <input type="text" name="value" id="textfield" />
                    <br/>
                    <br/>
                    <input type="submit" value="make" id="button"/>
                    
                </form>
                
               
                
            </div>
            
            <div class="right">
                
                
            </div>
             <div class="plot" id="curve_chart">
   
            </div>
            
        </div>
        
    </body>
</html>