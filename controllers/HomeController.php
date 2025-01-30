<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/ErrorHandler.php';

class HomeController extends BaseController {
    private $db;
    private $itemsPerPage = 5;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->validator = new Validator();
    }
    public function index() {
      try {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'event_datetime';
        $order = $_GET['order'] ?? '';

        $allowedSortFields = ['name', 'max_capacity'];
        $sort = in_array($sort, $allowedSortFields) ? $sort : 'event_datetime';
        $order = in_array(strtoupper($order), ['ASC', 'DESC']) ? strtoupper($order) : '';

        $offset = ($page - 1) * $this->itemsPerPage;
        $currentDateTime = date('Y-m-d H:i:s');
        $searchParam = "%{$search}%";

        $countQuery = "
            SELECT COUNT(*) as total 
            FROM events e
            WHERE e.event_datetime >= ?
            AND (e.name LIKE ? OR e.description LIKE ?)
        ";

        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute([$currentDateTime, $searchParam, $searchParam]);
        $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $query = "
            SELECT 
                e.*,
                u.name as creator_name,
                (SELECT COUNT(*) FROM event_attendees WHERE event_id = e.id) as attendees_count
            FROM events e
            JOIN users u ON e.user_id = u.id
            WHERE e.event_datetime >= ?
            AND (e.name LIKE ? OR e.description LIKE ?)
            ORDER BY " . $sort . " " . $order . "
            LIMIT " . (int)$this->itemsPerPage . " OFFSET " . (int)$offset;

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $currentDateTime,
            $searchParam,
            $searchParam
        ]);

        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalPages = max(1, ceil($totalCount / $this->itemsPerPage));

        $page = min($page, $totalPages);

        return $this->view('home/index', [
            'title' => 'Upcoming Events',
            'events' => $events,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'sort' => $sort,
            'order' => $order
        ]);

        } catch (PDOException $e) {
          ErrorHandler::handleError("An error occurred while fetching events. Please try again later.");
      }
    }
}