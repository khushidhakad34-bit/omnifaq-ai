<?php
/**
 * OmniFAQ AI - Core API Router & Authentication Handler
 * ----------------------------------------------------------------------------
 * Handles Authentication (Login/Register), Field-based AI FAQ interactive 
 * questions (e.g. Car, Tech, Billing), Difficulty Modes (Easy, Medium, Hard),
 * CRUD management, and search analytics.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';

function getRequestData() {
    $data = array_merge($_GET, $_POST);
    $jsonRaw = file_get_contents('php://input');
    if (!empty($jsonRaw)) {
        $jsonDecoded = json_decode($jsonRaw, true);
        if (is_array($jsonDecoded)) {
            $data = array_merge($data, $jsonDecoded);
        }
    }
    return $data;
}

$input  = getRequestData();
$action = isset($input['action']) ? trim($input['action']) : '';

if (empty($action)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing action parameter.']);
    exit;
}

$pdo = getDbConnection();

try {
    switch ($action) {
        // Authentication Actions
        case 'register':
            handleRegister($pdo, $input);
            break;

        case 'login':
            handleLogin($pdo, $input);
            break;

        // FAQ Query Actions
        case 'get_faqs':
            handleGetFaqs($pdo, $input);
            break;

        case 'generate_ai_questions':
            handleGenerateAiQuestions($pdo, $input);
            break;

        case 'ai_query':
            handleAiQuery($pdo, $input);
            break;

        case 'ai_interactive_faq':
            handleAiInteractiveFaq($pdo, $input);
            break;

        // Admin Actions
        case 'admin_save_faq':
            handleAdminSaveFaq($pdo, $input);
            break;

        case 'admin_delete_faq':
            handleAdminDeleteFaq($pdo, $input);
            break;

        case 'admin_get_analytics':
            handleAdminGetAnalytics($pdo, $input);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Invalid action requested: '{$action}'."]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server Error', 'details' => $e->getMessage()]);
}

// ============================================================================
// Auth & Feature Handlers
// ============================================================================

/**
 * Action: register
 * Registers a new user account with hashed password.
 */
function handleRegister(PDO $pdo, array $input) {
    $fullName = isset($input['full_name']) ? trim($input['full_name']) : '';
    $email    = isset($input['email']) ? strtolower(trim($input['email'])) : '';
    $password = isset($input['password']) ? trim($input['password']) : '';

    if (empty($fullName) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Full name, email, and password are required.']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid email address format.']);
        return;
    }

    // Check existing email
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'An account with this email already exists.']);
        return;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->execute([$fullName, $email, $hashedPassword]);
    $userId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Welcome to OmniFAQ AI.',
        'user' => [
            'id'        => $userId,
            'full_name' => $fullName,
            'email'     => $email,
            'role'      => 'user'
        ]
    ]);
}

/**
 * Action: login
 * Verifies email and password.
 */
