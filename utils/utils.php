<?php
session_start();

function getConnection():PDO{
    $db_username="durlesteanu";
    $db_password="1234567890";
    $db_name="webshop";
    $db_server="localhost:3306";
    return new PDO("mysql:host=$db_server;dbname=$db_name",$db_username,$db_password);
}

function registerUser(string $email , string $password){
    if(getUserByEmail($email)){
        return false;
    }
    $pdo = getConnection();
    $query = 'INSERT INTO users(email,password,isadmin) VALUES(:email,:password,0);';
    $stmt = $pdo ->prepare($query);
    return $stmt->execute([':email'=>$email,':password'=>$password]);

}


function addPagingInfoToQuery(string $query):string{
    $elementsInPage=isset($_GET['epp'])?$_GET['epp']:10;
    $pageNumber=isset($_GET['pg'])?$_GET['pg']:1;
    $offset=($pageNumber-1)*$elementsInPage;

    return $query." LIMIT $offset, $elementsInPage";

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
    //$numberOfElements=125;
    $numberOfPages=(int)(($numberOfElements+$elementsInPage-1) / $elementsInPage);

    echo"<div style='text-align: center'>";
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

function getUserByEmail(string $email){
    $pdo = getConnection();
    $query = 'SELECT * FROM users WHERE email=:email';
    $stmt = $pdo ->prepare($query);
    $stmt->execute([':email'=>$email]);
    return $stmt->fetch();
}



function getByID(PDO $pdo, int $id){
    $stmt = $pdo->prepare("SELECT c.id as cartId, c.status as castStatus, cp.product_id as productId, cp.quantity as prodQty,
                                p.brand, p.description, p.image, p.name, p.price
                                FROM cart c 
                                join cart_product cp
                                on c.id = cp.cart_id
                                left join product p
                                on cp.product_id = p.id
                                WHERE c.user_id=:id");
    $stmt->execute(['id' => $id]);
    return  $stmt->fetchAll();
}

