<?php 
exit;
include_once('./global.php'); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function addSlugManually($id , $slug, $lang){
    global $dbF;
    $sql = "INSERT INTO `seo_slug`(`seo_id`, `slug`, `lang`) VALUES (?, ?, ?)";
    $arr = array($id , $slug, $lang);
    $dbF->setRow($sql, $arr);
}


function addSeoManually($cat, $title , $slug){
    global $dbF;
    $ref_id =$pageLink = '/pCategory-'.$cat;
    $i = 1;
    $sql = "INSERT INTO `seo`( 
        `pageLink`, `ref_id`, `slug`, `title`, 
        `sIndex`, `sFollow`, `rewriteTitle`, `publish`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $arr = array($pageLink, $ref_id, $cat, $title, $i, $i, $i, $i);
    $seo_id = $dbF->setRow($sql, $arr);
     $eng_slug = $slug['Swedish']; // for english

    addSlugManually($seo_id, $eng_slug, 'English');
    
    foreach($slug as $slug_key => $slug_val){
        addSlugManually($seo_id, $slug_val, $slug_key);
    }
}

function addWebMenuManually($name,$slug,$under=0,$type="mobile_menu"){
    global $dbF;
    foreach($slug as $key => $val){
        $slug[$key] = '{{WEB_URL}}/'.$val;
    }
    $link = serialize($slug);
    $sql = "INSERT INTO `webmenu`(`name`, `link`, `type`, `under`) VALUES (?, ?, ?, ?)";
    $arr = array($name, $link, $type, $under);
    $last_id = $dbF->setRow($sql, $arr);
    return $last_id;
}

function addCatManually($name, $under=1, $type='main'){
    global $dbF;
    $sql = "INSERT INTO `categories`(`name`,  `type`,  `under`) VALUES ( ? ,? ,?)";
    $arr = array($name, 'main' ,$under);
    $mainCatId = $id_for_sub_cat = $dbF->setRow($sql, $arr);
    return $mainCatId;
}

// read file
$file = 'CATEGORYLIST1.csv';
$handle = fopen($file, "r");
$row = 0; // count find no of rows
// $val = fgetcsv($handle, 1000, ","); // First Heading column Skip

global $mainCatId;
global $mainCatIdas_ ;
global $mainWebMenuId;
while ( ($val = fgetcsv($handle, 1000, ",") ) !== FALSE) {
    @$cat = $val[0];
    @$sed_name = $val[1];
    @$sed_slug = $val[2];
    @$nor_name = $val[3];
    @$nor_slug = $val[4];
    @$den_name = $val[5];
    @$den_slug = $val[6];
    @$fin_name = $val[7];
    @$fin_slug = $val[8];
    
    $name =array(
            'Swedish' => $sed_name,
            'Norwegian' => $nor_name,
            'Danish' => $den_name,
            'Finnish' => $fin_name
                );
    $slug = array(
            'Swedish' => $sed_slug,
            'Norwegian' => $nor_slug,
            'Danish' => $den_slug,
            'Finnish' => $fin_slug,
            );
    // echo $val[0];
    // $dbF->prnt($name);
    $cat_name = serialize($name);
    
    if($cat == $sed_name){
        $mainCatId = addCatManually($cat_name);
        addSeoManually($mainCatId, $cat_name, $slug);
        $mainWebMenuId = addWebMenuManually($cat_name, $slug);
        // $mainWebMenuId =0010;
        // $mainCatId = 1110; 
    }else{
        $subCatId = addCatManually($cat_name, $mainCatId);
        addSeoManually($subCatId, $cat_name, $slug);
        addWebMenuManually($cat_name, $slug, $mainWebMenuId);
    }
    
    
}
exit;
// $dataToBeInsert = array(
//             array(
//                 'name' => array(
//                     'Swedish' => 'HERR',
//                     'Norwegian' => 'HERR',
//                     'Danish' => 'HERR',
//                     'Finnish' => 'HERRA',
//                     ),
//                 'slug' => array(
//                     'Swedish' => 'HERR',
//                     'Norwegian' => 'HERR',
//                     'Danish' => 'HERR',
//                     'Finnish' => 'HERRA',
//                     ),
//                 'sub_heading' => array(
//                     array(
//                             'name' => array(
//                                 'Swedish' => 'Alla Herr kläder & skydd',
//                                 'Norwegian' => 'Alle herreklær og beskyttere',
//                                 'Danish' => 'Alt herretøj og beskyttere',
//                                 'Finnish' => 'Kaikki miesten vaatteet ja suojat',
//                                 ),
//                             'slug' => array(
//                                 'Swedish' => 'Alla-Herr-klader&skydd',
//                                 'Norwegian' => 'Alle-herreklaer-og-beskyttere',
//                                 'Danish' => 'Alt-herretoj-og-beskyttere',
//                                 'Finnish' => 'Kaikki-miesten-vaatteet-ja-suojat',
//                                 )
//                         ),
//                         array(
//                             'name' => array(
//                                 'Swedish' => 'Herr jackor',
//                                 'Norwegian' => 'Herrejakker',
//                                 'Danish' => 'Herrejakker',
//                                 'Finnish' => 'Miesten takit',
//                                 ),
//                             'slug' => array(
//                                 'Swedish' => 'Herr-jackor',
//                                 'Norwegian' => 'Herrejakker',
//                                 'Danish' => 'Herrejakker',
//                                 'Finnish' => 'Miesten-takit',
//                                 )
//                             )
//                     )
//             )
            
        
        
//     );
// ;



// function addCatManually__($data){
//     global $dbF;
    
//     // Add main category
//     foreach($data as $key => $val){
        
//             $main_cat = serialize($val['name']);

//             $sql = "INSERT INTO `categories`(`name`,  `type`,  `under`) VALUES ( ? ,? ,?)";
//             $arr = array($main_cat, 'main' ,1);
//             $mainCatId = $id_for_sub_cat = $dbF->setRow($sql, $arr);
           
            
//             // add seo and seo slug for main category
//             addSeoManually($mainCatId, $main_cat, $val['slug']);
            
//             // add to webmenu
//             $webMenuId = addWebMenuManually($main_cat, $val['slug']);

//             // Add sub category;
//             foreach($val['sub_heading'] as $sub_key => $sub_val){
//                 $sub_cat_name =  serialize($sub_val['name']);
//                 $sql = "INSERT INTO `categories`(`name`,  `type`,  `under`) VALUES ( ? ,? ,?)";
//                 $arr = array($sub_cat_name, 'main' ,$id_for_sub_cat);
//                 $sub_cat_id = $dbF->setRow($sql, $arr);
                
//                 // add seo and seo slug for main category
//                 addSeoManually($sub_cat_id, $sub_cat_name, $sub_val['slug']);
                
//                 // add to webmenu
//                 $subWebMenuId = addWebMenuManually($sub_cat_name, $sub_val['slug'], $webMenuId);
                
//                 }
    
//             }  
            
// }

// addCatManually($dataToBeInsert);

// echo $dataToBeInsert[0]['slug']['Swedish']
//     echo $key.'     '.$val.'     ';
// }
?>