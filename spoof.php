<?php
// Form data
$phone = '09051547147';  // Replace with actual phone input
$password = '282200123';  // Replace with actual password input

// Initialize a cURL session
$ch = curl_init();

// Set the URL to which the POST request is to be sent
curl_setopt($ch, CURLOPT_URL, 'https://orentify.com/middleMan/login.php');

// Set the cURL option for a POST request
curl_setopt($ch, CURLOPT_POST, true);

// Set the POST fields (form data)
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'phone' => $phone,
    'password' => $password
]));

// Set custom HTTP headers to spoof the request
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Referer: https://orentify.com',  // Spoof the Referer header
    'Origin: https://orentify.com',   // Spoof the Origin header
    'Host: orentify.com',        // Custom Host
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',  // Spoofed User-Agent
));

// Enable response headers capture
curl_setopt($ch, CURLOPT_HEADER, 1);

// Return the response instead of outputting it directly
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
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

// Close the cURL session
curl_close($ch);

// Set cookies locally if any were returned
if (!empty($cookies)) {
    foreach ($cookies as $name => $value) {
        setcookie($name, $value, time() + 3600, "/");  // 1-hour cookie expiration
    }
}

// Output the response body (content)
echo $body;
?>
