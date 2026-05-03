<?php

require_once __DIR__ . '/.env.php';

header('Content-Type: text/html; charset=UTF-8');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptDir === '.' || $scriptDir === '/') {
    $scriptDir = '';
}
$baseUrl = $protocol . '://' . $host . $scriptDir;
if (substr($baseUrl, -1) === '/') {
    $baseUrl = substr($baseUrl, 0, -1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - REST API Documentation</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #333; line-height: 1.6; }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
        h1 { color: #2c3e50; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; }
        .section { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .section h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 15px; }
        .endpoint { margin-bottom: 20px; }
        .endpoint h3 { font-size: 16px; margin-bottom: 8px; }
        .method { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; color: white; margin-right: 8px; }
        .method-get { background: #27ae60; }
        .method-post { background: #3498db; }
        .method-put { background: #f39c12; }
        .method-delete { background: #e74c3c; }
        .path { font-family: monospace; background: #f8f9fa; padding: 8px 12px; border-radius: 4px; display: block; margin: 8px 0; }
        code { background: #ecf0f1; padding: 2px 6px; border-radius: 3px; }
        pre { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 13px; }
        .example { margin-top: 10px; }
        .example-title { font-size: 14px; color: #666; margin-bottom: 5px; }
        .note { background: #fff3cd; border-left: 4px solid #ffc107; padding: 10px 15px; margin-top: 20px; }
        .db-info { background: #e8f4fd; border-left: 4px solid #3498db; padding: 10px 15px; margin-bottom: 20px; }
        .alt-url { color: #7f8c8d; font-size: 13px; margin-top: 5px; }
        .api-link { display: inline-block; background: #3498db; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; margin: 5px 0; }
        .api-link:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo APP_NAME; ?></h1>
        <p class="subtitle">Simple REST API for Personal Accounting</p>

        <div class="db-info">
            <strong>Database:</strong> <?php echo DB_PATH; ?>
        </div>

        <div class="section">
            <h2>Test Links</h2>
            <p>Click to test the API endpoints:</p>
            <a href="<?php echo $baseUrl; ?>/api/transactions.php" class="api-link">GET Transactions</a>
            <a href="<?php echo $baseUrl; ?>/api/dues.php" class="api-link">GET Dues</a>
            <a href="<?php echo $baseUrl; ?>/api/profile.php" class="api-link">GET Profile</a>
        </div>

        <div class="section">
            <h2>Base URL</h2>
            <code><?php echo $baseUrl; ?>/</code>
            <p>You can access API via router or direct PHP files:</p>
        </div>

        <div class="section">
            <h2>A. Transactions API</h2>
            <p>Manage income and expense transactions</p>
            
            <div class="endpoint">
                <h3><span class="method method-get">GET</span> Get All Transactions</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/transactions.php</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/transactions</p>
                <p>Query params: <code>?limit=10</code> <code>&amp;offset=0</code></p>
                <div class="example">
                    <div class="example-title">Response:</div>
                    <pre>{
  "success": true,
  "data": {
    "transactions": [...],
    "pagination": { "limit": 10, "offset": 0 },
    "summary": { "cashIn": 1000, "cashOut": 500, "balance": 500 }
  }
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-post">POST</span> Create Transaction</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/transactions.php</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/transactions</p>
                <div class="example">
                    <div class="example-title">Request Body:</div>
                    <pre>{
  "title": "Salary",
  "amount": 5000,
  "type": "in",
  "date": "2024-01-15"
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-put">PUT</span> Update Transaction</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/transactions.php?id=1</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/transactions/1</p>
            </div>

            <div class="endpoint">
                <h3><span class="method method-delete">DELETE</span> Delete Transaction</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/transactions.php?id=1</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/transactions/1</p>
            </div>
        </div>

        <div class="section">
            <h2>B. Dues &amp; Debt API</h2>
            <p>Manage owed and receivable amounts</p>
            
            <div class="endpoint">
                <h3><span class="method method-get">GET</span> Get All Dues</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/dues.php</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/dues</p>
            </div>

            <div class="endpoint">
                <h3><span class="method method-post">POST</span> Create Due</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/dues.php</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/dues</p>
                <div class="example">
                    <div class="example-title">Request Body:</div>
                    <pre>{
  "name": "John Doe",
  "mobile": "+254700000000",
  "amount": 1000,
  "type": "owe",
  "reason": "Loan",
  "dueDate": "2024-02-01"
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-put">PUT</span> Update Due</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/dues.php?id=1</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/dues/1</p>
            </div>

            <div class="endpoint">
                <h3><span class="method method-delete">DELETE</span> Delete Due</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/dues.php?id=1</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/dues/1</p>
            </div>
        </div>

        <div class="section">
            <h2>C. User Profile API</h2>
            
            <div class="endpoint">
                <h3><span class="method method-get">GET</span> Get Profile</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/profile.php</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/profile</p>
            </div>

            <div class="endpoint">
                <h3><span class="method method-put">PUT</span> Update Profile</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/profile.php</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/profile</p>
                <div class="example">
                    <div class="example-title">Request Body:</div>
                    <pre>{
  "name": "John Doe",
  "email": "john@example.com"
}</pre>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>D. Extract API</h2>
            <p>Natural language text extraction</p>
            
            <div class="endpoint">
                <h3><span class="method method-post">POST</span> Extract from Text</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/extract.php</span>
                <p class="alt-url">Alt: <?php echo $baseUrl; ?>/api/extract</p>
                <div class="example">
                    <div class="example-title">Request Body:</div>
                    <pre>{
  "text": "I got 5000 salary today"
}</pre>
                    <div class="example-title">Response:</div>
                    <pre>{
  "title": "Salary",
  "amount": 5000,
  "type": "in",
  "date": "2024-01-15"
}</pre>
                </div>
            </div>
        </div>

        <div class="note">
            <strong>Note:</strong> All endpoints support two URL formats:
            <br><br>
            1. <strong>Direct PHP files:</strong> <code>/api/transactions.php</code> - Use <code>?id=1</code> for single record operations
            <br>
            2. <strong>Router format:</strong> <code>/api/transactions/1</code> - For single record, omit ID for list
            <br><br>
            All write endpoints return JSON responses with <code>Content-Type: application/json</code>. HTML documentation is shown only on root access.
        </div>
    </div>
</body>
</html>