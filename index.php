<?php
session_start();

require 'utils.php';
require 'model/database.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'about':
        require 'controllers/AboutController.php';
        $controller = new AboutController();
        $data = $controller->index();
        $title = 'À propos';
        $styles = ['about_style.css'];
        $scripts = [];
        $view = 'views/about/index.php';
        include 'views/layouts/base.php';
        break;

    case 'account':
        require 'controllers/AccountController.php';
        $controller = new AccountController();
        $data = $controller->index();
        $title = 'Mon compte';
        $styles = ['account_style.css'];
        $scripts = [];
        $view = 'views/account/index.php';
        include 'views/layouts/base.php';
        break;

    case 'cart':
        require 'controllers/CartController.php';
        $controller = new CartController();
        $data = $controller->index();
        $title = 'Panier';
        $styles = ['cart_style.css'];
        $scripts = [];
        $view = 'views/cart/index.php';
        include 'views/layouts/base.php';
        break;

    case 'order':
        require 'controllers/OrderController.php';
        $controller = new OrderController();

        if (($_GET['action'] ?? '') === 'submit') {
            $controller->submit();
            // pas de vue, la méthode redirige
        } else {
            $data = $controller->index();
            $title = 'Commande';
            $styles = ['order_style.css'];
            $scripts = ['payment_toggle.js'];
            $view = 'views/shop/order.php';
            include 'views/layouts/base.php';
        }
        break;

    case 'events':
        require 'controllers/EventsController.php';
        $controller = new EventsController();
        $data = $controller->index();
        $title = 'Événements';
        $styles = ['events_style.css'];
        $scripts = ['event_details_redirect.js', 'scroll_to_closest_event.js'];
        $view = 'views/events/index.php';
        include 'views/layouts/base.php';
        break;

    case 'event_details':
        require 'controllers/EventsController.php';
        $controller = new EventsController();
        $data = $controller->details();
        $title = 'Détail événement';
        $styles = ['event_details_style.css'];
        $scripts = ['open_media.js', 'add_media.js', 'open_gallery.js'];
        $view = 'views/events/details.php';
        include 'views/layouts/base.php';
        break;

    case 'event_subscription':
        require 'controllers/EventsController.php';
        $controller = new EventsController();
        $data = $controller->subscribe();

        if (is_array($data)) {
            $title = 'Inscription événement';
            $styles = ['event_subscription_style.css'];
            $view = 'views/events/subscribe.php';
            include 'views/layouts/base.php';
        }
        break;

    case 'my_gallery':
        require 'controllers/EventsController.php';
        $controller = new EventsController();
        $data = $controller->myGallery();
        $title = 'Ma galerie';
        $styles = ['my_gallery_style.css'];
        $scripts = ['open_media.js', 'add_media.js', 'delete_media.js'];
        $view = 'views/events/my_gallery.php';
        include 'views/layouts/base.php';
        break;

    case 'add_media':
        require 'controllers/EventsController.php';
        $controller = new EventsController();
        $controller->addMedia();
        break;

    case 'delete_media':
        require 'controllers/EventsController.php';
        $controller = new EventsController();
        $controller->deleteMedia();
        break;

    case 'shop':
        require 'controllers/ShopController.php';
        $controller = new ShopController();
        $data = $controller->index();
        $title = 'Boutique';
        $styles = ['shop_style.css'];
        $scripts = [];
        $view = 'views/shop/index.php';
        include 'views/layouts/base.php';
        break;

    case 'cart_add':
        require 'controllers/CartController.php';
        $controller = new CartController();
        $controller->add();
        break;

    case 'news':
        require 'controllers/NewsController.php';
        $controller = new NewsController();
        $data = $controller->index();
        $title = 'Actualités';
        $styles = ['news_style.css'];
        $scripts = ['news_details_redirect.js', 'scroll_to_closest_event.js'];
        $view = 'views/news/index.php';
        include 'views/layouts/base.php';
        break;

    case 'news_details':
        require 'controllers/NewsController.php';
        $controller = new NewsController();
        $data = $controller->details();
        $title = $data['news']['titre_actualite'];
        $styles = ['event_details_style.css'];
        $view = 'views/news/details.php';
        include 'views/layouts/base.php';
        break;

    case 'grade':
        require 'controllers/GradeController.php';
        $controller = new GradeController();
        $data = $controller->index();
        $title = 'Grades';
        $styles = ['grade_style.css'];
        $scripts = [];
        $view = 'views/grade/index.php';
        include 'views/layouts/base.php';
        break;

    case 'grade_subscribe':
        require 'controllers/GradeController.php';
        $controller = new GradeController();
        $data = $controller->subscribe();
        $title = 'Mon adhésion';
        $styles = ['grade_subscription_style.css', 'grade_style.css'];
        $scripts = ['payment_toggle.js'];
        $view = 'views/grade/subscribe.php';
        include 'views/layouts/base.php';
        break;

    case 'agenda':
        require 'controllers/AgendaController.php';
        $controller = new AgendaController();
        $data = $controller->index();
        $title = 'Agenda';
        $styles = ['planner_style.css'];
        $scripts = [];
        $view = 'views/agenda/index.php';
        include 'views/layouts/base.php';
        break;

    case 'login':
        require 'controllers/AuthController.php';
        $controller = new AuthController();
        $data = $controller->login();
        $title = 'Connexion';
        $styles = ['login_style.css'];
        $scripts = [];
        $view = 'views/auth/login.php';
        include 'views/layouts/base.php';
        break;

    case 'signin':
        require 'controllers/AuthController.php';
        $controller = new AuthController();
        $data = $controller->signin();
        $title = 'Inscription';
        $styles = ['login_style.css'];
        $scripts = [];
        $view = 'views/auth/signin.php';
        include 'views/layouts/base.php';
        break;

    case 'home':
    default:
        require 'controllers/HomeController.php';
        $controller = new HomeController();
        $data = $controller->index();
        $title = 'Accueil';
        $styles = ['index_style.css'];
        $scripts = ['event_details_redirect.js', 'bubble.js'];
        $view = 'views/home/index.php';
        include 'views/layouts/base.php';
        break;
}