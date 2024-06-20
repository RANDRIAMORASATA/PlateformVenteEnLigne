<?php

use App\Entity\Product;
use App\Entity\Category;
require_once "bootstrap.php";
require_once "image.php";

$faker = Faker\Factory::create();

// Function to generate slugs
function generateSlug($string) {
    // Replace non-letter or digits by -
    $string = preg_replace('~[^\pL\d]+~u', '-', $string);
    // Transliterate
    $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
    // Remove unwanted characters
    $string = preg_replace('~[^-\w]+~', '', $string);
    // Trim
    $string = trim($string, '-');
    // Remove duplicate -
    $string = preg_replace('~-+~', '-', $string);
    // Lowercase
    $string = strtolower($string);
    if (empty($string)) {
        return 'n-a';
    }
    return $string;
}

// Creating and persisting categories first
$categoriesData = [
    "Pantallon",
    "Robe",
    "Chemise",
    "Article bébé",
    "Jouets",
    "Décoration",
    "Pièce vélo",
];

$categories = [];
foreach ($categoriesData as $categoryName) {
    $category = (new Category())
        ->setName($categoryName)
        ->setSlug(generateSlug($categoryName)); // Set the slug
    $entityManager->persist($category);
    $categories[] = $category; // Storing category instances for later use
}

$entityManager->flush();

// Creating and persisting products with correct category references and slugs
$products = [
    (new Product())->setName("Pantallon Bébé 3ans")
                    ->setDescription("Pour un garçon de 1m max, couleur marron claire")
                    ->setCategory($categories[3]) // Assuming "Article bébé" is at index 3
                    ->setSlug(generateSlug("Pantallon Bébé 3ans"))
                    ->setPrice(29.99)
                    ->setImageUrl($images[2]),
    (new Product())->setName("Robe Femme M")
                    ->setDescription("Pour une fille de poid 12kg")
                    ->setCategory($categories[1]) // Assuming "Robe" is at index 1
                    ->setSlug(generateSlug("Robe Fille 3ans"))
                    ->setPrice(52.99)
                    ->setImageUrl($images[5]), 
    (new Product())->setName("Voiture electrique")
                    ->setDescription("Poid maximum 30kg")
                    ->setCategory($categories[4]) // Assuming "Jouets" is at index 4
                    ->setSlug(generateSlug("Voiture electrique"))
                    ->setPrice(69.99)
                    ->setImageUrl($images[6]), 
    (new Product())->setName("Robe Bébé 3ans")
                    ->setDescription("Pour une fille de 1m max, couleur rose")
                    ->setCategory($categories[3]) // Assuming "Article bébé" is at index 3
                    ->setSlug(generateSlug("Robe Bébé 3ans"))
                    ->setPrice(59.99)
                    ->setImageUrl($images[5]), 
    (new Product())->setName("Pant Homme L")
                    ->setDescription("Pour un homme de 90kg max, couleur beige claire")
                    ->setCategory($categories[0]) // Assuming "Pantallon" is at index 0
                    ->setSlug(generateSlug("Pant Homme L"))
                    ->setPrice(95.99)
                    ->setImageUrl($images[7]), 
    (new Product())->setName("Pantallon Bébé 1an")
                    ->setDescription("Pour un garçon de 60cm max, couleur rouge")
                    ->setCategory($categories[3]) // Assuming "Article bébé" is at index 3
                    ->setSlug(generateSlug("Pantallon Bébé 1an"))
                    ->setPrice(100.99)
                    ->setImageUrl($images[2]), 
    (new Product())->setName("Rideau Lourd")
                    ->setDescription("Poid 5kg, couleur gris foncé")
                    ->setCategory($categories[5]) // Assuming "Décoration" is at index 5
                    ->setSlug(generateSlug("Rideau Lourd"))
                    ->setPrice(28.99)
                    ->setImageUrl($images[4]), 
    (new Product())->setName("pneu taille M")
                    ->setDescription("Poid 5kg, BMC")
                    ->setCategory($categories[6]) // Assuming "Pièce vélo" is at index 6
                    ->setSlug(generateSlug("pneu taille M"))
                    ->setPrice(69.09)
                    ->setImageUrl($images[3]), 
    (new Product())->setName("chemise taille M")
                    ->setDescription("Pour un homme de taille M, provenant de Dubai")
                    ->setCategory($categories[3]) // Assuming "Pièce vélo" is at index 6
                    ->setSlug(generateSlug("chemise taille M"))
                    ->setPrice(59.99)
                    ->setImageUrl($images[0]), 
    (new Product())->setName("Chemise")
                    ->setDescription("taille L couleur Rose")
                    ->setCategory($categories[3]) // Assuming "Décoration" is at index 5
                    ->setSlug(generateSlug("chemise"))
                    ->setPrice(28.99)
                    ->setImageUrl($images[1]), 
    (new Product())->setName("pneu taille M")
                    ->setDescription("Poid 5kg, BMC")
                    ->setCategory($categories[6]) // Assuming "Pièce vélo" is at index 6
                    ->setSlug(generateSlug("pneu taille M"))
                    ->setPrice(69.09)
                    ->setImageUrl($images[0]), 
    (new Product())->setName("chemise taille M")
                    ->setDescription("Pour un homme de taille M, provenant de Dubai")
                    ->setCategory($categories[3]) // Assuming "Pièce vélo" is at index 6
                    ->setSlug(generateSlug("chemise taille M"))
                    ->setPrice(59.99)
                    ->setImageUrl($images[0]), 
];

foreach ($products as $product) {
    $entityManager->persist($product);
}

$entityManager->flush();
