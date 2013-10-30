<?php

// Initialize core
$f3=require("lib/base.php");
$f3->mset(array(
	"UI" => "app/view/",
	"LOGS" => "log/",
	"AUTOLOAD" => "app/"
));

// Load local configuration
$f3->config("config.ini");

// Set up error handling
$f3->set("ONERROR", function($f3) {
	switch($f3->get("ERROR.code")) {
		case 404:
			$f3->set("title", "Not Found");
			echo Template::instance()->render("error/404.html");
			break;
		case 403:
			echo "You do not have access to this page.";
			break;
		default:
			return false;
	}
});

// Connect to database
$f3->set("db.instance", new DB\SQL(
	"mysql:host=" . $f3->get("db.host") . ";port=3306;dbname=" . $f3->get("db.name"),
	$f3->get("db.user"),
	$f3->get("db.pass")
));

// Define routes
require_once "app/routes.php";

// Minify static resources
$f3->route('GET /minify/@type', function($f3, $args) {
	$f3->set('UI',$args['type'].'/');
	echo Web::instance()->minify($_GET['files']);
}, 3600);

// Load user if session exists
$user = new Model\User();
$user->loadCurrent();

// Run the application
$f3->run();