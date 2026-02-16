<?php 

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve(Database::class);
// Sanitize input

$customer = [
    'name'             => htmlspecialchars(trim($_POST['name'])),
    'customer_code'    => htmlspecialchars(trim($_POST['cus_code'])),
    'phone'            => htmlspecialchars(trim($_POST['phone'])),
    'email'            => htmlspecialchars(trim($_POST['email'])),
    'address'          => htmlspecialchars(trim($_POST['address'])),
    'city'             => htmlspecialchars(trim($_POST['city'])),
    'region'           => htmlspecialchars(trim($_POST['region'])),
    'country'          => htmlspecialchars(trim($_POST['country'])),
    'postbox'          => htmlspecialchars(trim($_POST['postbox'])),
    'company'          => htmlspecialchars(trim($_POST['company'])),
    'taxid'            => htmlspecialchars(trim($_POST['taxid'])),
    'group_id'         => (int) $_POST['customergroup'],

    'shipping_billing' => isset($_POST['customer1']) ? 'yes' : 'no',
    'shipping_name'    => htmlspecialchars(trim($_POST['name_s'])),
    'shipping_phone'   => htmlspecialchars(trim($_POST['phone_s'])),
    'shipping_email'   => htmlspecialchars(trim($_POST['email_s'])),
    'shipping_address' => htmlspecialchars(trim($_POST['address_s'])),
    'shipping_city'    => htmlspecialchars(trim($_POST['city_s'])),
    'shipping_region'  => htmlspecialchars(trim($_POST['region_s'])),
    'shipping_country' => htmlspecialchars(trim($_POST['country_s'])),
    'shipping_postbox' => htmlspecialchars(trim($_POST['postbox_s'])),
      'created_at' => date('Y-m-d H:i:s'),
    'created_by' => $_SESSION['user']['ID']
];

// Validate required fields
$errors = [];

if (!Validator::string($customer['name'], 2, 255)) {
    $errors['name'] = 'Please provide a valid name.';
}

if (!Validator::email($customer['email'])) {
    $errors['email'] = 'Please provide a valid email address.';
}

if (!Validator::string($customer['address'], 2, 255)) {
    $errors['address'] = 'Please provide a valid billing address.';
}
if (!Validator::string($customer['phone'], 7, 20)) {
    $errors['phone'] = 'Phone number is required and should be valid.';
}

if (!Validator::string($customer['shipping_name'], 2, 255)) {
    $errors['shipping_name'] = 'Shipping name must be valid.';
}

if (! empty($errors)) {
    return views('Sales/modal.view.php', [
        'errors' => $errors
    ]);
}

// Insert into DB
$db->query("INSERT INTO customers (name, customer_code, phone, email, address, city, region, country, postbox, company, taxid, group_id, shipping_billing, shipping_name, shipping_phone, shipping_email, shipping_address, shipping_city,
        shipping_region, shipping_country, shipping_postbox, created_at, created_by
    ) VALUES (
        :name, :customer_code, :phone, :email, :address, :city, :region, :country, :postbox, :company, :taxid, :group_id, :shipping_billing, :shipping_name, :shipping_phone, :shipping_email, :shipping_address, :shipping_city,
        :shipping_region, :shipping_country, :shipping_postbox, :created_at, :created_by
    )
", $customer);
$_SESSION['success'] = '
      <strong>Success</strong>: Customer has been created successfully!';
redirect('/AIS/create');
exit;