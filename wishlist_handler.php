<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false, 'action' => ''];



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $productId = $_POST['id'];

    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }

    $is_in_wishlist = false;
    foreach ($_SESSION['wishlist'] as $key => $item) {
        if ($item['id'] === $productId) {
            $is_in_wishlist = true;
            unset($_SESSION['wishlist'][$key]); // Remove the item
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index the array
            $response['success'] = true;
            $response['action'] = 'removed';
            break;
        }
    }

    

    if (!$is_in_wishlist) {
        // Add the item to the wishlist
        $_SESSION['wishlist'][] = ['id' => $productId];
        $response['success'] = true;
        $response['action'] = 'added';
    }
}

echo json_encode($response);
?>