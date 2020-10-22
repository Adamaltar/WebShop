<?php
    require 'DbConfig.php';
session_start();
setCartCookies();


function setCartCookies(){
    $id = $_SESSION['userID'] ?? '0';
    $cart = [];
    if (!isset($_COOKIE['cart']) && !isset($_SESSION['userID'])){
        setcookie('cart', json_encode($cart), time()+3600);
    }
}

function getConnection():PDO{

    $credentials = getCredentials();
    $db_username = $credentials['username'];
    $db_password=$credentials['password'];
    $db_name=$credentials['database'];
    $db_server=$credentials['server'];

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
    $elementsInPage=isset($_GET['epp'])?$_GET['epp']:9;
    $pageNumber=isset($_GET['pg'])?$_GET['pg']:1;
    $offset=($pageNumber-1)*$elementsInPage;

    return $query." LIMIT $offset, $elementsInPage";

}


function paging(string $table, bool $includeDeleted):void{

    $originalLink=$_SERVER['REQUEST_URI'];


    $elementsInPage=isset($_GET['epp'])?$_GET['epp']:9;
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
    if ($includeDeleted)
        $query="SELECT COUNT(*) as n FROM $table";
    else
        $query="SELECT COUNT(*) as n FROM $table WHERE deleted=0";

    $statement=$connection->prepare($query);
    $statement->execute();
    $numberOfElements=$statement->fetch()["n"];
    //$numberOfElements=125;
    $numberOfPages=(int)(($numberOfElements+$elementsInPage-1) / $elementsInPage);

    echo"<div style='text-align: center'>";
    for ($i=1;$i<=$numberOfPages;$i++){
        if ($i==$pageNumber)
            echo("<span style='padding: 10px'>$i </span>");
        else{
            $link=preg_replace("/pg=\d+/","pg=$i",$originalLink);
            //echo $originalLink;

            echo("<span style='padding: 7px'> <a href='".$link."'><u>$i</u></a> </span>");



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

function getUserById(int $id){
    $pdo = getConnection();
    $query = 'SELECT * FROM users WHERE id=:id';
    $stmt = $pdo ->prepare($query);
    $stmt->execute([':id'=>$id]);
    return $stmt->fetch();
}

function getCartDetails(int $userID){
    $pdo = getConnection();
    $query = 'SELECT cart.status as cart_status, cart.id as cart_id, 
       cp.product_id, cp.quantity,
       p.name as product_name,
       p.category_name as product_category,
       p.description as product_description, p.brand as product_brand, p.price as product_price, p.image as product_image
        FROM cart JOIN cart_product cp on cart.id = cp.cart_id JOIN product p on cp.product_id = p.id  WHERE user_id=:userID;';
    $stmt = $pdo->prepare($query);
    $stmt->execute([':userID'=>$userID]);
    return $stmt->fetchAll();
}



function getOrderByUserId(int $userID){
    $pdo = getConnection();
    $query = 'SELECT `order`.id as order_id, `order`.payment_method as order_payment , `order`.delivery_method as order_delivery,
        `order`.status as order_status , p.name as product_name , p.brand as product_brand, p.category_name as product_category,
       p.price as product_price , cp.quantity as product_quantity
       FROM `order` JOIN cart c on c.id = `order`.cart_id JOIN cart_product cp on c.id = cp.cart_id JOIN product p on p.id = cp.product_id WHERE `order`.user_id = :userID ORDER BY order_id DESC;';
    $stmt = $pdo->prepare($query);
    $stmt->execute([':userID'=>$userID]);
    return $stmt->fetchAll();

}

function getOrderById(int $orderID){
    $pdo = getConnection();
    $query = 'SELECT `order`.id as order_id, `order`.payment_method as order_payment , `order`.delivery_method as order_delivery,
        `order`.status as order_status , p.name as product_name , p.brand as product_brand, p.category_name as product_category,
       p.price as product_price , cp.quantity as product_quantity
       FROM `order` JOIN cart c on c.id = `order`.cart_id JOIN cart_product cp on c.id = cp.cart_id JOIN product p on p.id = cp.product_id WHERE `order`.id = :orderID ';
    $stmt = $pdo->prepare($query);
    $stmt->execute([':orderID'=>$orderID]);
    return $stmt->fetchAll();
}

function getOrdersIdByUserId(int $userID){
    $pdo = getConnection();
    $query = 'SELECT id from `order` WHERE user_id=:userID ORDER BY id DESC';
    $stmt = $pdo->prepare($query);
    $stmt->execute([':userID'=>$userID]);
    return $stmt->fetchAll();
}


function addOrder(array $orderDetails){
    $pdo = getConnection();
    $query = "INSERT INTO `order`(cart_id, user_id, payment_method, delivery_method, status) VALUES(:cartID, :userID, :payment, :delivery, :status)";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([
        ':cartID'=>$orderDetails['cartID'],
        ':userID'=>$orderDetails['userID'],
        ':payment'=>$orderDetails['payment'],
        ':delivery'=>$orderDetails['delivery'],
        ':status'=>$orderDetails['status']
    ]);
}



function getByID(PDO $pdo, int $id){
    $stmt = $pdo->prepare("SELECT c.id as cartId, c.status as castStatus, cp.product_id as productId, cp.quantity as prodQty,
                                p.brand, p.description, p.image, p.name, p.price
                                FROM cart c 
                                join cart_product cp
                                on c.id = cp.cart_id
                                left join product p
                                on cp.product_id = p.id
                                WHERE c.user_id=:id
                                AND c.status=:status");
    $stmt->execute(['id' => $id, 'status' => 'active']);
    return  $stmt->fetchAll();
}

function getCartByID(){
    $pdo = getConnection();
    $id = $_SESSION['userID'] ?? '-1';
    $stmt = $pdo->prepare("SELECT c.id FROM cart c WHERE user_id=:id AND status=:status");
    $stmt->execute(['id' => $id, 'status' => 'active']);
    $result = $stmt->fetch();
    if (($id !== '-1') && ($result === false)) {
        $newStmt = $pdo->prepare("INSERT INTO cart (status, user_id) VALUES (:status, :userId)");
        $newStmt->execute(['status' => 'active', 'userId' => $id]);
        return $pdo->lastInsertId();
    }

    return $result[0];
}

function deleteCartItem(PDO $pdo, $productId, $cartId){
    $stmt = $pdo->prepare("DELETE FROM cart_product WHERE cart_id=:cartId AND product_id=:productId");
    $stmt->execute(['cartId' => $cartId, 'productId' => $productId]);
}

function addCartItem(PDO $pdo, $productId, $cartId, $qty){

    $stmt = $pdo->prepare("SELECT * FROM cart_product WHERE product_id=:productId AND cart_id=:cartId");
    $stmt->execute(['productId' => $productId, 'cartId' => $cartId]);
    $valueExists = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$valueExists){
        $stmt = $pdo->prepare("INSERT INTO cart_product (cart_id, product_id, quantity) VALUES (:cartId, :productId, :qty )");
        $stmt->execute(['cartId' => $cartId, 'productId' => $productId, 'qty' => $qty]);
    } else {
        $stmt = $pdo->prepare("UPDATE cart_product SET quantity=quantity + :qty WHERE cart_id=:cartId AND product_id=:productId");
        $stmt->execute([ 'qty' => $qty, 'cartId' => $cartId, 'productId' => $productId]);
    }
}

function getProductById(PDO $pdo, $productId){
    $stmt = $pdo->prepare("SELECT id as productId, name, price FROM product WHERE id=:productId");
    $stmt->execute(['productId' => $productId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function mergeCookieToDBCart(){
    $cookieCart = json_decode($_COOKIE['cart'], true);
    $pdo = getConnection();
    $cartId = getCartByID();
    foreach ($cookieCart as $product){
        $productId = (int)$product['productId'];
        $qty = (int)$product['prodQty'];
        addCartItem($pdo, $productId, $cartId, $qty );
    }
    setcookie('cart', '', time() - 3600);
}

function getCommentsForProductId($prodId){
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT r.id, r.comment, r.title,r.createdAt as datetime, r.editedAt, 
                                            r.parentId, r.prodId, p.name, u.name as username, u.avatar
                                    FROM review r
                                    LEFT JOIN users u
                                    ON r.userID = u.id
                                    LEFT JOIN product p
                                    ON r.prodId = p.id
                                    WHERE r.prodId=:prodId");
    $stmt->execute(['prodId' => $prodId]);
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);

    $comments = [];

    foreach ($result as $row){
        $comments[$row->parentId][] = $row;
    }
    return $comments;
}

