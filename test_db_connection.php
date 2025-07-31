<?php
require_once 'config/database.php';

echo "Testando conexão com MySQL...\n";

try {
    // Test basic connection
    $result = $conn->query("SELECT 'Conexão MySQL bem-sucedida!' as status");
    echo $result->fetch()['status'] . "\n";
    
    // Test table creation
    $tables = $conn->query("SHOW TABLES");
    echo "Tabelas criadas:\n";
    while($table = $tables->fetch()) {
        echo "- " . $table['Tables_in_aneti_clube'] . "\n";
    }
    
    // Test data insertion
    $categorias = $conn->query("SELECT COUNT(*) as count FROM categorias");
    echo "\nCategorias inseridas: " . $categorias->fetch()['count'] . "\n";
    
    $empresas = $conn->query("SELECT COUNT(*) as count FROM empresas");
    echo "Empresas inseridas: " . $empresas->fetch()['count'] . "\n";
    
    $usuarios = $conn->query("SELECT COUNT(*) as count FROM usuarios");
    echo "Usuários inseridos: " . $usuarios->fetch()['count'] . "\n";
    
    $cupons = $conn->query("SELECT COUNT(*) as count FROM cupons");
    echo "Cupons inseridos: " . $cupons->fetch()['count'] . "\n";
    
    echo "\n✅ Migração para MySQL concluída com sucesso!\n";
    
} catch(Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "\n";
}
?>