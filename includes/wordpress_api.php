<?php
/**
 * WordPress API Integration for ANETI Login
 */

define('ANETI_API_URL', 'https://app.aneti.org.br/wp-json/aneti/v1/login');
define('ENABLE_API_DEBUG', false); // Set to false in production

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
            'Content-Length: ' . strlen($postData),
            'User-Agent: ANETI-Club/1.0'
        ],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3
    ]);
    
    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlInfo = curl_getinfo($ch);
    
    curl_close($ch);
    
    // Debug logging (remove in production)
    if (ENABLE_API_DEBUG) {
        error_log("API Request Debug - URL: " . ANETI_API_URL);
        error_log("API Request Debug - HTTP Code: " . $httpCode);
        error_log("API Request Debug - Response: " . $response);
        error_log("API Request Debug - cURL Error: " . $curlError);
        error_log("API Request Debug - Total Time: " . $curlInfo['total_time']);
    }
    
    // Check for cURL errors
    if ($curlError) {
        error_log("cURL Error in WordPress API: " . $curlError);
        return ['error' => 'Erro de conexão com o servidor. Verifique sua conexão com a internet e tente novamente.'];
    }
    
    // Check HTTP response code
    if ($httpCode !== 200) {
        error_log("WordPress API returned HTTP " . $httpCode . ": " . $response);
        
        // Handle specific HTTP codes
        if ($httpCode === 0) {
            return ['error' => 'Não foi possível conectar ao servidor. Verifique sua conexão com a internet.'];
        } elseif ($httpCode === 401) {
            // Unauthorized - Invalid credentials
            $errorData = json_decode($response, true);
            if (isset($errorData['error'])) {
                if (strpos($errorData['error'], 'Credenciais') !== false) {
                    return ['error' => 'E-mail ou senha incorretos. Verifique seus dados e tente novamente.'];
                }
                return ['error' => $errorData['error']];
            }
            return ['error' => 'E-mail ou senha incorretos. Verifique seus dados e tente novamente.'];
        } elseif ($httpCode === 403) {
            // Forbidden - Usually plan access issues
            $errorData = json_decode($response, true);
            if (isset($errorData['error'])) {
                // Check for specific error messages
                if (strpos($errorData['error'], 'sem nível de associação') !== false || 
                    strpos($errorData['error'], 'Usuário sem nível') !== false) {
                    return ['error' => 'Sua anuidade ANETI não está ativa. Para acessar o Clube de Vantagens, é necessário ter uma anuidade ativa.', 'show_membership_link' => true];
                } elseif (strpos($errorData['error'], 'Plano não dá acesso') !== false) {
                    return ['error' => 'Seu plano atual não dá acesso ao Clube de Vantagens. Entre em contato com a ANETI para mais informações.'];
                }
                return ['error' => $errorData['error']];
            }
            return ['error' => 'Acesso negado pelo servidor.'];
        } elseif ($httpCode === 404) {
            return ['error' => 'Serviço de login temporariamente indisponível.'];
        } elseif ($httpCode >= 500) {
            return ['error' => 'Servidor temporariamente indisponível. Tente novamente em alguns minutos.'];
        } else {
            return ['error' => 'Erro de conexão com o servidor (HTTP ' . $httpCode . ').'];
        }
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
        // Check if it's a connection error and offer fallback
        $errorMsg = $result['error'];
        if (strpos($errorMsg, 'conexão') !== false || strpos($errorMsg, 'conectar') !== false) {
            $errorMsg .= ' Se o problema persistir, entre em contato com o suporte da ANETI.';
        }
        return ['success' => false, 'message' => $errorMsg];
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