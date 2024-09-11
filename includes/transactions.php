<?php
require_once 'DB.php';

// Register user
function registerUser($username, $password, $role, $branch_id)
{
    global $pdo;
    $sql = "INSERT INTO `users` (username, role, branch_id, password) VALUES (:username, :role, :branch_id, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username, 'role' => $role, 'branch_id' => $branch_id, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
    return $stmt->rowCount();
}

// Function for log in user
function loginUser($username, $password, $role)
{
    global $pdo;

    $sql = "SELECT * FROM users WHERE username = :username AND role = :role";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username, 'role' => $role]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Change Password

function updatePassword($id, $newPassword)
{
    global $pdo;
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = :password WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':password' => $hashedPassword,
        ':id' => $id
    ]);
    return $stmt->rowCount();
}


// Generic function for get records
function getRecords($table, $conditions = [], $orderBy = '')
{
    global $pdo;

    // Base SQL query
    $sql = "SELECT * FROM `$table`";

    // Add conditions if provided
    if (!empty($conditions)) {
        $whereClauses = [];
        foreach ($conditions as $column => $value) {
            $whereClauses[] = "`$column` = :$column";
        }
        $sql .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // Add ordering if provided
    if (!empty($orderBy)) {
        $sql .= " ORDER BY $orderBy";
    }

    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);

    // Bind parameters if conditions are provided
    foreach ($conditions as $column => $value) {
        $stmt->bindValue(":$column", $value);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function for add Vendor
function addVendor($inputArr)
{
    global $pdo;
    $sql = "INSERT INTO `vendors` (`name`, `contact_info`, `address`, `branch_id`) VALUES (:name, :contact_info, :address, :branch_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'branch_id' => $inputArr['branch_id'],
        'name'    => $inputArr['name'],
        'contact_info'  => $inputArr['mobile'],
        'address' => $inputArr['address']
    ]);
    return $stmt->rowCount();
}

// Function for Add Employee
function addEmp($inputArr)
{
    global $pdo;
    // Check for duplicate mobile number
    $sqlCheck = "SELECT COUNT(*) FROM employees WHERE contact_number = :contact_number";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute(['contact_number' => $inputArr['mobile']]);
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        return 0;
    }

    $sql = "INSERT INTO `employees`(`branch_id`, `name`, `contact_number`, `address`, `img`, `id_img`, `salary`)
            VALUES (:branch_id, :name, :contact_number, :address, :img, :id_img, :salary)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'branch_id' => $inputArr['branch_id'],
        'name'    => $inputArr['name'],
        'contact_number'  => $inputArr['mobile'],
        'address' => $inputArr['address'],
        'img' => $inputArr['img'],
        'id_img' => $inputArr['proof'],
        'salary' => $inputArr['salary'],

    ]);
    return $stmt->rowCount();
}

// Function for Add Branch
function addBranch($inputArr)
{
    global $pdo;

    // Check for existing email
    $sqlCheck2 = "SELECT COUNT(*) FROM users WHERE username = :email";
    $stmtCheck2 = $pdo->prepare($sqlCheck2);
    $stmtCheck2->execute(['email' => $inputArr['email']]);
    $count2 = $stmtCheck2->fetchColumn();

    if ($count2 > 0) {
        return 0;
    }
    // Check for existing email
    $sqlCheck = "SELECT COUNT(*) FROM branches WHERE name = :name";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute(['name' => $inputArr['name']]);
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        return 0;
    }

    // Insert branch details
    $sqlBranch = "INSERT INTO `branches` (`name`, `address`, `contact_number`)
                  VALUES (:name, :address, :contact_number)";
    $stmtBranch = $pdo->prepare($sqlBranch);
    $stmtBranch->execute([
        'name' => $inputArr['name'],
        'address' => $inputArr['address'],
        'contact_number' => $inputArr['contact_number']
    ]);

    // Get the ID of the newly inserted branch
    $branch_id = $pdo->lastInsertId();

    // Register user with branch_id
    if ($branch_id) {
        registerUser($inputArr['email'], $inputArr['password'], 'branch', $branch_id);
    }

    return $stmtBranch->rowCount();
}

