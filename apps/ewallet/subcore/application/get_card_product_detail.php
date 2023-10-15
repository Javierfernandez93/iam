<?php 
define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new Umbrella\UserLogin();

if($UserLogin->logged === true){

  if($data["product_id"]){
    $data['html'] = buildCardHtml($data["product_id"]);
    $data['s'] = 1;
    $data['r'] = '';
    
  }else{  
    $data['s'] = 0;
    $data['r'] = 'PRODUCTO NO DISPONIBLE';
  }

}else{  
  $data['s'] = 0;
  $data['r'] = 'Inicie Sesión para continuar';
}


function buildCardHtml($product_id){

  $html="";
  // $product_id=false;
  if($product_id){

    $Product=new Umbrella\Product();
    
    if($Product->setId($product_id)){
      $html.='<div class="container">';
        $html.='<h2>Información del Producto</h2>';
        
        $html.='<div class="row">';
          $html.='<div class="col-12 product_detail">';
            $html.='<div>Nombre:<br> <span class="product_name">'.$Product->getProductName($product_id).'</span></div>';
            $html.='<div >ID: <span class="price_public"> '.$Product->getId().'</span></div>';
            $html.='<div >Puntos: <span class="price_public"> '.$Product->points.'</span></div>';
            $html.='<div >Precio Publico:<br> <span class="price_public"> $ '.$Product->price_public.' MXN </span></div>';
          $html.='</div>';
          $html.='<div class="offset-3 offset-sm-4 offset-md-5 img-detail">';
            $html.='<img class="d-block img-fluid" src="../../'.$Product->image.'" alt="'.$Product->title.' '.$Product->presentation.'" >';            
          $html.='</div>';
        $html.='</div>';

        $html.='<div class="row" style="margin-top:20px">';
          if($Product->line!='Herramientas'){
              if($Product->line=='GanoFood')
                $href="http://www.ganofood.Umbrella.com/apps/home/product.php?from=money_express";
              else if($Product->line=='Mer')
                $href="http://www.vivemer.Umbrella.com?from=money_express";
              else
                $href="http://www.biometricamx.Umbrella.com/apps/home/line_organic.php?from=money_express";

            $html.='<div class="col-6">';
              $html.='<a class="btn btn-primary" style="color:#FFF;" href="'.$href.'">Ficha Tecnica</a>';
            $html.='</div>';
            $html.='<div class="col-6">';
              $html.='<button name="close" onclick="closePopup()" class="btn btn-danger">Cerrar</button>';
            $html.='</div>';
            
          }else{
            $html.='<div class="col-12 text-center">';
              $html.='<button name="close" onclick="closePopup()" class="btn btn-danger">Cerrar</button>';
            $html.='</div>';
          }
          
        $html.='</div>';

        // $html.='<div> <a href="">Ver  del producto</a></div>';


    }else{
      $html="<h1>PRODUCTO NO ENCONTRADO</h1>";
    } 
  }else{
    $html="<h1>PRODUCTO NO DISPONIBLE</h1>";
  }


  
	return $html;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); ?>

