<?php
class sample extends Api
{

    //
    //orders
    //Thankyou
    //check out radio buttton address
    //acount auto add address

    public function __construct($method)
    {
        Api::Api_Method("auth_student", "GET", $method); // this tell the API to only allow GET method
        Api::Api_Method("auth_admin", "POST", $method); // this tell the API to only allow POST method
    }


    public function blog_details($parameter)
    {
        $parameter['postid'];
    }

    public function blog($parameter)
    {
        $parameter['page'];
        $parameter['perpage'];
    }


    public function featured($parameter)
    {

    }

    public function downloads($parameter)
    {
        $parameter['page'];
        $parameter['per_page'];
        $parameter['search'];
        $parameter['category'];
    }


    public function store($parameter)
    {

    }

    public function login($parameter)
    {
        $username = $parameter['username'];
        $password = $parameter['password'];
    }


    public function getdownloadbyid($parameter)
    {
        $parameter['id'];
    }


    public function fetch_account($parameter)
    {
        $parameter['access'];
    }


    public function savepassword($parameter)
    {
        $parameter['access'];
        $parameter['oldpassword'];
        $parameter['oldpassword'];
        $parameter['access'];
        $parameter['loginid'];
    }


    public function add_download($parameter)
    {
        $parameter['loginid'];
        $parameter['postid'];
    }

    public function user_wallet($parameter)
    {
        $parameter['loginid'];
    }


    public function coupon_code($parameter)
    {
        $parameter['loginid'];
        $parameter['itemid'];
        $parameter['coupon'];
    }


    public function wallet_payment($parameter)
    {
        $parameter['access'];
        $parameter['coupon'];
        $parameter['postid'];
        $parameter['orderid'];
    }


    public function saveprofile($parameter)
    {
        $parameter['access'];
        $parameter['username'];
        $parameter['name'];
    }


    public function savebank($parameter)
    {
        $parameter['access'];
        $parameter['paypal'];
        $parameter['bankname'];
        $parameter['acnumber'];
        $parameter['acname'];
    }


    public function fetch_my_files($parameter)
    {
        $parameter['loginid'];
    }


    public function support($parameter)
    {
        $parameter['message'];
        $parameter['subject'];
        $parameter['userid'];
    }

    public function register($parameter)
    {
        $parameter["name"];
        $parameter["email"];
        $parameter["username"];
        $parameter["password"];
    }

}
?>