// Function for Add Product
function addServices2($inputArr)
{
    global $pdo;

    // Check for existing product name
    $sqlCheck = "SELECT COUNT(*) FROM products WHERE name = :name";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([
        'name' => $inputArr['sname']
    ]);
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        return 0; // Duplicate product
    }

    // Insert product details
    $sqlProduct = "INSERT INTO `products`(`name`, `description`, `service_price`) VALUES(:name, :description, :service_price)";
    $stmtProduct = $pdo->prepare($sqlProduct);
    $stmtProduct->execute([
        'name' => $inputArr['sname'],
        'description' => $inputArr['sdescription'],
        'service_price' => $inputArr['sprice']
    ]);

    return $stmtProduct->rowCount();
}

function addServices($inputArr)
{
    global $pdo;

    // Start a transaction
    $pdo->beginTransaction();

    try {
        // Check for existing product name
        $sqlCheck = "SELECT COUNT(*) FROM products WHERE name = :name";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            'name' => $inputArr['sname']
        ]);
        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            $pdo->rollBack();
            return 0; // Duplicate product
        }

        // Insert product details
        $sqlProduct = "INSERT INTO `products`(`name`, `description`, `service_price`) VALUES(:name, :description, :service_price)";
        $stmtProduct = $pdo->prepare($sqlProduct);
        $stmtProduct->execute([
            'name' => $inputArr['sname'],
            'description' => $inputArr['sdescription'],
            'service_price' => $inputArr['sprice']
        ]);

        // Get the ID of the newly inserted service
        $service_id = $pdo->lastInsertId();

        if ($service_id) {
            // Insert service as a variant in the product_variants table
            $sqlVariant = "INSERT INTO `product_variants`(`product_id`, `variant_name`, `price`, `stock`) VALUES (:product_id, :variant_name, :price, :stock)";
            $stmtVariant = $pdo->prepare($sqlVariant);

            $stmtVariant->execute([
                'product_id' => $service_id,
                'variant_name' => $inputArr['sname'],
                'price' => $inputArr['sprice'],
                'stock' => '0'
            ]);
        }

        // Commit the transaction
        $pdo->commit();

        return $service_id;
    } catch (Exception $e) {
        // An error occurred; rollback the transaction
        $pdo->rollBack();
        return false;
    }
}

// Function for Add Product
function addProduct($inputArr)
{
    global $pdo;

    // Check for existing product name
    $sqlCheck = "SELECT COUNT(*) FROM products WHERE name = :name";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([
        'name' => $inputArr['product_name']
    ]);
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        return 0; // Duplicate product
    }

    // Insert product details
    $sqlProduct = "INSERT INTO `products`(`name`, `description`, `img`) VALUES(:name, :description, :img)";
    $stmtProduct = $pdo->prepare($sqlProduct);
    $stmtProduct->execute([
        'name' => $inputArr['product_name'],
        'description' => $inputArr['product_desc'],
        'img' => $inputArr['product_img']
    ]);

    // Get the ID of the newly inserted product
    $product_id = $pdo->lastInsertId();

    if ($product_id) {
        // Insert product variants
        $variants = $inputArr['variants'];
        $sqlVariant = "INSERT INTO `product_variants`(`product_id`, `variant_name`, `price`, `stock`) VALUES (:product_id, :variant_name, :price, :stock)";
        $stmtVariant = $pdo->prepare($sqlVariant);

        foreach ($variants as $key => $variant) {
            $stmtVariant->execute([
                'product_id' => $product_id,
                'variant_name' => $variant['name'],
                'price' => $variant['price'],
                'stock' => $variant['stock']
            ]);
        }
    }

    return $stmtProduct->rowCount();
}

// Generic function for delete records
function deleteRecord($table, $id)
{
    global $pdo;

    // Sanitize table name to prevent SQL injection
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

    $sql = "DELETE FROM `$table` WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->rowCount();
}

