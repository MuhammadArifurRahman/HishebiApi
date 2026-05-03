<?php

require_once __DIR__ . '/.env.php';

header('Content-Type: text/html; charset=UTF-8');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
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
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💰</text></svg>">
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
        .api-link { display: inline-block; background: #3498db; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; margin: 5px 5px 5px 0; }
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
            <a href="<?php echo $baseUrl; ?>/api/cash-in" class="api-link">GET Cash In</a>
            <a href="<?php echo $baseUrl; ?>/api/cash-out" class="api-link">GET Cash Out</a>
            <a href="<?php echo $baseUrl; ?>/api/dena" class="api-link">GET Dena</a>
            <a href="<?php echo $baseUrl; ?>/api/paona" class="api-link">GET Paona</a>
            <a href="<?php echo $baseUrl; ?>/api/profile" class="api-link">GET Profile</a>
        </div>

        <div class="section">
            <h2>Base URL</h2>
            <code><?php echo $baseUrl; ?>/api/</code>
            <p>All endpoints use clean URLs via .htaccess rewriting</p>
        </div>

        <div class="section">
            <h2>A. Cash In API</h2>
            <p>Manage income/money received</p>
            
            <div class="endpoint">
                <h3><span class="method method-get">GET</span> Get All Cash In Entries</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/cash-in</span>
                <p>Query params: <code>?limit=10</code> <code>&amp;offset=0</code></p>
                <div class="example">
                    <div class="example-title">Response:</div>
                    <pre>{
  "success": true,
  "data": {
    "entries": [...],
    "pagination": { "limit": 10, "offset": 0 },
    "summary": { "total": 50000 }
  }
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-post">POST</span> Create Cash In Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/cash-in</span>
                <div class="example">
                    <div class="example-title">Request Body:</div>
                    <pre>{
  "title": "Salary",
  "amount": 25000,
  "date": "2026-05-01"
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-put">PUT</span> Update Cash In Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/cash-in/{id}</span>
            </div>

            <div class="endpoint">
                <h3><span class="method method-delete">DELETE</span> Delete Cash In Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/cash-in/{id}</span>
            </div>
        </div>

        <div class="section">
            <h2>B. Cash Out API</h2>
            <p>Manage expenses/money spent</p>
            
            <div class="endpoint">
                <h3><span class="method method-get">GET</span> Get All Cash Out Entries</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/cash-out</span>
                <p>Query params: <code>?limit=10</code> <code>&amp;offset=0</code></p>
                <div class="example">
                    <div class="example-title">Response:</div>
                    <pre>{
  "success": true,
  "data": {
    "entries": [...],
    "pagination": { "limit": 10, "offset": 0 },
    "summary": { "total": 5000 }
  }
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-post">POST</span> Create Cash Out Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/cash-out</span>
                <div class="example">
                    <div class="example-title">Request Body:</div>
                    <pre>{
  "title": "Groceries",
  "amount": 1500,
  "date": "2026-05-02"
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-put">PUT</span> Update Cash Out Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/cash-out/{id}</span>
            </div>

            <div class="endpoint">
                <h3><span class="method method-delete">DELETE</span> Delete Cash Out Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/cash-out/{id}</span>
            </div>
        </div>

        <div class="section">
            <h2>C. Dena API</h2>
            <p>Money you owe to others</p>
            
            <div class="endpoint">
                <h3><span class="method method-get">GET</span> Get All Dena Entries</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/dena</span>
                <div class="example">
                    <div class="example-title">Response:</div>
                    <pre>{
  "success": true,
  "data": {
    "entries": [...],
    "summary": { "totalOwe": 5000 }
  }
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-post">POST</span> Create Dena Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/dena</span>
                <div class="example">
                    <div class="example-title">Request Body:</div>
                    <pre>{
  "name": "Abir",
  "mobile": "01700000000",
  "amount": 500,
  "reason": "Borrowed for lunch"
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-put">PUT</span> Update Dena Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/dena/{id}</span>
            </div>

            <div class="endpoint">
                <h3><span class="method method-delete">DELETE</span> Delete Dena Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/dena/{id}</span>
            </div>
        </div>

        <div class="section">
            <h2>D. Paona API</h2>
            <p>Money others owe to you (receivable)</p>
            
            <div class="endpoint">
                <h3><span class="method method-get">GET</span> Get All Paona Entries</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/paona</span>
                <div class="example">
                    <div class="example-title">Response:</div>
                    <pre>{
  "success": true,
  "data": {
    "entries": [...],
    "summary": { "totalReceivable": 10000 }
  }
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-post">POST</span> Create Paona Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/paona</span>
                <div class="example">
                    <div class="example-title">Request Body:</div>
                    <pre>{
  "name": "John",
  "mobile": "01800000000",
  "amount": 1000,
  "reason": "Loan given"
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-put">PUT</span> Update Paona Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/paona/{id}</span>
            </div>

            <div class="endpoint">
                <h3><span class="method method-delete">DELETE</span> Delete Paona Entry</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/paona/{id}</span>
            </div>
        </div>

        <div class="section">
            <h2>E. Profile API</h2>
            <p>User profile management</p>
            
            <div class="endpoint">
                <h3><span class="method method-get">GET</span> Get Profile</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/profile</span>
                <div class="example">
                    <div class="example-title">Response:</div>
                    <pre>{
  "success": true,
  "data": {
    "name": "User",
    "email": "user@example.com"
  }
}</pre>
                </div>
            </div>

            <div class="endpoint">
                <h3><span class="method method-put">PUT</span> Update Profile</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/profile</span>
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
            <h2>F. Extract API</h2>
            <p>Natural language text extraction</p>
            
            <div class="endpoint">
                <h3><span class="method method-post">POST</span> Extract from Text</h3>
                <span class="path"><?php echo $baseUrl; ?>/api/extract</span>
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
  "date": "2026-05-03"
}</pre>
                </div>
            </div>
        </div>

        <div class="note">
            <strong>Note:</strong> All endpoints use clean URLs (no .php extension). 
            The .htaccess file handles URL routing automatically.
            <br><br>
            All write endpoints return JSON responses with <code>Content-Type: application/json</code>.
        </div>
    </div>
</body>
</html>