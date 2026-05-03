<?php

require_once dirname(__DIR__) . '/includes/Response.php';

class ExtractAPI {
    public function extract() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $input = $_POST;
        }

        if (!isset($input['text'])) {
            Response::badRequest("Missing required field: text");
        }

        $text = strtolower(trim($input['text']));

        $title = $this->extractTitle($text);
        $amount = $this->extractAmount($text);
        $type = $this->extractType($text);
        $date = $this->extractDate($text);

        Response::success([
            "title" => $title,
            "amount" => $amount,
            "type" => $type,
            "date" => $date
        ]);
    }

    private function extractTitle($text) {
        $words = preg_split('/\s+/', $text);
        
        $ignoreWords = ['i', 'gave', 'got', 'received', 'paid', 'spent', 'earned', 'income', 'expense', 
                       'sh', 'usd', 'kshs', 'ksh', 'dollar', 'dollars', 'for', 'to', 'from', 'on'];
        
        $titleWords = [];
        foreach ($words as $word) {
            $cleanWord = preg_replace('/[^a-z]/', '', $word);
            if ($cleanWord && !in_array($cleanWord, $ignoreWords) && !is_numeric($cleanWord)) {
                $titleWords[] = ucfirst($cleanWord);
            }
        }

        return !empty($titleWords) ? implode(' ', $titleWords) : 'Transaction';
    }

    private function extractAmount($text) {
        $patterns = [
            '/(\d+(?:\.\d{1,2})?)\s*(?:usd|dollars?|sh|kshs?|ksh)/i',
            '/(?:usd|dollars?|sh|kshs?|ksh)\s*(\d+(?:\.\d{1,2})?)/i',
            '/(\d+(?:\.\d{1,2})?)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return (float)$matches[1];
            }
        }

        return 0;
    }

    private function extractType($text) {
        $inKeywords = ['got', 'received', 'earned', 'income', 'salary', 'profit', 'gain', 'deposit'];
        $outKeywords = ['gave', 'paid', 'spent', 'expense', 'bought', 'purchase', 'food', 'transport', 'bill'];

        foreach ($inKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return 'in';
            }
        }

        foreach ($outKeywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return 'out';
            }
        }

        return 'out';
    }

    private function extractDate($text) {
        $today = date('Y-m-d');

        if (preg_match('/today/i', $text)) {
            return $today;
        }

        if (preg_match('/yesterday/i', $text)) {
            return date('Y-m-d', strtotime('-1 day'));
        }

        if (preg_match('/(\d{4})[\-\/](\d{1,2})[\-\/](\d{1,2})/', $text, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[1], $matches[2], $matches[3]);
        }

        if (preg_match('/(\d{1,2})[\-\/](\d{1,2})[\-\/](\d{4})/', $text, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[3], $matches[1], $matches[2]);
        }

        return $today;
    }
}

if (basename($_SERVER['SCRIPT_FILENAME']) === 'extract.php') {
    $api = new ExtractAPI();
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'POST':
            $api->extract();
            break;
        default:
            Response::badRequest("Method not allowed");
    }
}