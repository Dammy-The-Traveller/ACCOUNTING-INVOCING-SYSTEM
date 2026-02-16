<?php 
namespace Http\Controllers\Customers;
use Core\App;
use Core\Database;
use Core\Validator;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
class Supports{
 public function manage()
{
    // Pass counts to the view
    $db = App::resolve(Database::class);

    // Count tickets by status
    $waitingCount = $db->query(
        "SELECT COUNT(*) as total FROM support_tickets WHERE status = 'waiting'"
    )->find()['total'];

    $processingCount = $db->query(
        "SELECT COUNT(*) as total FROM support_tickets WHERE status = 'processing'"
    )->find()['total'];

    $solvedCount = $db->query(
        "SELECT COUNT(*) as total FROM support_tickets WHERE status = 'solved'"
    )->find()['total'];
    views('crm/support/manage.view.php',
[
        'waitingCount' => $waitingCount,
        'processingCount' => $processingCount,
        'solvedCount' => $solvedCount
    ]
);
}


public function store()
{
    $db = App::resolve(Database::class);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = clean($_POST['name']) ?? '';
        $email = clean($_POST['email']) ?? '';
        $subject = clean($_POST['sub']) ?? '';
        $message = clean($_POST['message']) ?? '';

        $errors = [];

if (!Validator::string( $name, 2, 255)) {
    $errors['name'] = 'Please provide a valid name.';
}

if (!Validator::email($email)) {
    $errors['email'] = 'Please provide a valid email address.';
}

if (!Validator::string($subject, 2, 255)) {
    $errors['address'] = 'Please provide a valid billing address.';
}
if (!Validator::string($message, 10, INF)) {
    $errors['phone'] = 'Phone number is required and should be valid.';
}



if (! empty($errors)) {
    return views('crm/support/index.view.php', [
        'errors' => $errors
    ]);
}
         if (!$subject || !$message || !$email) {
        die("Missing required fields.");
    }
        // 1. Insert or find user
        $user = $db->query("SELECT id FROM ticket_users WHERE email = ?", [$email])->find();

        if (!$user) {
            $db->query("INSERT INTO ticket_users (name, email) VALUES (?, ?)", [$name, $email]);
            $user_id = $db->lastInsertId();
        } else {
            $user_id = $user->id;
        }

        // 2. Create ticket
        $db->query("INSERT INTO support_tickets (user_id, subject) VALUES (?, ?)", [$user_id, $subject]);
        $ticket_id = $db->lastInsertId();

        // 3. Insert initial message
        $db->query("INSERT INTO ticket_replies (ticket_id, sender_type, message) VALUES (?, ?, ?)", [$ticket_id, 'user', $message]);

        echo "Ticket submitted!";
    }
}

public function thread() {
    $id = (int)($_GET['id'] ?? 0);
    $db = App::resolve(Database::class);

    if (!$id) {
        return $this->invalidTicket();
    }

    $support = $db->query("SELECT * FROM support_tickets WHERE id = :id", [
        'id' => $id
    ])->find();

    if (!$support) {
        return $this->invalidTicket();
    }

    $user = $db->query("SELECT * FROM ticket_users WHERE id = :id", [
        'id' => $support['user_id']
    ])->find();

    if (!$user) {
        return $this->invalidTicket("Invalid User");
    }

    $replies = $db->query("SELECT * FROM ticket_replies WHERE ticket_id = :id", [
        'id' => $support['id']
    ])->get();

    $attachments = [];
    if (!empty($replies)) {
        $attachments = $db->query(
            "SELECT * FROM ticket_attachments WHERE reply_id IN (
                SELECT id FROM ticket_replies WHERE ticket_id = :ticket_id
            )",
            ['ticket_id' => $support['id']]
        )->get();
    }

    views('crm/support/tickets.view.php', [
        'support'     => $support,
        'user'        => $user,
        'replies'     => $replies,
        'attachments' => $attachments,
        'id'=> $id,
        'db' => $db
    ]);
}

