<?php 

use App\Entity\User;
use Slim\Views\Twig;
use Slim\Psr7\Request;
use App\Entity\Product;
use Slim\Psr7\Response;
use App\Entity\Category;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
require_once dirname(__DIR__)."/bootstrap.php";


$app = AppFactory::create();

$twig = Twig::create(dirname(__DIR__).'/templates', ['cache' => false]);

$app->add(TwigMiddleware::create($app, $twig));
// Add error middleware
$app->addErrorMiddleware(true, true, true);

//Fonction pour trouver la page
function getParams(){
    $params = isset($_SERVER["QUERY_STRING"]) ? explode("&", $_SERVER["QUERY_STRING"]) : [];
    $result = [];
    foreach ($params as $param) {
        $param = explode("=", $param);
        $result[$param[0]] = $param[1];
        # code...
    }
    return $result;
}

$categories = $entityManager->getRepository(Category::class)->findAll();

// Add routes
session_start();
$app->get('/', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    $isLoggedIn = isset($_SESSION['user_id']);
    $successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
    $errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
    $isLoggedIn = isset($_SESSION['user_id']);
    // Supprimer le message de la session après l'affichage
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
    return $view->render($response, 'home/home.html.twig', [
        'name' => "Test",
        'categories' => $GLOBALS["categories"],
        'success_message' => $successMessage,
        'error_message' => $errorMessage,
        'is_logged_in' => $isLoggedIn
    ]);
});
$app->get('/contact', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    $isLoggedIn = isset($_SESSION['user_id']);
    return $view->render($response, 'contact/contact.html.twig', [
        'name' => "Test",
        'categories' => $GLOBALS["categories"],
        'is_logged_in' => $isLoggedIn,
    ]);
});

// Route pour afficher les produits
$app->get('/products', function (Request $request, Response $response) use ($entityManager) {
    // Récupérer les informations de session
    $isLoggedIn = isset($_SESSION['user_id']);
    
    // Récupérer les produits depuis la base de données
    $productRepo = $entityManager->getRepository(Product::class)->findAll();
    //var_dump($productRepo);
    // Récupérer les éléments du panier s'ils existent
    $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    $view = Twig::fromRequest($request);
    $successMessageCart = isset($_SESSION['success_message_cart']) ? $_SESSION['success_message_cart'] : null;
    unset($_SESSION['success_message_cart']);
    
    // Rendre la vue en passant les produits, les catégories et les éléments du panier
    return $view->render($response, 'products/product.html.twig', [
        'products' => $productRepo,
        'categories' => $GLOBALS["categories"],
        'success_message_cart' => $successMessageCart,
        'is_logged_in' => $isLoggedIn, // Passer l'état de connexion à la vue Twig
        'cartItems' => $cartItems // Passer les éléments du panier à la vue Twig
    ]);
});


$app->get('/product/by/category/{slug}', function (Request $request, Response $response, $args) {
    $categoriesRepo = $GLOBALS["entityManager"]->getRepository(Category::class);
    $productRepo = $GLOBALS["entityManager"]->getRepository(Product::class);

    $view = Twig::fromRequest($request);
    $isLoggedIn = isset($_SESSION['user_id']);
    // Récupérer les éléments du panier s'ils existent
    $cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    $slug = $args["slug"];
    $category = $categoriesRepo->findOneBySlug($slug);
    $products = $category->getProducts()->getValues();
    //var_dump($products); exit();
    if(!$category){
        // ERROR
        return $view->render($response, 'errors/error-404.html.twig');
    }
    if(!$products){
        // ERROR
        return $view->render($response, 'errors/error-stock-epuise.html.twig', [
            'category'=> $category,
            'categories' => $GLOBALS["categories"],
            'products' =>  $products,
            'is_logged_in' => $isLoggedIn, // Passer l'état de connexion à la vue Twig
            'cartItems' => $cartItems // Passer les éléments du panier à la vue Twig
            
        ]);
    }
    
    
    // Vérifier le contenu des produits
    /*foreach ($products as $product) {
        var_dump($product->getImageUrl());
    }*/

        return $view->render($response, 'products/product.html.twig', [
            'category'=> $category,
            'categories' => $GLOBALS["categories"],
            'products' =>  $products,
            'is_logged_in' => $isLoggedIn, // Passer l'état de connexion à la vue Twig
            'cartItems' => $cartItems // Passer les éléments du panier à la vue Twig
            
        ]);

    
});
// Route pour la page d'inscription
$app->get('/inscription', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'inscription/inscription.html.twig');
});

// Route pour gérer la soumission du formulaire d'inscription
$app->post('/inscription', function (Request $request, Response $response) use ($entityManager) {
    //Récupère les données envoyées via le formulaire POST
    $parsedBody = $request->getParsedBody();

    // Validation des données
    $name = filter_var($parsedBody['name'], FILTER_SANITIZE_STRING);//filter_var: nettoyer et valider le donnée, FILTER_SANITIZE8STRING=pour nettoyer s il y a des balise html dans le donnée
    $email = filter_var($parsedBody['email'], FILTER_VALIDATE_EMAIL);//FILTER8VALIDATE8EMAIL=validation email
    $password = filter_var($parsedBody['password'], FILTER_SANITIZE_STRING);
    $adress = filter_var($parsedBody['adress'], FILTER_SANITIZE_STRING);
    $tel = filter_var($parsedBody['tel'], FILTER_SANITIZE_STRING);

    if (!$name || !$email || !$password || !$adress || !$tel) {
        // Gérer les erreurs de validation
        $view = Twig::fromRequest($request);
        return $view->render($response, 'inscription/inscription.html.twig', [
            'error' => 'Veuillez remplir tous les champs correctement.'
        ]);
    }

    // Création d'un nouvel utilisateur (un objet user)
    $user = new User();
    $user->setName($name)
         ->setEmail($email)
         ->setPassword(password_hash($password, PASSWORD_BCRYPT)) // Hachage du mot de passe
         ->setTel($tel)
         ->setShippingAdress($adress);

    // Persistance de l'utilisateur ou prepare l'objet pour etre sauvegarder
    $entityManager->persist($user);
    //execute les modifications dans la base de données
    $entityManager->flush();
    // Connexion réussie, créer une session
    session_start();
    $_SESSION['success_message'] = 'Inscription réussie !' ;

    // Redirection après l'inscription
    return $response->withHeader('Location', '/')->withStatus(302);
});