// Function for get branch name by id
function getBranchNameById($branch_id)
{
    global $pdo;
    $sql = "SELECT name FROM branches WHERE id = :branch_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['branch_id' => $branch_id]);
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);
    return $branch['name'];
}
function getProductAndVariantById($id)
{
    global $pdo;

    // Step 1: Fetch variant details
    $sqlVariant = "SELECT variant_name, product_id FROM product_variants WHERE id = :id";
    $stmtVariant = $pdo->prepare($sqlVariant);
    $stmtVariant->execute(['id' => $id]);
    $variant = $stmtVariant->fetch(PDO::FETCH_ASSOC);

    if (!$variant) {
        return null; // Return null if the variant doesn't exist
    }

    // Step 2: Fetch the corresponding product details
    $sqlProduct = "SELECT name AS product_name FROM products WHERE id = :product_id";
    $stmtProduct = $pdo->prepare($sqlProduct);
    $stmtProduct->execute(['product_id' => $variant['product_id']]);
    $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        return null; // Return null if the product doesn't exist
    }

    // Step 3: Combine the variant and product details into one array
    $result = [
        'product_name' => $product['product_name'],
        'variant_name' => $variant['variant_name'],
        'product_id' => $variant['product_id']
    ];

    return $result;
}

// Function for get product & their varients
function getProductDetails($branch_id = null)
{
    global $pdo;

    $query = "SELECT p.id AS product_id, p.name AS product_name, pv.id, pv.variant_name, pv.price, pv.stock FROM products p
              JOIN product_variants pv ON p.id = pv.product_id";

    if ($branch_id) {
        $query .= " WHERE p.branch_id = :branch_id";
    }

    $query .= " ORDER BY p.id DESC";

    $stmt = $pdo->prepare($query);

    if ($branch_id) {
        $stmt->execute(['branch_id' => $branch_id]);
    } else {
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductDetailsById($product_id)
{
    global $pdo;

    // Query to fetch product details and its variants
    $query = "SELECT p.id AS product_id, p.name AS product_name, p.description AS product_desc, p.img AS product_img, pv.variant_name, pv.price, pv.stock
              FROM products p LEFT JOIN product_variants pv ON p.id = pv.product_id WHERE p.id = :product_id";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['product_id' => $product_id]);

    // Fetch the results
    $product = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Separate product details and variants
    if (empty($product)) {
        return false; // No product found
    }

    $productDetails = [
        'id' => $product[0]['product_id'],
        'name' => $product[0]['product_name'],
        'description' => $product[0]['product_desc'],
        'img' => $product[0]['product_img'],
        'branch_id' => $product[0]['branch_id'],
        'variants' => []
    ];

    foreach ($product as $row) {
        if ($row['variant_name']) {
            $productDetails['variants'][] = [
                'name' => $row['variant_name'],
                'price' => $row['price'],
                'stock' => $row['stock']
            ];
        }
    }

    return $productDetails;
}

// Update product details
function updateProduct($inputArr)
{
    global $pdo;

    $sqlProduct = "UPDATE products SET name = :name, description = :description, img = :img WHERE id = :id";
    $stmtProduct = $pdo->prepare($sqlProduct);
    $stmtProduct->execute([
        'name' => $inputArr['product_name'],
        'description' => $inputArr['product_desc'],
        'img' => $inputArr['product_img'],
        'id' => $inputArr['product_id']
    ]);

    // Update product variants (this assumes a simple setup; adjust based on your actual schema)
    $stmtDeleteVariants = $pdo->prepare("DELETE FROM product_variants WHERE product_id = :product_id");
    $stmtDeleteVariants->execute(['product_id' => $inputArr['product_id']]);

    $stmtInsertVariant = $pdo->prepare("INSERT INTO product_variants (product_id, variant_name, price, stock) VALUES (:product_id, :variant_name, :price, :stock)");
    foreach ($inputArr['variants'] as $variant) {
        $stmtInsertVariant->execute([
            'product_id' => $inputArr['product_id'],
            'variant_name' => $variant['name'],
            'price' => $variant['price'],
            'stock' => $variant['stock']
        ]);
    }

    return $stmtProduct->rowCount() > 0;
}

function getVendorById($vendor_id)
{
    global $pdo;

    $query = "SELECT * FROM vendors WHERE id = :vendor_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['vendor_id' => $vendor_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateVendor($vendor_id, $data)
{
    global $pdo;

    $query = "UPDATE vendors SET branch_id = :branch_id, name = :name, contact_info = :contact_info, address = :address WHERE id = :vendor_id";
    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'branch_id' => $data['branch_id'],
        'name' => $data['name'],
        'contact_info' => $data['mobile'],
        'address' => $data['address'],
        'vendor_id' => $vendor_id
    ]);

    return $stmt->rowCount();
}

function getBranchById($id)
{
    global $pdo;

    $query = "SELECT * FROM branches WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateBranch($id, $data)
{
    global $pdo;

    $query = "UPDATE branches SET name = :name, address = :address, contact_number = :contact_number WHERE id = :id";
    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'name' => $data['name'],
        'address' => $data['address'],
        'contact_number' => $data['contact_number'],
        'id' => $id
    ]);

    return $stmt->rowCount();
}

