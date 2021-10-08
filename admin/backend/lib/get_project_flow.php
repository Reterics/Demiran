<?php
/**
 * Created by PhpStorm.
 * User: Attila Reterics
 * Date: 2021-10-07
 * Time: 20:01
 */

$Demiran->add_method("get_project_flow", function ($arguments, $connection){
    $sql = "SELECT p.id as id, p.users as users, p.price as price, p.deadline as deadline, p.category as category, p.client as client, p.title as title, u.full_name as client_name FROM project as p LEFT JOIN users as u ON u.id = p.client";

    if(!isset($_SESSION['role']) || ($_SESSION['role'] != 'owner' && $_SESSION['role'] != 'admin' && $_SESSION['role'] != 'client'))
    {
        echo "{}";
        return;
    }

    $userList = [];
    $projectList = [];
    $categories = [];
    $clients = [];
    $graph = array(
        "nodes"=>[],
        "links"=>[]
    );
    $query = mysqli_query($connection,$sql);
    if ($query) {
        while ($row = mysqli_fetch_array($query)) {
            if ( isset($row["users"]) && $row["users"] != "" && ($_SESSION['role'] != 'client' || $_SESSION['id'] == $row["client"])) {
                $pieces = explode(",", $row['users']);

                foreach ($pieces as $userID) {
                   if(!in_array($userID, $userList)) {
                       array_push($userList, $userID);
                   }
                }
                // Users to Project
                if(!in_array($row['title'], $projectList)) {
                    array_push($projectList, $row['title']);
                }
                if(!in_array($row['category'], $categories)) {
                    array_push($categories, $row['category']);
                }
                if(!in_array($row['client_name'], $clients)) {
                    array_push($clients, $row['client_name']);
                }
                foreach ($pieces as $userID) {
                    $link = array(
                                "source"=>"user_".$userID,
                                "target"=>"project_".$row['title'],
                                "value"=>1
                        );
                    array_push($graph['links'], $link);
                }
                $link = array(
                    "source"=>"project_".$row['title'],
                    "target"=>"client_".$row['client_name'],
                    "value"=>1
                );
                array_push($graph['links'], $link);
            }
        }
        $sql = "SELECT id,full_name as username FROM users WHERE id IN (".implode(",", $userList).")";
        $query = mysqli_query($connection,$sql);
        if ($query) {
            while ($row = mysqli_fetch_array($query)) {
                $userName = $row['username'];
                $userID = $row['id'];
                array_push($graph['nodes'], array(
                    "id" => "user_".$userID,
                    "name" => $userName,
                    "group" => "1"
                ));
            }
        }
        foreach ($projectList as $project) {
            array_push($graph['nodes'], array(
                "id" => "project_".$project,
                "name" => $project,
                "group" => "2"
            ));
        }
        foreach ($clients as $client) {
            array_push($graph['nodes'], array(
                "id" => "client_".$client,
                "name" => $client,
                "group" => "3"
            ));
        }
        echo json_encode($graph);
    }
});

