
<?php 
    session_start();
    
    if(isset($_POST['date1']) && isset($_POST['date2'])){
        
        require_once 'connection.php';
        
        $date1 = $_POST['date1'];
        $date2 = $_POST['date2'];
      
        try{
            
            $connection = new mysqli($host, $db_user, $password, $db_name);
            
         

             if($connection->connect_errno!=0){
                throw new Exception(mysqli_connect_errno());
             } else {
                 
                 $valid = true;
                 /*
                 $checkRes = $connection->query("SELECT * FROM chart");
                 $rowCheck = $checkRes->fetch_assoc();
                 
                 $how = mysqli_num_rows($checkRes);
                 
                 
                 for($i=1; $i<=$how; $i++){
                     $checkRes = $connection->query("SELECT Data FROM chart");
                     $rowsCH = $checkRes->fetch_assoc();
                     $dataCheck[$i] = $rowsCH['Data'];
                     if($date1!=$dataCheck[$i] || $date2!=$dataCheck[$i]){
                         $valid=false;
                         $_SESSION['error_date'] = "Zła data wprowadzona!";
                     }
                     
                 }
                 */
                 
                 if($valid==true){
                
                 $res1 = $connection->query("SELECT * FROM chart WHERE Data='$date1'");
                 $res2 = $connection->query("SELECT * FROM chart WHERE Data='$date2'");
                 
                 $row1 = $res1->fetch_assoc();
                 $row2 = $res2->fetch_assoc();
                 
                 $idstart= $row1['id'];
                 $idend = $row2['id'];
                 

           
                    
                    for($i = $idstart; $i<=$idend; $i++){
                        
                     $result = $connection->query("SELECT * FROM chart WHERE id = '$i'");
                     
                        $rows = $result->fetch_assoc();
                        
                         $a2[$i] = $rows['Data'];
                         $a3[$i] = $rows['Wartosci'];
                         
                        $do_wykresu[] = "['".$a2[$i]."', ".$a3[$i]."]";

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
                <a href="compare.php">Porównywanie </a>
                <div class="exaple"> Co wpisać - przykład;   Data: 18/12/2012</div>
            </div>
            <div class="left">
                <form method="post">
                    data od:
                    <br/>
                    <input type="text" name="date1" id="textfield"/>
                    <?php 
                        if(isset($_SESSION['error_date'])){
                            echo '<div class="error">'.$_SESSION['error_date'].'</div>'; 
                            unset($_SESSION['error_date']);
                        }
                    ?>
                    <br/>
                    <br/>
                    data do:
                    <br/>
                    <input type="text" name="date2" id="textfield"/>
                     <?php 
                        if(isset($_SESSION['error_date'])){
                            echo '<div class="error">'.$_SESSION['error_date'].'</div>'; 
                            unset($_SESSION['error_date']);
                        }
                    ?>
                    <br/>
                    <br/>
                    <input type="submit" value="make" id="button"/>
                    
                </form>
                
                <br/>
                <br/>
                 
                
            </div>
            
            <div class="right">
                  
                <?php 
                           echo' <table class="tab">';  
                    echo '<tr>';
                       echo '<td>Nr</td>';
                      echo  '<td>Data</td>';
                      echo  '<td>Wartości</td> ';
                    echo '</tr>';
                    
                    $a1 = 1;
                    
                    for($i = $idstart; $i<=$idend; $i++){
               
                    echo '<tr>';
                        echo '<td>'.$a1.'</td>';
                        echo '<td>'. $a2[$i].'</td>';
                        echo'<td>'.$a3[$i].'</td>';
                    echo'</tr>';
                    $a1++;
                 }
                  
                echo' </table>'; 
                ?>
      
            </div>
            
            <?php 
            // id="curve_chart" - do wykresu
            ?>
            
            <div class="plot" id="curve_chart" >
   
            </div>
            
        </div>
        
    </body>
</html>