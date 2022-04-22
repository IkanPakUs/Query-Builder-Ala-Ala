<?php
require_once 'Helpers/Model.php';

// $query = Model::table('product')->select('nama')->where('nama', '=', 'komang')->orWhere('nama', '=', "maye")->get();
// $query = Model::table('product')->get();
// $query = Model::table('product')->limit(3)->get();
// $query = Model::table('product')->find(1);
// $query = Model::table('product')->where('nama', '=', 'komang')->where('nim', '=', 200030704)->orWhere('email', "!=", "komang")->get();
// $query = Model::table('product')->insert([["nama" => "Maye", "nim" => 200030844, "email" => "maye3@gmail.com"]]);
// $query = Model::table('product')->insert([
//     ["nama" => "Maye", "nim" => 200030849, "email" => "maye@gmail.com"],
//     ["nama" => "Komang", "nim" => 200030704, "email" => "komang@gmail.com"],
// ]);
// $query = Model::table('product')->where('nama', "like", "%ma%")->update(["nama" => "Komang Arya"]);
// $query = Model::table('product')->where('nama', "=", "Komang Arya")->delete();

var_dump($query);
?>