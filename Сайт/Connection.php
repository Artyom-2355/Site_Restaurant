<?php 
$host='localhost'; 
$database='restoran'; 
$user='root'; 
$password=''; 
$action = $_GET["action"];
$link=mysqli_connect($host, $user, $password, $database) or die("Ошибка". mysqli_error($link)); 
if ($link == false){
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
else {
    // print("Соединение установлено успешно");

    if ($action=='table'){  
        $id_stolik = $_GET["id"];
        // var_dump($id_stolik);
        $result = $link->query("SELECT Fotografiya FROM stolik Where id = $id_stolik");
        while ($row = $result->fetch_row()) {
            echo($row[0]);
        }
    }
    if ($action=='brone'){
        $u_name = $_GET['name'];
        $u_phone = $_GET['phone'];
        $br_date = $_GET['date'];
        $br_dop = $_GET['dop'];
        $tb_id = $_GET['tbid'];

        // Проверяем нет ли брони на этот день
        $sql="SELECT id FROM bron Where Vremya = '$br_date' and id_stolik=$tb_id";
        $get_brone = $link->query($sql);
        $brone_id='';
        while ($row = $get_brone->fetch_row()) {
            $brone_id = $row[0];
        }
        if ($brone_id!=''){
            echo 'zakazan';
            exit;
        } 

        $sql="SELECT user.id FROM bron, user Where Vremya = '$br_date' and bron.id_user = user.id
        and user.Telefon = '$u_phone'";

        $test = $link->query($sql);
        $u_id='';
        while ($row = $test->fetch_row()) {
            $u_id= $row[0];
        }
        if ($u_id!=''){
            echo 'broniroval';
            exit;
        } 
        // Записываем пользователя
        $sql='INSERT INTO user (Imya, Telefon) VALUES("'.$u_name.'","'.$u_phone.'");';
        $dobavl = $link->query($sql);
        $user_id='';
        // Получаем id пользователя
        $sql='SELECT id FROM user Where Imya = "'.$u_name.'" and Telefon = "'.$u_phone.'"';
        $users = $link->query($sql);
        while ($row = $users->fetch_row()) {
            $user_id = $row[0];
        }
        // Записываем на бронь 
        $sql="INSERT INTO bron (Vremya, id_user, id_stolik) VALUES('$br_date',$user_id,$tb_id)";
        $brone = $link->query($sql);
        if ($brone){
            echo "success";
        }        
    }
    if ($action=='svob'){
        $br_date = $_GET['date'];
        $sql="SELECT id_stolik FROM bron WHERE bron.Vremya = '$br_date'";
        $brone = $link->query($sql);
        $arr=array();
        while ($row = $brone->fetch_row()) {
            array_push($arr,$row[0]);
        }
            echo json_encode($arr);
    }
    if($action == 'adminka'){
        $sql_select = $link->query("SELECT b.id, b.Vremya, u.Imya, u.Telefon, b.id_stolik FROM bron as b, user as u WHERE b.id_user = u.id");
        $arr = array();
        while($row = $sql_select->fetch_row()){
            array_push($arr,$row);
        }
        echo json_encode($arr); 
    }
    if($action == 'delbrone'){
        $id_brone = $_GET["id"];
        $sql_select = $link->query("DELETE FROM bron WHERE id = '$id_brone'");
        $arr = array();
        while($row = $sql_select->fetch_row()){
            array_push($arr,$row);
        }
        echo json_encode($arr); 
    }
}


?> 