private function invalidTicket($message = 'Invalid Ticket') {
    die("<script>alert('$message'); window.history.back();</script>");
}


public function index(){
        $db = App::resolve(Database::class);

    // Count tickets by status
    $waitingCount = $db->query(
        "SELECT COUNT(*) as total FROM support_tickets WHERE status = 'waiting'"
    )->find()['total'];

    $processingCount = $db->query(
        "SELECT COUNT(*) as total FROM support_tickets WHERE status = 'processing'"
    )->find()['total'];

    $solvedCount = $db->query(
        "SELECT COUNT(*) as total FROM support_tickets WHERE status = 'solved'"
    )->find()['total'];
    views('crm/support/index.view.php',
[
        'waitingCount' => $waitingCount,
        'processingCount' => $processingCount,
        'solvedCount' => $solvedCount
    ]
);
}

public function ajaxList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $draw = $_GET['draw'] ?? 1;
    $start = $_GET['start'] ?? 0;
    $length = $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';

    $where = "WHERE status IN ('processing', 'waiting')";
    $params = [];

    if (!empty($searchValue)) {
        $where .= " AND (subject LIKE :search OR status LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Total records (only with processing or waiting)
    $totalQuery = "SELECT COUNT(*) as total FROM support_tickets WHERE status IN ('processing', 'waiting')";
    $totalRecords = $db->query($totalQuery)->find()['total'];

    // Filtered records
    $filteredQuery = "SELECT COUNT(*) as total FROM support_tickets $where";
    $totalFiltered = !empty($searchValue)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // Main data
    $dataQuery = "
        SELECT id, subject, status, created_at 
        FROM support_tickets 
        $where 
        ORDER BY id DESC 
        LIMIT $start, $length
    ";

    $tickets = $db->query($dataQuery, $params)->get();

    $output = [];
    $counter = $start + 1;

    foreach ($tickets as $ticket) {

         switch (strtolower($ticket['status'])) {
            case 'solved':
                $status = 'Solved';
                $statusClass = 'st-solved';
                break;
            case 'processing':
                $status = 'Processing';
                $statusClass = 'st-Processing';
                break;
            case 'waiting':
                $status = 'Waiting';
                $statusClass = 'st-Waiting';
                break;
            default:
                $status = ucfirst($ticket['status']);
                $statusClass = 'st-Waiting';
                break;
        }
        $output[] = [
            $counter++,
            htmlspecialchars($ticket['subject']),
            date('Y-m-d H:i', strtotime($ticket['created_at'])),
            ' <span class="'.$statusClass.'">'.ucfirst(htmlspecialchars($status)).'</span>',
            '
            <a href="/AIS/ticket-thread?id=' . $ticket['id'] . '" class="btn btn-info btn-sm">View</a>
             <a href="#" data-object-id="' . $ticket['id'] . '" class="btn btn-danger btn-sm delete-object">Delete</a>'
        ];
    }

    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalFiltered,
        "data" => $output
    ]);
    exit;
}

