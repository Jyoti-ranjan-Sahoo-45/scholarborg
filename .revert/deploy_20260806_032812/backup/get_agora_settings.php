<?php
/**
 * Temporary script to read Agora settings
 */
include 'index.php'; // Bootstraps Laravel
$settings = getFeaturesSettings();
echo json_encode($settings);
