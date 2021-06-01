<?php

if (!function_exists('update')) {
    /**
     *
     * @Method name: update
     * @param:  $table,$update,$where
     * @returns:  boolean
     * @description:  Update into table.
     */
    function update($table, $update, $where)
    {
        $ci = &get_instance();
        $ci->db->where($where);
        $ci->db->update($table, $update);
        return true;
    }
}

if (!function_exists('setHeaders')) {
    /**
     *
     * @Method name: headers
     * @author: SwapnilS
     * @param:  none
     * @returns:  none
     * @description: Set the header for api 
     * @version:  1.0
     * @access_type:  public
     */
    function setHeaders()
    {
        //header('Access-Control-Allow-Origin: *');
        //header("Access-Control-Allow-Headers: *");
        //header("Access-Control-Allow-Headers: X-API-KEY,AUTH_API_KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Headers: token, Content-Type,Accept");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
    }
}

if (!function_exists('getHeader')) {
    /**
     *
     * @Method name: headers
     * @author: PiyushS
     * @param:  none
     * @returns:  none
     * @description: Set the header for api 
     * @version:  1.0
     * @access_type:  public
     */
    function getHeader()
    {
        return getallheaders();
    }
}


if (!function_exists('checkToken')) {

    function checkToken($postData, $headerData)
    {
        if(isset($headerData['Token']) && isset($postData['id'])){

            $ci = &get_instance();
        
            $columns = ['jwt', 'id'];
            $ci->db->select($columns);
            $conditions = ['id' => $postData['id'], 'jwt' => $headerData['Token']];
            
            $data = $ci->db->get_where('users', $conditions)->row();
            
            if (!empty($data)) {


                if ($data->jwt == $headerData['Token'] && $data->id == $postData['id']) {
                    return true;
                } else {
                
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

if (!function_exists('checkApiKey')) {

    function checkApiKey($headerData)
    {
        
        if(isset($headerData['x-api-key']) && !empty($headerData['x-api-key']) && $headerData['x-api-key'] == API_KEY){
            return true;
        } else {
            return false;
        }
    }
}

if(!function_exists('getRandomKey')){
    function getRandomKey($n) {
        $characters = 'abcdefghijk1234567890lmnopqrstuvwxyzABCDEFGHIJKLMNOPQUSTUVWXYZ!@#$%^&*()_+~';
        $randomString = '';
        
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
    
        return $randomString;
    }
}



?>