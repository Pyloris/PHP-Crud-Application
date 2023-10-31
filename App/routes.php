<?php
require_once __DIR__ . "/../vendor/autoload.php";

// grab the components needed
use sirJuni\Framework\Handler\Router;
use sirJuni\Framework\Middleware\Auth;

// grab the controllers
require_once __DIR__ . "/controllers/auth.php";
require_once __DIR__ . "/controllers/admin.php";


// configure Auth
Auth::set_fallback_route("/crud_app/login?message=Logged in users allowed only");


// auth routes
Router::add_route(["GET", "POST"], '/crud_app/login', [AuthController::class, 'login']);
Router::add_route(['GET', 'POST'], '/crud_app/signup', [AuthController::class, 'signup']);
Router::add_route("GET", '/crud_app/logout', [AuthController::class, 'logout'])->middleware(Auth::class);

// pass reset routes
Router::add_route("GET", "/crud_app/forgot-password", [AuthController::class, 'forgot_pass']);
Router::add_route("POST", "/crud_app/reset_link", [AuthController::class, 'reset_link']);
Router::add_route(["GET", "POST"], "/crud_app/reset", [AuthController::class, 'reset']);

// oauth routes
Router::add_route("GET", "/crud_app/oauth", [OAuthController::class, 'login']);
Router::add_route("GET", '/crud_app/callback', [OAuthController::class, 'oauth_callback']);

// CRUD routes
Router::add_route("GET", '/crud_app/admin', [AdminController::class, 'show'])->middleware(Auth::class);
Router::add_route("POST", '/crud_app/add_user', [AdminController::class, 'addUser'])->middleware(Auth::class);
Router::add_route("POST", '/crud_app/show_user', [AdminController::class, 'showUser'])->middleware(Auth::class);
Router::add_route("POST", '/crud_app/remove_user', [AdminController::class, 'removeUser'])->middleware(Auth::class);
Router::add_route("POST", '/crud_app/update_user', [AdminController::class, 'updateUser'])->middleware(Auth::class);
?>