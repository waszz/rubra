<?php

namespace App\Utils;

class FuzzyMatcher
{
    /**
     * Busca coincidencias difusas en un array de strings
     * 
     * @param string $query El término a buscar
     * @param array $items Array de items para buscar (puede ser array de strings o array de objects/arrays con 'nombre')
     * @param int $maxResults Máximo número de resultados a retornar (por defecto 5)
     * @param float $minSimilarity Similaridad mínima requerida 0-1 (por defecto 0.3)
     * @return array Array de resultados ordenados por similaridad descendente
     */
    public static function search(
        string $query,
        array $items,
        int $maxResults = 5,
        float $minSimilarity = 0.3
    ): array {
        if (empty($items) || empty(trim($query))) {
            return [];
        }

        $query = strtolower(trim($query));
        $queryWords = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);

        $results = [];

        foreach ($items as $item) {
            $itemText = self::extractText($item);
            if (empty($itemText)) continue;

            $itemText = strtolower($itemText);
            $itemWords = preg_split('/\s+/', $itemText, -1, PREG_SPLIT_NO_EMPTY);

            // Calcular similaridad como promedio de coincidencias de palabras clave
            $similarity = self::calculateSimilarity($queryWords, $itemWords);

            if ($similarity >= $minSimilarity) {
                $results[] = [
                    'item'        => $item,
                    'similarity'  => $similarity,
                    'distance'    => levenshtein($query, $itemText),
                ];
            }
        }

        // Ordenar por similaridad (descendente) y luego por distancia (ascendente)
        usort($results, function($a, $b) {
            if ($a['similarity'] != $b['similarity']) {
                return $b['similarity'] <=> $a['similarity'];
            }
            return $a['distance'] <=> $b['distance'];
        });

        return array_slice($results, 0, $maxResults);
    }

    /**
     * Calcula similaridad entre palabras clave
     */
    private static function calculateSimilarity(array $queryWords, array $itemWords): float
    {
        if (empty($queryWords)) {
            return 0;
        }

        $matches = 0;
        $totalScore = 0;

        foreach ($queryWords as $queryWord) {
            foreach ($itemWords as $itemWord) {
                // Similaridad parcial de palabras
                if (str_contains($itemWord, $queryWord) || str_contains($queryWord, $itemWord)) {
                    $matches++;
                    $totalScore += 0.9; // Alta puntuación para palabras contenidas
                    break;
                }

                // Calcular distancia Levenshtein normalizada (0-1)
                $distance = levenshtein($queryWord, $itemWord);
                $maxLen = max(strlen($queryWord), strlen($itemWord));
                
                if ($maxLen > 0) {
                    $similarity = 1 - ($distance / $maxLen);
                    if ($similarity >= 0.7) { // Solo considerar si es bastante similar
                        $totalScore += $similarity;
                        $matches++;
                        break;
                    }
                }
            }
        }

        if ($matches === 0) {
            return 0;
        }

        // Promedio de scores, ajustado por cobertura
        $coverage = $matches / count($queryWords);
        return ($totalScore / $matches) * $coverage;
    }

    /**
     * Extrae texto de diferentes tipos de items
     */
    private static function extractText($item): string
    {
        if (is_string($item)) {
            return $item;
        }

        if (is_array($item)) {
            return $item['nombre'] ?? $item['name'] ?? '';
        }

        if (is_object($item)) {
            if (isset($item->nombre)) return $item->nombre;
            if (isset($item->name)) return $item->name;
            if (method_exists($item, '__toString')) return (string) $item;
        }

        return '';
    }

    /**
     * Busca el mejor match de un query en un array
     * Útil para encontrar un recurso específico
     */
    public static function findBest(string $query, array $items, float $minSimilarity = 0.3)
    {
        $results = self::search($query, $items, 1, $minSimilarity);
        return $results[0] ?? null;
    }

    /**
     * Busca recursivamente palabras clave en un item
     * Útil para búsqueda de múltiples palabras (ej: "cable 2.5mm")
     */
    public static function searchMultiWord(
        string $query,
        array $items,
        array $wordWeights = []
    ): array {
        $query = strtolower(trim($query));
        $queryWords = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($queryWords)) {
            return [];
        }

        $results = [];

        foreach ($items as $item) {
            $itemText = self::extractText($item);
            if (empty($itemText)) continue;

            $itemText = strtolower($itemText);
            $matchScore = 0;

            // Verificar que contenga TODAS las palabras clave (conjunción)
            $allWordsFound = true;
            foreach ($queryWords as $word) {
                if (!str_contains($itemText, $word)) {
                    $allWordsFound = false;
                    break;
                }
            }

            if (!$allWordsFound) {
                continue;
            }

            // Calcular score de coincidencia (posición temprana es mejor)
            foreach ($queryWords as $word) {
                $pos = strpos($itemText, $word);
                $matchScore += (1 - ($pos / strlen($itemText)));
            }

            $matchScore = $matchScore / count($queryWords);
            $results[] = [
                'item'  => $item,
                'score' => $matchScore,
            ];
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        return $results;
    }
}
