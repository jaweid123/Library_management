<?php
// api.php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'connection.php';

// ----------------- users file (local storage) -----------------
$USERS_FILE = __DIR__ . '/users.json';

function ensure_users_file() {
    global $USERS_FILE;
    if (!file_exists($USERS_FILE)) {
        // default admin user (password hashed)
        $default = [
            ['id'=>1, 'username'=>'Jaweidmo', 'password'=>password_hash('ja1234', PASSWORD_DEFAULT), 'role'=>'admin']
        ];
        file_put_contents($USERS_FILE, json_encode($default, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }
}
function read_users() {
    global $USERS_FILE;
    ensure_users_file();
    $json = file_get_contents($USERS_FILE);
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}
function write_users($arr) {
    global $USERS_FILE;
    file_put_contents($USERS_FILE, json_encode($arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}

// ----------------- whitelist tables -----------------
$TABLES = [
    'Library_Db' => ['cols'=>['Library_id','BranchName','Location','LibraryManager','TotalBooks','StaffCount','MemberCount','BooksIssued','Status','Description'],'id'=>'Library_id'],
    'Faculty_Db' => ['cols'=>['Faculty_id','Library_id','FullName','Rank','DOB','Email','Address','AccountStatus'],'id'=>'Faculty_id'],
    'Student_Db' => ['cols'=>['Student_id','Library_id','Faculty_id','FullName','Rank','DOB','Email','City','Address','ContactNumber'],'id'=>'Student_id'],
    'Library_Staff_Db' => ['cols'=>['Staff_id','Library_id','FirstName','LastName','Email','Position','HireDate','ShiftTime','UserName','Password','Status','Phone'],'id'=>'Staff_id'],
    'Category_Db' => ['cols'=>['Category_id','CategoryName','Description'],'id'=>'Category_id'],
    'Book_Details_Db' => ['cols'=>['Book_id','Library_id','Category_id','PublisherName','AuthorName','BookName','Edition','PageCount','Description','CopyCount','Status'],'id'=>'Book_id'],
    'Warehouse_Db' => ['cols'=>['Storage_id','Library_id','Book_id','Location','ShelfNumber','Quantity','CurrentLoad','Status'],'id'=>'Storage_id'],
    'Transactions_Db' => ['cols'=>['Transaction_id','Faculty_id','Student_id','Book_id','IssueDate','ReturnDate','IssueBy','ReceiveBy','DueDate','Status','Note'],'id'=>'Transaction_id'],
    'Issue_Details_Db' => ['cols'=>['Issue_id','Student_id','Book_id','Faculty_id','IssueBy','IssueDate','ReturnDate'],'id'=>'Issue_id'],
    'Return_Details_Db' => ['cols'=>['Ret_id','Student_id','Book_id','ReceiveBy','IssueDate','ReturnDate','DueDate'],'id'=>'Ret_id'],
    'Penalty_Db' => ['cols'=>['Penalty_id','Student_id','Return_id','Amount','PenaltyDate','PaidStatus','DueDays'],'id'=>'Penalty_id'],
    'Registration_Db' => ['cols'=>['ID','Student_id','UserName','Password','Description'],'id'=>'ID'],
];

// helper
function bad($msg){ echo json_encode(['success'=>false,'message'=>$msg]); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';
$table = $_REQUEST['table'] ?? '';

// ----------------- Authentication endpoints -----------------
if ($action === 'login' && $method === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) { echo json_encode(['success'=>false,'message'=>'username & password required']); exit; }
    $users = read_users();
    foreach ($users as $u) {
        if (strcasecmp($u['username'], $username) === 0) {
            if (password_verify($password, $u['password'])) {
                // success -> set session
                $_SESSION['user'] = ['id'=>$u['id'],'username'=>$u['username'],'role'=>$u['role'] ?? 'user'];
                $resp = $_SESSION['user'];
                echo json_encode(['success'=>true,'user'=>$resp]);
                exit;
            } else {
                echo json_encode(['success'=>false,'message'=>'Invalid credentials']); exit;
            }
        }
    }
    echo json_encode(['success'=>false,'message'=>'Invalid credentials']); exit;
}

if ($action === 'logout' && $method === 'POST') {
    session_unset();
    session_destroy();
    echo json_encode(['success'=>true,'message'=>'Logged out']);
    exit;
}

if ($action === 'session' && $method === 'GET') {
    if (isset($_SESSION['user'])) {
        echo json_encode(['success'=>true,'user'=>$_SESSION['user']]);
    } else {
        echo json_encode(['success'=>false,'message'=>'No session']);
    }
    exit;
}

// ----------------- Users management (admin only) -----------------
if (in_array($action, ['users_list','users_add','users_update','users_delete'])) {
    // require login & admin
    if (!isset($_SESSION['user'])) bad('Not authenticated');
    if (($_SESSION['user']['role'] ?? '') !== 'admin') bad('Admin required');

    if ($action === 'users_list' && $method === 'GET') {
        $users = read_users();
        $out = array_map(function($u){ $u2 = $u; unset($u2['password']); return $u2; }, $users);
        echo json_encode(['success'=>true,'data'=>$out]); exit;
    }

    if ($action === 'users_add' && $method === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        if ($username === '' || $password === '') bad('username & password required');
        $users = read_users();
        foreach ($users as $u) if (strcasecmp($u['username'],$username)===0) bad('Username exists');
        $newId = count($users) ? (max(array_column($users,'id')) + 1) : 1;
        $new = ['id'=>$newId,'username'=>$username,'password'=>password_hash($password,PASSWORD_DEFAULT),'role'=>$role];
        $users[] = $new;
        write_users($users);
        $out = $new; unset($out['password']);
        echo json_encode(['success'=>true,'user'=>$out]); exit;
    }

    if ($action === 'users_update' && $method === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) bad('id required');
        $users = read_users();
        $found=false;
        foreach ($users as $i=>$u) {
            if ($u['id'] == $id) {
                $found=true;
                if (isset($_POST['username'])) $users[$i]['username'] = trim($_POST['username']);
                if (isset($_POST['password']) && $_POST['password'] !== '') $users[$i]['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                if (isset($_POST['role'])) $users[$i]['role'] = $_POST['role'];
                $out = $users[$i]; unset($out['password']);
                break;
            }
        }
        if (!$found) bad('User not found');
        write_users($users);
        echo json_encode(['success'=>true,'user'=>$out]); exit;
    }

    if ($action === 'users_delete' && $method === 'POST') {
        $id = intval($_POST['id'] ?? 0);
        if (!$id) bad('id required');
        $users = read_users();
        $found=false;
        foreach ($users as $i=>$u) {
            if ($u['id'] == $id) { $found=true; array_splice($users,$i,1); break; }
        }
        if (!$found) bad('User not found');
        write_users($users);
        echo json_encode(['success'=>true,'message'=>'Deleted']); exit;
    }
}

// ----------------- from here on: table-based actions -----------------
if (!isset($TABLES[$table])) bad('Invalid table');

$meta = $TABLES[$table];
$idcol = $meta['id'];
$cols = $meta['cols'];

// === LIST ===
if ($action === 'list' && $method === 'GET') {
    try {
        $sql = "SELECT " . implode(',', $cols) . " FROM [$table] ORDER BY $idcol DESC";
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();
        echo json_encode(['success'=>true,'data'=>$rows]);
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>'List error: '.$e->getMessage()]);
    }
    exit;
}

// === ADD ===
if ($action === 'add' && $method === 'POST') {
    $insertCols = array_filter($cols, fn($c)=> $c !== $idcol);
    $data = [];
    foreach ($insertCols as $c) {
        $data[$c] = array_key_exists($c, $_POST) ? ($_POST[$c] === '' ? null : $_POST[$c]) : null;
    }
    $fields = array_filter($insertCols, fn($c)=> isset($data[$c]));
    $placeholders = array_map(fn($c)=>':'.$c, $fields);
    if (count($fields) === 0) bad('No data to insert');
    $sql = "INSERT INTO [$table] (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    foreach ($fields as $f) $stmt->bindValue(':'.$f, $data[$f]);
    try {
        $stmt->execute();
        echo json_encode(['success'=>true,'message'=>'Inserted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>'Insert failed: '.$e->getMessage()]);
    }
    exit;
}

// === UPDATE ===
if ($action === 'update' && $method === 'POST') {
    $id = $_POST[$idcol] ?? null;
    if (!$id) bad('ID required for update');
    $updateCols = array_filter($cols, fn($c)=> $c !== $idcol);
    $setParts = [];
    $params = [];
    foreach ($updateCols as $c) {
        if (array_key_exists($c, $_POST)) {
            $setParts[] = "[$c] = :$c";
            $params[$c] = $_POST[$c] === '' ? null : $_POST[$c];
        }
    }
    if (count($setParts) === 0) bad('No fields to update');
    $sql = "UPDATE [$table] SET " . implode(', ', $setParts) . " WHERE [$idcol] = :_id";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k=>$v) $stmt->bindValue(':'.$k, $v);
    $stmt->bindValue(':_id', $id);
    try {
        $stmt->execute();
        echo json_encode(['success'=>true,'message'=>'Updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>'Update failed: '.$e->getMessage()]);
    }
    exit;
}

// === DELETE ===
if ($action === 'delete' && $method === 'POST') {
    $id = $_POST[$idcol] ?? null;
    if (!$id) bad('ID required for delete');
    $sql = "DELETE FROM [$table] WHERE [$idcol] = :_id";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([':_id'=>$id]);
        echo json_encode(['success'=>true,'message'=>'Deleted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success'=>false,'message'=>'Delete failed: '.$e->getMessage()]);
    }
    exit;
}

bad('Action not supported');
