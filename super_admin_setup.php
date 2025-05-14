<?php

// Create role 'super_admin' and assign it to user ID 1
$db = new PDO("mysql:host=localhost;dbname=filament", "root", "");

try {
    // Check and create user with ID 1 if needed
    $checkUser = $db->prepare("SELECT COUNT(*) FROM users WHERE id = 1");
    $checkUser->execute();
    $userExists = $checkUser->fetchColumn() > 0;
    
    if (!$userExists) {
        $insertUser = $db->prepare("INSERT INTO users (id, name, email, password, created_at, updated_at) VALUES (1, :name, :email, :password, NOW(), NOW())");
        $insertUser->execute([
            ':name' => 'Admin',
            ':email' => 'admin@example.com',
            ':password' => password_hash('admin123456', PASSWORD_DEFAULT)
        ]);
        echo "Created admin user with ID=1\n";
    } else {
        echo "User with ID=1 already exists\n";
    }
    
    // Check if roles table exists, create if not
    $checkRolesTable = $db->prepare("SHOW TABLES LIKE 'roles'");
    $checkRolesTable->execute();
    $rolesTableExists = $checkRolesTable->rowCount() > 0;
    
    if (!$rolesTableExists) {
        $db->exec("CREATE TABLE `roles` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `guard_name` varchar(255) NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
        )");
        echo "Created roles table\n";
    }
    
    // Check if model_has_roles table exists, create if not
    $checkModelHasRolesTable = $db->prepare("SHOW TABLES LIKE 'model_has_roles'");
    $checkModelHasRolesTable->execute();
    $modelHasRolesTableExists = $checkModelHasRolesTable->rowCount() > 0;
    
    if (!$modelHasRolesTableExists) {
        $db->exec("CREATE TABLE `model_has_roles` (
            `role_id` bigint(20) UNSIGNED NOT NULL,
            `model_type` varchar(255) NOT NULL,
            `model_id` bigint(20) UNSIGNED NOT NULL,
            PRIMARY KEY (`role_id`,`model_id`,`model_type`),
            KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
            CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
        )");
        echo "Created model_has_roles table\n";
    }
    
    // Check and create super_admin role if needed
    $checkRole = $db->prepare("SELECT id FROM roles WHERE name = 'super_admin'");
    $checkRole->execute();
    $role = $checkRole->fetch(PDO::FETCH_OBJ);
    
    if (!$role) {
        $insertRole = $db->prepare("INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES ('super_admin', 'web', NOW(), NOW())");
        $insertRole->execute();
        $roleId = $db->lastInsertId();
        echo "Created super_admin role\n";
    } else {
        $roleId = $role->id;
        echo "super_admin role already exists\n";
    }
    
    // Check and assign role to user ID 1 if needed
    $checkAssignment = $db->prepare("SELECT COUNT(*) FROM model_has_roles WHERE role_id = :role_id AND model_id = 1 AND model_type = 'App\\\\Models\\\\User'");
    $checkAssignment->execute([':role_id' => $roleId]);
    $assignmentExists = $checkAssignment->fetchColumn() > 0;
    
    if (!$assignmentExists) {
        $assignRole = $db->prepare("INSERT INTO model_has_roles (role_id, model_id, model_type) VALUES (:role_id, 1, 'App\\\\Models\\\\User')");
        $assignRole->execute([':role_id' => $roleId]);
        echo "Assigned super_admin role to user ID 1\n";
    } else {
        echo "User ID 1 already has super_admin role\n";
    }
    
    echo "Super admin setup complete!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}