function getEmployeeById($id)
{
    global $pdo;
    $sql = "SELECT * FROM employees WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $emp = $stmt->fetch(PDO::FETCH_ASSOC);
    if (is_array($emp)) {
        return $emp['name'];
    } else {
        return 'Employee Not Exist';
    }
}

function updateEmployee($id, $inputArr)
{
    global $pdo;
    $sql = "UPDATE employees SET branch_id = :branch_id, name = :name, contact_number = :contact_number, address = :address, img = :img, id_img = :id_img, salary = :salary WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $id,
        'branch_id' => $inputArr['branch_id'],
        'name' => $inputArr['name'],
        'contact_number' => $inputArr['mobile'],
        'address' => $inputArr['address'],
        'img' => $inputArr['img'],
        'id_img' => $inputArr['proof'],
        'salary' => $inputArr['salary']
    ]);
    return $stmt->rowCount();
}

// Function for converting 1233 => 1 Thousand 233
function formatAmountInINR($amount)
{
    $units = [
        'Crore' => 10000000,
        'Lakh' => 100000,
        'Thousand' => 1000,
        'Hundred' => 100
    ];

    if ($amount == 0) {
        return 'Zero';
    }

    $result = '';
    foreach ($units as $unit => $value) {
        if ($amount >= $value) {
            $count = floor($amount / $value);
            $amount -= $count * $value;
            $result .= ($result ? ' ' : '') . number_format($count) . ' ' . $unit;
        }
    }

    // Handle remaining amount (less than 100)
    if ($amount > 0) {
        $result .= ($result ? ' ' : '') . number_format($amount);
    }

    return $result;
}

// Generate Purchase Id automatically
function generatePurchaseId()
{
    global $pdo;
    $stmt = $pdo->query("SELECT MAX(id) AS max_id FROM purchases");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextId = $row['max_id'] + 1;
    return 'PUR-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
}