function handleLogin(PDO $pdo, array $input) {
    $email    = isset($input['email']) ? strtolower(trim($input['email'])) : '';
    $password = isset($input['password']) ? trim($input['password']) : '';

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Please provide both email and password.']);
        return;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode([
            'success' => true,
            'message' => 'Login successful!',
            'user' => [
                'id'        => $user['id'],
                'full_name' => $user['full_name'],
                'email'     => $user['email'],
                'role'      => $user['role']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid email or password.']);
    }
}

/**
 * Action: get_faqs
 * Filters FAQs based on category, difficulty, or search term.
 */
function handleGetFaqs(PDO $pdo, array $input) {
    $category   = isset($input['category']) ? trim($input['category']) : '';
    $difficulty = isset($input['difficulty']) ? trim($input['difficulty']) : '';
    $search     = isset($input['search']) ? trim($input['search']) : '';
    $faqId      = isset($input['id']) ? intval($input['id']) : 0;

    if ($faqId > 0) {
        $updateStmt = $pdo->prepare("UPDATE faqs SET views = views + 1 WHERE id = ?");
        $updateStmt->execute([$faqId]);

        $stmt = $pdo->prepare("SELECT * FROM faqs WHERE id = ?");
        $stmt->execute([$faqId]);
        $faq = $stmt->fetch();

        if ($faq) {
            echo json_encode(['success' => true, 'data' => $faq]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'FAQ item not found.']);
        }
        return;
    }

    $sql    = "SELECT * FROM faqs WHERE 1=1";
    $params = [];

    if (!empty($category) && strtolower($category) !== 'all') {
        $sql .= " AND category = ?";
        $params[] = $category;
    }

    if (!empty($difficulty) && strtolower($difficulty) !== 'all') {
        $sql .= " AND difficulty = ?";
        $params[] = $difficulty;
    }

    if (!empty($search)) {
        $sql .= " AND (question LIKE ? OR answer LIKE ? OR tags LIKE ?)";
        $searchTerm = '%' . $search . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $faqs = $stmt->fetchAll();

    if (!empty($search)) {
        $matchCount   = count($faqs);
        $isUnresolved = ($matchCount === 0) ? 1 : 0;
        $logStmt      = $pdo->prepare("INSERT INTO search_analytics (query_text, field_category, difficulty, matched_results_count, is_unresolved) VALUES (?, ?, ?, ?, ?)");
        $logStmt->execute([$search, $category ?: 'General', $difficulty ?: 'Easy', $matchCount, $isUnresolved]);
    }

    echo json_encode([
        'success' => true,
        'count'   => count($faqs),
        'data'    => $faqs
    ]);
}

/**
 * Action: ai_interactive_faq
 * Prompts questions & answers dynamically based on chosen Field (e.g. Car) and Difficulty Level (Easy, Medium, Hard).
 */
function handleAiInteractiveFaq(PDO $pdo, array $input) {
    $field      = isset($input['field']) ? trim($input['field']) : 'Car';
    $difficulty = isset($input['difficulty']) ? trim($input['difficulty']) : 'Easy';

    // Fetch FAQs matching field and difficulty
    $stmt = $pdo->prepare("SELECT * FROM faqs WHERE category = ? AND difficulty = ? ORDER BY RAND() LIMIT 4");
    $stmt->execute([$field, $difficulty]);
    $fieldFaqs = $stmt->fetchAll();

    // Fallback if none match exact difficulty
    if (empty($fieldFaqs)) {
        $stmt = $pdo->prepare("SELECT * FROM faqs WHERE category = ? ORDER BY RAND() LIMIT 4");
        $stmt->execute([$field]);
        $fieldFaqs = $stmt->fetchAll();
    }

    $introMessage = "Great choice! I have activated the **{$field}** domain mode on **[{$difficulty}]** difficulty level.\n\nHere are interactive questions & answers tailored to your selected field:";

    echo json_encode([
        'success'        => true,
        'field'          => $field,
        'difficulty'     => $difficulty,
        'ai_intro'       => $introMessage,
        'questions_list' => $fieldFaqs
    ]);
}

/**
 * Action: ai_query
 * Standard natural language AI query processing.
 */
function handleAiQuery(PDO $pdo, array $input) {
    $userQuery = isset($input['query']) ? trim($input['query']) : '';
    $field     = isset($input['field']) ? trim($input['field']) : '';

    if (empty($userQuery)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Query text is required.']);
        return;
    }

    $words = preg_split('/\s+/', strtolower($userQuery));
    $stopWords = ['how', 'to', 'do', 'i', 'the', 'a', 'an', 'what', 'is', 'are', 'in', 'on', 'can', 'for', 'where', 'my', 'or', 'and', 'with'];
    
    $cleanKeywords = [];
    foreach ($words as $w) {
        $clean = preg_replace('/[^a-z0-9]/', '', $w);
        if (strlen($clean) >= 3 && !in_array($clean, $stopWords)) {
            $cleanKeywords[] = $clean;
        }
    }

    if (empty($cleanKeywords)) {
        $cleanKeywords = [$userQuery];
    }

    $scoreConditions = [];
    $params = [];

    foreach ($cleanKeywords as $term) {
        $likeTerm = '%' . $term . '%';
        $scoreConditions[] = "(CASE WHEN question LIKE ? THEN 4 ELSE 0 END + CASE WHEN tags LIKE ? THEN 3 ELSE 0 END + CASE WHEN answer LIKE ? THEN 1 ELSE 0 END)";
        $params[] = $likeTerm;
        $params[] = $likeTerm;
        $params[] = $likeTerm;
    }

    $innerSql = "SELECT *, (" . implode(' + ', $scoreConditions) . ") AS relevance_score FROM faqs WHERE 1=1";
    if (!empty($field) && strtolower($field) !== 'all') {
        $innerSql .= " AND category = ?";
        $params[] = $field;
    }

    $sql = "SELECT * FROM ({$innerSql}) AS scored WHERE relevance_score > 0 ORDER BY relevance_score DESC, views DESC LIMIT 3";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $matches = $stmt->fetchAll();

    $matchCount   = count($matches);
    $isUnresolved = ($matchCount === 0) ? 1 : 0;

    $analyticsStmt = $pdo->prepare("INSERT INTO search_analytics (query_text, field_category, matched_results_count, is_unresolved) VALUES (?, ?, ?, ?)");
    $analyticsStmt->execute([$userQuery, $field ?: 'General', $matchCount, $isUnresolved]);

    if ($matchCount > 0) {
        $topMatchId = $matches[0]['id'];
        $pdo->prepare("UPDATE faqs SET views = views + 1 WHERE id = ?")->execute([$topMatchId]);

        $primaryAnswer  = $matches[0]['answer'];
        $aiResponseText = "Here is what I found regarding **\"" . htmlspecialchars($userQuery) . "\"**:\n\n" . $primaryAnswer;

        if (count($matches) > 1) {
            $aiResponseText .= "\n\n**Related Knowledge Topics:**\n";
            for ($i = 1; $i < count($matches); $i++) {
                $aiResponseText .= "- **" . htmlspecialchars($matches[$i]['question']) . "**: " . htmlspecialchars($matches[$i]['answer']) . "\n";
            }
        }

        echo json_encode([
            'success'       => true,
            'is_resolved'   => true,
            'ai_answer'     => $aiResponseText,
            'primary_match' => $matches[0],
            'sources'       => $matches,
            'confidence'    => ($matches[0]['relevance_score'] >= 4) ? 'High' : 'Medium'
        ]);
    } else {
        $aiResponseText = "I couldn't find an exact match in our knowledge base for **\"" . htmlspecialchars($userQuery) . "\"**.\n\nThis query has been logged for our domain team to add.";

        echo json_encode([
            'success'       => true,
            'is_resolved'   => false,
            'ai_answer'     => $aiResponseText,
            'primary_match' => null,
            'sources'       => [],
            'confidence'    => 'Unresolved'
        ]);
    }
}

/**
 * Action: admin_save_faq
 */
function handleAdminSaveFaq(PDO $pdo, array $input) {
    $id         = isset($input['id']) && !empty($input['id']) ? intval($input['id']) : null;
    $question   = isset($input['question']) ? trim($input['question']) : '';
    $answer     = isset($input['answer']) ? trim($input['answer']) : '';
    $category   = isset($input['category']) ? trim($input['category']) : 'General';
    $difficulty = isset($input['difficulty']) ? trim($input['difficulty']) : 'Easy';
    $tags       = isset($input['tags']) ? trim($input['tags']) : '';

    if (empty($question) || empty($answer)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Both Question and Answer fields are required.']);
        return;
    }

    if ($id && $id > 0) {
        $stmt = $pdo->prepare("UPDATE faqs SET question = ?, answer = ?, category = ?, difficulty = ?, tags = ? WHERE id = ?");
        $stmt->execute([$question, $answer, $category, $difficulty, $tags, $id]);
        $message = "FAQ ID {$id} updated successfully.";
        $faqId   = $id;
    } else {
        $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, category, difficulty, tags) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$question, $answer, $category, $difficulty, $tags]);
        $faqId   = $pdo->lastInsertId();
        $message = "New FAQ created successfully with ID {$faqId}.";
    }

    $fetchStmt = $pdo->prepare("SELECT * FROM faqs WHERE id = ?");
    $fetchStmt->execute([$faqId]);
    $savedFaq = $fetchStmt->fetch();

    echo json_encode([
        'success' => true,
        'message' => $message,
        'data'    => $savedFaq
    ]);
}

