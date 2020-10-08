<?php

function getConnection():PDO{
    $db_username="root";
    $db_password="";
    $db_name="webshop";
    $db_server="localhost:3308";
    return new PDO("mysql:host=$db_server;dbname=$db_name",$db_username,$db_password);
}

function registerUser(string $email , string $password){
    $pdo = getConnection();
    $query = 'INSERT INTO users(email,password,isadmin) VALUES(:email,:password,0);';
    $stmt = $pdo ->prepare($query);
    return $stmt->execute([':email'=>$email,':password'=>$password]);

}

function addPagingInfoToQuery(string $query):string{
    $elementsInPage=isset($_GET['epp'])?$_GET['epp']:10;
    $pageNumber=isset($_GET['pg'])?$_GET['pg']:1;
    $offset=($pageNumber-1)*$elementsInPage;

    return $query." LIMIT $offset $elementsInPage";

}


function paging(string $table):void{

    $originalLink=$_SERVER['REQUEST_URI'];


    $elementsInPage=isset($_GET['epp'])?$_GET['epp']:10;
    if (!isset($_GET['pg'])){
        //echo("asd");
        $pageNumber = 1;
        if (strpos($originalLink,".php?"))
            $originalLink=$originalLink."&pg=1";
        else
            $originalLink=$originalLink."?pg=1";

    } else
        $pageNumber=$_GET['pg'];


    $connection=getConnection();
    $query="SELECT COUNT(*) as n FROM $table";
    $statement=$connection->prepare($query);
    $statement->execute();
    $numberOfElements=$statement->fetch()["n"];
    $numberOfElements=125;
    $numberOfPages=(int)(($numberOfElements+$elementsInPage-1) / $elementsInPage);

    echo"<div>";
    for ($i=1;$i<=$numberOfPages;$i++){
        if ($i==$pageNumber)
            echo("<span>$i </span>");
        else{
            $link=preg_replace("/pg=\d+/","pg=$i",$originalLink);
            //echo $originalLink;

            echo("<span> <a href='".$link."'><u>$i</u></a> </span>");



        }
    }
    echo"</div>";

}