// Function for Add Purchase
function addPurchase($inputArr)
{
    global $pdo;

    // Insert purchase details
    $sqlPurchase = "INSERT INTO `purchases`(`purchase_date`, `purchase_id`, `vendor_id`, `grand_total`, `paid_amt`, `due_date`, `due_amt`, `notes`)
    VALUES(:purchase_date, :purchase_id, :vendor_id, :grand_total, :paid_amt, :due_date, :due_amt, :notes)";
    $stmtPurchase = $pdo->prepare($sqlPurchase);
    $stmtPurchase->execute([
        'purchase_date' => $inputArr['purchase_date'],
        'purchase_id' => $inputArr['purchase_id'],
        'vendor_id' => $inputArr['vendor_id'],
        'grand_total' => $inputArr['grand_total'],
        'paid_amt' => $inputArr['paid_amt'],
        'due_date' => $inputArr['due_date'],
        'due_amt' => $inputArr['due_amt'],
        'notes' => $inputArr['notes']
    ]);

    // Get the ID of the newly inserted purchase
    $purchase_id = $inputArr['purchase_id'];

    if ($purchase_id) {
        // Insert purchased products
        $products = $inputArr['products'];
        $sqlProduct = "INSERT INTO `purchase_items`(`purchase_id`, `product_id`, `price`, `quantity`, `total`) VALUES (:purchase_id, :product_id, :price, :quantity, :total)";
        $stmtProduct = $pdo->prepare($sqlProduct);

        foreach ($products as $key => $product) {
            $stmtProduct->execute([
                'purchase_id' => $purchase_id,
                'product_id' => $product['varient_id'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'total' => $product['total']
            ]);
        }
    }
    updateStockOnPurchase($purchase_id);
    return $stmtPurchase->rowCount();
}

// Get product_id of corresponding purchase_id
function getProductIdByPurchaseId($purchase_id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT product_id FROM purchase_items WHERE purchase_id = :purchase_id");
    $stmt->execute(['purchase_id' => $purchase_id]);
    $product_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $product_id;
}

// Function for get purchase_items price and quantity
function getPurchaseItems($purchase_id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT product_id, price, quantity FROM purchase_items WHERE purchase_id = :purchase_id");
    $stmt->execute(['purchase_id' => $purchase_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $items;
}

// Function for get services from products table where price > 0
function getServices($branch = null)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE service_price > 0");
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $services;
}

// Function for get one services by ID
function getOneService($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    return $service;
}

function updateService2($inputArr)
{
    global $pdo;

    $sql = "UPDATE products SET name = :name, service_price = :service_price, description = :description WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name' => $inputArr['sname'],
        'service_price' => $inputArr['sprice'],
        'description' => $inputArr['sdesc'],
        'id' => $inputArr['sid']
    ]);
    return $stmt->rowCount();
}

function updateService($inputArr)
{
    global $pdo;

    // Start a transaction
    $pdo->beginTransaction();

    try {
        // Update the products table
        $sqlProduct = "UPDATE products
                       SET name = :name,
                           service_price = :service_price,
                           description = :description
                       WHERE id = :id";
        $stmtProduct = $pdo->prepare($sqlProduct);
        $stmtProduct->execute([
            'name' => $inputArr['sname'],
            'service_price' => $inputArr['sprice'],
            'description' => $inputArr['sdesc'],
            'id' => $inputArr['sid']
        ]);

        // Update the product_variants table
        $sqlVariant = "UPDATE product_variants
                       SET variant_name = :variant_name,
                           price = :price
                       WHERE product_id = :product_id";
        $stmtVariant = $pdo->prepare($sqlVariant);
        $stmtVariant->execute([
            'variant_name' => $inputArr['sname'],
            'price' => $inputArr['sprice'],
            'product_id' => $inputArr['sid']
        ]);

        // Commit the transaction
        $pdo->commit();

        // Return the total number of affected rows
        return $stmtProduct->rowCount() + $stmtVariant->rowCount();
    } catch (Exception $e) {
        // An error occurred; rollback the transaction
        $pdo->rollBack();
        return false;
    }
}

function addExpense($inputArr)
{
    global $pdo;
    $sql = "INSERT INTO `expenses`(`branch_id`, `expense_date`, `expense_type`, `amount`, `description`, `employee_id`)
    VALUES (:branch_id, :date, :type, :amount, :description, :employee)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'branch_id' => $inputArr['branch_id'],
        'date' => $inputArr['date'],
        'type' => $inputArr['type'],
        'amount' => $inputArr['amount'],
        'description' => $inputArr['notes'],
        'employee' => $inputArr['employee'],
    ]);
    return $stmt->rowCount();
}

function addCustomer($inputArr)
{
    global $pdo;

    // Check if customer already exists based on contact number
    $sql = "SELECT id FROM customers WHERE contact_number = :contact_number";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['contact_number' => $inputArr['mobile']]);
    $existingCustomer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingCustomer) {
        // Customer already exists, return the existing customer ID
        return $existingCustomer['id'];
    } else {
        // Insert new customer
        $sql = "INSERT INTO customers (branch_id, name, city, contact_number, dob, address)
                VALUES (:branch_id, :name, :city, :contact_number, :dob, :address)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'branch_id' => $inputArr['branch_id'],
            'name' => $inputArr['name'],
            'city' => $inputArr['city'],
            'contact_number' => $inputArr['mobile'],
            'dob' => $inputArr['dob'],
            'address' => $inputArr['address'],
        ]);
        return $pdo->lastInsertId();
    }
}


