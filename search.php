<?php
if (isset($_GET['query'])) {
    $query = strtolower(trim($_GET['query'])); // Normalize the input
    $query_no_space = str_replace(' ', '', $query);

    // Simple redirects based on keyword
    if ($query === 'contacts' || $query === 'contact') {
        header("Location: contact.php");
        exit;
    } elseif ($query === 'shop') {
        header("Location: shop.php");
        exit;
    } elseif ($query === 'blog') {
        header("Location: blog.php");
        exit;
    } elseif ($query === 'about us' || $query === 'about') {
        header("Location: about-us.php");
        exit;
    }  elseif ($query === 'shop details' || $query === 'about') {
        header("Location: shop-details.php");
        exit;
    }   elseif ($query === 'blog details' || $query === 'blog') {
        header("Location: blog-details.php");
        exit;
    } elseif ($query === 'shopping cart' || $query === 'cart') {
        header("Location: shoppingcart.php");
        exit;
    } elseif ($query === 'checkout') {
        header("Location: checkout.php");
        exit;
    }  elseif ($query === 'faqs' || $query === 'faq') {
        header("Location: faqs.php");
        exit; 
    } elseif ($query === 'purchase history' || $query === 'purchase history') {
        header("Location: purchase_history.php");
        exit;
    
    }elseif ($query === 'checkout') {
        header("Location: checkout.php");
        exit;
    }elseif ($query === 'check out' && basename($_SERVER['PHP_SELF']) !== 'checkout.php') {
        header("Location: checkout.php");
        exit;
    } else {
        // Default: Show search result message
        echo "<h2>Search Results for: <em>" . htmlspecialchars($query) . "</em></h2>";
        // Here you can display database results, etc.
    }
} else {
    echo "<h2>No search query received.</h2>";
}
?>