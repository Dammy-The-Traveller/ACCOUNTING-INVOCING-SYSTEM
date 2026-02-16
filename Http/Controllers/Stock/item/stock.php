<?php 
namespace Http\Controllers\Stock\item;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;

class Stock{
    public function index(){
        $db = App::resolve(Database::class);
        $warehouses = null;
        if (isset($_GET['id'])) {
          $categoryId = $_GET['id'];
      
               if (!$categoryId) {
                  die("<script>alert('Invalid Token'); window.history.back();</script>");
              }
          // Fetch category
          $warehouses = $db->query("SELECT * FROM warehouses WHERE id = :id AND status = 'active'", [
              'id' => $categoryId
          ])->get();
      
           if (!$warehouses) {
              die("<script>alert('Warehouse not found.'); window.history.back();</script>");
           
          }
      
      
          views('Stock/item/stock-transfer/index.view.php', [
          'warehouses' => $warehouses,
      ]);
      exit;
      }

      $warehouses = $db->query("SELECT * FROM warehouses WHERE status = 'active'")->get();
        return views("Stock/item/stock-transfer/index.view.php", [
            'warehouses' => $warehouses
        ]);
    }

    public function getProductsByWarehouse()
    {
        $db = App::resolve(Database::class);
        $warehouseId = $_GET['warehouse_id'] ?? null;
        $search = $_GET['q'] ?? '';
        
        $sql = "SELECT id, name FROM products WHERE warehouse_id = ?";
        $params = [$warehouseId];
        
        if ($search) {
            $sql .= " AND name LIKE ?";
            $params[] = "%$search%";
        }
        
        $products = $db->query($sql, $params)->get();
        
        header('Content-Type: application/json');
        echo json_encode(array_map(function($product) {
            return [
                'id' => $product['id'],
                'name' => $product['name']
            ];
        }, $products));
    }  
    
    public function storeTransfer()
{
    // ddd($_POST);
    // exit;
    $db = App::resolve(Database::class);
    $fromWarehouse = $_POST['from_warehouse'] ?? null;
    $toWarehouse = $_POST['to_warehouse'] ?? null;
    $productIds = $_POST['products_l'] ?? [];
    $quantity = (int)($_POST['quantity'] ?? 0);

if (!$fromWarehouse || !$toWarehouse || empty($productIds) || $quantity <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    return;
}

try {
    $db->beginTransaction();

    foreach ($productIds as $productId) {

        $product = $db->query("SELECT * FROM products WHERE id = ? AND warehouse_id = ?", [$productId, $fromWarehouse])->find();
        if (!$product || $product['quantity'] < $quantity) {
            $successMessage = '
            <strong>Error</strong>: Not enough stock in the source warehouse for product ID '.$productId.'';
          $_SESSION['success'] = $successMessage;
            redirect('/AIS/stock-transfer');
         exit;
        }

        // Decode warehouse stock (handle null or empty case)
        $stockData = !empty($product['stock_by_warehouse'])
        ? json_decode($product['stock_by_warehouse'], true)
        : [];

        // $currentStock = isset($stockData[$fromWarehouse]) ? $stockData[$fromWarehouse] : 0;
        if (!isset($stockData[$fromWarehouse])) $stockData[$fromWarehouse] = $product['quantity'];
        if (!isset($stockData[$toWarehouse])) $stockData[$toWarehouse] = 0;
    

        // Adjust quantities
        $stockData[$fromWarehouse] -= $quantity;
        $stockData[$toWarehouse] += $quantity;

     
        $db->query("UPDATE products  SET stock_by_warehouse = ?, quantity = ? WHERE id = ?", [
            json_encode($stockData),  $stockData[$fromWarehouse], $productId
        ]);

        // Optionally, log the transfer
        $db->query("INSERT INTO stock_transfers (product_id, from_warehouse_id, to_warehouse_id, quantity, created_at) VALUES (?, ?, ?, ?, NOW())", [
            $productId, $fromWarehouse, $toWarehouse, $quantity
        ]);
    }

    $db->commit();
    $successMessage = '
    <strong>Success</strong>: Stock transferred successfully!!';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/stock-transfer');
} catch (Exception $e) {
    $db->rollBack();
    $successMessage = '
    <strong>Error</strong>: '.$e->getMessage().'';
  $_SESSION['success'] = $successMessage;
    redirect('/AIS/stock-transfer');
}
}
}