function updateCustomer($id, $inputArr)
{
    global $pdo;
    // Update query
    $sql = "UPDATE `customers` SET `branch_id` = :branch_id, `name` = :name, `city` = :city,
            `contact_number` = :mobile, `dob` = :dob, `address` = :address WHERE `id` = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge($inputArr, ['id' => $id]));
    return $stmt->rowCount();
}

function getOneCustomer($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    return $customer;
}

function addIncome($incomeData)
{
    global $pdo;

    $sql = "INSERT INTO income (customer_id, branch_id, total_amount, paid_amount, due_amount, due_date, notes)
            VALUES (:customer_id, :branch_id, :total_amount, :paid_amount, :due_amount, :due_date, :notes)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'customer_id' => $incomeData['customer_id'],
        'branch_id' => $incomeData['branch_id'],
        'total_amount' => $incomeData['total_amount'],
        'paid_amount' => $incomeData['paid_amount'],
        'due_amount' => $incomeData['due_amount'],
        'due_date' => $incomeData['due_date'],
        'notes' => $incomeData['notes']
    ]);

    return $pdo->lastInsertId();
}

function addItemUsed($itemData)
{
    global $pdo;

    $sql = "INSERT INTO items_used (income_id, varient_id, price, quantity, total)
            VALUES (:income_id, :varient_id, :price, :quantity, :total)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'income_id' => $itemData['income_id'],
        'varient_id' => $itemData['varient_id'],
        'price' => $itemData['price'],
        'quantity' => $itemData['quantity'],
        'total' => $itemData['total']
    ]);

    return $pdo->lastInsertId();
}

function deletePurchase($purchaseId)
{
    global $pdo;
    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Delete the purchase items associated with the purchase ID
        $stmt = $pdo->prepare("DELETE FROM purchase_items WHERE purchase_id = :purchaseId");
        $stmt->bindParam(':purchaseId', $purchaseId, PDO::PARAM_STR);
        $stmt->execute();

        // Delete the purchase record from the purchases table
        $stmt = $pdo->prepare("DELETE FROM purchases WHERE purchase_id = :purchaseId");
        $stmt->bindParam(':purchaseId', $purchaseId, PDO::PARAM_STR);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        return true; // Return true if deletion is successful
    } catch (Exception $e) {
        // Rollback the transaction if there is an error
        $pdo->rollBack();
        throw new Exception("Failed to delete purchase: " . $e->getMessage());
    }
}

