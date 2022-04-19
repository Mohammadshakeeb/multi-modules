<?php

namespace Multi\Back\Controllers;

use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
use Phalcon\Http\Response\Cookies;

class LoginController extends Controller
{

    public function logoAction()
    {
        echo "logo";
        die;
    }
    public function logoutAction()
    {

        echo "logout";
    }
    public function indexAction()
    {
    }

    public function productAction()
    {
        // if(is)
        $data = $this->mongo->products->find();

        $this->view->product = $data;
    }
    public function searchAction()
    {
        // $data = $this->mongo->products->find();

        // $this->view->product = $data;
        $postdata = $_POST ?? array();
        // print_r($postdata);
        $val = $postdata['search'];
        $data = $this->mongo->products->find();
        $prod = array();
        foreach ($data as $key => $value) {
            if ($value->name == $val) {
                // echo $value->name;
                // die;
                // $this->view
                array_push($prod, $value);
            }
        }
        // echo "<pre>";
        // print_r($data);

        // die;
        $this->view->prod = $prod;
    }
    public function editAction()
    {
        if (isset($_POST['submit'])) {

            $postdata = $_POST ?? array();

            $data = $this->mongo->products->find();
            $id = $postdata['id'];
            $name = $postdata['name'];
            $category = $postdata['category'];
            $price = $postdata['price'];
            $stock = $postdata['stock'];
            $brand = $postdata['brand'];
            $brand_val = $postdata['brand_val'];
            $scnd_val_0 = $postdata['scnd_val'][0];
            $scnd_val_1 = $postdata['scnd_val'][1];
            $scnd_val_2 = $postdata['scnd_val'][0];
            $scnd_val_3 = $postdata['scnd_val'][1];
            $up_prod = array(
                'name' => $name,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                "$brand" => $brand_val,
                "$scnd_val_0" => $scnd_val_1,
                "$scnd_val_2" => $scnd_val_3

            );
            // foreach($data as $key=>$value){
            // if($value->_id == $id){
            //     echo "<pre>";
            //     print_r($postdata);
            echo "<pre>";
            print_r($up_prod);
            // $res=$this->mongo->products->updateOne(['_id'=>new Mongo\BSON\ObjectID($id)],['$set'=>$up_prod]);
            $this->mongo->products->updateOne(["_id" => new MongoDB\BSON\ObjectID($id)], ['$set' => $up_prod]);
            if ($res) {
                echo "Product Saved";
                $this->response->redirect($_SERVER . "HTTP_REFERER");
            }
            //     }
            // }
            // print_r($data);
            die;
        }
        $postdata = $this->request->getPost();
        $id = $postdata['id_container'];
        // echo "<pre>";
        // print_r($postdata);
        // die;
        // print_r($id);
        // die;
        $data = $this->mongo->products->find();
        foreach ($data as $key => $value) {
            if ($value->_id == $id) {
                // echo $value->_id;
                // $val = json_decode($value);
                // print_r($val);

                // die('edit');
                $this->view->data = $value;
            }
        }
    }
    public function deleteAction()
    {
        $postdata = $this->request->getPost();
        print_r($postdata);
        // die;
        $id = $postdata['id_container'];
        $this->mongo->products->deleteOne(array("_id" => new MongoDB\BSON\ObjectId("$id")));
        header('Location:http://localhost:8080/login/product');
    }
    public function createorderAction()
    {
        if (isset($_POST['submit'])) {
            $postdata = $this->request->getPost();
            // print_r($postdata);
            // die;
            $name = $postdata['name'];
            $quantity = $postdata['quantity'];
            $variation = $postdata['variation'];
            $date = $postdata['date'];
            $product_id = $postdata['product_name'];
            // die($variation);
            // $name = $postdata['name'];
            $order = array(
                "name" => $name,
                "quantity" => $quantity,
                "variation" => $variation,
                "date" => $date,
                "product_id" => $product_id,
                'status' => "pending"
            );
            $createorder = $this->mongo->order->insertOne($order);
            header("Location:http://localhost:8080/login/createorder");

            // print_r($createorder);
            // die;

        }
        $data = $this->mongo->products->find()->toarray();
        $arr = array();
        $variants = array();
        foreach ($data as $value) {
            array_push($arr, $value);
            array_push($variants, $value->added_variants);
        }
        // echo "<pre>";
        // print_r($arr);
        // die;
        $this->view->data = $arr;
        $this->view->variants = $variants;
    }
    public function orderlistAction()
    {
        $total = $this->mongo->order->find();
        $this->view->order = $total;
        // echo "<pre>";
        // print_r($total);
        // die;
    }
    public function getvariationAction()
    {
        $id = $this->request->getPost();
        // print_r( $id['id']);
        // die;
        $data = $this->mongo->products->find();
        foreach ($data as $value) {
            // print_r( $value->_id);
            // die;
            if ($value->_id == $id['id']) {
                // return $value;
                //  print_r($value);
                echo json_encode(array($value));
                die;
            }
        }
    }
    public function updateProductstatusAction()
    {
        $data = $this->request->getPost();
        $val = $data['val'];
        $id = $data['id'];
        // print_r($data);
        // echo $data['id'];
        // die;
        $this->mongo->order->updateOne(
            ['_id' => new MongoDB\BSON\ObjectID($id)],
            ['$set' => ["status" => "$val"]],
        );
        echo "value ";
        die;
        // $data = $this->mongo->products->find();
        // foreach($data as $value){
        //     // print_r( $value->_id);
        //     // die;
        //     if($value->_id == $id['id']){
        //         $this->mongo->products->updateOne(

        //         )
        //         // print_r($value);
        //         // die;
        //         // return $value;
        //         //  print_r($value);
        //         // echo json_encode(array($value));
        //         // die;

        //     }
    }
    public function first_date_monthAction()
    {
        $startdate = date("m/d/Y", strtotime('first day of this month'));
        $orders = $this->mongo->orders->find(["date" => ['$gte' => $startdate, '$lte' => date("m/d/Y")]])->toArray();

        $data = $this->mongo->order->find();
    }
}
