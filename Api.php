<?php

require_once 'DbConnect.php';

    $response = array();

    $traget_dir = "uploads/";

    if(isset($_GET['apicall'])){

        switch($_GET['apicall']){

            case 'upload':
                
                $message = "Params ";
                $is_error = false;

                if(!isset($_POST['desc'])){
                    $is_error = true;
                    $message .= " desc, ";
                }
                if(!isset($_FILES['image']['name'])){
                    $is_error = true;
                    $message .= " image is required";
                }
                if($is_error){
                    $response['error'] = true;
                    $response['message'] = $message;
                }else{
                    $traget_file = $traget_dir . uniqid() . '.' . pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
                    
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $traget_file)){
                        $stmt = $conn->prepare("INSERT INTO uploads ('path', 'description') VALUES (?, ?)");
                        $stmt->bind_param("ss", $traget_file, $_POST['desc']);

                        if($stmt->execute()){
                            $response['error'] = false;
                            $response['message'] = "Image upload successfully";
                            $response['image'] = getBaseURL() . $traget_file;
                        }else{
                            $response['error'] = true;
                            $response['message'] = "Try again later..";
                        }
                    }else{
                        $response['error'] = true;
                        $response['message'] = "Try again later..";
                    }

                }
            break;
        }
    }
    function getBaseURL(){
        $url = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url .= $_SERVER['SERVER_NAME'];
        $url .= $_SERVER['REQUEST_URI'];
        return dirname($url) . '/'; 
    }

    header('Content-Type', 'application/json');
    echo json_encode($response);