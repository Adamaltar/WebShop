
<!doctype html>
<html lang="en">

<?php require "../layout/header.php"?>
<div class="container">
<div>  <h1 style="text-align: center"> <?= $prod['prod']['name'] ?>   </h1>   <br></div><br><hr class="my-4"><br>
    <div class="row">
        <div class="col-sm ">
            <img class="card-img-top border border-dark" style="height:300px; width:350px; " src="../images/<?=$prod['prod']['image'] ?>" alt=""/>
        </div>
        <div class="col-sm">
            <h3> By  <?=$prod['prod']['brand'] ?>  </h3>
            <h4>  Description: <?=$prod['prod']['description'] ?></h4>
            <h4>  Category: <?=$prod['prod']['category_name'] ?></h4>
            <hr class="my-4">
            <h5>   Standard delivery    </h5>
            <span style="color: green">Monday - Saturday 09:00 - 17:00   </span>
        </div>
        <hr class="m-4">
        <div class="col-sm" >
            <h2> Price: <?=$prod['prod']['price'] ?>  USD    </h2>
            <?php
            $color = 'green';
            $stock = 'in stock';
            if($prod['prod']['stock'] <= 0 ){
                $color = 'red';
                $stock = 'out of stock';
            } elseif ($prod['prod']['stock'] == 1){
                $color = 'orange';
                $stock = 'last product in stock';
            } ?>
            <h5 class="card-text" style=" color: <?=$color ?>"><?=$stock ?>  </h5>
            <a class="btn btn-lg" style="background-color: #FAE1DF;color: black;" href="#">Add to cart</a>

        </div>
    </div>
</div>
<div class="container">
<hr class="my-4"><br>
<h4> We also recommend you</h4>
<div class="row">
   <?php
   foreach ( $prod['rand'] as $product )
   {
       echo  ' <div class="col col-sm-12 col-md-6 col-lg-4"> 
                    <div class="card" style=" width:200px;"> 
                        <img class="card-img-top" style="height:150px; width:auto;" src="../images/'. $product["image"] .'" alt=""/> 
                            <div class="card-header"> 
                                    <h6 class="card-title">Product name: '. $product["name"].'</h6>                                                                
                            </div>
                               <div class="card-body">
                                  <div class="card-text" style="text-align: center;">           
                 <a class="btn" style="background-color: #9EA3B0;color: black;" href="/shop/product.php?id='.$product['id'] .'"> Details </a>                
                                    </div>
                               </div> 
                    </div> <br>
                </div>';
   }

   ?>
</div>
</div>

<?php require "../layout/footer.php"?>
</html>




