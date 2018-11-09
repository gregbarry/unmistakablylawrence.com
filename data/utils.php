<?
include_once('../credentials/wp.php');
$dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
$item_per_page = 10;

if(isset($_POST["page"])){
    $page_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
    if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
    $page_number = 1; //if there's no page number, set it to 1
}

function makeImage($src) {
    return "<img src='" . $src . "'/>";
}

function paginate_function($item_per_page, $current_page, $total_records, $total_pages) {
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $pagination .= '<ul class="pagination">';

        $right_links    = $current_page + 10;
        $previous       = $current_page - 1;
        $next           = $current_page + 1;
        $first_link     = true;

        if($current_page > 1){
            $previous_link = ($previous==0)?1:$previous;
            $pagination .= '<a href="#" data-page="1" title="First"><li class="first">&laquo;</li></a>'; //first link
            $pagination .= '<a href="#" data-page="'.$previous_link.'" title="Previous"><li>&lt;</li></a>'; //previous link
                for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= '<a href="#" data-page="'.$i.'" title="Page'.$i.'"><li>'.$i.'</li></a>';
                    }
                }
            $first_link = false; //set first link to false
        }

        if($first_link){ //if current active page is first link
            $pagination .= '<li class="first active">'.$current_page.'</li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="last active">'.$current_page.'</li>';
        }else{ //regular current link
            $pagination .= '<li class="active">'.$current_page.'</li>';
        }

        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<a href="#" data-page="'.$i.'" title="Page '.$i.'"><li>'.$i.'</li></a>';
            }
        }
        if($current_page < $total_pages){
                $next_link = ($i > $total_pages)? $total_pages : $i;
                $pagination .= '<a href="#" data-page="'.$next_link.'" title="Next"><li>&gt;</li></a>'; //next link
                $pagination .= '<a href="#" data-page="'.$total_pages.'" title="Last"><li class="last">&raquo;</li></a>'; //last link
        }

        $pagination .= '</ul>';
    }
    return $pagination; //return pagination links
}
?>