function updateStockOnPurchase($purchaseId)
{
    global $pdo;
    try {
        // Get all purchase items related to the purchase ID
        $stmt = $pdo->prepare("
            SELECT product_id, quantity
            FROM purchase_items
            WHERE purchase_id = :purchaseId
        ");
        $stmt->bindParam(':purchaseId', $purchaseId, PDO::PARAM_STR);
        $stmt->execute();
        $purchaseItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Update the stock for each product variant
        foreach ($purchaseItems as $item) {
            $stmt = $pdo->prepare("
                UPDATE product_variants
                SET stock = stock + :quantity
                WHERE id = :productId
            ");
            $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':productId', $item['product_id'], PDO::PARAM_INT);
            $stmt->execute();
        }

        return true; // Return true if the stock update is successful
    } catch (Exception $e) {
        throw new Exception("Failed to update stock: " . $e->getMessage());
    }
}

function reverseStockOnPurchaseEdit($purchaseId)
{
    global $pdo;
    try {
        // Get all purchase items related to the purchase ID
        $stmt = $pdo->prepare("
            SELECT product_id, quantity
            FROM purchase_items
            WHERE purchase_id = :purchaseId
        ");
        $stmt->bindParam(':purchaseId', $purchaseId, PDO::PARAM_STR);
        $stmt->execute();
        $purchaseItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Reverse the stock for each product variant
        foreach ($purchaseItems as $item) {
            $stmt = $pdo->prepare("
                UPDATE product_variants
                SET stock = stock - :quantity
                WHERE id = :productId
            ");
            $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':productId', $item['product_id'], PDO::PARAM_INT);
            $stmt->execute();
        }

        return true; // Return true if the stock reversal is successful
    } catch (Exception $e) {
        throw new Exception("Failed to reverse stock: " . $e->getMessage());
    }
}
function updateStockOnItemsUsed($incomeId)
{
    global $pdo;

    try {
        // Get all items used related to the income ID
        $stmt = $pdo->prepare("
            SELECT varient_id, quantity
            FROM items_used
            WHERE income_id = :incomeId
        ");
        $stmt->bindParam(':incomeId', $incomeId, PDO::PARAM_INT);
        $stmt->execute();
        $itemsUsed = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Update the stock for each product variant
        foreach ($itemsUsed as $item) {
            $stmt = $pdo->prepare("
                UPDATE product_variants
                SET stock = stock - :quantity
                WHERE id = :variantId
            ");
            $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':variantId', $item['varient_id'], PDO::PARAM_INT);
            $stmt->execute();
        }

        return true; // Return true if the stock update is successful
    } catch (Exception $e) {
        throw new Exception("Failed to update stock: " . $e->getMessage());
    }
}


// Function for getting row count of table

function getRowCount($table)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table");
    $stmt->execute();
    $rowCount = $stmt->fetchColumn();
    return $rowCount;
}

function getRecentIncomeEntries($limit = 5)
{
    global $pdo;

    $query = "
        SELECT
            income.id,
            customers.name AS customer_name,
            income.total_amount,
            income.paid_amount,
            income.due_amount,
            income.due_date,
            income.notes,
            income.created_at
        FROM
            income
        INNER JOIN
            customers ON income.customer_id = customers.id
        ORDER BY
            income.created_at DESC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getDailyEntries2($branch = null)
{
    global $pdo;

    $query = "
        SELECT
            income.id AS income_id,
            customers.name AS customer_name,
            income.total_amount,
            income.paid_amount,
            income.due_amount,
            income.due_date,
            income.notes,
            items_used.varient_id,
            items_used.price,
            items_used.quantity,
            items_used.total AS item_total,
            items_used.created_at AS item_created_at
        FROM
            income
        INNER JOIN
            customers ON income.customer_id = customers.id
        INNER JOIN
            items_used ON income.id = items_used.income_id
    ";

    // Add branch condition if $branch is not null
    if ($branch !== null) {
        $query .= " AND income.branch_id = :branch_id";
    }

    $query .= " ORDER BY income.created_at DESC";

    $stmt = $pdo->prepare($query);

    if ($branch !== null) {
        $stmt->bindValue(':branch_id', $branch, PDO::PARAM_INT);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get daily entries with optional date range
function getDailyEntries($branch = null, $from = null, $to = null)
{
    global $pdo;

    $query = "
        SELECT
            income.id AS income_id,
            customers.name AS customer_name,
            customers.contact_number AS mobile,
            income.total_amount,
            income.paid_amount,
            income.due_amount,
            income.due_date,
            income.notes,
            items_used.varient_id,
            items_used.price,
            items_used.quantity,
            items_used.total AS item_total,
            items_used.created_at AS item_created_at
        FROM
            income
        INNER JOIN
            customers ON income.customer_id = customers.id
        INNER JOIN
            items_used ON income.id = items_used.income_id
    ";

    // Add conditions based on branch and date range
    $conditions = [];
    $params = [];

    if ($branch !== null) {
        $conditions[] = "income.branch_id = :branch";
        $params[':branch'] = $branch;
    }

    if ($from && $to) {
        $conditions[] = "DATE(income.created_at) BETWEEN :from AND :to";
        $params[':from'] = $from;
        $params[':to'] = $to;
    } else {
        $conditions[] = "DATE(income.created_at) = CURDATE()";
    }

    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY income.created_at DESC";

    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get Product and varient name by varient_id
function getProductAndVarientName($varient_id)
{
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT
            products.name AS product_name,
            product_variants.variant_name AS variant_name
        FROM
            product_variants
        INNER JOIN
            products ON product_variants.product_id = products.id
        WHERE
            product_variants.id = :varient_id
    ");
    $stmt->bindParam(':varient_id', $varient_id, PDO::PARAM_INT);
    $stmt->execute();
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    return $p;
}

function getUsedItemName($id)
{
    $product_variant = getProductAndVarientName($id);

    if ($product_variant && is_array($product_variant) && !empty($product_variant)) {
        // If it's a product variant, return the combined name
        return $product_variant['product_name'] . ' ' . $product_variant['variant_name'];
    } else {
        // If not a product variant, try to get a service
        $service = getOneService($id);

        if ($service && is_array($service) && !empty($service)) {
            // If it's a service, return the name
            return $service['name'];
        }
    }

    // If neither a product variant nor a service is found, return null
    return null;
}

function getIncomeAndExpenseByBranch2($branch_id = null)
{
    global $pdo;

    // Base query to get income and expense totals
    $query = "
        SELECT
            branches.name AS branch_name,
            SUM(income.total_amount) AS total_income,
            SUM(expenses.amount) AS total_expense
        FROM
            branches
        LEFT JOIN
            income ON branches.id = income.branch_id
        LEFT JOIN
            expenses ON branches.id = expenses.branch_id
    ";

    // If a specific branch is provided, add a WHERE clause
    if (!is_null($branch_id)) {
        $query .= " WHERE branches.id = :branch_id";
    }

    // Group by branch to get totals for each branch
    $query .= " GROUP BY branches.id";

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);

    // Bind branch_id if provided
    if (!is_null($branch_id)) {
        $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    }

    $stmt->execute();

    // Fetch the results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getIncomeAndExpenseByBranch($branch_id = null, $from_date = null, $to_date = null)
{
    global $pdo;

    // Subquery for total income
    $incomeQuery = "
        SELECT
            branches.id AS branch_id,
            branches.name AS branch_name,
            SUM(income.paid_amount) AS total_income
        FROM
            branches
        LEFT JOIN
            income ON branches.id = income.branch_id
    ";

    // Add conditions for income dates
    $incomeConditions = [];
    if (!is_null($branch_id)) {
        $incomeConditions[] = "branches.id = :branch_id";
    }
    if (!is_null($from_date)) {
        $incomeConditions[] = "income.created_at >= :from_date";
    }
    if (!is_null($to_date)) {
        $incomeConditions[] = "income.created_at <= :to_date";
    }

    if (!empty($incomeConditions)) {
        $incomeQuery .= " WHERE " . implode(" AND ", $incomeConditions);
    }

    $incomeQuery .= " GROUP BY branches.id";

    // Subquery for total expenses
    $expenseQuery = "
        SELECT
            branches.id AS branch_id,
            SUM(expenses.amount) AS total_expense
        FROM
            branches
        LEFT JOIN
            expenses ON branches.id = expenses.branch_id
    ";

    // Add conditions for expense dates
    $expenseConditions = [];
    if (!is_null($branch_id)) {
        $expenseConditions[] = "branches.id = :branch_id";
    }
    if (!is_null($from_date)) {
        $expenseConditions[] = "expenses.created_at >= :from_date";
    }
    if (!is_null($to_date)) {
        $expenseConditions[] = "expenses.created_at <= :to_date";
    }

    if (!empty($expenseConditions)) {
        $expenseQuery .= " WHERE " . implode(" AND ", $expenseConditions);
    }

    $expenseQuery .= " GROUP BY branches.id";

    // Combine income and expense queries
    $query = "
        SELECT
            income.branch_name,
            income.total_income,
            IFNULL(expense.total_expense, 0) AS total_expense
        FROM
            ($incomeQuery) AS income
        LEFT JOIN
            ($expenseQuery) AS expense ON income.branch_id = expense.branch_id
    ";

    // Prepare and execute the query
    $stmt = $pdo->prepare($query);

    // Bind parameters if provided
    if (!is_null($branch_id)) {
        $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
        $stmt->bindParam(':branch_id', $branch_id, PDO::PARAM_INT);
    }
    if (!is_null($from_date)) {
        $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
        $stmt->bindParam(':from_date', $from_date, PDO::PARAM_STR);
    }
    if (!is_null($to_date)) {
        $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
        $stmt->bindParam(':to_date', $to_date, PDO::PARAM_STR);
    }

    $stmt->execute();

    // Fetch the results
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