public function storeReply()
{
    global $db; // Using your DB instance
    
    try {
        $db->beginTransaction();

        // var_dump($_FILES['attachment']['name']);
        // exit;
        $ticketId   = (int)($_POST['ticket_id'] ?? null);
        $senderType = $_POST['sender_type'] ?? null; // 'user' or 'admin'
        $message    = htmlspecialchars( html_entity_decode(strip_tags($_POST['content'] ?? '')));
        $sentVia    = $_POST['sent_via'] ?? 'web';

        if (!$ticketId || !$senderType || !$message) {
             $_SESSION['success'] = '<strong>error</strong>: Missing required fields!';
        redirect('/AIS/ticket-thread?id=' . $ticketId);
        exit;
        }

        // Save the reply
        $db->query("INSERT INTO ticket_replies (ticket_id, sender_type, message, sent_via)
            VALUES (?, ?, ?, ?)
        ", [$ticketId, $senderType, $message, $sentVia]);


        $replyId = $db->lastInsertId();

        // Handle file attachment(s)
        if (!empty($_FILES['attachment']['name'])) {
            $uploadDir = base_path('Public/uploads/ticket_attachments/');
        //        var_dump($uploadDir);
        // exit;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // If multiple attachments are allowed
            if (is_array($_FILES['attachment']['name'])) {
                foreach ($_FILES['attachment']['name'] as $index => $name) {
                    if (!empty($name)) {
                        $tmpName = $_FILES['attachment']['tmp_name'][$index];
                        $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $name);
                        $targetPath = $uploadDir . $safeName;

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $db->query("
                                INSERT INTO ticket_attachments (reply_id, file_path, file_name)
                                VALUES (?, ?, ?)
                            ", [$replyId, 'uploads/ticket_attachments/' . $safeName, $name]);
                        }
                    }
                }
            } else {
                // Single file
                $name = $_FILES['attachment']['name'];
                $tmpName = $_FILES['attachment']['tmp_name'];
                $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $name);
                $targetPath = $uploadDir . $safeName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $db->query("
                        INSERT INTO ticket_attachments (reply_id, file_path, file_name)
                        VALUES (?, ?, ?)
                    ", [$replyId, 'uploads/ticket_attachments/' . $safeName, $name]);
                }
            }
        }

        // OPTIONAL: If admin replies, send email to user
    //     if ($senderType === 'admin') {
    //         $ticket = $db->find("
    //             SELECT su.email, t.subject 
    //             FROM support_tickets t
    //             JOIN ticket_users su ON su.id = t.user_id
    //             WHERE t.id = ?
    //         ", [$ticketId]);

    //         if ($ticket) {
    //             $fullname = $ticket['name'];
    //             // $subject = "Reply to your support ticket: " . $ticket['subject'];
    //             $body = nl2br(htmlspecialchars($message));
    //             // mail($ticket['email'], $subject, $body, "From: support@yourdomain.com\r\nContent-Type: text/html");
    //             $mail = new PHPMailer(true);

    //                 try {
    //     $mail->isSMTP();
    //     $mail->Host = 'dtt.com'; // SMTP server
    //     $mail->SMTPAuth = true;
    //     $mail->Username = $_ENV['SMTP_USERNAME']; 
    //     $mail->Password = $_ENV['SMTP_PASSWORD'];
    //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    //     $mail->Port = 587;

    //     $mail->setFrom('dtt.com ', 'DAMMY TECH');
    //     $mail->addAddress($ticket['email']);

    //     $mail->isHTML(true);
    //     $mail->Subject =  "Reply to your support ticket: " . $ticket['subject'];
    //     $mail->Body = "<p>Dear <b>$fullname</b>,</p>
    //     <p>$body.</p>
    //     <br>
    //     <p>Best Regards,<br>DAMMY TECH</p>";
        

    //     $mail->send();
    // } catch (Exception $e) {
    //     die('<script>alert("Mail Error: ' . addslashes($mail->ErrorInfo) . '"); window.history.back();</script>');
    // }
    //         }
    //     }

        $db->commit();

        $_SESSION['success'] = '<strong>Success</strong>: Reply has been sent successfully!';
        redirect('/AIS/ticket-thread?id=' . $ticketId);
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['success'] = '<strong>Error</strong>: Reply failed!';
        redirect('/AIS/ticket-thread?id=' . $ticketId);
        exit;

    //        error_log("Reply store error: " . $e->getMessage());
    // die("DB Error: " . $e->getMessage()); // For debugging only
    }
}

    public function updateStatus()
{
    $status = $_POST['status'] ?? null;
    $returnId = $_POST['id'] ?? null;

    if (!$status || !$returnId) {
        abort(400);
        die("<script>alert('Your ticket does not exist. Try again.'); window.history.back();</script>");
      
    }

     $db = App::resolve(Database::class);
    try {
        $db->beginTransaction();

        // Update returns table
   $db->query("UPDATE support_tickets SET status = ? WHERE id = ? LIMIT 1", [$status, $returnId]);

   
        $db->commit();
            $successMessage = '
      <strong>Success</strong>: Details updated successfully!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/ticket-thread?id='.$returnId.''); 
    } catch (Exception $e) {
        $db->rollBack();
         $successMessage = '
      <strong>Error</strong>: Failed to update status!!';
    $_SESSION['success'] = $successMessage;
      redirect('/AIS/ticket-thread?id='.$returnId.''); 
    }
}

