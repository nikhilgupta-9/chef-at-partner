<?php
include_once('../db-conn.php');

if (isset($_GET['id'])) {
    $service_id = (int) $_GET['id'];

    // Get service data
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();

    if ($service) {
        // Get features
        $features = $conn->query("SELECT * FROM service_features WHERE service_id = $service_id ORDER BY sort_order");
        $features_array = [];
        while ($feature = $features->fetch_assoc()) {
            $features_array[] = $feature;
        }

        // Get FAQs
        $faqs = $conn->query("SELECT * FROM service_faqs WHERE service_id = $service_id ORDER BY sort_order");
        $faqs_array = [];
        while ($faq = $faqs->fetch_assoc()) {
            $faqs_array[] = $faq;
        }

        echo json_encode([
            'success' => true,
            'service' => $service,
            'features' => $features_array,
            'faqs' => $faqs_array
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Service not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No service ID provided']);
}
?>