/**
 * Action: admin_delete_faq
 */
function handleAdminDeleteFaq(PDO $pdo, array $input) {
    $id = isset($input['id']) ? intval($input['id']) : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valid FAQ ID required.']);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => "FAQ ID {$id} deleted."]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => "FAQ ID {$id} not found."]);
    }
}

/**
 * Action: admin_get_analytics
 */
function handleAdminGetAnalytics(PDO $pdo, array $input) {
    $totalFaqs          = $pdo->query("SELECT COUNT(*) FROM faqs")->fetchColumn();
    $totalViews         = $pdo->query("SELECT COALESCE(SUM(views), 0) FROM faqs")->fetchColumn();
    $totalSearches      = $pdo->query("SELECT COUNT(*) FROM search_analytics")->fetchColumn();
    $unresolvedSearches = $pdo->query("SELECT COUNT(*) FROM search_analytics WHERE is_unresolved = 1")->fetchColumn();

    $recentStmt = $pdo->prepare("SELECT * FROM search_analytics ORDER BY searched_at DESC LIMIT 50");
    $recentStmt->execute();
    $recentLogs = $recentStmt->fetchAll();

    $unresolvedStmt = $pdo->prepare("SELECT query_text, COUNT(*) as frequency FROM search_analytics WHERE is_unresolved = 1 GROUP BY query_text ORDER BY frequency DESC LIMIT 10");
    $unresolvedStmt->execute();
    $topUnresolved = $unresolvedStmt->fetchAll();

    $catStmt = $pdo->prepare("SELECT category, COUNT(*) as faq_count, COALESCE(SUM(views), 0) as total_views FROM faqs GROUP BY category");
    $catStmt->execute();
    $categories = $catStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'summary' => [
            'total_faqs'          => intval($totalFaqs),
            'total_views'         => intval($totalViews),
            'total_searches'      => intval($totalSearches),
            'unresolved_searches' => intval($unresolvedSearches),
            'resolution_rate'     => $totalSearches > 0 ? round((($totalSearches - $unresolvedSearches) / $totalSearches) * 100, 1) . '%' : '100%'
        ],
        'recent_searches'  => $recentLogs,
        'top_unresolved'   => $topUnresolved,
        'category_metrics' => $categories
    ]);
}