// Route pour la connexion
$app->post('/login', function (Request $request, Response $response) use ($entityManager) {
    $parsedBody = $request->getParsedBody();
    //var_dump($parsedBody); exit();
    
    // Vérifier si les clés "name" et "password" existent dans le tableau $parsedBody
    if (!isset($parsedBody['name']) || !isset($parsedBody['password'])) {
        // Afficher un message d'erreur si les champs ne sont pas remplis
       
        //$view = Twig::fromRequest($request);
        session_start();
        $_SESSION['error_message'] = 'Veuillez remplir tous les champs.';
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    // Si les clés existent, récupérer les valeurs
    $name = filter_var($parsedBody['name'], FILTER_SANITIZE_STRING);
    $password = filter_var($parsedBody['password'], FILTER_SANITIZE_STRING);

    // Recherche de l'utilisateur dans la base de données
    $userRepository = $entityManager->getRepository(User::class);
    $user = $userRepository->findOneBy(['name' => $name]);
    

    if (!$user || !password_verify($password, $user->getPassword())) {
        // Nom d'utilisateur ou mot de passe incorrect
        
        //$view = Twig::fromRequest($request);
        session_start();
        $_SESSION['error_message'] = 'Nom d’utilisateur ou mot de passe incorrect.';
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    // Connexion réussie, créer une session
    session_start();
    $_SESSION['user_id'] = $user->getId();
    $_SESSION['success_message'] = 'Connexion réussie !' ;
    $_SESSION['error_message1'] = 'Veuillez remplir tous les champs. !';
    $_SESSION['error_message2'] = 'Nom d’utilisateur ou mot de passe incorrect !';

    // Redirection vers une autre page après la connexion réussie
    return $response->withHeader('Location', '/')->withStatus(302);

    /*$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
    
    // Supprimer le message de la session après l'affichage
    unset($_SESSION['success_message']);
    
    return $view->render($response, 'home.html.twig', [
        'success_message' => $successMessage
    ]);*/
});

//Route pour la deconnexion
$app->post('/logout', function (Request $request, Response $response) {
    session_start();
    session_destroy();
    return $response->withHeader('Location', '/')->withStatus(302);
});

//Ajout au panier

$app->post('/add-to-cart/{id}', function (Request $request, Response $response, array $args) use ($entityManager) {
    //Pour savoir lidentifieant du produit ajouter
    $productId = $args['id'];
    //var_dump($productId); exit();
    //Pour récupère les données du POST
    $params = $request->getParsedBody();
    
    //Pour savoir le quantité, si il n'est donnée 1 la valeur par défaut
    $quantity = isset($params['quantity']) ? (int)$params['quantity'] : 1;
    
    

    $productRepository = $entityManager->getRepository(Product::class);
    //récupère le produit dans la bd
    $product = $productRepository->find($productId);
    
    
    //Si le produit n existe pas
    if (!$product) {
        return $response->withStatus(404)->write('Produit non trouvé');
    }
    
    //vérifie si la session déjà des paniers
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    
    $productExists = false;
    //parcourt les paniers dans les paniers
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['product_id'] == $productId) {
            $cartItem['quantity'] += $quantity;
            $productExists = true;
            break;
        }
    }
    if (!$productExists) {
        $_SESSION['cart'][] = [
            'product_id' => $productId,
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'quantity' => $quantity,
            'price' => $product->getPrice() 
        ];
    }

    //var_dump($product); exit();
    //var_dump($_SESSION['cart']); exit();
    $_SESSION['success_message_cart'] = 'Ajout au panier réussie ! Vous pouvez les valider en cliquant sur le bouton valider en bas' ;
    //Réussie
    return $response->withHeader('Location', '/products')->withStatus(302);
});

// Route pour la page du panier
$app->get('/cart', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    $isLoggedIn = isset($_SESSION['user_id']);
    // Assurez-vous que la session cart existe et qu'elle n'est pas vide
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $cartItems = $_SESSION['cart'];
       //paasse ici les elements du panier
        return $view->render($response, 'cart/cart.html.twig', [
            'cartItems' => $cartItems,
            'is_logged_in' => $isLoggedIn,
            'categories' => $GLOBALS["categories"],
        ]);
    } else {
        // Redirigez l'utilisateur vers la page des produits s'il n'y a rien dans le panier
        return $response->withHeader('Location', '/products')->withStatus(302);
    }
});
// Route page de paiement
$app->get('/payment', function (Request $request, Response $response) {
    $view = Twig::fromRequest($request);
    $isLoggedIn = isset($_SESSION['user_id']);
    
    // Vérification de la session cart existe et qu'elle n'est pas vide
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $cartItems = $_SESSION['cart'];
        // les éléments du panier 
        return $view->render($response, 'payment/payment.html.twig', [
            'cartItems' => $cartItems,
            'is_logged_in' => $isLoggedIn,
        ]);
    } else {
        // Redirigez l'utilisateur vers la page des produits s'il n'y a rien dans le panier
        return $response->withHeader('Location', '/products')->withStatus(302);
    }
});






$app->run();