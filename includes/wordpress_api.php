<?php
/**
 * WordPress API Integration for ANETI Login
 */

define('ANETI_API_URL', 'https://app.aneti.org.br/wp-json/aneti/v1/login');

/**
 * Authenticate user via ANETI WordPress API
 * 
 * @param string $email User email
 * @param string $password User password
 * @return array|false Returns user data on success, false on failure
 */
function authenticateViaAPI($email, $password) {
    // Prepare the data for API request
    $postData = json_encode([
        'email' => $email,
        'senha' => $password
    ]);
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt_array($ch, [
        CURLOPT_URL => ANETI_API_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postData)
        ],
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_FOLLOWLOCATION => true
    ]);
    
    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    curl_close($ch);
    
    // Check for cURL errors
    if ($curlError) {
        error_log("cURL Error in WordPress API: " . $curlError);
        return false;
    }
    
    // Check HTTP response code
    if ($httpCode !== 200) {
        error_log("WordPress API returned HTTP " . $httpCode . ": " . $response);
        return false;
    }
    
    // Decode JSON response
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return false;
    }
    
    // Check if authentication was successful
    if (isset($data['success']) && $data['success'] === true) {
        // Validate required fields
        if (!isset($data['user_id']) || !isset($data['nome']) || !isset($data['email']) || !isset($data['plano'])) {
            error_log("WordPress API response missing required fields");
            return false;
        }
        
        // Check if user plan has access to the club
        $allowedPlans = ['Júnior', 'Pleno', 'Sênior', 'Honra', 'Diretivo'];
        if (!in_array($data['plano'], $allowedPlans)) {
            return ['error' => 'Plano não dá acesso ao clube.'];
        }
        
        // Return user data
        return [
            'success' => true,
            'user_id' => $data['user_id'],
            'nome' => $data['nome'],
            'email' => $data['email'],
            'plano' => $data['plano']
        ];
    } else {
        // Authentication failed
        $errorMessage = isset($data['error']) ? $data['error'] : 'Credenciais inválidas.';
        return ['error' => $errorMessage];
    }
}

/**
 * Login user and create session
 * 
 * @param string $email User email
 * @param string $password User password
 * @return array Returns result with success status and message
 */
function loginUserViaAPI($email, $password) {
    $result = authenticateViaAPI($email, $password);
    
    if ($result === false) {
        return ['success' => false, 'message' => 'Erro de conexão com o servidor. Tente novamente.'];
    }
    
    if (isset($result['error'])) {
        return ['success' => false, 'message' => $result['error']];
    }
    
    if (isset($result['success']) && $result['success'] === true) {
        // Create session with mapped plan
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['user_nome'] = $result['nome'];
        $_SESSION['user_email'] = $result['email'];
        $_SESSION['user_plano'] = mapPlanToInternal($result['plano']);
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        return ['success' => true, 'message' => 'Login realizado com sucesso!'];
    }
    
    return ['success' => false, 'message' => 'Erro desconhecido durante o login.'];
}

/**
 * Map plan names to internal format
 * 
 * @param string $plano Plan name from API
 * @return string Internal plan format
 */
function mapPlanToInternal($plano) {
    $planMap = [
        'Júnior' => 'junior',
        'Pleno' => 'pleno',
        'Sênior' => 'senior',
        'Honra' => 'honra',
        'Diretivo' => 'diretivo'
    ];
    
    return isset($planMap[$plano]) ? $planMap[$plano] : 'junior';
}

/**
 * Get user plan display name
 * 
 * @param string $plano Internal plan name
 * @return string Display plan name
 */
function getPlanDisplayName($plano) {
    $planNames = [
        'junior' => 'Júnior',
        'pleno' => 'Pleno',
        'senior' => 'Sênior',
        'honra' => 'Honra',
        'diretivo' => 'Diretivo'
    ];
    
    return isset($planNames[$plano]) ? $planNames[$plano] : 'Júnior';
}
?>