/**
 * Action: generate_ai_questions
 * Connects securely to the Google Gemini API to generate technical interview questions for any searched topic,
 * inserts them into the MySQL database, and returns them to the frontend.
 */
function handleGenerateAiQuestions(PDO $pdo, array $input) {
    $topic = isset($input['topic']) ? trim($input['topic']) : '';

    if (empty($topic)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Please provide a technology topic to generate questions.']);
        return;
    }

    // Check if the API key is configured
    if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY) || GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY') {
        echo json_encode([
            'success' => false,
            'api_key_missing' => true,
            'error' => 'Gemini API Key is not configured. Please add your key in db.php to enable live AI generation.'
        ]);
        return;
    }

    // Craft prompt
    $prompt = 'Generate exactly 3 to 4 realistic technical interview questions on the topic: "' . $topic . '". ' .
              'Return a JSON array of objects. Each object must have: ' .
              '"question" (the interview question), ' .
              '"answer" (a highly detailed sample model answer/explanation suitable for a technical interview candidate), ' .
              '"category" (e.g. JavaScript, React, Node.js, SQL, System Design, or the specific topic name if different), ' .
              '"difficulty" (either "Easy", "Medium", or "Hard"), and ' .
              '"tags" (comma-separated lowercase tags). ' .
              'Do not return any markdown formatting outside of JSON. Return ONLY the raw JSON array. ' .
              'Example format: [{"question":"Q?","answer":"A.","category":"Cat","difficulty":"Medium","tags":"t1,t2"}]';

    $model = defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-1.5-flash';
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . GEMINI_API_KEY;

    $postData = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'responseMimeType' => 'application/json'
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local XAMPP environments where SSL certs might be missing
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to connect to Google Gemini API.',
            'details' => 'HTTP Code: ' . $httpCode . ', Response: ' . $response
        ]);
        return;
    }

    $responseData = json_decode($response, true);
    $generatedText = isset($responseData['candidates'][0]['content']['parts'][0]['text']) 
        ? trim($responseData['candidates'][0]['content']['parts'][0]['text']) 
        : '';

    // Strip markdown code block wrappers if the model still generated them
    if (strpos($generatedText, '```') !== false) {
        $generatedText = preg_replace('/```(?:json)?\s*([\s\S]*?)\s*```/', '$1', $generatedText);
    }
    $generatedText = trim($generatedText);

    $questions = json_decode($generatedText, true);

    if (!is_array($questions)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'API returned invalid JSON format.',
            'raw' => $generatedText
        ]);
        return;
    }

    // Insert questions into database and collect them with their IDs
    $savedFaqs = [];
    $insertStmt = $pdo->prepare("INSERT INTO faqs (question, answer, category, difficulty, tags) VALUES (?, ?, ?, ?, ?)");
    $fetchStmt = $pdo->prepare("SELECT * FROM faqs WHERE id = ?");

    foreach ($questions as $qItem) {
        $q = isset($qItem['question']) ? trim($qItem['question']) : '';
        $a = isset($qItem['answer']) ? trim($qItem['answer']) : '';
        $c = isset($qItem['category']) ? trim($qItem['category']) : $topic;
        $d = isset($qItem['difficulty']) ? trim($qItem['difficulty']) : 'Easy';
        $t = isset($qItem['tags']) ? trim($qItem['tags']) : '';

        if (!empty($q) && !empty($a)) {
            $insertStmt->execute([$q, $a, $c, $d, $t]);
            $faqId = $pdo->lastInsertId();

            $fetchStmt->execute([$faqId]);
            $savedFaqs[] = $fetchStmt->fetch();
        }
    }

    // Log this search as successfully resolved
    $logStmt = $pdo->prepare("INSERT INTO search_analytics (query_text, field_category, matched_results_count, is_unresolved) VALUES (?, ?, ?, 0)");
    $logStmt->execute([$topic, $topic, count($savedFaqs)]);

    echo json_encode([
        'success' => true,
        'count' => count($savedFaqs),
        'data' => $savedFaqs
    ]);
}
