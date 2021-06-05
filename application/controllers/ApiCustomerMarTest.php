<?php 
  if (!defined('BASEPATH')) exit('No direct script access allowed');
  require "vendor/autoload.php";
  use Firebase\JWT\JWT;

  class ApiCustomerMarTest extends CI_Controller{

    var $postData = null;
    var $headerData = null;

    function __construct()
    {
      
      parent::__construct();
      $this->load->helper('basic_helper');
      setHeaders();

      $this->postData = $this->post();
      $this->headerData = getheader();

      if (checkToken($this->postData, $this->headerData)) {
        $this->isValid = true;
      } else {
        $this->isValid = false;
      }

      if(checkApiKey($this->headerData)){
        $this->isvalidApi = true;
      } else {
        $this->isvalidApi = false;
      }

    }

  	private function post($index = NULL, $xss_clean = NULL)
    {
      return json_decode(file_get_contents("php://input"), true);
    }

    private function checkMethod()
    {
      if($_SERVER['REQUEST_METHOD'] == "POST"){
        return true;
      } else {
        return false;
      }
    }
  
    /**
    * register method
    * @description this function use to register user
    * @param string form data
    * @return json array
    */
    public function register()
    {
      $res = array();
      if($this->isvalidApi){
        if($this->checkMethod()){
          if($this->postData){
            if(empty($this->postData['username']) || empty($this->postData['email'])){
              http_response_code(500);
              $res['status'] = 500;
              $res['message'] = 'All fields are mandatory';
            } else {
              $this->db->where('email', $this->postData['email']);
              $result = $this->db->get("users")->num_rows();
              if($result > 0){
                http_response_code(302);
                $res['status'] = 302;
                $res['message'] = "Email Exist...";
              } else {
                $n=10;
        
                function getName($n) {
                  $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQUSTUVWXYZ!@#$%^&*()_+~';
                  $randomString = '';
                
                  for ($i = 0; $i < $n; $i++) {
                      $index = rand(0, strlen($characters) - 1);
                      $randomString .= $characters[$index];
                  }
              
                  return $randomString;
                }
        
                $this->db->insert('users', $this->postData);
                $insert_id = $this->db->insert_id();
        
                $token = array("sub" => $_SERVER['SERVER_NAME'], "id" => $insert_id, "iat" => strtotime('now'));
        
                $key = getName($n);
                $jwt = JWT::encode($token, $key);
                $jwtToken = trim($jwt);
            
                if (update('users', ['jwt' => $jwtToken], ['id' => $insert_id])) {
                  $this->db->select(['username','email','surname','gender','phone','created_at','jwt']);
                  $this->db->where('id', $insert_id);
                  $user_data = $this->db->get("users")->row_array();
        
                  $user = array(
                    'username' => $user_data['username'], 
                    'email' => $user_data['email'],
                    'surname' => $user_data['surname'],
                    'gender' => $user_data['gender'],
                    'phone' => $user_data['phone'],
                    'created_at' => $user_data['created_at']


                  );
        
                  $res['user_data'] = $user;
                  $res['status'] = 200;
                  $res['message'] = 'Data inserted successfully.';
                } else {
                  http_response_code(500);
                    $res['status'] = 500;
                    $res['message'] = 'Something went wrong!!';
                }
              }
            }
          } else {
            http_response_code(500);
            $res['status'] = 500;
            $res['message'] = 'Something went wrong!!';
          }
        } else {
          http_response_code(405);
          $res['status'] = 405;
          $res['message'] = 'Wrong http method selected : ' . $_SERVER['REQUEST_METHOD'];
        }
      } else {
        http_response_code(401);
        $res['status'] = 401;
        $res['message'] = 'Invalid API key';
      }

      echo json_encode($res);
      
    }



    /**
    * Login method
    * @description this function use to login user
    * @param string form data
    * @return json array
    */
    public function login()
    {
      $res = array();
     
      if($this->isvalidApi){
        if($this->checkMethod()){
          
          $this->db->select('*');
          $data = $this->db->get_where('users', $this->postData)->row_array();

          if(!empty($data)){
            $user = array(
              'email' => $data['email'],
              'jwt' => $data['jwt'],
              'created_at' => $data['created_at']


            );

            $res['user_data'] = $user;
            $res['status'] = 200;
            $res['message'] = 'Login successfully';
          } else {
            http_response_code(400);
            $res['status'] = 400;
            $res['message'] = 'Please check the credentials';
          }
        } else {
          http_response_code(405);
          $res['status'] = 405;
          $res['message'] = 'Wrong http method selected : ' . $_SERVER['REQUEST_METHOD'];
        }
      } else {
        http_response_code(401);
        $res['status'] = 401;
        $res['message'] = 'Invalid API key';
      }
      

      echo json_encode($res);
    }

    /****
     * 
     * 
     * 
     * 
     * 
     * *** uncomment this function to generate the new api key
     * 
     * 
     ******/
    private function generateApiKey(){
      $n = 20;
      return getRandomKey($n);
    }

    // public function getApiKey(){
    //   echo base64_encode($this->generateApiKey());
    // }


    











































































  
  }
  



?>