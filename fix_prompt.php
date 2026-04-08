<?php
$file = 'app/Livewire/Proyecto/ChatbotRubi.php';
$content = file_get_contents($file);

// Split by PROMPT;
$parts = explode('PROMPT;', $content);

if (count($parts) >= 3) {
    // Keep first part + first PROMPT; + first part of second section + rest
    $newContent = $parts[0] . 'PROMPT;' . PHP_EOL . PHP_EOL;
    $newContent .= '        // Construir historial — Groq usa formato OpenAI (igual que Claude pero con system en el array)' . PHP_EOL;
    $newContent .= $parts[2];
    
    file_put_contents($file, $newContent);
    echo "File cleaned: removed duplicate PROMPT section\n";
} else {
    echo "Could not find multiple PROMPT; sections\n";
    echo "Found " . count($parts) . " parts\n";
}
?>