public function mangeAjaxList()
{
    $db = App::resolve(Database::class);
    header('Content-Type: application/json');

    $draw = $_GET['draw'] ?? 1;
    $start = $_GET['start'] ?? 0;
    $length = $_GET['length'] ?? 10;
    $searchValue = $_GET['search']['value'] ?? '';

    $where = "WHERE status IN ('processing', 'waiting')";
    $params = [];

    if (!empty($searchValue)) {
        $where .= " AND (subject LIKE :search OR status LIKE :search)";
        $params['search'] = '%' . $searchValue . '%';
    }

    // Total records (only with processing or waiting)
    $totalQuery = "SELECT COUNT(*) as total FROM support_tickets WHERE status IN ('processing', 'waiting', 'solved', 'closed')";
    $totalRecords = $db->query($totalQuery)->find()['total'];

    // Filtered records
    $filteredQuery = "SELECT COUNT(*) as total FROM support_tickets $where";
    $totalFiltered = !empty($searchValue)
        ? $db->query($filteredQuery, $params)->find()['total']
        : $totalRecords;

    // Main data
    $dataQuery = "
        SELECT id, subject, status, created_at 
        FROM support_tickets 
        $where 
        ORDER BY id DESC 
        LIMIT $start, $length
    ";

    $tickets = $db->query($dataQuery, $params)->get();

    $output = [];
    $counter = $start + 1;

    foreach ($tickets as $ticket) {

         switch (strtolower($ticket['status'])) {
            case 'solved':
                $status = 'Solved';
                $statusClass = 'st-solved';
                break;
            case 'processing':
                $status = 'Processing';
                $statusClass = 'st-Processing';
                break;
            case 'waiting':
                $status = 'Waiting';
                $statusClass = 'st-Waiting';
                break;
                 case 'closed':
                $status = 'closed';
                $statusClass = 'st-Closed';
                break;
            default:
                $status = ucfirst($ticket['status']);
                $statusClass = 'st-Waiting';
                break;
        }
        $output[] = [
            $counter++,
            htmlspecialchars($ticket['subject']),
            date('Y-m-d H:i', strtotime($ticket['created_at'])),
            ' <span class="'.$statusClass.'">'.ucfirst(htmlspecialchars($status)).'</span>',
            '
            <a href="/AIS/ticket-thread?id=' . $ticket['id'] . '" class="btn btn-info btn-sm">View</a>
             <a href="#" data-object-id="' . $ticket['id'] . '" class="btn btn-danger btn-sm delete-object">Delete</a>'
        ];
    }

    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalFiltered,
        "data" => $output
    ]);
    exit;
}

public function delete()
{
    $db = App::resolve(Database::class);

    $ticketId = (int)($_GET['id'] ?? 0);

    if (!$ticketId) {
       echo json_encode(['status' => 'error', 'message' => 'Invalid ticket ID.']);
        return;
    }

    try {
        $db->beginTransaction();

        // Delete the ticket (replies + attachments will be auto-deleted by ON DELETE CASCADE)
        $db->query("DELETE FROM support_tickets WHERE id = ?", [$ticketId]);

        $db->commit();

     echo json_encode(['status' => 'success', 'message' => 'Ticket deleted successfully.']);

    } catch (Exception $e) {
        $db->rollBack();
       echo json_encode(['status' => 'error', 'message' => 'Failed to delete ticket.']);
    }
}

}