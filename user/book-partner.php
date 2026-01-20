<?php
session_start();
include_once('../config/connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get available partners
$partners_stmt = $conn->prepare("
    SELECT p.*, u.full_name, u.email, u.phone, u.profile_image,
           (SELECT AVG(rating) FROM reviews WHERE partner_id = p.id) as avg_rating,
           (SELECT COUNT(*) FROM bookings WHERE partner_id = p.id AND status = 'completed') as completed_bookings,
           GROUP_CONCAT(DISTINCT s.specialization) as specializations
    FROM partners p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN partner_specializations ps ON p.id = ps.partner_id
    LEFT JOIN specializations s ON ps.specialization_id = s.id
    WHERE p.is_verified = 1 AND p.availability = 'available'
    AND u.status = 'active'
    GROUP BY p.id
    ORDER BY avg_rating DESC
");
$partners_stmt->execute();
$partners = $partners_stmt->get_result();

// Get cuisines for filter
$cuisines_stmt = $conn->prepare("SELECT DISTINCT cuisine_type FROM partners WHERE cuisine_type IS NOT NULL");
$cuisines_stmt->execute();
$cuisines = $cuisines_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Partner - CHEF AT PARTNER</title>
    <?php include_once '../links.php' ?>
    <style>
        .filter-sidebar {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 100px;
        }

        .partner-card-large {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
        }

        .partner-card-large:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .partner-header {
            display: flex;
            align-items: center;
            padding: 25px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .partner-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .partner-info {
            flex: 1;
            margin-left: 20px;
        }

        .partner-stats {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }

        .partner-details {
            padding: 25px;
        }

        .specializations {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .specialization-tag {
            background: rgba(199, 160, 125, 0.1);
            color: var(--primary-dark);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .availability-calendar {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }

        .time-slot {
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .time-slot:hover {
            border-color: var(--primary-color);
            background: rgba(199, 160, 125, 0.05);
        }

        .time-slot.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .booking-summary {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 100px;
        }
    </style>
</head>

<body>
    <?php include_once '../includes/header.php' ?>

    <div class="container-fluid mt-5 pt-4">
        <div class="row" style="margin-top: 80px;">
            <!-- Sidebar -->
            <div class="col-lg-3 col-xl-2">
                <div class="sidebar">
                    <?php include 'includes/sidebar.php'; ?>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10">
                <div class="mb-4">
                    <h1 class="h3 mb-2">Book a Partner</h1>
                    <p class="text-muted">Choose from our verified partners and book your culinary experience</p>
                </div>

                <!-- Search and Filter -->
                <div class="dashboard-card mb-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control border-0" id="searchPartners"
                                    placeholder="Search partners by name, cuisine, or specialization...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="sortPartners">
                                <option value="rating">Sort by: Highest Rating</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="experience">Most Experienced</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Partners Grid -->
                <div class="row" id="partnersContainer">
                    <?php while ($partner = $partners->fetch_assoc()): ?>
                        <div class="col-lg-6 mb-4 partner-item" data-name="<?php echo strtolower($partner['full_name']); ?>"
                            data-cuisine="<?php echo strtolower($partner['cuisine_type']); ?>"
                            data-specializations="<?php echo strtolower($partner['specializations']); ?>"
                            data-rating="<?php echo $partner['avg_rating'] ?? 0; ?>"
                            data-price="<?php echo $partner['hourly_rate']; ?>"
                            data-experience="<?php echo $partner['experience_years']; ?>">
                            <div class="partner-card-large">
                                <div class="partner-header">
                                    <img src="<?php echo !empty($partner['profile_image']) ? '../uploads/' . $partner['profile_image'] : 'https://via.placeholder.com/100'; ?>"
                                        class="partner-avatar" alt="<?php echo $partner['full_name']; ?>">
                                    <div class="partner-info">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h4 class="mb-1">
                                                    <?php echo $partner['full_name']; ?>
                                                </h4>
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-utensils"></i>
                                                    <?php echo ucfirst($partner['partner_type']); ?>
                                                    <?php if ($partner['cuisine_type']): ?>
                                                        •
                                                        <?php echo ucfirst($partner['cuisine_type']); ?> Cuisine
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <span class="badge bg-success">Available</span>
                                        </div>

                                        <div class="partner-stats">
                                            <div class="stat-item">
                                                <div class="stat-value">
                                                    <?php echo $partner['avg_rating'] ? round($partner['avg_rating'], 1) : '0.0'; ?>
                                                </div>
                                                <div class="stat-label">Rating</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value">
                                                    <?php echo $partner['completed_bookings']; ?>
                                                </div>
                                                <div class="stat-label">Bookings</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value">
                                                    <?php echo $partner['experience_years']; ?>y
                                                </div>
                                                <div class="stat-label">Experience</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value">₹
                                                    <?php echo $partner['hourly_rate']; ?>
                                                </div>
                                                <div class="stat-label">/hour</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="partner-details">
                                    <?php if ($partner['specializations']): ?>
                                        <h6 class="mb-2">Specializations</h6>
                                        <div class="specializations">
                                            <?php
                                            $specializations = explode(',', $partner['specializations']);
                                            foreach ($specializations as $spec):
                                                ?>
                                                <span class="specialization-tag">
                                                    <?php echo trim($spec); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-4">
                                        <a href="book-partner-detail.php?id=<?php echo $partner['id']; ?>"
                                            class="btn btn-theme w-100">
                                            <i class="fas fa-calendar-plus me-1"></i> Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Empty State -->
                <div id="noResults" class="empty-state" style="display: none;">
                    <div class="empty-state-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="mb-3">No Partners Found</h3>
                    <p class="text-muted mb-4">Try adjusting your search or filter to find what you're looking for.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/footer.php' ?>

    <script>
        // Search and Filter functionality
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchPartners');
            const sortSelect = document.getElementById('sortPartners');
            const partnersContainer = document.getElementById('partnersContainer');
            const partnerItems = document.querySelectorAll('.partner-item');
            const noResults = document.getElementById('noResults');

            function filterAndSortPartners() {
                const searchTerm = searchInput.value.toLowerCase();
                const sortBy = sortSelect.value;
                let visibleCount = 0;

                // Filter
                partnerItems.forEach(item => {
                    const name = item.dataset.name;
                    const cuisine = item.dataset.cuisine;
                    const specializations = item.dataset.specializations;

                    const matchesSearch = !searchTerm ||
                        name.includes(searchTerm) ||
                        cuisine.includes(searchTerm) ||
                        specializations.includes(searchTerm);

                    if (matchesSearch) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Sort
                const visibleItems = Array.from(partnersContainer.querySelectorAll('.partner-item[style*="block"]'));

                visibleItems.sort((a, b) => {
                    switch (sortBy) {
                        case 'rating':
                            return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                        case 'price_low':
                            return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                        case 'price_high':
                            return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                        case 'experience':
                            return parseFloat(b.dataset.experience) - parseFloat(a.dataset.experience);
                        default:
                            return 0;
                    }
                });

                // Reorder in DOM
                visibleItems.forEach(item => partnersContainer.appendChild(item));

                // Show/hide no results message
                noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            searchInput.addEventListener('input', filterAndSortPartners);
            sortSelect.addEventListener('change', filterAndSortPartners);
        });
    </script>
</body>

</html>