<?php
require_once 'includes/config.php';
require_once 'includes/auth_functions.php';

// Get featured offers (for non-logged in users) or all offers (for logged in users)
try {
    if (isLoggedIn()) {
        // For logged in users, show their offers
        $stmt = $pdo->prepare("SELECT * FROM hotel_offers WHERE user_id = ? ORDER BY created_at DESC LIMIT 6");
        $stmt->execute([$_SESSION['user_id']]);
        $offers = $stmt->fetchAll();
        $page_title = "My Offers";
    } else {
        // For guests, show featured offers
        $stmt = $pdo->prepare("SELECT * FROM hotel_offers ORDER BY created_at DESC LIMIT 6");
        $stmt->execute();
        $offers = $stmt->fetchAll();
        $page_title = "Featured Hotel Offers";
    }
} catch (PDOException $e) {
    die("Error fetching offers: " . $e->getMessage());
}
?>

<?php include 'includes/header.php'; ?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Find Your Perfect Stay</h1>
        <p>Discover amazing hotel offers at the best prices</p>
        <?php if (!isLoggedIn()): ?>
            <div class="hero-buttons">
                <a href="<?php echo BASE_URL; ?>/auth/register.php" class="btn btn-secondary">Sign Up</a>
                <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn">Login</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <h2 class="section-title"><?php echo $page_title; ?></h2>
    
    <?php if (empty($offers)): ?>
        <p>No offers found.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($offers as $offer): ?>
                <div class="card">
                    <?php if (!empty($offer['image_path'])): ?>
                        <img src="<?php echo BASE_URL . '/' . $offer['image_path']; ?>" alt="<?php echo htmlspecialchars($offer['title']); ?>" class="card-img">
                    <?php else: ?>
                        <img src="<?php echo BASE_URL; ?>/assets/images/default-hotel.jpg" alt="Default hotel image" class="card-img">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($offer['title']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars(substr($offer['description'], 0, 100)); ?>...</p>
                        <div class="card-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($offer['location']); ?>
                        </div>
                        <div class="card-price">$<?php echo number_format($offer['price'], 2); ?></div>
                        
                        <?php if (isLoggedIn()): ?>
                            <div class="d-flex justify-content-between">
                                <a href="offers/edit.php?id=<?php echo $offer['id']; ?>" class="btn btn-warning">Edit</a>
                                <a href="offers/delete.php?id=<?php echo $offer['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this offer?')">Delete</a>
                            </div>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-secondary">View Details</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <div class="text-center mt-4">
                <a href="offers/" class="btn btn-secondary">View All My Offers</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>