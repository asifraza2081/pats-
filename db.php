<?php
$conn = new mysqli("localhost", "root", "", "pats");
if ($conn->connect_error) {
    die("Connection Failed");
}
session_start();
